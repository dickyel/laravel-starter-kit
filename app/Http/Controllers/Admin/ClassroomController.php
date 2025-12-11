<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClassroomExport;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('students')->get();
        // Cek export
        if (request()->has('export')) {
             if (request()->get('export') == 'pdf') {
                 return $this->exportPdf();
             }
             if (request()->get('export') == 'excel') {
                 return $this->exportExcel();
             }
        }
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function exportPdf()
    {
        $classrooms = Classroom::with(['homeroomTeacher'])->withCount('students')->get();
        $pdf = Pdf::loadView('admin.classrooms.pdf', compact('classrooms'));
        return $pdf->download('laporan-kelas.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ClassroomExport, 'data-kelas.xlsx');
    }

    public function create()
    {
        $subjects = Subject::all();
        $teachers = \App\Models\User::whereHas('roles', function($q){
            $q->where('slug', 'teacher')->orWhere('slug', 'guru');
        })->get();
        return view('admin.classrooms.create', compact('subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
            'grid_rows' => 'required|integer|min:1', 
            'grid_columns' => 'required|integer|min:1',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $classroom = Classroom::create($request->except('subjects'));
        
        if($request->has('subjects')) {
            $classroom->subjects()->sync($request->subjects);
        }

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load(['students', 'subjects', 'schedules.subject', 'schedules.teacher', 'homeroomTeacher']);
        
        // 1. Siswa yang SUDAH ada di kelas ini (untuk list anggota)
        // Sudah diload via relationship $classroom->students
        
        // 2. Siswa yang BELUM punya kelas aktif di manapun (untuk calon anggota baru)
        // Kita exclude siswa yang sudah punya is_active=true di student_classrooms
        $studentsAvailable = \App\Models\User::whereDoesntHave('classrooms', function($q) {
            $q->where('is_active', true);
        })->get();

        return view('admin.classrooms.show', compact('classroom', 'studentsAvailable'));
    }

    public function edit(Classroom $classroom)
    {
        $subjects = Subject::all();
        $teachers = \App\Models\User::whereHas('roles', function($q){
            $q->where('slug', 'teacher')->orWhere('slug', 'guru');
        })->get();
        return view('admin.classrooms.edit', compact('classroom', 'subjects', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
            'grid_rows' => 'required|integer|min:1', 
            'grid_columns' => 'required|integer|min:1',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $classroom->update($request->except('subjects'));

        if($request->has('subjects')) {
            $classroom->subjects()->sync($request->subjects);
        } else {
            $classroom->subjects()->detach();
        }

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function assignSeat(Request $request, Classroom $classroom)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'seat_number' => 'required|integer|min:1',
        ]);

        // 1. Kosongkan kursi target jika ada orang lain di sana
        \Illuminate\Support\Facades\DB::table('student_classrooms')
            ->where('classroom_id', $classroom->id)
            ->where('seat_number', $request->seat_number)
            ->where('is_active', true)
            ->update(['seat_number' => null]);

        // 2. Cek apakah student ini sudah ada di kelas ini (pindah kursi)
        $existingEntry = \Illuminate\Support\Facades\DB::table('student_classrooms')
            ->where('classroom_id', $classroom->id)
            ->where('user_id', $request->user_id)
            ->where('is_active', true)
            ->first();

        if ($existingEntry) {
            // Update kursi
            \Illuminate\Support\Facades\DB::table('student_classrooms')
                ->where('id', $existingEntry->id)
                ->update(['seat_number' => $request->seat_number]);
        } else {
            // Masukkan siswa baru ke kelas ini di kursi tersebut
            $classroom->students()->attach($request->user_id, [
                'seat_number' => $request->seat_number,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Siswa berhasil ditempatkan.');
    }

    public function unassignSeat(Request $request, Classroom $classroom)
    {
        $request->validate(['seat_number' => 'required|integer']);

        \Illuminate\Support\Facades\DB::table('student_classrooms')
            ->where('classroom_id', $classroom->id)
            ->where('seat_number', $request->seat_number)
            ->where('is_active', true)
            ->update(['seat_number' => null]);
            
            // Opsional: Jika unassign seat berarti kick dari kelas, gunakan delete() atau is_active=false. 
            // Untuk sekarang, logicnya adalah "Siswa berdiri" (masih di kelas, tapi tanpa kursi).

        return redirect()->back()->with('success', 'Kursi berhasil dikosongkan.');
    }

    public function enroll(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        // Cek Kapasitas
        $currentCount = $classroom->students()->count();
        $toAddCount = count($validated['user_ids']);
        
        if (($currentCount + $toAddCount) > $classroom->max_students) {
            return redirect()->back()->with('error', 'Gagal: Kapasitas kelas penuh! Sisa ' . ($classroom->max_students - $currentCount) . ' kursi.');
        }

        foreach($validated['user_ids'] as $userId) {
            // Cek double enroll (preventif)
            if (!$classroom->students->contains($userId)) {
                $classroom->students()->attach($userId, [
                    'seat_number' => null, // Masuk kelas dulu, kursi belakangan
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        return redirect()->back()->with('success', $toAddCount . ' siswa berhasil ditambahkan ke kelas.');
    }

    public function kick(Request $request, Classroom $classroom)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        // Set is_active = false (Soft delete dari kelas aktif)
        $classroom->students()->updateExistingPivot($request->user_id, ['is_active' => false, 'seat_number' => null]);
        
        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // Tampilkan denah kelas interaktif
    public function seatingChart(Classroom $classroom)
    {
        $classroom->load(['students' => function($query) {
            $query->wherePivot('is_active', true)
                  ->wherePivotNotNull('seat_number');
        }]);

        // Parse layout JSON atau buat default
        $layout = $classroom->layout ? json_decode($classroom->layout, true) : $this->generateDefaultLayout($classroom);

        return view('admin.classrooms.seating-chart', compact('classroom', 'layout'));
    }

    // Update denah dan assignment siswa
    public function updateSeatingChart(Request $request, Classroom $classroom)
    {
        $request->validate([
            'layout' => 'required|json',
            'assignments' => 'nullable|json', // {seat_id: user_id}
        ]);

        // Save layout
        $classroom->update(['layout' => $request->layout]);

        // Update seat assignments
        if ($request->has('assignments')) {
            $assignments = json_decode($request->assignments, true);
            
            // Reset all seats first
            \Illuminate\Support\Facades\DB::table('student_classrooms')
                ->where('classroom_id', $classroom->id)
                ->where('is_active', true)
                ->update(['seat_number' => null]);

            // Assign new seats
            foreach ($assignments as $seatId => $userId) {
                if ($userId) {
                    \Illuminate\Support\Facades\DB::table('student_classrooms')
                        ->where('classroom_id', $classroom->id)
                        ->where('user_id', $userId)
                        ->where('is_active', true)
                        ->update(['seat_number' => $seatId]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Denah berhasil disimpan!']);
    }

    private function generateDefaultLayout($classroom)
    {
        $layout = [];
        $rows = $classroom->grid_rows ?? 4;
        $cols = $classroom->grid_columns ?? 6;

        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $layout[] = [
                    'id' => ($row * $cols) + $col + 1,
                    'row' => $row,
                    'col' => $col,
                    'type' => 'seat', // seat, table, empty, teacher_desk
                ];
            }
        }

        return $layout;
    }
}

