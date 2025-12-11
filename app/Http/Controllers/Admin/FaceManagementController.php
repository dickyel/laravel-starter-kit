<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPhoto;
use Illuminate\Http\Request;

class FaceManagementController extends Controller
{
    /**
     * Display users and their photos for face descriptor management
     */
    public function index()
    {
        $users = User::with(['photos'])->get();
        return view('admin.face-management.index', compact('users'));
    }

    /**
     * Store face descriptor for a specific photo
     */
    public function storeFaceDescriptor(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|exists:user_photos,id',
            'face_descriptor' => 'required|array'
        ]);

        $photo = UserPhoto::findOrFail($request->photo_id);
        $photo->face_descriptor = json_encode($request->face_descriptor);
        $photo->save();

        return response()->json([
            'success' => true,
            'message' => 'Face descriptor berhasil disimpan untuk foto ini.'
        ]);
    }

    /**
     * Get all photos that need face descriptors
     */
    public function getPhotosNeedingDescriptors()
    {
        $photos = UserPhoto::with('user')
            ->whereNull('face_descriptor')
            ->get()
            ->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'user_name' => $photo->user->name,
                    'photo_url' => asset('storage/' . $photo->photo_path)
                ];
            });

        return response()->json($photos);
    }
}
