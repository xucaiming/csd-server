<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PaymentTypeRequest;
use App\Http\Resources\PaymentTypeResource;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::query()
            ->orderBy('id', 'desc')
            ->get();
        return $this->success(PaymentTypeResource::collection($paymentTypes));
    }

    public function store(PaymentTypeRequest $request)
    {
        $saveData = $request->all();

        $paymentType = new PaymentType($saveData);
        $paymentType->save();

        return $this->success(new PaymentTypeResource($paymentType->refresh()));
    }

    public function update(PaymentTypeRequest $request, PaymentType $paymentType)
    {
        $saveData = $request->all();

        $paymentType->fill($saveData);
        $paymentType->save();

        return $this->success(new PaymentTypeResource($paymentType->refresh()));
    }

    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();
        return $this->success();
    }
}
