@extends('layouts.app')

@section('title', 'My Teacher Dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>My Teacher Dashboard</h3>
                <p class="text-subtitle text-muted">Dashboard Guru: Kelola Ujian & Cek Absensi Pribadi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Teacher</li>
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
                        <h4 class="card-title">Absensi Saya</h4>
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

            <!-- Right Side: My Exams -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Ujian yang Saya Buat</h4>
                        <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Buat Ujian Baru
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table_exams">
                                <thead>
                                    <tr>
                                        <th>Judul Ujian</th>
                                        <th>Mapel / Kelas</th>
                                        <th>Waktu Mulai</th>
                                        <th>Durasi</th>
                                        <th>Peserta</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($myExams as $exam)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $exam->title }}</div>
                                                <small class="text-muted">{{ $exam->type }}</small>
                                            </td>
                                            <td>
                                                {{ $exam->subject->name ?? '-' }}
                                                <br>
                                                <span class="badge bg-light-primary text-primary">{{ $exam->classroom->name ?? 'Semua Kelas' }}</span>
                                            </td>
                                            <td>{{ $exam->start_time ? $exam->start_time->format('d M Y H:i') : '-' }}</td>
                                            <td>{{ $exam->duration_minutes }} Menit</td>
                                            <td>
                                                <span class="badge bg-info">{{ $exam->attempts_count }} Percobaan</span>
                                            </td>
                                            <td>
                                                @if($exam->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Non-Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-sm btn-info" title="Lihat & Koreksi">
                                                        <i class="bi bi-eye"></i> Detail / Koreksi
                                                    </a>
                                                    <a href="{{ route('exams.edit', $exam->id) }}" class="btn btn-sm btn-warning" title="Edit Ujian">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Anda belum membuat ujian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $myExams->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
