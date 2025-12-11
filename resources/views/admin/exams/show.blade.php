@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Kelola Ujian: {{ $exam->title }}</h3>
    <p class="text-muted">{{ $exam->subject->name }} | {{ strtoupper($exam->type) }}</p>
</div>

<div class="row">
    <!-- Bagian Tambah Soal -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5>Tambah Soal Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('exams.questions.store', $exam->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Teks Soal</label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Soal</label>
                        <select name="type" class="form-select" id="questionType" onchange="toggleOptions()">
                            <option value="multiple_choice">Pilihan Ganda</option>
                            <option value="essay">Essay / Uraian</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Poin / Bobot Nilai</label>
                        <input type="number" name="points" class="form-control" value="5">
                    </div>

                    <!-- Options Container -->
                    <div id="optionsContainer">
                        <label class="form-label">Pilihan Jawaban (Centang yang benar)</label>
                        <!-- A -->
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="0" required checked>
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan A" required>
                        </div>
                        <!-- B -->
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="1">
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan B" required>
                        </div>
                        <!-- C -->
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="2">
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan C">
                        </div>
                        <!-- D -->
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="correct_option" value="3">
                            </div>
                            <input type="text" name="options[]" class="form-control" placeholder="Pilihan D">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Simpan Soal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bagian List Soal -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5>Daftar Soal ({{ $exam->questions->count() }})</h5>
            </div>
            <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                @foreach($exam->questions as $key => $q)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>No. {{ $key + 1 }} ({{ $q->points }} Poin)</strong>
                            <span class="badge bg-secondary">{{ $q->type }}</span>
                        </div>
                        <p class="mt-2 text-dark">{{ $q->question_text }}</p>
                        
                        @if($q->type == 'multiple_choice')
                            <ul class="list-group">
                                @foreach($q->options as $opt)
                                    <li class="list-group-item py-1 {{ $opt->is_correct ? 'list-group-item-success' : '' }}">
                                        {{ $opt->option_text }}
                                        @if($opt->is_correct) <i class="bi bi-check-circle-fill"></i> @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function toggleOptions() {
        const type = document.getElementById('questionType').value;
        const container = document.getElementById('optionsContainer');
        if (type === 'essay') {
            container.style.display = 'none';
            // Disable inputs to avoid validation errors if required
            container.querySelectorAll('input').forEach(i => i.disabled = true);
        } else {
            container.style.display = 'block';
            container.querySelectorAll('input').forEach(i => i.disabled = false);
        }
    }
</script>
@endsection
