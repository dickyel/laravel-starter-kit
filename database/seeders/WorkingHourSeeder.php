<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkingHour;

class WorkingHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkingHour::create([
            'name' => 'Jam Kerja Kantor (Default)',
            'check_in_start' => '07:00:00', // Bisa check-in mulai jam 7 pagi
            'check_in_end' => '08:00:00', // Batas waktu on-time = jam 8 pagi
            'check_in_late_tolerance' => '08:15:00', // Toleransi terlambat sampai jam 8:15
            'check_out_start' => '17:00:00', // Jam pulang resmi = jam 5 sore
            'check_out_end' => '18:00:00', // Batas check-out = jam 6 sore
            'is_active' => true,
        ]);
    }
}
