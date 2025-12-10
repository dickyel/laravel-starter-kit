@extends('layouts.app')

@section('content')
    <div class="page-heading">
        <h3>Profil Saya</h3>
    </div>
    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informasi Dasar</h5>
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="user_id_number">ID User (Nomor Induk)</label>
                                <input type="text" class="form-control @error('user_id_number') is-invalid @enderror"
                                    id="user_id_number" name="user_id_number" value="{{ old('user_id_number', $user->user_id_number) }}">
                                @error('user_id_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Keamanan</h5>
                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="mb-3">Kontak & File</h5>
                            <div class="row">
                                <div class="col-md-6">
                                     <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone_number">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="signature_photo">Foto Tanda Tangan</label>
                                        @if($user->signature_photo_path)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $user->signature_photo_path) }}" alt="Signature" style="height: 50px; border: 1px solid #ccc;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('signature_photo') is-invalid @enderror"
                                            id="signature_photo" name="signature_photo">
                                        <small class="text-muted">Upload baru untuk mengganti.</small>
                                        @error('signature_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="profile_photos">Foto Profil</label>
                                        @if($user->photos && $user->photos->count() > 0)
                                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                                @foreach($user->photos as $photo)
                                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Photo" class="rounded" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ddd;">
                                                @endforeach
                                            </div>
                                        @endif
                                        <div id="file-input-wrapper">
                                            <input type="file" class="form-control mb-2 @error('profile_photos') is-invalid @enderror"
                                                name="profile_photos[]" multiple>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary mt-1" id="add-photo-btn">
                                            <i class="bi bi-plus"></i> Tambah Foto Lain
                                        </button>
                                        <small class="text-muted d-block mt-1">Akan ditambahkan ke foto yang sudah ada.</small>
                                        @error('profile_photos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dynamic File Input
            document.getElementById('add-photo-btn').addEventListener('click', function() {
                var wrapper = document.getElementById('file-input-wrapper');
                var input = document.createElement('input');
                input.type = 'file';
                input.name = 'profile_photos[]';
                input.className = 'form-control mb-2';
                input.multiple = true;
                wrapper.appendChild(input);
            });
        });
    </script>
@endpush
