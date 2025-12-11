<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Lantai 1'); // Nama lantai/gedung
            $table->integer('floor_number')->default(1); // Nomor lantai
            $table->json('grid_data')->nullable(); // Data grid/denah dalam JSON
            $table->integer('width')->default(1200); // Lebar canvas dalam px
            $table->integer('height')->default(800); // Tinggi canvas dalam px
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tambah kolom position ke classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('school_layout_id')->nullable()->after('homeroom_teacher_id')->constrained()->onDelete('set null');
            $table->integer('position_x')->nullable()->after('school_layout_id'); // Posisi X di canvas
            $table->integer('position_y')->nullable()->after('position_x'); // Posisi Y di canvas
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['school_layout_id']);
            $table->dropColumn(['school_layout_id', 'position_x', 'position_y']);
        });
        
        Schema::dropIfExists('school_layouts');
    }
};
