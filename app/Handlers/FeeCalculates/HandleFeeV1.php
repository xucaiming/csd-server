<?php
namespace App\Handlers\FeeCalculates;

use App\Models\Model;
use Illuminate\Http\Request;

class HandleFeeV1 implements FeeCalculate
{
    public $feeCode = 'order_handle';
    public $ruleTypeCode = 'HandleFeeV1';

    public function afterValidate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'weight_unit' => 'required',
            'rules.*.weight' => 'required',
            'rules.*.fee' => 'required',
            'additional_weight' => 'required_with:additional_weight_fee',
            'additional_weight_fee' => 'required_with:additional_weight',
        ], [], [
            'name' => '规则名称',
            'weight_unit' => '重量单位',
            'rules.*.weight' => '规则重量段',
            'rules.*.fee' => '规则费用',
            'additional_weight' => '续重',
            'additional_weight_fee' => '续重费',
        ]);

        return [
            'name' => $request->input('name'),
            'remark' => $request->input('remark'),
            'code' => $this->feeCode,
            'rule_type_code' => $this->ruleTypeCode,
            'rule_content' => [
                'rules' => $request->input('rules'),
                'surcharge' =>  [
                    'over_weight_fee' => $request->input('over_weight_fee'),
                    'over_size_fee' => $request->input('over_size_fee'),
                ],
                'weight_unit' => $request->input('weight_unit'),
                'additional_weight' => $request->input('additional_weight'),
                'additional_weight_fee' => $request->input('additional_weight_fee'),
            ],
        ];
    }


    public function calculate(array $rule_content, Model $outboundOrder = null, $options = [])
    {
        $outboundOrder->load('package');

        $cost = 0;
        foreach ($outboundOrder->orderSkus as $sku) {
            $single_sku_cost = 0;
            if ($rule_content['weight_unit'] == 'g') {
                $weight = $sku->sku->weight_checked * 1000;
                $optional_weight = 22679.6185;
            } else if ($rule_content['weight_unit'] == 'oz') {
                $weight = $sku->sku->weight_checked * 35.2739619496;
                $optional_weight = 800;
            } else if ($rule_content['weight_unit'] == 'lb') {
                $weight = $sku->sku->weight_checked * 2.20462262185;
                $optional_weight = 50;
            } else {
                $optional_weight = 22.6796185;
                $weight = $sku->sku->weight_checked;
            }

            $rules = $rule_content['rules'];

            $lastRule = end($rules);
            if ($weight > $lastRule['weight']) {
                $single_sku_cost = $lastRule['fee'] + ceil(($weight - $lastRule['weight']) / $rule_content['additional_weight']) * $rule_content['additional_weight_fee'];
            } else {
                foreach ($rules as $rule) {
                    if($rule['weight'] >= $weight){
                        $single_sku_cost = $rule['fee'];
                        break;
                    }
                }
            }

            // 单件包裹超过50lb，加收1美元附加费
            if ($weight > $optional_weight) {
                $single_sku_cost += $rule_content['surcharge']['over_weight_fee'];
            }

            // 超长处理费
            $sizeArr = [$sku->sku->length_checked, $sku->sku->width_checked, $sku->sku->height_checked];
            sort($sizeArr);
            if ($sizeArr[2] > 121.92 || $sizeArr[1] > 76.2 || ($sizeArr[2] + ($sizeArr[1] + $sizeArr[0]) * 2) > 266.7) {
                $single_sku_cost += $rule_content['surcharge']['over_size_fee'];
            }
            $cost = $single_sku_cost * $sku->sku_quantity;
        }

        return $cost;
    }
}
