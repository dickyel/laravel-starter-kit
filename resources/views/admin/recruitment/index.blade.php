@extends('layouts.app')

@section('title', 'Daftar Calon Siswa')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Calon Siswa (Recruitment)</h3>
                <p class="text-subtitle text-muted">Kelola penerimaan siswa baru dan update status kelulusan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Recruitment List</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">List Calon Siswa</h4>
                <div>
                    <a href="{{ route('recruitment.export.pdf') }}" class="btn btn-danger btn-sm me-2">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
                    </a>
                    <a href="{{ route('recruitment.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jarak (km)</th>
                                <th>Status Pendaftaran</th>
                                <th>Detail Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $candidate->name }}</div>
                                        <small class="text-muted">{{ $candidate->email }}</small>
                                    </td>
                                    <td>{{ number_format($candidate->distance_to_school, 2) }} km</td>
                                    <td>
                                        @if(str_contains($candidate->recruitment_status, 'Tidak') || $candidate->recruitment_status == 'Gagal')
                                            <span class="badge bg-danger">{{ $candidate->recruitment_status }}</span>
                                        @elseif(str_contains($candidate->recruitment_status, 'Masih'))
                                            <span class="badge bg-warning text-dark">{{ $candidate->recruitment_status }}</span>
                                        @elseif($candidate->recruitment_status == 'Diterima')
                                            <span class="badge bg-success">{{ $candidate->recruitment_status }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $candidate->recruitment_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>Lat: {{ $candidate->latitude }}</small><br>
                                        <small>Lng: {{ $candidate->longitude }}</small><br>
                                        <small>{{ Str::limit($candidate->address, 30) }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal{{ $candidate->id }}">
                                            <i class="bi bi-pencil-square"></i> Ubah Status
                                        </button>

                                        <!-- Status Modal -->
                                        <div class="modal fade" id="statusModal{{ $candidate->id }}" tabindex="-1" role="dialog" aria-labelledby="statusModalTitle{{ $candidate->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalTitle{{ $candidate->id }}">Update Status: {{ $candidate->name }}</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <i data-feather="x"></i>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('recruitment.update', $candidate->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Status Penerimaan</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="Diterima" {{ $candidate->recruitment_status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                                                    <option value="Gagal" {{ $candidate->recruitment_status == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                                                                    <option value="Mungkin Diterima" {{ $candidate->recruitment_status == 'Mungkin Diterima' ? 'selected' : '' }}>Mungkin Diterima (Reset)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                                <i class="bx bx-x d-block d-sm-none"></i>
                                                                <span class="d-none d-sm-block">Batal</span>
                                                            </button>
                                                            <button type="submit" class="btn btn-primary ml-1">
                                                                <i class="bx bx-check d-block d-sm-none"></i>
                                                                <span class="d-none d-sm-block">Simpan</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada calon siswa yang mendaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                     {{ $candidates->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
