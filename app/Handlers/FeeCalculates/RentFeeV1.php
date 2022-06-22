<?php
namespace App\Handlers\FeeCalculates;

use App\Models\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RentFeeV1 implements FeeCalculate
{
    public $feeCode = 'sku_rent';
    public $ruleTypeCode = 'RentFeeV1';

    public function afterValidate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'rules.*.vol' => 'required|min:0',
            'rules.*.unit' => 'required|in:sku,vol',
            'rules.*.rules.*.day' => 'required|min:1|integer',
            'rules.*.rules.*.fee' => 'required|min:0',
        ], [], [
            'name' => '规则名称',
            'rules.*.vol' => '体积分段体积值',
            'rules.*.rules.*.day' => '体积分段规则中库龄天数',
            'rules.*.rules.*.fee' => '体积分段规则中费用',
        ]);

        return [
            'name' => $request->input('name'),
            'remark' => $request->input('remark'),
            'code' => $this->feeCode,
            'rule_type_code' => $this->ruleTypeCode,
            'rule_content' => $request->input('rules'),
        ];
    }

    // $order传值null、 $options形如 [ 'days' => (库龄天数), 'quantity' => (对应库龄数量), 'volume' => (单个SKU体积) ];
    public function calculate(array $rule_content, Model $order = null, $options = [])
    {
        $rule_content = Arr::sort($rule_content, function ($item) {
            return $item['vol'];
        });
        $lastVolRule = end($rule_content);
        $totalVolume = $options['quantity'] * $options['volume'];
        if ($totalVolume > $lastVolRule['vol']) {
            $volumeRule = $lastVolRule;
        } else {
            $volumeRule = $rule_content[0];
            foreach ($rule_content as $volRule) {
                if ($volRule['vol'] > $totalVolume) { // TODO 是否是'>='取决于报价方式
                    $volumeRule = $volRule;
                    break;
                }
            }
        }

        $rules = Arr::sort($volumeRule['rules'], function ($item) {
            return $item['day'];
        });
        $lastDayRule = end($rules);
        if ($options['days'] > $lastDayRule['day']) {
            $unit_fee = $lastDayRule['fee'];
        } else {
            $unit_fee = 0;
            foreach ($rules as $rule) {
                if ($rule['day'] > $options['days']) { // TODO 是否是'>='取决于报价方式
                    $unit_fee = $rule['fee'];
                    break;
                }
            }
        }

        return [
            'type' => $volumeRule['unit'],
            'unit_fee' => $unit_fee,  // 一个单位的费用
        ];
    }
}
