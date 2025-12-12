@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Edit Informasi Buku</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Penulis</label>
                            <input type="text" name="author" class="form-control" value="{{ $book->author }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Penerbit</label>
                            <input type="text" name="publisher" class="form-control" value="{{ $book->publisher }}">
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ $book->price }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ $book->stock }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Cover Buku</label>
                    <input type="file" name="image" class="form-control">
                    @if($book->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $book->image) }}" width="100" alt="Current Image">
                            <p class="text-muted small">Gambar saat ini</p>
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
