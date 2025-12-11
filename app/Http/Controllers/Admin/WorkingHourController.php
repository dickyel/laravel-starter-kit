<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    public function index()
    {
        $workingHours = WorkingHour::orderBy('is_active', 'desc')->get();
        return view('admin.working-hours.index', compact('workingHours'));
    }

    public function create()
    {
        return view('admin.working-hours.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'check_in_start' => 'required',
            'check_in_end' => 'required',
            'check_in_late_tolerance' => 'required',
            'check_out_start' => 'required',
            'check_out_end' => 'required',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate all others
        if ($request->has('is_active') && $request->is_active) {
            WorkingHour::query()->update(['is_active' => false]);
        }

        WorkingHour::create($validated);

        return redirect()->route('working-hours.index')->with('success', 'Jam kerja berhasil ditambahkan.');
    }

    public function edit(WorkingHour $workingHour)
    {
        return view('admin.working-hours.edit', compact('workingHour'));
    }

    public function update(Request $request, WorkingHour $workingHour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'check_in_start' => 'required',
            'check_in_end' => 'required',
            'check_in_late_tolerance' => 'required',
            'check_out_start' => 'required',
            'check_out_end' => 'required',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate all others
        if ($request->has('is_active') && $request->is_active) {
            WorkingHour::where('id', '!=', $workingHour->id)->update(['is_active' => false]);
        }

        $workingHour->update($validated);

        return redirect()->route('working-hours.index')->with('success', 'Jam kerja berhasil diperbarui.');
    }

    public function destroy(WorkingHour $workingHour)
    {
        $workingHour->delete();
        return redirect()->route('working-hours.index')->with('success', 'Jam kerja berhasil dihapus.');
    }

    public function activate(WorkingHour $workingHour)
    {
        WorkingHour::query()->update(['is_active' => false]);
        $workingHour->update(['is_active' => true]);
        
        return redirect()->route('working-hours.index')->with('success', 'Jam kerja diaktifkan.');
    }
}
