<?php

namespace App\Http\Requests\Api;

class CustomOfficeRequest extends FormRequest
{

    public function rules()
    {
        return [
            'company_id' => 'required',
            'factory_part_id' => 'required',
            'department_id' => 'required',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '请输入科室名称',
        ];
    }
}
