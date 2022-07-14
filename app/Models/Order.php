<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order';

    protected $fillable = [
        'subsector_id',
        'company_id',
        'po_number',
        'make_at',
        'order_status_id',
        'purchasing_agent',
        'payment_type_id',
        'remark',
        'created_user_id',
    ];

    public function customCompany()
    {
        return $this->belongsTo(CustomCompany::class, 'company_id');
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function orderMaterials()
    {
        return $this->hasMany(OrderMaterial::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function orderFile()
    {
        return $this->hasOne(OrderFile::class);
    }

    public function subsector()
    {
        return $this->belongsTo(Subsector::class);
    }
}
