<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier';

    protected $fillable = [
        'name',
        'email',
        'contact',
        'phone',
        'address',
        'supplier_type',
        'pay_type',
        'bank_name',
        'account',
        'remark',
        'created_user_id',
    ];
}
