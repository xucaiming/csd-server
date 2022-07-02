<?php

namespace App\Http\Requests\Api;

class CustomCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'short_name' => 'required',
            'subsector_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '公司名称',
            'short_name' => '公司简称',
            'subsector_id' => '所属分部',
        ];
    }
}
