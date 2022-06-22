<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    // 定义系统角色
    const Chairman = 'chairman';
    const Accountancy = 'accountancy';
    const Cashier = 'cashier';
    const Buyer = 'buyer';
    const SalesManager = 'sales_manager';
    const SalesMan = 'sales_man';
    const Technician = 'technician';
    const Support = 'support';

    // 定义查询权限
    public function scopeWithListQuery($query)
    {
        //
    }

    // 重写此方法用以返回指定的时间格式
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
