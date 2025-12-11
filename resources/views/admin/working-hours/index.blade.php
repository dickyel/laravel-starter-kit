@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Kelola Jam Kerja</h3>
    <p class="text-muted">Atur jam check-in dan check-out untuk absensi</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Jam Kerja</h5>
        <a href="{{ route('working-hours.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Baru
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Check-In Mulai</th>
                        <th>Batas Tepat Waktu</th>
                        <th>Toleransi Terlambat</th>
                        <th>Check-Out Mulai</th>
                        <th>Check-Out Akhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workingHours as $wh)
                    <tr>
                        <td>
                            <strong>{{ $wh->name }}</strong>
                            @if($wh->is_active)
                                <span class="badge bg-success ms-2">Aktif</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($wh->check_in_start)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($wh->check_in_end)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($wh->check_in_late_tolerance)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($wh->check_out_start)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($wh->check_out_end)->format('H:i') }}</td>
                        <td>
                            @if($wh->is_active)
                                <span class="badge bg-success">Sedang Dipakai</span>
                            @else
                                <form action="{{ route('working-hours.activate', $wh) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('working-hours.edit', $wh) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$wh->is_active)
                                <form action="{{ route('working-hours.destroy', $wh) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Penjelasan Jam Kerja</h6>
    </div>
    <div class="card-body">
        <ul class="mb-0">
            <li><strong>Check-In Mulai:</strong> Waktu paling awal karyawan bisa check-in</li>
            <li><strong>Batas Tepat Waktu:</strong> Batas waktu untuk dianggap "Tepat Waktu"</li>
            <li><strong>Toleransi Terlambat:</strong> Setelah waktu ini = "Sangat Terlambat"</li>
            <li><strong>Check-Out Mulai:</strong> Waktu resmi pulang</li>
            <li><strong>Check-Out Akhir:</strong> Setelah waktu ini dihitung lembur</li>
        </ul>
    </div>
</div>
@endsection
