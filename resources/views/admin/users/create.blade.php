@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/choices.js/public/assets/styles/choices.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <h3>Tambah User Baru</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informasi Dasar</h5>
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="user_id_number">ID User (Nomor Induk)</label>
                                <input type="text" class="form-control @error('user_id_number') is-invalid @enderror"
                                    id="user_id_number" name="user_id_number" value="{{ old('user_id_number') }}">
                                @error('user_id_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Keamanan & Role</h5>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                            
                            <hr> <!-- Divider -->

                            <div class="form-group">
                                <label for="password_2">Password Cadangan (Opsional)</label>
                                <input type="password" class="form-control @error('password_2') is-invalid @enderror"
                                    id="password_2" name="password_2">
                                <small class="text-muted">Password alternatif untuk login.</small>
                                @error('password_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_2_confirmation">Konfirmasi Password Cadangan</label>
                                <input type="password" class="form-control" id="password_2_confirmation"
                                    name="password_2_confirmation">
                            </div>
                            <div class="form-group">
                                <label for="roles">Role</label>
                                <select class="choices form-select @error('roles') is-invalid @enderror" id="roles"
                                    name="roles[]" multiple> <!-- Added multiple -->
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone_number">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="signature_photo">Foto Tanda Tangan (Opsional)</label>
                                        <input type="file" class="form-control @error('signature_photo') is-invalid @enderror"
                                            id="signature_photo" name="signature_photo">
                                        @error('signature_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="profile_photos">Foto Profil (Bisa Banyak)</label>
                                        <div id="file-input-wrapper">
                                            <input type="file" class="form-control mb-2 @error('profile_photos') is-invalid @enderror"
                                                name="profile_photos[]" multiple>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary mt-1" id="add-photo-btn">
                                            <i class="bi bi-plus"></i> Tambah Foto Lain
                                        </button>
                                        @error('profile_photos')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const choices = document.querySelectorAll('.choices');
                new Choices(choices[i], {
                    removeItemButton: true
                });
            }

            // Dynamic File Input
            document.getElementById('add-photo-btn').addEventListener('click', function() {
                var wrapper = document.getElementById('file-input-wrapper');
                var input = document.createElement('input');
                input.type = 'file';
                input.name = 'profile_photos[]'; // Array name important
                input.className = 'form-control mb-2';
                input.multiple = true;
                wrapper.appendChild(input);
            });
        });
    </script>
@endpush
