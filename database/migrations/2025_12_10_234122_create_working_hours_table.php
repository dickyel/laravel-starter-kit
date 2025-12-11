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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default'); // e.g., "Shift Pagi", "Shift Siang"
            $table->time('check_in_start'); // e.g., 07:00 (earliest allowed check-in)
            $table->time('check_in_end'); // e.g., 08:00 (on-time check-in deadline)
            $table->time('check_in_late_tolerance'); // e.g., 08:15 (after this = late)
            $table->time('check_out_start'); // e.g., 17:00 (official check-out time)
            $table->time('check_out_end'); // e.g., 18:00 (latest expected check-out)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
