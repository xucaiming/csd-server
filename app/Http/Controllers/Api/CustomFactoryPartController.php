<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\CustomFactoryPartRequest;
use App\Http\Resources\CustomFactoryPartResource;
use App\Models\CustomFactoryPart;
use Illuminate\Http\Request;

class CustomFactoryPartController extends Controller
{
    public function index(Request $request)
    {
        $builder = CustomFactoryPart::query();

        if ($company_id = $request->input('company_id')) {
            $builder->where('company_id', $company_id);
        }

        $paymentTypes = $builder->orderBy('id', 'desc')->get();
        return $this->success(CustomFactoryPartResource::collection($paymentTypes));
    }

    public function store(CustomFactoryPartRequest $request)
    {
        $saveData = $request->all();

        $part = new CustomFactoryPart($saveData);
        $part->save();

        return $this->success(new CustomFactoryPartResource($part->refresh()));
    }

    public function update(CustomFactoryPartRequest $request, CustomFactoryPart $customFactoryPart)
    {
        $saveData = $request->all();

        $customFactoryPart->fill($saveData);
        $customFactoryPart->save();

        return $this->success(new CustomFactoryPartResource($customFactoryPart->refresh()));
    }

    public function destroy(CustomFactoryPart $customFactoryPart)
    {
        $customFactoryPart->delete();
        return $this->success();
    }
}
