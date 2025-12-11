<?php

namespace App\Models;

// 1. Tambahkan use statement ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Traits\LogsAllActivity;

class User extends Authenticatable
{
    // 2. Tambahkan trait ini di dalam class
    use HasFactory, Notifiable, LogsAllActivity;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'password_2',
        'address',
        'phone_number',
        'user_id_number',
        'signature_photo_path',
        'face_descriptor',
    ];

    public function photos()
    {
        return $this->hasMany(UserPhoto::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_2',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_2' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'student_classrooms')
                    ->withPivot('seat_number', 'is_active')
                    ->withTimestamps();
    }

    // Helper untuk mengambil kelas yang sedang aktif
    public function currentClassroom()
    {
        return $this->classrooms()->wherePivot('is_active', true)->first();
    }

    // Relasi untuk Guru ke Mata Pelajaran
    public function teacherSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->withTimestamps();
    }
}
