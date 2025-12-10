@extends('layouts.app')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Kelas</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">Kelas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" action="{{ route('classrooms.update', $classroom) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Kelas</label>
                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                                name="name" placeholder="Contoh: 10 IPA 1" value="{{ old('name', $classroom->name) }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="max_students">Kapasitas Maksimal (Siswa)</label>
                                            <input type="number" id="max_students" class="form-control @error('max_students') is-invalid @enderror"
                                                name="max_students" placeholder="30" value="{{ old('max_students', $classroom->max_students) }}">
                                            @error('max_students')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="grid_rows">Jumlah Baris Kursi</label>
                                            <input type="number" id="grid_rows" class="form-control @error('grid_rows') is-invalid @enderror"
                                                name="grid_rows" placeholder="5" value="{{ old('grid_rows', $classroom->grid_rows) }}">
                                            @error('grid_rows')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="grid_columns">Jumlah Kolom Kursi</label>
                                            <input type="number" id="grid_columns" class="form-control @error('grid_columns') is-invalid @enderror"
                                                name="grid_columns" placeholder="6" value="{{ old('grid_columns', $classroom->grid_columns) }}">
                                            @error('grid_columns')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-3">
                                        <h6 class="mb-2">Mata Pelajaran (Opsional)</h6>
                                        <div class="row">
                                            @foreach ($subjects as $subject)
                                                <div class="col-md-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject{{ $subject->id }}"
                                                            {{ $classroom->subjects->contains($subject->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="subject{{ $subject->id }}">
                                                            {{ $subject->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Update</button>
                                        <a href="{{ route('classrooms.index') }}" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
