<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentType extends Model
{
    use HasFactory;

    protected $table = 'payment_type';

    protected $fillable = [
        'name',
        'remark',
    ];
}
