@extends('layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center">
    <div>
        <h3>Ujian Saya</h3>
        <p class="text-muted">Daftar ujian yang tersedia untuk Anda kerjakan.</p>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Export
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('student.exams.index', ['export' => 'excel']) }}">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Excel
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('student.exams.index', ['export' => 'pdf']) }}">
                    <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="row">
    @forelse($exams as $exam)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-primary">{{ strtoupper($exam->type) }}</span>
                    <small class="text-muted">{{ $exam->subject->name }}</small>
                </div>
                <h5 class="card-title">{{ $exam->title }}</h5>
                <p class="card-text text-muted mb-2">
                    <i class="bi bi-clock"></i> {{ $exam->duration_minutes }} Menit<br>
                    <i class="bi bi-calendar-event"></i> {{ $exam->start_time->format('d M H:i') }} - {{ $exam->end_time->format('d M H:i') }}
                </p>
                
                @php
                    $attempt = $exam->attempts()->where('user_id', Auth::id())->first();
                @endphp

                <div class="mt-4">
                    @if($attempt)
                        @if($attempt->status == 'submitted')
                             <button class="btn btn-secondary w-100" disabled>Sudah Dikerjakan (Skor: {{ $attempt->score }})</button>
                        @else
                             <!-- Resume -->
                             <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-warning w-100">Lanjutkan</a>
                        @endif
                    @else
                        @if(now() >= $exam->start_time && now() <= $exam->end_time)
                            <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-primary w-100">Kerjakan Sekarang</a>
                        @elseif(now() < $exam->start_time)
                            <button class="btn btn-light w-100" disabled>Belum Dimulai</button>
                        @else
                            <button class="btn btn-light-danger w-100" disabled>Waktu Habis</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">Belum ada ujian yang tersedia untuk Anda saat ini.</div>
    </div>
    @endforelse
</div>
@endsection
