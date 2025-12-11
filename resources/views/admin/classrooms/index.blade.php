@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Kelas</h3>
                    <p class="text-subtitle text-muted">Kelola daftar kelas dan siswa.</p>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Kelas</h4>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('classrooms.index', ['export' => 'excel']) }}">
                                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('classrooms.index', ['export' => 'pdf']) }}">
                                        <i class="bi bi-file-earmark-pdf me-2"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('classrooms.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Kelas</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-classrooms">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Kapasitas</th>
                                    <th>Siswa Aktif</th>
                                    <th>Grid (Baris x Kolom)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classrooms as $classroom)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $classroom->name }}</td>
                                        <td>{{ $classroom->max_students }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $classroom->students_count }}</span>
                                        </td>
                                        <td>{{ $classroom->grid_rows }} x {{ $classroom->grid_columns }}</td>
                                        <td>
                                            <a href="{{ route('classrooms.show', $classroom) }}" class="btn btn-sm btn-info" title="Lihat Denah"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('classrooms.edit', $classroom) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                            <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" class="d-inline" id="delete-form-{{ $classroom->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $classroom->id }}"><i class="bi bi-trash-fill"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-classrooms').DataTable();

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var formId = $(this).closest('form').attr('id');
                Swal.fire({
                    title: 'Yakin hapus kelas?',
                    text: "Data siswa di kelas ini akan kehilangan relasi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#' + formId).submit();
                    }
                })
            });
        });
    </script>
@endpush
