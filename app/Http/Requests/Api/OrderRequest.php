<?php

namespace App\Http\Requests\Api;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->id ?: 0;
        return [
            'company_id' => 'required',
            'po_number' => 'required|unique:order,po_number,$id,id,deleted_at,null',
            'make_at' => 'required',
            'purchasing_agent' => 'required',
            'payment_type_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'po_number.unique' => '订单号已被占用，请重新输入订单号',
        ];
    }
}
