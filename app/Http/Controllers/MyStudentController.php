<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\ExamAttempt;

class MyStudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user is student (optional constraint, though menu is hidden otherwise)
        // If we want to be strict, we can abort(403) if not student, but for now let's just show data.

        // 1. Attendance Summary (Last 30 Days or All Time? Let's do recent 50 for now or paginate)
        $attendances = Attendance::where('user_id', $user->id)
            ->with(['schedule.subject', 'schedule.classroom'])
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(10, ['*'], 'attendance_page');

        // 2. Exam Results
        $examAttempts = ExamAttempt::where('user_id', $user->id)
            ->with(['exam.subject'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'exam_page');

        // 3. Purchased Items
        $purchasedBooks = \App\Models\OrderItem::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('item_type', \App\Models\Book::class)->with('item')->get();

        $purchasedUniforms = \App\Models\OrderItem::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('item_type', \App\Models\Uniform::class)->with('item')->get();

        return view('student.my-student.index', compact('attendances', 'examAttempts', 'purchasedBooks', 'purchasedUniforms'));
    }
}
