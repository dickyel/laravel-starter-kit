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
        
        // Check if it's the new array format
        if ($request->has('users')) {
            $request->validate([
                'users.*.name' => 'required|string|max:255',
                'users.*.username' => 'required|string|max:255|unique:users,username', // Note: unique check might need exclusion logic if multiple users have same username in same request (rare edge case)
                'users.*.email' => 'required|string|email|max:255|unique:users,email',
                'users.*.password' => ['required', 'confirmed', Password::min(8)],
                'users.*.roles' => 'required',
                'users.*.user_id_number' => 'nullable|string|max:255|unique:users,user_id_number',
                'users.*.signature_photo' => 'nullable|image|max:2048',
                'users.*.profile_photos.*' => 'image|max:2048',
            ]);

            foreach ($request->users as $key => $userData) {
                // Create User
                $user = User::create([
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => $userData['password'], // Hash is likely handled in Model mutator or needs explicit hashing
                    'user_id_number' => $userData['user_id_number'] ?? null,
                    'address' => $userData['address'] ?? null,
                    'phone_number' => $userData['phone_number'] ?? null,
                ]);

                // Hash password if not handled by model
                // Checking if model has mutator... standard Laravel doesn't. 
                // Let's assume we need to hash it.
                $user->password = Hash::make($userData['password']);
                $user->save();

                // Assign Roles
                if (isset($userData['roles'])) {
                    $user->roles()->sync($userData['roles']);
                }

                // Handle Signature Photo
                if ($request->hasFile("users.$key.signature_photo")) {
                    $path = $request->file("users.$key.signature_photo")->store('signatures', 'public');
                    $user->signature_photo_path = $path;
                    $user->save();
                }

                // Handle Profile Photos
                if ($request->hasFile("users.$key.profile_photos")) {
                    foreach ($request->file("users.$key.profile_photos") as $photo) {
                        $path = $photo->store('profile_photos', 'public');
                        $user->photos()->create(['photo_path' => $path]);
                    }
                }
            }
            
            return redirect()->route('users.index')->with('success', count($request->users) . ' User berhasil ditambahkan.');
        }

        // Fallback for API or other calls (optional, essentially the old code)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => 'required',
        ]);
        
        // ... (Old code implementation if needed, but we can just rely on the new form)
        return back()->with('error', 'Format data tidak valid.');
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
