@extends('layouts.app')

@push('styles')
    <!-- Quill Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-heading">
    <h3>Tambah Berita Baru</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm">
                @csrf
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Thumbnail</label>
                    <input type="file" name="thumbnail" class="form-control">
                </div>
                <div class="form-group">
                    <label>Konten</label>
                    <div id="editor" style="height: 300px;"></div>
                    <input type="hidden" name="content" id="content">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" checked>
                    <label class="form-check-label" for="is_published">Publish Sekarang?</label>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('news.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow'
        });

        document.getElementById('newsForm').onsubmit = function() {
            document.getElementById('content').value = quill.root.innerHTML;
        };
    </script>
@endpush
