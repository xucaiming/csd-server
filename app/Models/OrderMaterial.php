<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMaterial extends Model
{
    use HasFactory;

    protected $table = 'order_material';

    protected $fillable = [
        'factory_part_id',
        'department_id',
        'office_id',
        'window_id',
        'material_id',
        'order_id',
        'quantity',
        'unit_price',
        'tax_unit_price',
        'tax_rate',
        'total_price',
        'total_rate_price',
        'delivery_date',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function customFactoryPart()
    {
        return $this->belongsTo(CustomFactoryPart::class, 'factory_part_id');
    }

    public function customDepartment()
    {
        return $this->belongsTo(CustomDepartment::class, 'department_id');
    }

    public function customOffice()
    {
        return $this->belongsTo(CustomOffice::class, 'office_id');
    }

    public function customWindow()
    {
        return $this->belongsTo(CustomWindow::class, 'window_id');
    }
}
