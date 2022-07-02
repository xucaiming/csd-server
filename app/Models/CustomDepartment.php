<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDepartment extends Model
{
    use HasFactory;

    protected $table = 'custom_department';

    protected $fillable = [
        'company_id',
        'factory_part_id',
        'name',
        'remark',
    ];

    public function offices()
    {
        return $this->hasMany(CustomOffice::class, 'department_id');
    }
}
