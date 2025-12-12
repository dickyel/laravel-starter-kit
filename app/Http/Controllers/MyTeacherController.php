<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Exam;

class MyTeacherController extends Controller
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

        // 2. My Exams (Exams created by this teacher)
        $myExams = Exam::where('created_by', $user->id)
            ->with(['subject', 'classroom'])
            ->withCount('attempts') // Count how many students attempted
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'exam_page');

        return view('teacher.my-teacher.index', compact('myAttendance', 'myExams'));
    }
}
