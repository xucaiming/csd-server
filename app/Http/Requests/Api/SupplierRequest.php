<?php

namespace App\Http\Requests\Api;

class SupplierRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'contact' => 'required',
        ];
    }
}
