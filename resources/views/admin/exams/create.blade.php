@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Buat Ujian Baru</h3>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('exams.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul Ujian</label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Kuis Matematika Bab 1">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="subject_id" class="form-select" required>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select" required>
                        <option value="quiz">Kuis (Harian/Mingguan)</option>
                        <option value="uts">UTS (Tengah Semester)</option>
                        <option value="uas">UAS (Akhir Semester)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas (Opsional)</label>
                    <select name="classroom_id" class="form-select">
                        <option value="">-- Semua Kelas / Umum --</option>
                        @foreach($classrooms as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika dipilih, ujian hanya muncul untuk siswa di kelas ini.</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai (Batas Akses)</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Durasi Pengerjaan (Menit)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="60" required>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Simpan & Lanjut ke Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection
