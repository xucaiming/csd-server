<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\MaterialTypeRequest;
use App\Http\Resources\MaterialTypeResource;
use App\Models\MaterialType;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    public function index()
    {
        $materialTypes = MaterialType::query()
            ->orderBy('id', 'desc')
            ->get();
        return $this->success(MaterialTypeResource::collection($materialTypes));
    }

    public function store(MaterialTypeRequest $request)
    {
        $saveData = $request->all();

        $materialType = new MaterialType($saveData);
        $materialType->save();

        return $this->success(new MaterialTypeResource($materialType->refresh()));
    }

    public function update(materialTypeRequest $request, MaterialType $materialType)
    {
        $saveData = $request->all();

        $materialType->fill($saveData);
        $materialType->save();

        return $this->success(new MaterialTypeResource($materialType->refresh()));
    }

    public function destroy(MaterialType $materialType)
    {
        $materialType->delete();
        return $this->success();
    }
}
