<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'material';

    protected $fillable = [
        'code',
        'custom_code',
        'factory_code',
        'name',
        'material_type_id',
        'material_unit_id',
        'remark',
        'created_user_id',
    ];

    public function materialType()
    {
        return $this->belongsTo(MaterialType::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function (Material $material) {
            if (!$material->custom_code) {
                $material->custom_code = 'CSD' . str_pad($material->id, '5', '0', STR_PAD_LEFT);
                $material->save();
            }
        });
    }

    public function materialUnit()
    {
        return $this->belongsTo(MaterialUnit::class);
    }

    public function materialImageFiles()
    {
        return $this->hasMany(MaterialImageFile::class);
    }

    public function materialDrawingFile()
    {
        return $this->hasOne(MaterialDrawingFile::class);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
