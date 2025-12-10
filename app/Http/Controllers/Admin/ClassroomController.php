<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('students')->get();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.classrooms.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
            'grid_rows' => 'required|integer|min:1', 
            'grid_columns' => 'required|integer|min:1',
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
        $classroom->load(['students', 'subjects', 'schedules.subject']);
        
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
        return view('admin.classrooms.edit', compact('classroom', 'subjects'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
            'grid_rows' => 'required|integer|min:1', 
            'grid_columns' => 'required|integer|min:1',
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
}
