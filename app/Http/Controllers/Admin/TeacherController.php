<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'teacher')->orWhere('slug', 'guru');
        })->with('teacherSubjects')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function exportExcel() 
    {
        return Excel::download(new TeacherExport, 'data-guru.xlsx');
    }

    public function exportPdf()
    {
        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'teacher')->orWhere('slug', 'guru');
        })->with('teacherSubjects')->get();

        $pdf = Pdf::loadView('admin.teachers.pdf', compact('teachers'));
        return $pdf->download('data-guru.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = \App\Models\Subject::all();
        return view('admin.teachers.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'confirm_password' => 'same:password',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'password_2' => \Illuminate\Support\Facades\Hash::make($request->password), 
        ]);

        $role = \App\Models\Role::firstOrCreate(
            ['slug' => 'guru'],
            ['name' => 'Guru']
        );
        $user->roles()->attach($role);

        if ($request->has('subjects')) {
            $user->teacherSubjects()->sync($request->subjects);
        }

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $teacher = \App\Models\User::whereHas('roles', function($q){
            $q->where('slug', 'guru')->orWhere('slug', 'teacher');
        })->findOrFail($id);

        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = \App\Models\User::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
            $rules['confirm_password'] = 'same:password';
        }

        $request->validate($rules);

        $teacher->name = $request->name;
        $teacher->username = $request->username;
        $teacher->email = $request->email;

        if ($request->filled('password')) {
            $teacher->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $teacher->password_2 = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $teacher->save();

        return redirect()->route('teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = \App\Models\User::findOrFail($id);
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus.');
    }
}
