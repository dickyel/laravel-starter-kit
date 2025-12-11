<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamExport;
use App\Exports\MyExamExport;

class ExamController extends Controller
{
    // === TEACHER / ADMIN METHODS ===

    public function index()
    {
        $exams = Exam::with(['subject', 'classroom', 'teacher'])->orderBy('created_at', 'desc')->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function exportExcel()
    {
        return Excel::download(new ExamExport, 'data-ujian.xlsx');
    }

    public function exportPdf()
    {
        $exams = Exam::with(['subject', 'classroom', 'teacher', 'questions'])->latest()->get();
        $pdf = Pdf::loadView('admin.exams.pdf', compact('exams'));
        return $pdf->download('data-ujian.pdf');
    }

    public function create()
    {
        $subjects = \App\Models\Subject::all();
        $classrooms = \App\Models\Classroom::all();
        return view('admin.exams.create', compact('subjects', 'classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:quiz,uts,uas',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1'
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'classroom_id' => $request->classroom_id, // nullable
            'type' => $request->type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'created_by' => Auth::id(),
            'is_active' => true
        ]);

        return redirect()->route('exams.show', $exam->id)->with('success', 'Ujian berhasil dibuat. Silakan tambah soal.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['questions.options', 'attempts']);
        return view('admin.exams.show', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        // Simple logic to add question (single or multiple choice)
        $request->validate([
            'question_text' => 'required',
            'type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer',
            // options if multiple_choice
        ]);

        $question = Question::create([
            'exam_id' => $exam->id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'points' => $request->points
        ]);

        if ($request->type == 'multiple_choice' && $request->has('options')) {
            foreach ($request->options as $key => $optText) {
                if(!empty($optText)) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optText,
                        'is_correct' => ($request->correct_option == $key)
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Ujian dihapus.');
    }

    // === STUDENT METHODS ===

    public function studentIndex(Request $request)
    {
        // Show exams available for the student (based on their classroom or global)
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Logic: active exams, where user is enrolled in the subject's classroom OR exam is for specific classroom
        // This is a simplified fetch:
        $currentClassroom = $user->currentClassroom(); // <-- Fixed: Removed ->first()
        $classroomId = $currentClassroom ? $currentClassroom->id : null;

        $exams = Exam::with(['subject', 'attempts']) // Eager load attempts
            ->where('is_active', true)
            ->where(function($q) use ($classroomId) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $classroomId);
            })
            // ->where('end_time', '>', now()) // User might want to see history of past exams too in export
            ->orderBy('start_time', 'desc')
            ->get();

        // Handle Export
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new MyExamExport($exams), 'ujian-saya.xlsx');
            }
            if ($request->export == 'pdf') {
                $pdf = Pdf::loadView('student.exams.pdf', compact('exams'));
                return $pdf->download('ujian-saya.pdf');
            }
        }

        // Filter for view (active only? or all? let's keep all for now so they see history)
        return view('student.exams.index', compact('exams'));
    }

    public function takeExam(Exam $exam)
    {
        // Check availability
        if (now() < $exam->start_time || now() > $exam->end_time) {
            return redirect()->back()->with('error', 'Ujian tidak tersedia saat ini.');
        }
        
        // Check if already attempted
        $existingAttempt = ExamAttempt::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingAttempt && $existingAttempt->status == 'submitted') {
            return redirect()->back()->with('info', 'Anda sudah mengerjakan ujian ini.');
        }

        // Create or resume attempt
        $attempt = $existingAttempt ?? ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => Auth::id(),
            'start_time' => now(),
            'status' => 'in_progress'
        ]);

        $exam->load(['questions.options']);
        return view('student.exams.take', compact('exam', 'attempt'));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $attempt = ExamAttempt::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $answers = $request->input('answers', []); // [question_id => answer_value]

        foreach ($answers as $qId => $val) {
            $question = Question::find($qId);
            if (!$question) continue;

            $data = [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $qId,
            ];

            if ($question->type == 'multiple_choice') {
                $data['selected_option_id'] = $val;
                // Auto grade
                $opt = QuestionOption::find($val);
                if ($opt && $opt->is_correct) {
                     $data['score_obtained'] = $question->points;
                } else {
                     $data['score_obtained'] = 0;
                }
            } else {
                $data['answer_text'] = $val;
                // Essay needs manual grading, score 0 for now
                $data['score_obtained'] = 0; 
            }

            ExamAnswer::updateOrCreate(
                ['exam_attempt_id' => $attempt->id, 'question_id' => $qId],
                $data
            );
        }

        // Calculate total score (for MC only initially)
        $totalScore = ExamAnswer::where('exam_attempt_id', $attempt->id)->sum('score_obtained');
        
        $attempt->update([
            'submit_time' => now(),
            'status' => 'submitted',
            'score' => $totalScore
        ]);

        return redirect()->route('student.exams.index')->with('success', 'Ujian berhasil dikumpulkan!');
    }
}
