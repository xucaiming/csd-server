<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InternalRequestException;
use App\Exports\OrderMaterialImportTemplate;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderStatusResource;
use App\Imports\DataImport;
use App\Models\CustomWindow;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\MaterialUnit;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\OrderStatus;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    const RelationShip = [
        'customCompany',
        'orderStatus',
        'orderMaterials.material.materialType',
        'orderMaterials.material.materialUnit',
        'orderMaterials.material.materialImageFiles',
        'orderMaterials.material.materialDrawingFile',
        'orderMaterials.customFactoryPart',
        'orderMaterials.customDepartment',
        'orderMaterials.customOffice',
        'orderMaterials.customWindow',
        'paymentType',
        'orderFile',
        'subsector',
    ];

    public function save(OrderRequest $request, OrderService $service)
    {
        if ($id = $request->input('id')) {
            $order = Order::query()->findOrFail($id);
        } else {
            $order = new Order();
        }

        $posted_materials = $request->input('order_materials', []);
        $allPassed = $service->makeMaterialDataValid($posted_materials);

        if (!$allPassed) {
            return $this->success(compact('allPassed', 'posted_materials'));
        }

        $currentUser = auth('api')->user();
        $orderSaveData = $request->all();
        $orderSaveData['created_user_id'] = $currentUser->id;
        $orderSaveData['subsector_id'] = $request->header('subsector');
        $order->fill($orderSaveData);
        $order->save();

        if ($request->hasFile('order_file')) {
            if ($order->orderFile) {
                try {
                    unlink(public_path($order->file->file_path));
                } catch (\Exception $e) {}
            }
            if ($fileData = $service->uploadFile($request->file('order_file'), 'order', $currentUser)) {
                $order->orderFile()->updateOrCreate([
                    'order_id' => $order->id,
                ], [
                    'order_id' => $order->id,
                    'original_name' => $fileData['original_name'],
                    'file_path' => $fileData['file_path'],
                ]);
            }
        }

        $postedIds = array_column($posted_materials, 'id');
        $originIds = $order->orderMaterials->pluck('id')->toArray();
        $diffIds = array_diff($originIds, $postedIds);
        $order->orderMaterials()->whereIn('id', $diffIds)->delete();

        foreach ($posted_materials as $posted_material) {
            $window = CustomWindow::query()->findOrFail($posted_material['window_id']);
            $orderMaterialSaveData = [
                'factory_part_id' => $window->factory_part_id,
                'department_id' => $window->department_id,
                'office_id' => $window->office_id,
                'window_id' => $posted_material['window_id'],
                'quantity' => $posted_material['quantity'],
                'tax_rate' => $posted_material['tax_rate'],
                'delivery_date' => $posted_material['delivery_date'],
                'order_id' => $order->id,
            ];

            if (isset($posted_material['unit_price'])) {
                $orderMaterialSaveData['unit_price'] = $posted_material['unit_price'];
                $orderMaterialSaveData['tax_unit_price'] = round($posted_material['unit_price'] * (100 + $posted_material['tax_rate']) / 100, 2);
                $orderMaterialSaveData['total_price'] = round($posted_material['unit_price'] * $posted_material['quantity'], 2);
                $orderMaterialSaveData['total_rate_price'] = round(($posted_material['unit_price'] * (100 + $posted_material['tax_rate']) / 100) * $posted_material['quantity'], 2);
            }

            if (isset($posted_material['tax_unit_price']) && !isset($posted_material['unit_price'])) {
                $orderMaterialSaveData['unit_price'] = round(100 * $posted_material['tax_unit_price'] / (100 + $posted_material['tax_rate']), 2);
                $orderMaterialSaveData['tax_unit_price'] = $posted_material['tax_unit_price'];
                $orderMaterialSaveData['total_price'] = round((100 * $posted_material['tax_unit_price'] / (100 + $posted_material['tax_rate'])) * $posted_material['quantity'], 2);
                $orderMaterialSaveData['total_rate_price'] = round($posted_material['tax_unit_price'] * $posted_material['quantity'], 2);
            }

            if (!$material = Material::query()->where('code', $posted_material['material_code'])->first()) {
                $material = new Material([
                    'code' => $posted_material['material_code'],
                    'name' => $posted_material['material_name'],
                    'material_type_id' => $posted_material['material_type_id'],
                    'material_unit_id' => $posted_material['material_unit_id'],
                    'created_user_id' => $currentUser->id,
                ]);
                $material->save();
            }
            $orderMaterialSaveData['material_id'] = $material->id;

            if (isset($posted_material['id'])) {
                $order_material = OrderMaterial::query()->findOrFail($posted_material['id']);
            } else {
                $order_material = new OrderMaterial($orderMaterialSaveData);
            }
            $order_material->save();
        }

        $item = new OrderResource($order->refresh()->load(self::RelationShip));
        return $this->success(compact('allPassed', 'item'));
    }

    public function index(Request $request)
    {
        $builder = Order::query()->with([
            'customCompany',
            'orderStatus',
            'paymentType',
            'orderFile',
            'subsector',
        ]);
        //$builder = Order::query()->with(self::RelationShip);

        $subsector_id = $request->header('subsector');
        $builder->where('subsector_id', $subsector_id);

        list($pageSize, $offset) = $this->getPaginationParams($request);

        $statusCountArr = $builder->clone()
            ->select(DB::raw('count(*) as count, order_status_id as status_id'))
            ->groupBy('order_status_id')
            ->get();

        if ($status_id = $request->input('status_id', 0)) {
            $builder->where('order_status_id', $status_id);
        }

        $total = $builder->count();
        $orders = $builder->offset($offset)->limit($pageSize)->orderBy('created_at', 'desc')->get();

        $items = [
            'list' => OrderResource::collection($orders),
            'status_count' => $statusCountArr,
        ];

        return $this->success(compact('items', 'total'));
    }

    public function statuses()
    {
        return $this->success(OrderStatusResource::collection(OrderStatus::all()));
    }

    public function downloadMaterialTemplate(OrderMaterialImportTemplate $template)
    {
        return $template;
    }

    public function getMaterialExcelData(Request $request, DataImport $import, OrderService $service)
    {
        if (!$company_id = $request->input('company_id')) {
            throw new InternalRequestException('请先选择客户公司再读取Excel数据');
        }

        $excelData = array_slice(\Excel::toArray($import, $request->file)[0], 1);
        //\Log::info(json_encode($excelData, JSON_UNESCAPED_UNICODE));
        $types = array_column(MaterialType::all()->toArray(), 'id', 'name');
        $units = array_column(MaterialUnit::all()->toArray(), 'id', 'name');

        $currentSubsectorWindows = CustomWindow::query()->where('company_id', $company_id)->get()->toArray();
        $windows = array_column($currentSubsectorWindows, 'id', 'name');

        try {
            $importData = [];
            foreach ($excelData as $key => $value) {
                $orderMaterial['material_code'] = $value[0];
                $orderMaterial['material_name'] = $value[1];
                $orderMaterial['material_type_id'] = $types[$value[2]] ?? NULL;
                $orderMaterial['material_unit_id'] = $units[$value[3]] ?? NULL;
                $orderMaterial['quantity'] = $value[4];
                $orderMaterial['window_id'] = $windows[$value[5]] ?? NULL;
                $orderMaterial['unit_price'] = $value[6];
                $orderMaterial['tax_unit_price'] = $value[7];
                $orderMaterial['tax_rate'] = $value[8];
                $orderMaterial['delivery_date'] = $value[9];

                array_push($importData, $orderMaterial);
            }
            $allPassed = $service->makeMaterialDataValid($importData);

            return $this->success(compact('allPassed', 'importData'));
        } catch (\Exception $e) {
            return $this->failed('请检查选择的Excel文件是否和模板对应');
        }

    }

    public function checkMaterialExcelData(Request $request, OrderService $service)
    {
        $importData = $request->input('importData', []);
        changeImportDataEmptyStringToNull($importData);
        $allPassed = $service->makeMaterialDataValid($importData);

        return $this->success(compact('allPassed', 'importData'));
    }

    public function show($id)
    {
        $order = Order::with(self::RelationShip)->findOrFail($id);
        return $this->success(new OrderResource($order));
    }
}
