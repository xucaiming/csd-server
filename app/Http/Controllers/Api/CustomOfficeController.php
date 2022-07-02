<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomOfficeRequest;
use App\Http\Resources\CustomOfficeResource;
use App\Models\CustomOffice;
use Illuminate\Http\Request;

class CustomOfficeController extends Controller
{
    public function index(Request $request)
    {
        $builder = CustomOffice::query();

        if ($company_id = $request->input('company_id')) {
            $builder->where('company_id', $company_id);
        }

        if ($factory_part_id = $request->input('factory_part_id')) {
            $builder->where('factory_part_id', $factory_part_id);
        }

        if ($department_id = $request->input('department_id')) {
            $builder->where('department_id', $department_id);
        }

        $offices = $builder->orderBy('id', 'desc')->get();
        return $this->success(CustomOfficeResource::collection($offices));
    }

    public function store(CustomOfficeRequest $request)
    {
        $saveData = $request->all();

        $office = new CustomOffice($saveData);
        $office->save();

        return $this->success(new CustomOfficeResource($office->refresh()));
    }

    public function update(CustomOfficeRequest $request, CustomOffice $customOffice)
    {
        $saveData = $request->all();

        $customOffice->fill($saveData);
        $customOffice->save();

        return $this->success(new CustomOfficeResource($customOffice->refresh()));
    }

    public function destroy(CustomOffice $customOffice)
    {
        $customOffice->delete();
        return $this->success();
    }
}
