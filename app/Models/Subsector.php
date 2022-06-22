<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subsector extends Model
{
    use HasFactory;

    protected $table = 'subsector';

    protected $fillable = [
        'name',
        'status',
        'remark',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subsector', 'subsector_id', 'user_id');
    }
}
