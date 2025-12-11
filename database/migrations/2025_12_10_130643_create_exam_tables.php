<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Ujian/Kuis
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Guru pembuat
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('cascade'); // Opsional: jika ujian khusus kelas tertentu
            $table->string('title');
            $table->enum('type', ['quiz', 'uts', 'uas', 'other'])->default('quiz');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Soal
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->timestamps();
        });

        // Tabel Pilihan Jawaban (untuk PG)
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // Tabel Percobaan Ujian (Siswa mengerjakan)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('submit_time')->nullable();
            $table->decimal('score', 8, 2)->nullable(); // Nilai akhir
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->timestamps();
        });

        // Tabel Jawaban Siswa
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->onDelete('set null'); // Untuk PG
            $table->text('answer_text')->nullable(); // Untuk Essay
            $table->decimal('score_obtained', 8, 2)->default(0); // Nilai per soal (essay manual grade)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
