<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CustomWindowRequest extends FormRequest
{
    public function rules()
    {
        return [
            'company_id' => 'required',
            'factory_part_id' => 'required',
            'department_id' => 'required',
            'office_id' => 'required',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '请输入窗口名称',
        ];
    }
}
