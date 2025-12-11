<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'max_students', 'grid_rows', 'grid_columns', 'homeroom_teacher_id', 'layout', 'school_layout_id', 'position_x', 'position_y'];

    // Relasi ke User (Siswa)
    // Kita filter hanya yang aktif is_active = true
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_classrooms')
                    ->withPivot('seat_number', 'is_active')
                    ->wherePivot('is_active', true);
    }

    // Relasi ke Mata Pelajaran
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'classroom_subject');
    }

    // Relasi ke Jadwal
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class)->orderBy('day')->orderBy('start_time');
    }

    // Relasi ke Wali Kelas (Guru)
    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    // Relasi ke Denah Sekolah
    public function schoolLayout()
    {
        return $this->belongsTo(SchoolLayout::class);
    }
}
