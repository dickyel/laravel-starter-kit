@extends('layouts.app')

@push('styles')
<style>
    .classroom-layout {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 40px;
        min-height: 600px;
        position: relative;
        overflow: hidden;
    }

    .classroom-grid {
        display: grid;
        gap: 15px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }

    .seat {
        background: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .seat:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .seat.occupied {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .seat.empty {
        background: rgba(255, 255, 255, 0.3);
        border: 2px dashed rgba(255, 255, 255, 0.5);
    }

    .seat-number {
        font-size: 0.8rem;
        opacity: 0.7;
        margin-bottom: 5px;
    }

    .student-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .teacher-desk {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .students-sidebar {
        background: white;
        border-radius: 15px;
        padding: 20px;
        max-height: 600px;
        overflow-y: auto;
    }

    .student-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: move;
        transition: all 0.2s;
        border-left: 4px solid #667eea;
    }

    .student-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    .student-item.assigned {
        opacity: 0.5;
        background: #d4edda;
        border-left-color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Denah Kelas: {{ $classroom->name }}</h3>
            <p class="text-muted">Atur tempat duduk siswa dengan drag & drop</p>
        </div>
        <a href="{{ route('classrooms.show', $classroom) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Layout Kelas -->
    <div class="col-lg-9">
        <div class="classroom-layout">
            <div class="teacher-desk">
                <i class="bi bi-easel fs-3"></i>
                <div>Meja Guru</div>
                @if($classroom->homeroomTeacher)
                    <small>{{ $classroom->homeroomTeacher->name }}</small>
                @endif
            </div>

            <div class="classroom-grid" id="classroom-grid" 
                 style="grid-template-columns: repeat({{ $classroom->grid_columns }}, 1fr);">
                
                @php
                    $totalSeats = $classroom->grid_rows * $classroom->grid_columns;
                    $seatMap = [];
                    
                    // Buat mapping seat_number => student
                    foreach($classroom->students as $student) {
                        if ($student->pivot->seat_number) {
                            $seatMap[$student->pivot->seat_number] = $student;
                        }
                    }
                @endphp

                @for($i = 1; $i <= $totalSeats; $i++)
                    <div class="seat {{ isset($seatMap[$i]) ? 'occupied' : 'empty' }}" 
                         data-seat="{{ $i }}"
                         ondrop="drop(event)" 
                         ondragover="allowDrop(event)">
                        <div class="seat-number">Kursi #{{ $i }}</div>
                        @if(isset($seatMap[$i]))
                            <div class="student-name" data-user-id="{{ $seatMap[$i]->id }}">
                                {{ $seatMap[$i]->name }}
                            </div>
                        @else
                            <div class="text-muted small">Kosong</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <div class="mt-3">
            <button type="button" class="btn btn-success btn-lg" id="save-layout">
                <i class="bi bi-save"></i> Simpan Denah
            </button>
            <button type="button" class="btn btn-warning" id="reset-layout">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Daftar Siswa -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-people"></i> Daftar Siswa</h6>
            </div>
            <div class="card-body students-sidebar">
                @foreach($classroom->students()->wherePivotNull('seat_number')->orWherePivot('seat_number', 0)->get() as $student)
                    <div class="student-item" 
                         draggable="true" 
                         ondragstart="drag(event)" 
                         data-user-id="{{ $student->id }}">
                        <strong>{{ $student->name }}</strong><br>
                        <small class="text-muted">{{ $student->user_id_number ?? 'No ID' }}</small>
                    </div>
                @endforeach

                @if($classroom->students()->wherePivotNull('seat_number')->orWherePivot('seat_number', 0)->count() == 0)
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle fs-1"></i>
                        <p class="mt-2">Semua siswa sudah di-assign!</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6>📊 Statistik</h6>
                <p class="mb-1"><strong>Total Kursi:</strong> {{ $totalSeats }}</p>
                <p class="mb-1"><strong>Terisi:</strong> {{ count($seatMap) }}</p>
                <p class="mb-1"><strong>Kosong:</strong> {{ $totalSeats - count($seatMap) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let draggedUserId = null;
    let draggedFromSeat = null;

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        draggedUserId = ev.target.getAttribute('data-user-id');
        draggedFromSeat = null;
        ev.dataTransfer.effectAllowed = 'move';
    }

    function drop(ev) {
        ev.preventDefault();
        
        const targetSeat = ev.currentTarget;
        const seatNumber = targetSeat.getAttribute('data-seat');
        
        if (!draggedUserId) return;

        // Cek jika kursi sudah terisi
        const existingStudent = targetSeat.querySelector('.student-name');
        if (existingStudent && existingStudent.getAttribute('data-user-id')) {
            alert('Kursi ini sudah terisi! Kosongkan dulu atau pilih kursi lain.');
            return;
        }

        // Update UI
        const studentName = event.dataTransfer.getData('text') || 
                           document.querySelector(`[data-user-id="${draggedUserId}"]`)?.textContent?.trim();
        
        targetSeat.classList.remove('empty');
        targetSeat.classList.add('occupied');
        targetSeat.innerHTML = `
            <div class="seat-number">Kursi #${seatNumber}</div>
            <div class="student-name" data-user-id="${draggedUserId}">
                Loading...
            </div>
        `;

        // Ambil nama dari sidebar atau seat lain
        const sourceElement = document.querySelector(`.student-item[data-user-id="${draggedUserId}"], .student-name[data-user-id="${draggedUserId}"]`);
        if (sourceElement) {
            const nama = sourceElement.textContent.trim().split('\n')[0];
            targetSeat.querySelector('.student-name').textContent = nama;
            
            // Hapus dari sidebar jika dari sidebar
            if (sourceElement.classList.contains('student-item')) {
                sourceElement.remove();
            }
        }

        draggedUserId = null;
    }

    // Save Layout
    document.getElementById('save-layout').addEventListener('click', function() {
        const assignments = {};
        
        document.querySelectorAll('.seat.occupied').forEach(seat => {
            const seatNumber = seat.getAttribute('data-seat');
            const studentName = seat.querySelector('.student-name');
            const userId = studentName?.getAttribute('data-user-id');
            
            if (userId) {
                assignments[seatNumber] = userId;
            }
        });

        // Send to server
        fetch('{{ route('classrooms.update-seating-chart', $classroom) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                layout: JSON.stringify({}), // Bisa dikembangkan untuk custom layout
                assignments: JSON.stringify(assignments)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ Denah berhasil disimpan!');
                window.location.reload();
            } else {
                alert('❌ Gagal menyimpan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Terjadi kesalahan sistem');
        });
    });

    // Reset
    document.getElementById('reset-layout').addEventListener('click', function() {
        if (confirm('Yakin ingin reset denah? Semua siswa akan kembali ke sidebar.')) {
            window.location.reload();
        }
    });

    // Make seats draggable too (for moving students between seats)
    document.querySelectorAll('.seat.occupied').forEach(seat => {
        const studentDiv = seat.querySelector('.student-name');
        if (studentDiv) {
            seat.setAttribute('draggable', 'true');
            seat.addEventListener('dragstart', function(e) {
                draggedUserId = studentDiv.getAttribute('data-user-id');
                draggedFromSeat = seat.getAttribute('data-seat');
            });
        }
    });
</script>
@endpush
