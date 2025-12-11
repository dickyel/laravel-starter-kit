<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolLayout extends Model
{
    protected $fillable = [
        'name',
        'floor_number',
        'grid_data',
        'width',
        'height',
        'background_image',
        'is_active',
    ];

    public function classrooms()
    {
        return $this->hasMany(\App\Models\Classroom::class);
    }
}
