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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id_number')->nullable()->unique()->after('id'); // ID User (Nomor Induk/Karyawan)
            $table->text('address')->nullable()->after('email');
            $table->string('phone_number')->nullable()->after('address');
            $table->string('signature_photo_path')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_id_number', 'address', 'phone_number', 'signature_photo_path']);
        });
    }
};
