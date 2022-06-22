<?php

namespace App\Handlers\FeeCalculates;

use App\Models\Model;
use Illuminate\Http\Request;

interface FeeCalculate {

    // 组装存储数据
    public function afterValidate(Request $request);

    // 费用计算，如果涉及订单相关的传$order，否则$order传null、传附加参数数组$options
    public function calculate(array $rule_content, Model $order = null, $options = []);
}
