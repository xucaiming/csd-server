<?php

namespace App\Http\Requests\Api;

class SubsectorRequest extends FormRequest
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
            'name.required' => '请输入公司分部名称',
        ];
    }
}
