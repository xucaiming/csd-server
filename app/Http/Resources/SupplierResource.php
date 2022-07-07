<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'bank_name' => $this->bank_name,
            'account' => $this->account,
            'address' => $this->address,
            'supplier_type' => $this->supplier_type,
            'pay_type' => $this->pay_type,
            'status' => boolval($this->status),
            'remark' => $this->remark,
            'created_at' => (string) $this->created_at,
        ];
    }
}
