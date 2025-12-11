<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_layouts', function (Blueprint $table) {
            $table->string('background_image')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('school_layouts', function (Blueprint $table) {
            $table->dropColumn('background_image');
        });
    }
};
