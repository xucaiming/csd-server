<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeItem extends Model
{
    use HasFactory;

    protected $table = 'fee_item';

    protected $fillable = [
        'type',
        'name',
        'remark',
    ];
}
