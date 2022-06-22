<?php

namespace App\Http\Requests\Api;

class FeeItemRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => 'required|in:income,expend',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => '请选择费用名目类型',
            'name.required' => '请输入费用项名称',
        ];
    }
}
