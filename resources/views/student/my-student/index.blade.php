@extends('layouts.app')

@section('title', 'My Student Summary')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>My Student Summary</h3>
                <p class="text-subtitle text-muted">Rangkuman Aktivitas dan Hasil Belajar Saya</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Student</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Attendance Summary -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Absensi</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table_attendance">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                            <td>{{ $attendance->schedule->subject->name ?? '-' }}</td>
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
                            {{ $attendances->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Results Summary -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hasil Ujian (Exam)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table_exams">
                                <thead>
                                    <tr>
                                        <th>Tanggal Ujian</th>
                                        <th>Judul Ujian</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Nilai (Score)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($examAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->start_time ? $attempt->start_time->format('d M Y H:i') : '-' }}</td>
                                            <td>{{ $attempt->exam->title ?? '-' }}</td>
                                            <td>{{ $attempt->exam->subject->name ?? '-' }}</td>
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada data ujian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $examAttempts->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Purchased Books -->
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Buku yang Dibeli</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Judul</th>
                                        <th>Tanggal Beli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchasedBooks as $item)
                                        <tr>
                                            <td>
                                                @if($item->item->image)
                                                    <img src="{{ asset('storage/' . $item->item->image) }}" width="40" alt="Cover">
                                                @else
                                                    <span class="text-muted">No Img</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->item->title ?? 'Item dihapus' }}</td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada buku yang dibeli.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchased Uniforms -->
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Seragam yang Dibeli</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama</th>
                                        <th>Ukuran</th>
                                        <th>Tanggal Beli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchasedUniforms as $item)
                                        <tr>
                                            <td>
                                                @if($item->item->image)
                                                    <img src="{{ asset('storage/' . $item->item->image) }}" width="40" alt="Img">
                                                @else
                                                    <span class="text-muted">No Img</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->item->name ?? 'Item dihapus' }}</td>
                                            <td>{{ $item->item->size ?? '-' }}</td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada seragam yang dibeli.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
