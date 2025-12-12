<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uniform extends Model
{
    protected $fillable = [
        'name',
        'size',
        'price',
        'stock',
        'image',
    ];
}
