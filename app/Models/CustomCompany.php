<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCompany extends Model
{
    use HasFactory;

    protected $table = 'custom_company';

    protected $fillable = [
        'name',
        'short_name',
        'subsector_id',
        'remark',
    ];

    public function subsector()
    {
        return $this->belongsTo(Subsector::class);
    }

    public function factoryParts()
    {
        return $this->hasMany(CustomFactoryPart::class, 'company_id');
    }
}
