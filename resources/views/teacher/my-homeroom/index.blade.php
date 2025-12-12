@extends('layouts.app')

@section('title', 'My Wali Kelas Summary')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>My Wali Kelas Summary</h3>
                <p class="text-subtitle text-muted">Halaman khusus untuk Wali Kelas</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Wali Kelas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Left Side: My Attendance -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Absensi Saya (Guru)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table_attendance">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jadwal / Mapel</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Ket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($myAttendance as $attendance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                            <td>
                                                {{ $attendance->schedule->subject->name ?? '-' }} <br>
                                                <small class="text-muted">{{ $attendance->schedule->classroom->name ?? '' }}</small>
                                            </td>
                                            <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                                            <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                                            <td>
                                                @if($attendance->status == 'present')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($attendance->status == 'late')
                                                    <span class="badge bg-warning">Terlambat</span>
                                                @elseif($attendance->status == 'absent')
                                                    <span class="badge bg-danger">Alpha</span>
                                                @elseif($attendance->status == 'permission')
                                                    <span class="badge bg-info">Izin</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($attendance->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $attendance->notes }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data absensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $myAttendance->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side (or below): My Class Students' Exams -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Hasil Ujian Siswa (Kelas: {{ $classroom->name ?? 'Anda bukan Wali Kelas' }})</h4>
                    </div>
                    <div class="card-body">
                        @if(!$classroom)
                            <div class="alert alert-warning">
                                Anda belum terdaftar sebagai Wali Kelas untuk kelas manapun.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover" id="table_exams">
                                    <thead>
                                        <tr>
                                            <th>Siswa</th>
                                            <th>Ujian / Mapel</th>
                                            <th>Waktu Selesai</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($studentAttempts as $attempt)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $attempt->user->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $attempt->user->user_id_number ?? '' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $attempt->exam->title ?? '-' }}</div>
                                                    <span class="text-muted">{{ $attempt->exam->subject->name ?? '-' }}</span>
                                                </td>
                                                <td>{{ $attempt->submit_time ? $attempt->submit_time->format('d M Y H:i') : '-' }}</td>
                                                <td>
                                                    <span class="fw-bold fs-5">{{ $attempt->score ?? 0 }}</span>
                                                </td>
                                                <td>
                                                    @if($attempt->status == 'submitted' || $attempt->status == 'graded')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @elseif($attempt->status == 'in_progress')
                                                        <span class="badge bg-warning">Sedang Mengerjakan</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- Button Detail hanya jika user adalah pembuat exam --}}
                                                    @if(optional($attempt->exam)->created_by == Auth::id())
                                                        <a href="{{ route('exams.show', $attempt->exam_id) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-eye"></i> Detail Exam
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled title="Hanya pembuat soal yang bisa mengedit">
                                                            <i class="bi bi-lock"></i> Read Only
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada data ujian dari siswa kelas ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {{ $studentAttempts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $studentAttempts->links() : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
