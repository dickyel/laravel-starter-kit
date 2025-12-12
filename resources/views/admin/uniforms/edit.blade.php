@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Edit Informasi Seragam</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('uniforms.update', $uniform->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama Seragam</label>
                    <input type="text" name="name" class="form-control" value="{{ $uniform->name }}" required>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ukuran</label>
                            <select name="size" class="form-select" required>
                                <option value="S" {{ $uniform->size == 'S' ? 'selected' : '' }}>S</option>
                                <option value="M" {{ $uniform->size == 'M' ? 'selected' : '' }}>M</option>
                                <option value="L" {{ $uniform->size == 'L' ? 'selected' : '' }}>L</option>
                                <option value="XL" {{ $uniform->size == 'XL' ? 'selected' : '' }}>XL</option>
                                <option value="XXL" {{ $uniform->size == 'XXL' ? 'selected' : '' }}>XXL</option>
                                <option value="ALL SIZE" {{ $uniform->size == 'ALL SIZE' ? 'selected' : '' }}>ALL SIZE</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ $uniform->price }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ $uniform->stock }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Gambar Seragam</label>
                    <input type="file" name="image" class="form-control">
                    @if($uniform->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $uniform->image) }}" width="100" alt="Current Image">
                            <p class="text-muted small">Gambar saat ini</p>
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('uniforms.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
