<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolLayout;
use App\Models\Classroom;
use Illuminate\Http\Request;

class SchoolLayoutController extends Controller
{
    public function index()
    {
        $layouts = SchoolLayout::orderBy('floor_number')->get();
        return view('admin.school-layouts.index', compact('layouts'));
    }

    public function create()
    {
        return view('admin.school-layouts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'floor_number' => 'required|integer',
            'width' => 'nullable|integer|min:800|max:2000',
            'height' => 'nullable|integer|min:600|max:1500',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // Handle image upload
        if ($request->hasFile('background_image')) {
            $image = $request->file('background_image');
            $imageName = 'layout_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/school-layouts'), $imageName);
            $validated['background_image'] = 'storage/school-layouts/' . $imageName;
        }

        SchoolLayout::create($validated);

        return redirect()->route('school-layouts.index')->with('success', 'Denah lantai berhasil dibuat.');
    }

    public function edit(SchoolLayout $schoolLayout)
    {
        // Get all classrooms untuk di-drag-drop
        $classrooms = Classroom::all();
        
        // Get classrooms yang sudah ada di layout ini
        $classroomsInLayout = Classroom::where('school_layout_id', $schoolLayout->id)->get();

        return view('admin.school-layouts.edit', compact('schoolLayout', 'classrooms', 'classroomsInLayout'));
    }

    public function update(Request $request, SchoolLayout $schoolLayout)
    {
        // Check if JSON request (from drawing save via fetch JSON)
        if ($request->isJson()) {
            $validated = $request->validate([
                'name' => 'required|string',
                'floor_number' => 'required|integer',
                'width' => 'nullable|integer',
                'height' => 'nullable|integer',
                'grid_data' => 'nullable|string',
                'background_image' => 'nullable|string',
            ]);

            // Handle background image deletion
            if ($request->has('_delete_bg') && $request->_delete_bg) {
                if ($schoolLayout->background_image && file_exists(public_path($schoolLayout->background_image))) {
                    unlink(public_path($schoolLayout->background_image));
                }
                $validated['background_image'] = null;
            }

            $schoolLayout->update($validated);

            return response()->json(['success' => true]);
        }

        // Form request (from upload or FormData)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'floor_number' => 'required|integer',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'grid_data' => 'nullable|string', // From FormData
        ]);

        // Handle image upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($schoolLayout->background_image && file_exists(public_path($schoolLayout->background_image))) {
                unlink(public_path($schoolLayout->background_image));
            }

            $image = $request->file('background_image');
            $imageName = 'layout_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/school-layouts'), $imageName);
            $validated['background_image'] = 'storage/school-layouts/' . $imageName;
        }

        $schoolLayout->update($validated);

        return redirect()->route('school-layouts.edit', $schoolLayout)->with('success', 'Berhasil disimpan.');
    }

    public function destroy(SchoolLayout $schoolLayout)
    {
        // Reset classroom positions
        Classroom::where('school_layout_id', $schoolLayout->id)
            ->update(['school_layout_id' => null, 'position_x' => null, 'position_y' => null]);
            
        $schoolLayout->delete();
        
        return redirect()->route('school-layouts.index')->with('success', 'Denah berhasil dihapus.');
    }

    // Update posisi classroom di denah
    public function updateClassroomPositions(Request $request, SchoolLayout $schoolLayout)
    {
        $request->validate([
            'positions' => 'required|array',
        ]);

        // If empty array, clear all positions for this layout
        if (empty($request->positions)) {
            Classroom::where('school_layout_id', $schoolLayout->id)
                ->update([
                    'school_layout_id' => null,
                    'position_x' => null,
                    'position_y' => null,
                ]);

            return response()->json(['success' => true, 'message' => 'Semua posisi dikosongkan!']);
        }

        // Reset all seats first for this layout
        Classroom::where('school_layout_id', $schoolLayout->id)
            ->update([
                'school_layout_id' => null,
                'position_x' => null,
                'position_y' => null,
            ]);

        // Update new positions
        foreach ($request->positions as $position) {
            if (isset($position['classroom_id'], $position['x'], $position['y'])) {
                Classroom::where('id', $position['classroom_id'])
                    ->update([
                        'school_layout_id' => $schoolLayout->id,
                        'position_x' => $position['x'],
                        'position_y' => $position['y'],
                    ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Posisi kelas berhasil disimpan!']);
    }
}
