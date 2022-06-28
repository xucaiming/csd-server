<?php

namespace App\Http\Controllers\Api;
use App\Exports\MaterialImportTemplate;
use App\Http\Requests\Api\MaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Imports\MaterialImport;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\MaterialUnit;
use App\Services\BaseService;
use App\Services\MaterialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialImageFile;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    const RelationShip = [
        'materialType',
        'materialUnit',
        'materialImageFiles',
        'materialDrawingFile.createdUser',
        'createdUser',
    ];

    public function save(MaterialRequest $request, Material $material, MaterialService $service)
    {
        if ($id = $request->input('id', 0)) {
            $material = Material::query()->findOrFail($id);
        }

        DB::beginTransaction();
        try {
            $user = auth('api')->user();
            $saveData = $request->all();
            $saveData['created_user_id'] = $user->id;
            $material->fill($saveData);
            $material->save();

            if ($request->hasFile('drawing_file')) {
                if ($drawingFileData = $service->uploadFile($request->file('drawing_file'), 'materialDrawings', $user)) {
                    if ($material->materialDrawingFile) {
                        try {
                            unlink(public_path($material->materialDrawingFile->file_path));
                        } catch (\Exception $e) {}
                    }
                    $material->materialDrawingFile()->updateOrCreate([
                        'material_id' => $material->id,
                    ], [
                        'material_id' => $material->id,
                        'number' => $request->input('drawing_number'),
                        'original_name' => $drawingFileData['original_name'],
                        'file_path' => $drawingFileData['file_path'],
                        'created_user_id' => $user->id,
                    ]);
                }
            } else {
                $material->materialDrawingFile()->update([
                    'number' => $request->input('drawing_number'),
                ]);
            }

            $images = $request->images;
            $postedImageIds = $originalImageIds = [];
            if ($material->materialImageFiles) {
                $originalImageIds = $material->materialImageFiles->pluck('id')->toArray();
            }
            foreach ($images as $k => $v) {
                if ($request->hasFile('images.' . $k)) {
                    if ($imageFileData = $service->uploadFile($request->file('images.' . $k), 'materialImages', $user)) {
                        $imageFile = new MaterialImageFile([
                            'material_id' => $material->id,
                            'file_path' => $imageFileData['file_path'],
                        ]);
                        $imageFile->save();
                    }
                }
                if (is_array($v) && isset($v['id'])) {
                    array_push($postedImageIds, $v['id']);
                }
            }
            $deletingImageFiles = MaterialImageFile::query()
                ->whereIn('id', array_diff($originalImageIds, $postedImageIds))
                ->get();

            foreach ($deletingImageFiles as $file) {
                try {
                    unlink(public_path($file->file_path));
                } catch (\Exception $e) {}
                $file->delete();
            }
            DB::commit();
            return $this->success(new MaterialResource($material->refresh()->load(self::RelationShip)));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info('MaterialController@save', [$e->getMessage(), $e->getTrace()]);
            return $this->failed('服务器内部错误！');
        }
    }

    public function index(Request $request, BaseService $service)
    {
        $builder = Material::query()->with(self::RelationShip);

        if ($code = $request->input('code')) {
            $code_arr = explode(',', $code);
            if (count($code_arr) == 1) {
                $builder->where('code', 'like', "%$code%");
            } else {
                $builder->whereIn('code', $code_arr);
            }
        }

        if ($custom_code = $request->input('custom_code')) {
            $custom_code_arr = explode(',', $custom_code);
            if (count($custom_code_arr) == 1) {
                $builder->where('custom_number', $custom_code);
            } else {
                $builder->whereIn('custom_number', $custom_code_arr);
            }
        }

        if ($name = $request->input('name')) {
            $builder->where('name', 'like', '%'. $name .'%');
        }

        if ($material_type_id = $request->input('material_type_id')) {
            $builder->where('material_type_id', $material_type_id);
        }

        if ($factory_code = $request->input('factory_code')) {
            $factory_code_arr = explode(',', $factory_code);
            if (count($factory_code_arr) == 1) {
                $builder->where('factory_code', $factory_code);
            } else {
                $builder->whereIn('factory_code', $factory_code_arr);
            }
        }

        list($pageSize, $offset) = $this->getPaginationParams($request);

        $total = $builder->count();
        $materials = $builder->offset($offset)->limit($pageSize)
            ->orderBy('id', 'desc')
            ->get();

        $items = MaterialResource::collection($materials);

        return $this->success(compact('total', 'items'));
    }

    public function downloadTemplate(MaterialImportTemplate $template)
    {
        return $template;
    }

    public function getExcelData(Request $request, MaterialImport $import, MaterialService $service)
    {
        $importData = changExcelRowToDataMap([
            'code',
            'name',
            'unit_name',
            'type_name',
            'factory_code',
            'material_images',
            'remark',
        ], $import->getExcelData($request->file));
        $allPassed = $service->makeValid($importData);
        return $this->success(compact('allPassed', 'importData'));
    }

    public function checkExcelData(Request $request, MaterialService $service)
    {
        $importData = $request->input('importData', []);
        changeImportDataEmptyStringToNull($importData);
        $allPassed = $service->makeValid($importData);

        return $this->success(compact('allPassed', 'importData'));
    }

    public function saveExcelData(Request $request, MaterialService $service, MaterialImport $import)
    {
        $importData = $request->input('importData', []);
        $currentUser = auth('api')->user();

        $units = array_column(MaterialUnit::all()->toArray(), 'id', 'name');
        $types = array_column(MaterialType::all()->toArray(), 'id', 'name');

        if ($allPassed = $service->makeValid($importData)) {
            DB::beginTransaction();
            try {
                foreach ($importData as $row) {
                    $saveData = [
                        'code' => $row['code'],
                        'factory_code' => $row['factory_code'] ?? NULL,
                        'name' => $row['name'],
                        'material_type_id' => $types[$row['type_name']],
                        'material_unit_id' => $units[$row['unit_name']],
                        'remark' => $row['remark'] ?? NULL,
                        'created_user_id' => $currentUser->id,
                    ];
                    $material = new Material($saveData);
                    $material->save();

                    if (isset($row['material_images']) && is_array($row['material_images'])) {
                        foreach ($row['material_images'] as $path) {
                            $filePath = 'uploads/materialImages/' . date("Ym/d");
                            $filename = $currentUser->id . '-' .  Str::random(15) . '.' . pathinfo($path)['extension'];
                            createFolder($filePath);
                            copy(public_path($path), public_path($filePath . '/' . $filename));
                            $materialImage = new MaterialImageFile([
                                'material_id' => $material->id,
                                'file_path' => $filePath . '/' . $filename,
                            ]);
                            $materialImage->save();
                        }
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                \Log::info('MaterialController@saveExcelData', [$e->getMessage(), $e->getTrace()]);
                DB::rollBack();
                return $this->failed('服务器内部错误');
            }
        }
        $import->clearTempFiles();
        return $this->success(compact('allPassed', 'importData'));
    }
}
