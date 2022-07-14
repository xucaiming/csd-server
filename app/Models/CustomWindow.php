<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomWindow extends Model
{
    use HasFactory;

    protected $table = 'custom_window';

    protected $fillable = [
        'company_id',
        'factory_part_id',
        'department_id',
        'office_id',
        'name',
        'phone',
        'remark',
    ];

    public function customCompany()
    {
        return $this->belongsTo(CustomCompany::class, 'company_id');
    }
}
