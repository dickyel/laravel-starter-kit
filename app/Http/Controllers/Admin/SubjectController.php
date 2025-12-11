<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubjectExport;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('teachers')->get(); // eager load
        return view('admin.subjects.index', compact('subjects'));
    }

    public function exportExcel()
    {
        return Excel::download(new SubjectExport, 'mata-pelajaran.xlsx');
    }

    public function exportPdf()
    {
        $subjects = Subject::with('teachers')->get();
        $pdf = Pdf::loadView('admin.subjects.pdf', compact('subjects'));
        return $pdf->download('mata-pelajaran.pdf');
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Subject::create($request->only('name'));
        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $subject->update($request->only('name'));
        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
