@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Tambah Jam Kerja Baru</h3>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('working-hours.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Jam Kerja</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" 
                       placeholder="contoh: Shift Pagi, Jam Kantor Normal">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="check_in_start" class="form-label">Check-In Mulai</label>
                        <input type="time" class="form-control @error('check_in_start') is-invalid @enderror" 
                               id="check_in_start" name="check_in_start" value="{{ old('check_in_start', '07:00') }}">
                        <small class="text-muted">Waktu paling awal bisa check-in</small>
                        @error('check_in_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="check_in_end" class="form-label">Batas Tepat Waktu</label>
                        <input type="time" class="form-control @error('check_in_end') is-invalid @enderror" 
                               id="check_in_end" name="check_in_end" value="{{ old('check_in_end', '08:00') }}">
                        <small class="text-muted">Sebelum jam ini = Tepat Waktu</small>
                        @error('check_in_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="check_in_late_tolerance" class="form-label">Toleransi Terlambat</label>
                        <input type="time" class="form-control @error('check_in_late_tolerance') is-invalid @enderror" 
                               id="check_in_late_tolerance" name="check_in_late_tolerance" value="{{ old('check_in_late_tolerance', '08:15') }}">
                        <small class="text-muted">Setelah ini = Sangat Terlambat</small>
                        @error('check_in_late_tolerance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="check_out_start" class="form-label">Check-Out Mulai</label>
                        <input type="time" class="form-control @error('check_out_start') is-invalid @enderror" 
                               id="check_out_start" name="check_out_start" value="{{ old('check_out_start', '17:00') }}">
                        <small class="text-muted">Jam pulang resmi</small>
                        @error('check_out_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="check_out_end" class="form-label">Check-Out Akhir</label>
                        <input type="time" class="form-control @error('check_out_end') is-invalid @enderror" 
                               id="check_out_end" name="check_out_end" value="{{ old('check_out_end', '18:00') }}">
                        <small class="text-muted">Setelah jam ini = Lembur</small>
                        @error('check_out_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">
                        Aktifkan jam kerja ini
                    </label>
                    <div class="form-text">Jam kerja yang aktif akan digunakan untuk menghitung status absensi</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('working-hours.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
