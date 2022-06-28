<?php

namespace App\Http\Requests\Api;

class MaterialTypeRequest extends FormRequest
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
            'name.required' => '请输入物料分类名称',
        ];
    }
}
