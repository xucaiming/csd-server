<?php

namespace App\Http\Requests\Api;

class CustomFactoryPartRequest extends FormRequest
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
            'name.required' => '请输入厂别名称',
        ];
    }
}
