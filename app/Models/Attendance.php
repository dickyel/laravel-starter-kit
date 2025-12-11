<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_schedule_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'check_in_status',
        'check_out_status',
        'late_minutes',
        'overtime_minutes',
        'evidence_photo',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }
}
