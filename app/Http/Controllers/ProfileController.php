<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil untuk user yang sedang login.
     */
    public function edit()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();
        $user->load('photos');
        return view('profile.edit', compact('user'));
    }

    /**
     * Mengupdate data profil user yang sedang login.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */ // <-- TAMBAHKAN BARIS INI
        $user = Auth::user(); // Ambil user di awal

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'user_id_number' => 'nullable|string|max:255|unique:users,user_id_number,' . $user->id,
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'signature_photo' => 'nullable|image|max:2048',
            'profile_photos' => 'nullable|array',
            'profile_photos.*' => 'image|max:2048',
        ]);

        // Upload Signature Photo Baru (Replace)
        if ($request->hasFile('signature_photo')) {
            // Hapus yang lama jika ada
            if ($user->signature_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature_photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature_photo_path);
            }
            $user->signature_photo_path = $request->file('signature_photo')->store('signatures', 'public');
        }

        // Upload Tambahan Profile Photos
        if ($request->hasFile('profile_photos')) {
            foreach ($request->file('profile_photos') as $photo) {
                $path = $photo->store('profile_photos', 'public');
                $user->photos()->create(['photo_path' => $path]);
            }
        }

        // Siapkan data untuk diupdate
        $dataToUpdate = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'user_id_number' => $validated['user_id_number'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ];

        // Jika password baru diisi, tambahkan ke data yang akan diupdate
        // Model User kita akan otomatis melakukan hashing karena sudah di-setting di $casts
        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = $validated['password'];
        }

        // Gunakan method update()
        $user->update($dataToUpdate);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
