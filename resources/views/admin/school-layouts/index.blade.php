@extends('layouts.app')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Denah Sekolah</h3>
            <p class="text-muted">Kelola denah lantai dan posisi ruang kelas</p>
        </div>
        <a href="{{ route('school-layouts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Lantai Baru
        </a>
    </div>
</div>

<div class="row">
    @forelse($layouts as $layout)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $layout->name }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Lantai:</strong> {{ $layout->floor_number }}</p>
                <p><strong>Ukuran Canvas:</strong> {{ $layout->width }} x {{ $layout->height }} px</p>
                <p><strong>Jumlah Kelas:</strong> {{ $layout->classrooms->count() }} kelas</p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('school-layouts.edit', $layout) }}" class="btn btn-success">
                        <i class="bi bi-map"></i> Atur Denah
                    </a>
                    <form action="{{ route('school-layouts.destroy', $layout) }}" method="POST" 
                          onsubmit="return confirm('Hapus denah ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Belum ada denah lantai. Klik "Tambah Lantai Baru" untuk membuat denah pertama.
        </div>
    </div>
    @endforelse
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@endsection
