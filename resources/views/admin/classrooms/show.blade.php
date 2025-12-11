@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .seat-grid-wrapper {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .seat-grid {
        display: grid;
        grid-template-columns: repeat({{ $classroom->grid_columns }}, minmax(160px, 1fr));
        gap: 15px;
        margin-top: 20px;
        padding-bottom: 20px;
        min-width: max-content; /* Ensure grid doesn't squash */
    }
    .table-container {
        background-color: #fff;
        border: 2px dashed #dce1e9;
        border-radius: 12px;
        min-height: 110px;
        display: flex;
        flex-direction: row;
        gap: 8px;
        padding: 12px 6px 6px 6px;
        position: relative;
        transition: all 0.2s ease;
    }
    .table-container:hover {
        border-color: #9cb0d1;
    }
    .table-label {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #fff;
        padding: 0 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #888;
        border-radius: 20px;
        border: 1px solid #dee2e6;
        z-index: 2;
    }
    .seat-sub-item {
        flex: 1;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
    }
    .seat-sub-item:hover {
        background-color: #fff;
        border-color: #435ebe;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .seat-sub-item.filled {
        background-color: #e5f3ff;
        border: 1px solid #435ebe;
    }
    .seat-sub-number {
        position: absolute;
        top: 2px;
        left: 5px;
        font-size: 0.65rem;
        color: #adb5bd;
        font-weight: bold;
    }
    .student-avatar-small img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .student-name-small {
        margin-top: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.1;
        padding: 0 2px;
    }

</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Visualisasi Kelas: {{ $classroom->name }}</h3>
                <p class="text-subtitle text-muted">
                    Wali Kelas: <strong>{{ $classroom->homeroomTeacher ? $classroom->homeroomTeacher->name : '-' }}</strong> | 
                    Manage denah, anggota kelas, dan jadwal pelajaran.
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">Kelas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TABS NAVIGATION -->
    <ul class="nav nav-tabs mb-3" id="classTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="denah-tab" data-bs-toggle="tab" href="#denah" role="tab" aria-controls="denah" aria-selected="true">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i> Denah & Anggota
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="jadwal-tab" data-bs-toggle="tab" href="#jadwal" role="tab" aria-controls="jadwal" aria-selected="false">
                <i class="bi bi-calendar-week-fill me-2"></i> Jadwal Pelajaran
            </a>
        </li>
    </ul>

    <div class="tab-content" id="classTabContent">
        
        <!-- TAB 1: DENAH & ANGGOTA -->
        <div class="tab-pane fade show active" id="denah" role="tabpanel" aria-labelledby="denah-tab">
            <div class="row">
                <!-- Left Column: Denah Kursi -->
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Denah Tempat Duduk</h4>
                            <div class="fs-6">
                                <span class="badge bg-light-secondary text-secondary me-2"><i class="bi bi-square"></i> Kosong</span>
                                <span class="badge bg-light-primary text-primary"><i class="bi bi-square-fill"></i> Terisi</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light-secondary text-center py-1 mb-3">
                                <small><i class="bi bi-arrow-up-circle"></i> Whiteboard / Guru (Depan)</small>
                            </div>
        
                            <div class="seat-grid-wrapper">
                                <div class="seat-grid">
                                    @php $totalTables = $classroom->grid_rows * $classroom->grid_columns; @endphp
                                    @for ($t = 1; $t <= $totalTables; $t++)
                                        <div class="table-container">
                                            <span class="table-label">Meja {{ $t }}</span>
                                            
                                            @php
                                                // Seat A (Ganjil) & Seat B (Genap)
                                                $seatA = ($t * 2) - 1;
                                                $seatB = ($t * 2);
                                                
                                                $studentA = $classroom->students->where('pivot.seat_number', $seatA)->first();
                                                $studentB = $classroom->students->where('pivot.seat_number', $seatB)->first();
                                            @endphp

                                            <!-- Left Seat -->
                                            <div class="seat-sub-item {{ $studentA ? 'filled' : '' }}"
                                                onclick="openSeatModal({{ $seatA }}, '{{ $studentA ? addslashes($studentA->name) : '' }}', {{ $studentA ? 'true' : 'false' }})">
                                                <span class="seat-sub-number">{{ $seatA }}</span>
                                                @if ($studentA)
                                                    <div class="student-avatar-small"><img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar"></div>
                                                    <div class="student-name-small text-truncate w-100">{{ $studentA->name }}</div>
                                                @else
                                                    <div class="text-muted opacity-25"><i class="bi bi-plus"></i></div>
                                                @endif
                                            </div>

                                            <!-- Right Seat -->
                                            <div class="seat-sub-item {{ $studentB ? 'filled' : '' }}"
                                                onclick="openSeatModal({{ $seatB }}, '{{ $studentB ? addslashes($studentB->name) : '' }}', {{ $studentB ? 'true' : 'false' }})">
                                                <span class="seat-sub-number">{{ $seatB }}</span>
                                                @if ($studentB)
                                                    <div class="student-avatar-small"><img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar"></div>
                                                    <div class="student-name-small text-truncate w-100">{{ $studentB->name }}</div>
                                                @else
                                                    <div class="text-muted opacity-25"><i class="bi bi-plus"></i></div>
                                                @endif
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Right Column: Daftar Anggota -->
                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Anggota Kelas</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid mb-3">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal">
                                    <i class="bi bi-person-plus-fill"></i> Tambah Siswa (Bulk)
                                </button>
                            </div>
        
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Siswa: <strong>{{ $classroom->students->count() }}</strong> / {{ $classroom->max_students }}</span>
                            </div>
                            <div class="progress mb-4" style="height: 10px;">
                                @php $percent = ($classroom->students->count() / $classroom->max_students) * 100; @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
        
                            <h6 class="text-muted mb-2">Belum Dapat Kursi</h6>
                            <ul class="list-group mb-4">
                                @php 
                                    $unseated = $classroom->students->whereNull('pivot.seat_number'); 
                                @endphp
                                @forelse($unseated as $student)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold">{{ $student->name }}</span>
                                        </div>
                                        <form action="{{ route('classrooms.kick', $classroom) }}" method="POST" onsubmit="return confirm('Keluarkan siswa ini dari kelas?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_id" value="{{ $student->id }}">
                                            <button type="submit" class="btn btn-sm btn-light-danger text-danger p-0 border-0" title="Keluarkan"><i class="bi bi-x-circle-fill"></i></button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted small fst-italic">Semua siswa sudah duduk.</li>
                                @endforelse
                            </ul>
        
                            <h6 class="text-muted mb-2">Sudah Dapat Kursi</h6>
                            <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                @foreach($classroom->students->whereNotNull('pivot.seat_number') as $seatedStudent)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <small>{{ $seatedStudent->name }}</small>
                                        <span class="badge bg-light-primary text-primary">Meja {{ $seatedStudent->pivot->seat_number }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: JADWAL PELAJARAN -->
        <div class="tab-pane fade" id="jadwal" role="tabpanel" aria-labelledby="jadwal-tab">
            <div class="row">
                <!-- Form Input Jadwal (Kiri) -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Tambah Jadwal</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classrooms.schedules.store', $classroom) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Hari</label>
                                    <select name="day" class="form-select select2 @error('day') is-invalid @enderror" required>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mata Pelajaran</label>
                                    <select name="subject_id" id="subject_select" class="form-select select2 @error('subject_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>-- Pilih Mapel --</option>
                                        @if($classroom->subjects->count() == 0)
                                            <option disabled>Kelas ini belum punya list mapel.</option>
                                        @else
                                            @foreach($classroom->subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="form-text">
                                        <a href="{{ route('classrooms.edit', $classroom) }}">Tambah Mapel ke Kelas ini</a>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Guru Pengampu</label>
                                    <select name="teacher_id" id="teacher_select" class="form-select select2" required disabled>
                                        <option value="">-- Pilih Mapel Dulu --</option>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" name="start_time" class="form-control" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" name="end_time" class="form-control" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary d-block w-100">Simpan Jadwal</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Jadwal (Kanan) -->
                <div class="col-lg-8">
                    <div class="row">
                        @php
                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $schedulesByDay = $classroom->schedules->groupBy('day');
                        @endphp

                        @foreach($days as $day)
                            <div class="col-md-6 col-12 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light pb-2 pt-3">
                                        <h5 class="text-center mb-0">{{ $day }}</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        @if(isset($schedulesByDay[$day]))
                                            <ul class="list-group list-group-flush">
                                                @foreach($schedulesByDay[$day] as $sched)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold text-primary">{{ $sched->subject->name }}</div>
                                                            <div class="small text-dark"><i class="bi bi-person"></i> {{ $sched->teacher->name ?? '-' }}</div>
                                                            <small class="text-muted"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}</small>
                                                        </div>
                                                        <form action="{{ route('schedules.destroy', $sched) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center py-4 text-muted small">Libur / Kosong</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Enroll (Bulk Add) -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambahkan Siswa ke Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('classrooms.enroll', $classroom) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Pilih siswa-siswa yang akan dimasukkan ke kelas <strong>{{ $classroom->name }}</strong>. <br>
                    <small class="text-muted">*Hanya menampilkan siswa yang belum memiliki kelas aktif.</small></p>
                    
                    <div class="form-group">
                        <label>Pilih Siswa (Tahan CTRL untuk pilih banyak)</label>
                        <select name="user_ids[]" class="form-select" multiple size="10" required>
                            @forelse($studentsAvailable as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->username }})</option>
                            @empty
                                <option disabled>Tidak ada siswa tersedia (semua sudah punya kelas).</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambahkan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Seat (Single Assign) -->
