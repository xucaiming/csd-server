<?php

namespace App\Http\Requests\Api;

class CustomDepartmentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'company_id' => 'required',
            'factory_part_id' => 'required',
            'name' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '部门名称',
        ];
    }
}
