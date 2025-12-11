@extends('layouts.app')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Kuis & Ujian</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>List Ujian</h4>
                <div>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('exams.export.excel') }}">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('exams.export.pdf') }}">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('exams.create') }}" class="btn btn-primary">Buat Ujian Baru</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table-exams">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Mapel</th>
                            <th>Tipe</th>
                            <th>Waktu Mulai</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                        <tr>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->subject->name }}</td>
                            <td>
                                <span class="badge bg-{{ $exam->type == 'quiz' ? 'info' : ($exam->type == 'uts' ? 'warning' : 'danger') }}">
                                    {{ strtoupper($exam->type) }}
                                </span>
                            </td>
                            <td>{{ $exam->start_time->format('d M Y H:i') }}</td>
                            <td>{{ $exam->duration_minutes }} Menit</td>
                            <td>
                                @if($exam->is_active && $exam->end_time > now())
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai/Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-sm btn-info">Kelola Soal</a>
                                <form action="{{ route('exams.destroy', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ujian ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
