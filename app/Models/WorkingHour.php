<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $fillable = [
        'name',
        'check_in_start',
        'check_in_end',
        'check_in_late_tolerance',
        'check_out_start',
        'check_out_end',
        'is_active',
    ];
}
