<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;



class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user.roles');

        // Filter by month
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereYear('date', $month->year)
                  ->whereMonth('date', $month->month);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('user.roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by check-in status
        if ($request->filled('check_in_status')) {
            $query->where('check_in_status', $request->check_in_status);
        }

        $attendances = $query->orderBy('date', 'desc')
                             ->orderBy('check_in_time', 'desc')
                             ->get();

        // Get unique roles for filter
        $roles = \App\Models\Role::all();

        return view('admin.attendances.index', compact('attendances', 'roles'));
    }

    /**
     * Show the form for checking in (Face Recognition Page).
     */
    public function create()
    {
        return view('admin.attendances.create');
    }

    /**
     * Handle the check-in request with Face Descriptor or manual fallback.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|string', // Base64 image
            'face_descriptor' => 'nullable|array', // Float array from frontend
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        $user = null;

        // 1. If descriptor provided, match with DB
        if ($request->has('face_descriptor')) {
            $inputDescriptor = $request->face_descriptor;
            $user = $this->findUserByFace($inputDescriptor);
        } 
        // 2. Fallback: Authenticated User (if logged in on personal device)
        elseif (Auth::check()) {
            $user = Auth::user();
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Wajah tidak dikenali atau belum terdaftar.'], 404);
        }

        // Check if already checked in today
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->first();

        if ($existing) {
             return response()->json(['success' => false, 'message' => 'Anda sudah absen hari ini.', 'user' => $user->name], 200);
        }

        // Create Attendance
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'check_in_time' => Carbon::now(),
            'status' => 'present',
            'evidence_photo' => $this->saveBase64Image($request->image, $user->id),
        ]);

        return response()->json(['success' => true, 'message' => 'Absen berhasil!', 'user' => $user->name]);
    }

    /**
     * Helper to find user by face descriptor (Euclidean Distance).
     */
    private function findUserByFace($inputDescriptor)
    {
        $threshold = 0.5; // Strictness (0.4 - 0.6 usually)
        $bestMatch = null;
        $lowestDistance = 100;

        // 1. Check Users table (Main Profile)
        $users = User::whereNotNull('face_descriptor')->get();

        foreach ($users as $user) {
            if (!$user->face_descriptor) continue;
            
            $dbDescriptor = json_decode($user->face_descriptor);
            if (!is_array($dbDescriptor)) continue;

            $distance = $this->euclideanDistance($inputDescriptor, $dbDescriptor);

            if ($distance < $threshold && $distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatch = $user;
            }
        }

        // 2. Check UserPhotos table (Additional Photos)
        // Only if we haven't found a very close match, or to find a BETTER match
        $photos = \App\Models\UserPhoto::whereNotNull('face_descriptor')->with('user')->get();

        foreach ($photos as $photo) {
            if (!$photo->face_descriptor) continue;
            
            $dbDescriptor = json_decode($photo->face_descriptor);
            if (!is_array($dbDescriptor)) continue;

            $distance = $this->euclideanDistance($inputDescriptor, $dbDescriptor);

            // If we find a better match (lower distance)
            if ($distance < $threshold && $distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatch = $photo->user;
            }
        }

        return $bestMatch;
    }

    private function euclideanDistance($a, $b)
    {
        if (count($a) !== count($b)) return 100;
        
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
    
    private function saveBase64Image($base64Image, $userId)
    {
        if (!$base64Image) return null;
        
        // Remove header like "data:image/png;base64,"
        $image_parts = explode(";base64,", $base64Image);
        if (count($image_parts) < 2) return null;
        
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'attendance_' . $userId . '_' . time() . '.png';
        $path = public_path('storage/attendances/' . $fileName);
        
        // Ensure dir exists
        if (!file_exists(public_path('storage/attendances/'))) {
            mkdir(public_path('storage/attendances/'), 0777, true);
        }
        
        file_put_contents($path, $image_base64);
        return 'storage/attendances/' . $fileName;
    }

    // --- Face Registration Methods ---
    
    public function showRegisterFace()
    {
        return view('admin.attendances.register_face');
    }

    public function storeFace(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'face_descriptor' => 'required|array'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->face_descriptor = json_encode($request->face_descriptor);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Data wajah berhasil disimpan.']);
    }

    // --- KIOSK METHODS ---
    public function kioskIndex()
    {
        return view('attendance.kiosk');
    }

    public function storeKiosk(Request $request)
    {
        $request->validate([
            'image' => 'nullable|string',
            'face_descriptor' => 'required|array',
        ]);

        $inputDescriptor = $request->face_descriptor;
        $user = $this->findUserByFace($inputDescriptor);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Wajah tidak dikenali.'], 200);
        }

        // Get working hours (ambil yang aktif)
        $workingHour = \App\Models\WorkingHour::where('is_active', true)->first();
        
        if (!$workingHour) {
            return response()->json(['success' => false, 'message' => 'Jam kerja belum dikonfigurasi.'], 200);
        }

        // Check if record exists for today
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->first();

        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        if ($existing) {
            // Jika sudah check-in, sekarang check-out
            if ($existing->check_out_time) {
                return response()->json([
                    'success' => true,
                    'message' => 'Anda sudah melakukan check-in dan check-out hari ini.',
                    'user_name' => $user->name,
                    'already_complete' => true
                ]);
            }

            // Update dengan check-out
            $checkOutTime = Carbon::parse($currentTime);
            $checkOutStart = Carbon::parse($workingHour->check_out_start);
            $checkOutEnd = Carbon::parse($workingHour->check_out_end);

            // Hitung status check-out
            if ($checkOutTime->lt($checkOutStart)) {
                $checkOutStatus = 'early'; // Pulang lebih awal
                $overtimeMinutes = 0;
            } elseif ($checkOutTime->gt($checkOutEnd)) {
                $checkOutStatus = 'overtime'; // Lembur
                $overtimeMinutes = $checkOutTime->diffInMinutes($checkOutEnd);
            } else {
                $checkOutStatus = 'on_time'; // Tepat waktu
                $overtimeMinutes = 0;
            }

            $existing->update([
                'check_out_time' => $currentTime,
                'check_out_status' => $checkOutStatus,
                'overtime_minutes' => $overtimeMinutes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil!',
                'user_name' => $user->name,
                'action' => 'check_out',
                'check_out_status' => $checkOutStatus,
                'already_present' => false
            ]);
        }

        // Buat record baru untuk check-in
        $checkInTime = Carbon::parse($currentTime);
        $checkInEnd = Carbon::parse($workingHour->check_in_end);
        $checkInLateTolerance = Carbon::parse($workingHour->check_in_late_tolerance);

        // Hitung status check-in
        if ($checkInTime->lte($checkInEnd)) {
            $checkInStatus = 'on_time'; // Tepat waktu
            $lateMinutes = 0;
        } elseif ($checkInTime->lte($checkInLateTolerance)) {
            $checkInStatus = 'late'; // Terlambat (masih dalam toleransi)
            $lateMinutes = $checkInTime->diffInMinutes($checkInEnd);
        } else {
            $checkInStatus = 'very_late'; // Sangat terlambat
            $lateMinutes = $checkInTime->diffInMinutes($checkInEnd);
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'check_in_time' => $currentTime,
            'status' => 'present',
            'check_in_status' => $checkInStatus,
            'late_minutes' => $lateMinutes,
            'evidence_photo' => $this->saveBase64Image($request->image, $user->id),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'user_name' => $user->name,
            'action' => 'check_in',
            'check_in_status' => $checkInStatus,
            'late_minutes' => $lateMinutes,
            'already_present' => false
        ]);
    }
}
