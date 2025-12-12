<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ExamAttempt;

class MyHomeroomController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. My Attendance (Teacher's own attendance)
        $myAttendance = Attendance::where('user_id', $user->id)
            ->with(['schedule.subject', 'schedule.classroom'])
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(10, ['*'], 'attendance_page');

        // 2. My Class Students' Exams
        $classroom = Classroom::where('homeroom_teacher_id', $user->id)->first();
        
        $studentAttempts = collect();
        if ($classroom) {
            // Get all student IDs in this class
            $studentIds = $classroom->students()->pluck('users.id');

            // Get attempts by these students
            $studentAttempts = ExamAttempt::whereIn('user_id', $studentIds)
                ->with(['exam', 'user', 'exam.subject'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'exam_page');
        }

        return view('teacher.my-homeroom.index', compact('myAttendance', 'studentAttempts', 'classroom'));
    }
}
