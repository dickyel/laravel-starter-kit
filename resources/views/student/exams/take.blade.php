@extends('layouts.app')

@push('styles')
<style>
    .timer-badge {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 50px;
        background-color: #ffebee;
        color: #d32f2f;
        border: 1px solid #ffcdd2;
    }
    .question-card {
        border-left: 4px solid #435ebe;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-md-3 order-md-2 mb-4">
            <div class="card sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-body text-center">
                    <h5 class="mb-3">Sisa Waktu</h5>
                    <div id="timer" class="timer-badge mb-4">00:00:00</div>
                    
                    <h6 class="text-start">Navigasi Soal</h6>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @foreach($exam->questions as $index => $q)
                            <a href="#q-{{ $q->id }}" class="btn btn-sm btn-outline-primary" style="width: 40px;">{{ $index + 1 }}</a>
                        @endforeach
                    </div>

                    <hr>
                    <button type="button" class="btn btn-success w-100" onclick="confirmSubmit()">Selesai & Kumpulkan</button>
                </div>
            </div>
        </div>

        <!-- Exam Questions -->
        <div class="col-md-9 order-md-1">
            <div class="page-heading">
                <h3>{{ $exam->title }}</h3>
                <p class="text-muted">{{ $exam->subject->name }} | Total Soal: {{ $exam->questions->count() }}</p>
            </div>

            <form action="{{ route('student.exams.submit', $exam->id) }}" method="POST" id="examForm">
                @csrf
                
                @foreach($exam->questions as $index => $question)
                    <div class="card question-card mb-4" id="q-{{ $question->id }}">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between">
                                <span>No. {{ $index + 1 }}</span>
                                <span class="badge bg-light text-dark">{{ $question->points }} Poin</span>
                            </h5>
                            <div class="mb-4">
                                <p class="fs-5">{{ $question->question_text }}</p>
                            </div>

                            <div class="options-area">
                                @if($question->type == 'multiple_choice')
                                    @foreach($question->options as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                name="answers[{{ $question->id }}]" 
                                                id="opt-{{ $option->id }}" 
                                                value="{{ $option->id }}">
                                            <label class="form-check-label w-100 py-1 px-2 border rounded cursor-pointer" for="opt-{{ $option->id }}">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="form-group">
                                        <label class="mb-2">Jawaban Anda:</label>
                                        <textarea class="form-control" name="answers[{{ $question->id }}]" rows="5" placeholder="Tulis jawaban essay..."></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Timer Logic
    // Exam End Time vs Attempt Start Time + Duration
    const examEndTime = new Date("{{ $exam->end_time }}").getTime();
    const durationMs = {{ $exam->duration_minutes }} * 60 * 1000;
    const attemptStart = new Date("{{ $attempt->start_time }}").getTime();
    
    // User must finish by MIN(Exam End Time, Attempt Start + Duration)
    const finishTime = Math.min(examEndTime, attemptStart + durationMs);

    function updateTimer() {
        const now = new Date().getTime();
        const distance = finishTime - now;

        if (distance < 0) {
            document.getElementById("timer").innerHTML = "WAKTU HABIS";
            document.getElementById("examForm").submit();
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("timer").innerHTML = 
            (hours < 10 ? "0" + hours : hours) + ":" + 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
    }

    setInterval(updateTimer, 1000);
    updateTimer(); // init

    // Confirm Submit
    function confirmSubmit() {
        Swal.fire({
            title: 'Yakin kumpulkan jawaban?',
            text: "Anda tidak bisa mengubah jawaban setelah dikumpulkan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kumpulkan',
            cancelButtonText: 'Cek Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('examForm').submit();
            }
        });
    }

    // Restore previous answers (if simple reload protection needed - optional)
    // Currently logic just restarts form on load unless saved to DB periodically. 
    // Ideally use localStorage or AJAX auto-save.
</script>
<style>
    /* Radio Button Style */
    .form-check-input:checked + .form-check-label {
        background-color: #e5f3ff;
        border-color: #435ebe !important;
        font-weight: bold;
        color: #435ebe;
    }
</style>
@endpush
