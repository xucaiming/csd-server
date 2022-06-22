<?php

namespace App\Http\Requests\Api;

class PaymentTypeRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '请输入结款方式名称',
        ];
    }
}
