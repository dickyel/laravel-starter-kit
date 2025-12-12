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
                    <div id="users-container">
                        <div class="user-entry border p-3 mb-3 rounded position-relative">
                            <h5 class="mb-3">User #1</h5>
                            <!-- Delete Button (Hidden for first item) -->
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-3 btn-remove-user" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2 text-primary">Informasi Dasar</h6>
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" class="form-control" name="users[0][name]" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control" name="users[0][username]" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="users[0][email]" required>
                                    </div>
                                    <div class="form-group">
                                        <label>ID User (Nomor Induk)</label>
                                        <input type="text" class="form-control" name="users[0][user_id_number]">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2 text-primary">Keamanan & Role</h6>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="users[0][password]" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Konfirmasi Password</label>
                                        <input type="password" class="form-control" name="users[0][password_confirmation]" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="form-select" name="users[0][roles][]" multiple required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2 text-primary">Kontak</h6>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea class="form-control" name="users[0][address]" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Telepon</label>
                                        <input type="text" class="form-control" name="users[0][phone_number]">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2 text-primary">File</h6>
                                    <div class="form-group">
                                        <label>Foto Profil (Opsional)</label>
                                        <input type="file" class="form-control" name="users[0][profile_photos][]" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-success" id="btn-add-user">
                            <i class="bi bi-plus-circle"></i> Tambah User Lain
                        </button>
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
            let userCount = 1;

            document.getElementById('btn-add-user').addEventListener('click', function() {
                const container = document.getElementById('users-container');
                const template = container.firstElementChild.cloneNode(true);
                
                // Update Title
                template.querySelector('h5').innerText = 'User #' + (userCount + 1);
                
                // Show delete button
                template.querySelector('.btn-remove-user').style.display = 'block';

                // Update Input Names and Clear Values
                const inputs = template.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[\d+\]/, `[${userCount}]`);
                    }
                    if (input.type !== 'checkbox' && input.type !== 'radio') {
                        input.value = '';
                    }
                });

                container.appendChild(template);
                userCount++;
                
                // Scroll to new element
                template.scrollIntoView({ behavior: 'smooth' });
            });

            // Delegate event for removal
            document.getElementById('users-container').addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-user')) {
                    e.target.closest('.user-entry').remove();
                    // Optional: Re-number headers? For now, we leave them or could re-loop to fix numbers.
                }
            });
        });
    </script>
@endpush
