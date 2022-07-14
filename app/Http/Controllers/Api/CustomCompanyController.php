<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomCompanyRequest;
use App\Http\Resources\CustomCompanyResource;
use App\Models\CustomCompany;
use Illuminate\Http\Request;

class CustomCompanyController extends Controller
{
    public function index(Request $request)
    {
        $builder = CustomCompany::query()->with('subsector')->orderBy('id', 'desc');
        if ($subsector_id = $request->header('subsector')) {
            $builder->where('subsector_id', $subsector_id);
        }

        $companies = $builder->get();
        return $this->success(CustomCompanyResource::collection($companies));
    }

    public function store(CustomCompanyRequest $request)
    {
        $saveData = $request->all();

        $company = new CustomCompany($saveData);
        $company->save();

        return $this->success(new CustomCompanyResource($company->refresh()->load('subsector')));
    }

    public function update(CustomCompanyRequest $request, CustomCompany $customCompany)
    {
        $saveData = $request->all();

        $customCompany->fill($saveData);
        $customCompany->save();

        return $this->success(new CustomCompanyResource($customCompany->refresh()->load('subsector')));
    }

    public function destroy(CustomCompany $customCompany)
    {
        $customCompany->delete();
        return $this->success();
    }

    public function getTree(Request $request)
    {
        $builder = CustomCompany::query()->with('factoryParts.departments.offices.windows');
        if ($subsector_id = $request->header('subsector')) {
            $builder->where('subsector_id', $subsector_id);
        }

        $companies = $builder->get();

        return $this->success(CustomCompanyResource::collection($companies));
    }
}
