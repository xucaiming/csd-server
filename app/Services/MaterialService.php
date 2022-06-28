<?php
namespace App\Services;

use App\Models\MaterialType;
use App\Models\MaterialUnit;
use Illuminate\Support\Arr;

class MaterialService extends BaseService
{
    public function makeValid(array &$data)
    {
        $units = MaterialUnit::all()->pluck('name')->toArray();
        $types = MaterialType::all()->pluck('name')->toArray();

        $codArr = Arr::pluck($data, 'code');

        $rules = [
            'code' => [
                'required',
                'unique:material',
                function ($attribute, $value, $fail) use ($codArr) {
                    $theSame = Arr::where($codArr, function($item) use ($value) {
                        return $value == $item;
                    });
                    if (count($theSame) > 1){
                        return $fail('物料编码有重复。');
                    }
                    if (!preg_match('/^[0-9a-zA-Z_\-#]*$/', $value)) {
                        return $fail('物料编码含有非规定字符');
                    };
                }
            ],
            'name' => 'required',
            'unit_name' => 'required|in:' . implode(',', $units),
            'type_name' => 'required|in:' . implode(',', $types),
        ];

        $attributes = [
            'code' => '物料编码',
            'name' => '物料名称',
            'unit_name' => '计量单位',
            'type_name' => '物料类型',
        ];

        return validateImportData($data, $rules, [], $attributes);
    }
}
