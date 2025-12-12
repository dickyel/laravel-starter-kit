@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <h3>Pembelian Seragam Sekolah (Master Data)</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                 <a href="{{ route('uniforms.create') }}" class="btn btn-primary">Tambah Seragam</a>
            </div>
             <div>
                <a href="{{ route('uniforms.export.pdf') }}" class="btn btn-danger btn-sm me-2">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
                </a>
                <a href="{{ route('uniforms.export.excel') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Seragam</th>
                            <th>Ukuran</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uniforms as $uniform)
                        <tr>
                            <td>
                                @if($uniform->image)
                                    <img src="{{ asset('storage/' . $uniform->image) }}" width="50" alt="Image">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $uniform->name }}</td>
                            <td>{{ $uniform->size }}</td>
                            <td>Rp {{ number_format($uniform->price, 0, ',', '.') }}</td>
                            <td>{{ $uniform->stock }}</td>
                            <td>
                                <a href="{{ route('uniforms.edit', $uniform->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('uniforms.destroy', $uniform->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus seragam ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/static/js/pages/simple-datatables.js') }}"></script>
@endpush
