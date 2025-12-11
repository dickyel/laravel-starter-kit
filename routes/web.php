<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ActivityLogController;




Route::get('/', function () {
    return view('landing');
})->name('landing');


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot Password Routes
    Route::get('password/reset', [\App\Http\Controllers\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [\App\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [\App\Http\Controllers\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\ForgotPasswordController::class, 'reset'])->name('password.update');
});

// --- RUTE PUBLIK KIOSK ABSENSI ---
Route::get('/attendance-kiosk', [\App\Http\Controllers\Admin\AttendanceController::class, 'kioskIndex'])->name('attendance.kiosk');
Route::post('/api/attendance-kiosk/check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'storeKiosk'])->name('attendance.kiosk.check-in');




// Grup untuk Rute yang Membutuhkan Autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // --- RUTE BARU UNTUK MANAJEMEN AKSES ---
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class); 
    Route::resource('menus', MenuController::class);



    // -----------------------------------------
    // -----------------------------------------
        // --- RUTE BARU UNTUK MANAJEMEN Barang ---
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    // -----------------------------------------

    // --- RUTE AKADEMIK (SEKOLAH) ---
    Route::get('teachers/export/excel', [\App\Http\Controllers\Admin\TeacherController::class, 'exportExcel'])->name('teachers.export.excel');
    Route::get('teachers/export/pdf', [\App\Http\Controllers\Admin\TeacherController::class, 'exportPdf'])->name('teachers.export.pdf');
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::get('subjects/export/excel', [\App\Http\Controllers\Admin\SubjectController::class, 'exportExcel'])->name('subjects.export.excel');
    Route::get('subjects/export/pdf', [\App\Http\Controllers\Admin\SubjectController::class, 'exportPdf'])->name('subjects.export.pdf');
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);
    Route::post('classrooms/{classroom}/enroll', [\App\Http\Controllers\Admin\ClassroomController::class, 'enroll'])->name('classrooms.enroll');
    Route::delete('classrooms/{classroom}/kick', [\App\Http\Controllers\Admin\ClassroomController::class, 'kick'])->name('classrooms.kick');
    Route::post('classrooms/{classroom}/assign-seat', [\App\Http\Controllers\Admin\ClassroomController::class, 'assignSeat'])->name('classrooms.assign-seat');
    Route::post('classrooms/{classroom}/unassign-seat', [\App\Http\Controllers\Admin\ClassroomController::class, 'unassignSeat'])->name('classrooms.unassign-seat');
    
    // Denah Sekolah
    Route::resource('school-layouts', \App\Http\Controllers\Admin\SchoolLayoutController::class);
    Route::post('school-layouts/{schoolLayout}/update-positions', [\App\Http\Controllers\Admin\SchoolLayoutController::class, 'updateClassroomPositions'])->name('school-layouts.update-positions');
    
    // Jadwal
    Route::post('classrooms/{classroom}/schedules', [\App\Http\Controllers\Admin\ClassScheduleController::class, 'store'])->name('classrooms.schedules.store');
    Route::delete('schedules/{schedule}', [\App\Http\Controllers\Admin\ClassScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('api/subjects/{subject}/teachers', [\App\Http\Controllers\Admin\ClassScheduleController::class, 'getTeachersBySubject'])->name('api.subjects.teachers');
    // -------------------------------

    // --- RUTE ABSENSI & WAJAH ---
    Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/export/excel', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportExcel'])->name('attendance.export.excel');
    Route::get('attendance/export/pdf', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf');
    // Route::get('attendance/scan', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('attendance.scan');
    // Route::post('attendance/check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
    
    // // Registrasi Wajah
    // Route::get('face-register', [\App\Http\Controllers\Admin\AttendanceController::class, 'showRegisterFace'])->name('face.register');
    // Route::post('face-register', [\App\Http\Controllers\Admin\AttendanceController::class, 'storeFace'])->name('face.store');

    // Face Management - Process Photos for Face Recognition
    Route::get('face-management', [\App\Http\Controllers\Admin\FaceManagementController::class, 'index'])->name('face-management.index');
    Route::get('api/face-management/photos-needing-descriptors', [\App\Http\Controllers\Admin\FaceManagementController::class, 'getPhotosNeedingDescriptors']);
    Route::post('api/face-management/store-descriptor', [\App\Http\Controllers\Admin\FaceManagementController::class, 'storeFaceDescriptor']);

    // Working Hours Management
    Route::resource('working-hours', \App\Http\Controllers\Admin\WorkingHourController::class);
    Route::patch('working-hours/{workingHour}/activate', [\App\Http\Controllers\Admin\WorkingHourController::class, 'activate'])->name('working-hours.activate');

    // --- RUTE UJIAN (LMS) ---
    Route::get('exams/export/excel', [\App\Http\Controllers\Admin\ExamController::class, 'exportExcel'])->name('exams.export.excel');
    Route::get('exams/export/pdf', [\App\Http\Controllers\Admin\ExamController::class, 'exportPdf'])->name('exams.export.pdf');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    Route::post('exams/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'storeQuestion'])->name('exams.questions.store');
    
    // Student Exam Access
    Route::get('my-exams', [\App\Http\Controllers\Admin\ExamController::class, 'studentIndex'])->name('student.exams.index');
    Route::get('my-exams/{exam}/take', [\App\Http\Controllers\Admin\ExamController::class, 'takeExam'])->name('student.exams.take');
    Route::post('my-exams/{exam}/submit', [\App\Http\Controllers\Admin\ExamController::class, 'submitExam'])->name('student.exams.submit');




    // --- RUTE UNTUK PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('audit-logs', [ActivityLogController::class, 'index'])->name('audit-logs.index');


    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
