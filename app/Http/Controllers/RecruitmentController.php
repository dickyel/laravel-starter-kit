<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\CandidatesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RecruitmentController extends Controller
{
    // School Location (Example: Monas, Jakarta)
    const SCHOOL_LAT = -6.175392;
    const SCHOOL_LNG = 106.827153;

    // Admin: List all Candidates
    public function index()
    {
        // Fetch users who have recruitment_status (meaning they registered via recruitment)
        $candidates = User::whereNotNull('recruitment_status')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.recruitment.index', compact('candidates'));
    }

    // Admin: Update Status (Accept/Reject)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:Diterima,Gagal',
        ]);

        $user->update([
            'recruitment_status' => $request->status,
        ]);

        return redirect()->back()->with('success', "Status calon siswa {$user->name} berhasil diubah menjadi {$request->status}.");
    }

    // Public: Show Registration Form
    public function createPublic()
    {
        return view('auth.register-student');
    }

    // Public: Store Registration
    public function storePublic(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Calculate Distance
        $distance = $this->calculateDistance(
            self::SCHOOL_LAT, 
            self::SCHOOL_LNG, 
            $request->latitude, 
            $request->longitude
        );

        // Determine Status
        $status = 'Mungkin Tidak Diterima';
        if ($distance <= 5) {
            $status = 'Mungkin Diterima';
        } elseif ($distance <= 8) { 
            $status = 'Masih Mungkin Diterima';
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_to_school' => $distance,
            'recruitment_status' => $status,
        ]);

        // Assign Role 'recruitment'
        // Check if role exists, if not create it (safe fallback)
        $recruitmentRole = Role::firstOrCreate(['name' => 'recruitment'], ['display_name' => 'Calon Siswa']);
        $user->roles()->attach($recruitmentRole->id);

        // Redirect to Login with Success Message
        return redirect()->route('login')->with('success', "Pendaftaran Berhasil! Silakan login untuk melihat status penerimaan Anda. (Status Awal: $status)");
    }

    // Admin: Show Create Form (Internal)
    public function create()
    {
        return view('admin.recruitment.create');
    }

    // Admin: Store (Internal)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Calculate Distance
        $distance = $this->calculateDistance(
            self::SCHOOL_LAT, 
            self::SCHOOL_LNG, 
            $request->latitude, 
            $request->longitude
        );

        // Determine Status
        $status = 'Mungkin Tidak Diterima';
        if ($distance <= 5) {
            $status = 'Mungkin Diterima';
        } elseif ($distance <= 8) { 
            $status = 'Masih Mungkin Diterima';
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_to_school' => $distance,
            'recruitment_status' => $status,
        ]);

        // Assign Role 'recruitment'
        $recruitmentRole = Role::firstOrCreate(['name' => 'recruitment'], ['display_name' => 'Calon Siswa']);
        $user->roles()->attach($recruitmentRole->id);

        return redirect()->route('recruitment.index')->with('success', "Calon siswa berhasil didaftarkan. Status: $status");
    }

    public function exportExcel()
    {
        return Excel::download(new CandidatesExport, 'recruitment_candidates.xlsx');
    }

    public function exportPdf()
    {
        $candidates = User::whereNotNull('recruitment_status')->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.recruitment.pdf', compact('candidates'));
        return $pdf->download('recruitment_candidates.pdf');
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
