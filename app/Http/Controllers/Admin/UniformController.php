<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UniformController extends Controller
{
    public function index()
    {
        $uniforms = \App\Models\Uniform::latest()->get();
        return view('admin.uniforms.index', compact('uniforms'));
    }

    public function create()
    {
        return view('admin.uniforms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string|max:10',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uniforms', 'public');
        }

        \App\Models\Uniform::create($validated);

        return redirect()->route('uniforms.index')->with('success', 'Seragam berhasil ditambahkan.');
    }

    public function edit(\App\Models\Uniform $uniform)
    {
        return view('admin.uniforms.edit', compact('uniform'));
    }

    public function update(Request $request, \App\Models\Uniform $uniform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string|max:10',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
             if ($uniform->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($uniform->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($uniform->image);
            }
            $validated['image'] = $request->file('image')->store('uniforms', 'public');
        }

        $uniform->update($validated);

        return redirect()->route('uniforms.index')->with('success', 'Seragam berhasil diperbarui.');
    }

    public function destroy(\App\Models\Uniform $uniform)
    {
        if ($uniform->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($uniform->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($uniform->image);
        }
        $uniform->delete();
        return redirect()->route('uniforms.index')->with('success', 'Seragam berhasil dihapus.');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UniformsExport, 'data-seragam.xlsx');
    }

    public function exportPdf()
    {
        $uniforms = \App\Models\Uniform::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.uniforms.pdf', compact('uniforms'));
        return $pdf->download('data-seragam.pdf');
    }
}
