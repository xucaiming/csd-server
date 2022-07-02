<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFactoryPart extends Model
{
    use HasFactory;

    protected $table = 'custom_factory_part';

    protected $fillable = [
        'company_id',
        'name',
        'remark',
    ];

    public function departments()
    {
        return $this->hasMany(CustomDepartment::class, 'factory_part_id');
    }
}
