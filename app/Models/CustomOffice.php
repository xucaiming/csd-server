<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOffice extends Model
{
    use HasFactory;

    protected $table = 'custom_office';

    protected $fillable = [
        'company_id',
        'factory_part_id',
        'department_id',
        'name',
        'remark',
    ];

    public function windows()
    {
        return $this->hasMany(CustomWindow::class, 'office_id');
    }
}
