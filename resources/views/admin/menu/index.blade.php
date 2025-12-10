@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <h3>{{$pageTitle ?? 'Default Judul'}}</h3>
    </div>

    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="row">
            <section class="section">
                <div class="row" id="table-bordered">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Daftar Menu</h5>
                                    @can('permission.create')
                                    <a href="{{ route('menus.create') }}" class="btn btn-primary"><i
                                            class="bi bi-plus-lg"></i> Tambah Menu</a>
                                    @endcan
                                </div>

                            </div>
                            <div class="card-content">
                                <table class="table table-striped table-hover" id="table-menus">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th>Nama Menu</th>
                                            <th>Induk Menu</th>
                                            <th>Route</th>
                                            <th>Status</th>
                                            <th>Ikon</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($menus as $menu)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $menu->name }}</td>
                                                <td>
                                                    {{-- Tampilkan nama parent jika ada, jika tidak tampilkan strip --}}
                                                    <span
                                                        class="badge bg-light-secondary">{{ $menu->parent ? $menu->parent->name : '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-info">{{ $menu->route_name ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    @if($menu->is_active)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td><i class="{{ $menu->icon }}"></i></td>
                                                <td class="text-center">
                                                    @can('menu.edit')
                                                    <a href="{{ route('menus.edit', $menu) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    @endcan

                                                    @can('menu.delete')
                                                    <form action="{{ route('menus.destroy', $menu) }}" method="POST"
                                                        class="d-inline" id="delete-form-{{ $menu->id }}">
                                                        @csrf
                                                        @method('DELETE')

                                                        {{-- Tambahkan class "btn-delete" untuk target JavaScript --}}
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-form-id="delete-form-{{ $menu->id }}">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </div>
@endsection



@push('scripts')
    <script src="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-menus').DataTable();
        });
    </script>
    <script>
        // Tunggu sampai semua HTML dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua tombol dengan kelas .btn-delete
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); // Mencegah aksi default tombol

                    // Ambil form terdekat dari tombol yang diklik
                    const form = this.closest('form');

                    // Tampilkan SweetAlert
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        // Jika pengguna menekan "Ya, hapus!"
                        if (result.isConfirmed) {
                            // Submit form untuk menghapus data
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
