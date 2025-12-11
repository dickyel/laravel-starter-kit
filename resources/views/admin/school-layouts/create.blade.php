@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Tambah Denah Lantai Baru</h3>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('school-layouts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nama Denah</label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" 
                       placeholder="contoh: Lantai 1, Gedung A" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor Lantai</label>
                <input type="number" class="form-control" name="floor_number" value="{{ old('floor_number', 1) }}" 
                       min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Gambar Denah (Opsional)</label>
                <input type="file" class="form-control" name="background_image" accept="image/*">
                <small class="text-muted">Format: JPG, PNG. Max: 5MB. Gambar akan dijadikan background canvas.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lebar Canvas (px)</label>
                    <input type="number" class="form-control" name="width" value="{{ old('width', 1200) }}" 
                           min="800" max="2000">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tinggi Canvas (px)</label>
                    <input type="number" class="form-control" name="height" value="{{ old('height', 800) }}" 
                           min="600" max="1500">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('school-layouts.index') }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
