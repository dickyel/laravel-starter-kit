<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize("users.view");

        $users = User::with('roles')->latest()->get();

        return view("admin.users.index", compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize("users.create");

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize("users.create");
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_2' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => 'required',
            'roles.*' => 'exists:roles,id',
            'user_id_number' => 'nullable|string|max:255|unique:users,user_id_number',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'signature_photo' => 'nullable|image|max:2048', // Max 2MB
            'profile_photos' => 'nullable|array',
            'profile_photos.*' => 'image|max:2048',
        ]);

        // Upload Signature Photo
        if ($request->hasFile('signature_photo')) {
            $validated['signature_photo_path'] = $request->file('signature_photo')->store('signatures', 'public');
        }

        // Buat user baru
        $user = User::create($validated);

        // Upload Multiple Profile Photos
        if ($request->hasFile('profile_photos')) {
            foreach ($request->file('profile_photos') as $photo) {
                $path = $photo->store('profile_photos', 'public');
                $user->photos()->create(['photo_path' => $path]);
            }
        }

        // Berikan role
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize("users.edit");
        $user->load('photos'); // Load foto profil
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize("users.edit"); // Fix permission name
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'password_2' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => 'required', // Consistensi nama input 'roles'
            'roles.*' => 'exists:roles,id',
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

        // Update basic info
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->user_id_number = $validated['user_id_number'];
        $user->address = $validated['address'];
        $user->phone_number = $validated['phone_number'];
        
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        if (!empty($validated['password_2'])) {
            $user->password_2 = $validated['password_2'];
        }

        $user->save();

        // Update role
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize("users.delete");
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
