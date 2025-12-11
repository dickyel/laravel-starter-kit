@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Data Absensi</h3>
    <p class="text-subtitle text-muted">Log harian kehadiran dengan filter lengkap.</p>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Filter Data</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('attendance.index') }}" id="filter-form">
            <div class="row g-3">
                <!-- Filter Bulan -->
                <div class="col-md-3">
                    <label for="month" class="form-label">Bulan</label>
                    <input type="month" class="form-control" name="month" id="month" value="{{ request('month') }}">
                </div>

                <!-- Filter Range Date -->
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                </div>

                <!-- Filter Role -->
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" name="role" id="role">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Kehadiran -->
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Kehadiran</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">Semua Status</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                        <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="permission" {{ request('status') == 'permission' ? 'selected' : '' }}>Izin</option>
                    </select>
                </div>

                <!-- Filter Status Check-In -->
                <div class="col-md-3">
                    <label for="check_in_status" class="form-label">Status Check-In</label>
                    <select class="form-select" name="check_in_status" id="check_in_status">
                        <option value="">Semua</option>
                        <option value="on_time" {{ request('check_in_status') == 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="late" {{ request('check_in_status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="very_late" {{ request('check_in_status') == 'very_late' ? 'selected' : '' }}>Sangat Terlambat</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-6">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    <div class="btn-group ms-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('attendance.export.excel', request()->all()) }}">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('attendance.export.pdf', request()->all()) }}">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-attendance">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status Masuk</th>
                        <th>Status Pulang</th>
                        <th>Terlambat</th>
                        <th>Lembur</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                        <td>{{ $row->user->name }}</td>
                        <td>
                            @foreach($row->user->roles as $role)
                                <span class="badge bg-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($row->check_in_time)
                                {{ \Carbon\Carbon::parse($row->check_in_time)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->check_out_time)
                                {{ \Carbon\Carbon::parse($row->check_out_time)->format('H:i') }}
                            @else
                                <span class="badge bg-warning">Belum Check-Out</span>
                            @endif
                        </td>
                        <td>
                            @if($row->check_in_status == 'on_time')
                                <span class="badge bg-success">Tepat Waktu</span>
                            @elseif($row->check_in_status == 'late')
                                <span class="badge bg-warning">Terlambat</span>
                            @elseif($row->check_in_status == 'very_late')
                                <span class="badge bg-danger">Sangat Terlambat</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->check_out_status == 'on_time')
                                <span class="badge bg-success">Tepat Waktu</span>
                            @elseif($row->check_out_status == 'early')
                                <span class="badge bg-info">Pulang Cepat</span>
                            @elseif($row->check_out_status == 'overtime')
                                <span class="badge bg-primary">Lembur</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->late_minutes > 0)
                                <span class="text-danger fw-bold">{{ $row->late_minutes }} menit</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->overtime_minutes > 0)
                                <span class="text-primary fw-bold">{{ $row->overtime_minutes }} menit</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($row->evidence_photo)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#photoModal{{ $row->id }}">
                                    <img src="{{ asset($row->evidence_photo) }}" width="50" class="rounded-circle" alt="Foto">
                                </a>

                                <!-- Modal -->
                                <div class="modal fade" id="photoModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $row->user->name }} - {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset($row->evidence_photo) }}" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script>
    // Initialize DataTable
    if (document.getElementById('table-attendance')) {
        const dataTable = new simpleDatatables.DataTable("#table-attendance", {
            searchable: true,
            fixedHeight: false,
            perPage: 25,
        });
    }
</script>
@endpush
