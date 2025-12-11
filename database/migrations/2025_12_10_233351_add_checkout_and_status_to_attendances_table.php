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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('check_in_status')->default('on_time')->after('status'); // on_time, late, very_late
            $table->string('check_out_status')->nullable()->after('check_in_status'); // on_time, early, overtime
            $table->integer('late_minutes')->default(0)->after('check_out_status');
            $table->integer('overtime_minutes')->default(0)->after('late_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in_status', 'check_out_status', 'late_minutes', 'overtime_minutes']);
        });
    }
};
