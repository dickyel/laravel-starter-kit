<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    // 1. Tampilkan Form Input Email
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // 2. Kirim Link Reset ke Email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.'
        ]);

        $token = Str::random(60);

        // Simpan token di tabel password_reset_tokens
        // Pastikan tabel ini ada (standar Laravel biasanya ada)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token, 
                'created_at' => Carbon::now()
            ]
        );

        $link = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        // LOG linknya agar bisa diakses di local environment
        Log::info("RESET PASSWORD LINK for {$request->email}: {$link}");

        return back()->with('status', 'Link reset password telah dikirim! (Cek maillog/laravel.log untuk link dev)');
    }

    // 3. Tampilkan Form Reset Password (Input Password Baru)
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // 4. Proses Reset Password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Cek Token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        // Validasi expired (opsional, misal 60 menit)
        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
        }

        // Update Password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
            'password_2' => Hash::make($request->password) // sesuaikan jika ada field legacy
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
