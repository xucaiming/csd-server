<?php

namespace App\Http\Requests\Api;

class MaterialRequest extends FormRequest
{

    public function rules()
    {
        return [
            'code' => 'required',
            'factory_code' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (strlen($value) != 10 || strlen($value) != 14) {
                        return $fail('原厂料号只能是10位或14位');
                    }
                }
            ],
            'name' => 'required',
            'material_type_id' => 'required',
            'material_unit_id' => 'required',

            //'drawing_file' => 'required_if:drawing_number,!null',
            //'drawing_number' => 'required_if:drawing_file,!null',
        ];
    }
}
