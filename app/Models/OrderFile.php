<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    protected $table = 'order_file';

    protected $fillable = [
        'order_id',
        'original_name',
        'file_path',
    ];
}
