<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CustomWindowRequest;
use App\Http\Resources\CustomWindowResource;
use App\Models\CustomWindow;
use Illuminate\Http\Request;

class CustomWindowController extends Controller
{
    public function index(Request $request)
    {
        $builder = CustomWindow::query();

        if ($company_id = $request->input('company_id')) {
            $builder->where('company_id', $company_id);
        }

        if ($factory_part_id = $request->input('factory_part_id')) {
            $builder->where('factory_part_id', $factory_part_id);
        }

        if ($department_id = $request->input('department_id')) {
            $builder->where('department_id', $department_id);
        }

        if ($office_id = $request->input('office_id')) {
            $builder->where('office_id', $office_id);
        }

        $windows = $builder->orderBy('id', 'desc')->get();
        return $this->success(CustomWindowResource::collection($windows));
    }

    public function store(CustomWindowRequest $request)
    {
        $saveData = $request->all();

        $window = new CustomWindow($saveData);
        $window->save();

        return $this->success(new CustomWindowResource($window->refresh()));
    }

    public function update(CustomWindowRequest $request, CustomWindow $customWindow)
    {
        $saveData = $request->all();

        $customWindow->fill($saveData);
        $customWindow->save();

        return $this->success(new CustomWindowResource($customWindow->refresh()));
    }

    public function destroy(CustomWindow $customWindow)
    {
        $customWindow->delete();
        return $this->success();
    }
}
