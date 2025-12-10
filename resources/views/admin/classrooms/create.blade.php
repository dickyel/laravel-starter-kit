@extends('layouts.app')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Buat Kelas Baru</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">Kelas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat</li>
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
                            <form class="form" action="{{ route('classrooms.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Kelas</label>
                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                                name="name" placeholder="Contoh: 10 IPA 1" value="{{ old('name') }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="max_students">Kapasitas Maksimal (Siswa)</label>
                                            <input type="number" id="max_students" class="form-control @error('max_students') is-invalid @enderror"
                                                name="max_students" placeholder="30" value="{{ old('max_students', 30) }}">
                                            @error('max_students')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="grid_rows">Jumlah Baris Kursi</label>
                                            <input type="number" id="grid_rows" class="form-control @error('grid_rows') is-invalid @enderror"
                                                name="grid_rows" placeholder="5" value="{{ old('grid_rows', 5) }}">
                                            @error('grid_rows')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="grid_columns">Jumlah Kolom Kursi</label>
                                            <input type="number" id="grid_columns" class="form-control @error('grid_columns') is-invalid @enderror"
                                                name="grid_columns" placeholder="6" value="{{ old('grid_columns', 6) }}">
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
                                                        <input class="form-check-input" type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject{{ $subject->id }}">
                                                        <label class="form-check-label" for="subject{{ $subject->id }}">
                                                            {{ $subject->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
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
