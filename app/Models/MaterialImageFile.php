<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImageFile extends Model
{
    use HasFactory;

    protected $table = 'material_image_file';

    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'file_path',
    ];
}
