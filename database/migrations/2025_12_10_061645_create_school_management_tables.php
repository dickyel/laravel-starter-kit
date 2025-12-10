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
        // 1. Tabel Kelas
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: 10 IPA 1
            $table->integer('max_students')->default(30);
            $table->integer('grid_rows')->default(5); // Untuk visualisasi (Baris)
            $table->integer('grid_columns')->default(6); // Untuk visualisasi (Kolom)
            $table->timestamps();
        });

        // 2. Tabel Mata Pelajaran
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Matematika Wajib
            $table->timestamps();
        });

        // 3. Pivot: Mata Pelajaran apa saja di Kelas ini
        Schema::create('classroom_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Pivot: Siswa (User) ada di Kelas ini + Nomor Kursi
        Schema::create('student_classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siswanya
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->integer('seat_number')->nullable(); // Nomor kursi (1 s/d max_students)
            $table->boolean('is_active')->default(true); // Histori kelas
            $table->timestamps();
            
            // Cegah duplikasi siswa aktif di kelas yang sama
            // $table->unique(['user_id', 'is_active']); // Opsional, logic di controller saja biar fleksibel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classrooms');
        Schema::dropIfExists('classroom_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
    }
};
