<?php

namespace App\Services;

use App\Models\MaterialType;
use App\Models\MaterialUnit;
use Illuminate\Support\Arr;

class OrderService extends BaseService
{
    public function makeMaterialDataValid(&$data)
    {
        $po_number_arr = Arr::pluck($data, 'po_number');
        $rules = [
            'material_code' => [
                'required',
                function ($attribute, $value, $fail) use ($po_number_arr) {
                    if (!(strlen($value) == 10 || strlen($value) == 14)) {
                        return $fail('物料编码为10或14位');
                    }
                    $theSame = Arr::where($po_number_arr, function($item) use ($value) {
                        return $value == $item;
                    });
                    if (count($theSame) > 1) {
                        return $fail('物料编号有重复');
                    }
                    if (!preg_match('/^[0-9a-zA-Z_\-#]*$/', $value)) {
                        return $fail('物料编码含有非规定字符');
                    }
                }
            ],
            'material_name' => 'required',
            'material_type_id' => 'required',
            'material_unit_id' => 'required',
            'quantity' => 'required',
            'window_id' => 'required',
            'unit_price' => 'required_without:tax_unit_price',
            'tax_unit_price' => 'required_without:unit_price',
            'tax_rate' => 'required',
            'delivery_date' => 'required|date_format:Y-m-d',
        ];

        $attributes = [
            'material_code' => '物料编码',
            'material_name' => '物料名称',
            'material_type_id' => '物料类别',
            'material_unit_id' => '单位',
            'quantity' => '数量',
            'window_id' => '窗口',
            'unit_price' => '单价',
            'tax_unit_price' => '含税单价',
            'tax_rate' => '税率',
            'delivery_date' => '交货日期',
        ];

        return validateImportData($data, $rules, [], $attributes);
    }
}
