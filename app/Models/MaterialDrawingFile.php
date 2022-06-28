<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDrawingFile extends Model
{
    use HasFactory;

    protected $table = 'material_drawing_file';

    protected $fillable = [
        'material_id',
        'number',
        'original_name',
        'file_path',
        'created_user_id',
        'created_at',
    ];

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
