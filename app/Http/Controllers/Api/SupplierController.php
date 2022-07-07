<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $builder = Supplier::query();
        if ($name = $request->input('name')) {
            $builder->where('name', 'like', '%'. $name .'%');
        }

        if ($email = $request->input('email')) {
            $builder->where('email', 'like', '%'. $email .'%');
        }

        if ($request->has('page') && $request->has('pageSize')) {
            list($pageSize, $offset) = $this->getPaginationParams($request);
            $total = $builder->count();
            $stores = $builder->offset($offset)->limit($pageSize)->orderBy('created_at', 'desc')->get();
            $items = SupplierResource::collection($stores);
            return $this->success(compact('total', 'items'));
        }

        return $this->success(SupplierResource::collection($builder->get()));
    }

    public function store(SupplierRequest $request)
    {
        $saveData = $request->all();
        $saveData['created_user_id'] = auth('api')->id();
        $supplier = new Supplier($saveData);
        $supplier->save();
        return $this->success();
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $saveData = $request->all();
        $supplier->fill($saveData);
        $supplier->save();
        return $this->success(new SupplierResource($supplier));
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->status = !$supplier->status;
        $supplier->save();
        return $this->success();
    }
}
