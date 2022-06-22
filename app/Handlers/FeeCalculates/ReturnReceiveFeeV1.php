<?php

namespace App\Handlers\FeeCalculates;

use App\Models\Model;
use Illuminate\Http\Request;

class ReturnReceiveFeeV1 implements FeeCalculate
{

    public $feeCode = 'return_receive';
    public $ruleTypeCode = 'ReturnReceiveFeeV1';

    public function afterValidate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'rules.*.volume' => 'required',
            'rules.*.fee' => 'required',
            'additional_volume' => 'required_with:additional_volume_fee',
            'additional_volume_fee' => 'required_with:additional_volume',
        ], [], [
            'name' => '规则名称',
            'rules.*.volume' => '规则体积段',
            'rules.*.fee' => '规则费用',
            'additional_volume' => '续体积',
            'additional_volume_fee' => '续体积费',
        ]);

        return [
            'name' => $request->input('name'),
            'remark' => $request->input('remark'),
            'code' => $this->feeCode,
            'rule_type_code' => $this->ruleTypeCode,
            'rule_content' => [
                'rules' => $request->input('rules'),
                'additional_volume' => $request->input('additional_volume'),
                'additional_volume_fee' => $request->input('additional_volume_fee'),
            ],
        ];
    }

    public function calculate(array $rule_content, Model $returnShipment = null, $options = [])
    {
        $returnShipment->load('shipmentPackages');
        $cost = 0;
        foreach ($returnShipment->shipmentPackages as $package) {
            $single_package_cost = 0;
            $rules = $rule_content['rules'];
            $volume = ($package->length /100) * ($package->width / 100) * ($package->height / 100);
            $lastRule = end($rules);
            if ($volume > $lastRule['volume']) {
                $single_package_cost = $lastRule['fee'] + ($volume - $lastRule['volume']) / $rule_content['additional_volume'] * $rule_content['additional_volume_fee'];
            } else {
                foreach ($rules as $rule) {
                    if($rule['volume'] >= $volume){
                        $single_package_cost = $rule['fee'];
                        break;
                    }
                }
            }
            $cost = $single_package_cost * $package->quantity;
        }
        return $cost;
    }
}