<div class="modal fade" id="seatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Kursi No. <span id="modalSeatDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('classrooms.assign-seat', $classroom) }}" method="POST" id="assignForm">
                    @csrf
                    <input type="hidden" name="seat_number" id="inputSeatNumber">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Siswa</label>
                        <select class="form-select" name="user_id" id="user_id" required>
                            <option value="" selected disabled>-- Pilih Siswa --</option>
                            <optgroup label="Sudah di Kelas Ini (Pindah Kursi)">
                                @foreach($classroom->students as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} {{ $s->pivot->seat_number ? '(Meja '.$s->pivot->seat_number.')' : '(Belum duduk)' }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Siswa Baru (Auto Enroll)">
                                @foreach($studentsAvailable as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan Posisi</button>
                    </div>
                </form>

                <div id="unassignSection" class="mt-4 pt-3 border-top d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Kursi diduduki: <br>
                            <strong id="currentStudentName" class="text-dark"></strong>
                        </div>
                        <form action="{{ route('classrooms.unassign-seat', $classroom) }}" method="POST">
                            @csrf
                            <input type="hidden" name="seat_number" id="inputSeatNumberUnassign">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Kosongkan Kursi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%' 
        });
    });
</script>
<script>
    var seatModal = new bootstrap.Modal(document.getElementById('seatModal'), {});

    function openSeatModal(seatNumber, currentStudentName, isFilled) {
        document.getElementById('modalSeatDisplay').innerText = seatNumber;
        document.getElementById('inputSeatNumber').value = seatNumber;
        document.getElementById('inputSeatNumberUnassign').value = seatNumber;

        document.getElementById('unassignSection').classList.add('d-none');
        document.getElementById('assignForm').reset();
        document.getElementById('user_id').value = ""; 

        if (isFilled) {
            document.getElementById('unassignSection').classList.remove('d-none');
            document.getElementById('currentStudentName').innerText = currentStudentName;
        }
        seatModal.show();
    }

    // Dynamic Teacher Filter
    $('#subject_select').on('change', function() {
        var subjectId = $(this).val();
        var teacherSelect = $('#teacher_select');
        
        // Reset state
        teacherSelect.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if(subjectId) {
            $.ajax({
                url: '/api/subjects/' + subjectId + '/teachers',
                type: 'GET',
                success: function(data) {
                    teacherSelect.empty();
                    teacherSelect.append('<option value="">-- Pilih Guru --</option>');
                    
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            teacherSelect.append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                        teacherSelect.prop('disabled', false);
                    } else {
                        teacherSelect.append('<option value="">Tidak ada guru untuk mapel ini</option>');
                    }
                    // Trigger update select2 kalau perlu, tapi biasanya otomatis update DOM
                },
                error: function() {
                    teacherSelect.html('<option value="">Gagal memuat guru</option>');
                }
            });
        } else {
             teacherSelect.html('<option value="">-- Pilih Mapel Dulu --</option>');
        }
    });
</script>
@endpush
