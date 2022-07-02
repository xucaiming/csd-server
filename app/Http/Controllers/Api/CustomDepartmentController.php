<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomDepartmentRequest;
use App\Http\Resources\CustomDepartmentResource;
use App\Models\CustomDepartment;
use Illuminate\Http\Request;

class CustomDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $builder = CustomDepartment::query();

        if ($company_id = $request->input('company_id')) {
            $builder->where('company_id', $company_id);
        }

        if ($factory_part_id = $request->input('factory_part_id')) {
            $builder->where('factory_part_id', $factory_part_id);
        }

        $departments = $builder->orderBy('id', 'desc')->get();
        return $this->success(CustomDepartmentResource::collection($departments));
    }

    public function store(CustomDepartmentRequest $request)
    {
        $saveData = $request->all();

        $department = new CustomDepartment($saveData);
        $department->save();

        return $this->success(new CustomDepartmentResource($department->refresh()));
    }

    public function update(CustomDepartmentRequest $request, CustomDepartment $customDepartment)
    {
        $saveData = $request->all();

        $customDepartment->fill($saveData);
        $customDepartment->save();

        return $this->success(new CustomDepartmentResource($customDepartment->refresh()));
    }

    public function destroy(CustomDepartment $customDepartment)
    {
        $customDepartment->delete();
        return $this->success();
    }
}
