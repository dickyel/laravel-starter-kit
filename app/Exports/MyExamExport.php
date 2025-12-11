<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MyExamExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $exams;

    public function __construct($exams)
    {
        $this->exams = $exams;
    }

    public function collection()
    {
        return $this->exams;
    }

    public function headings(): array
    {
        return [
            'Judul Ujian',
            'Mata Pelajaran',
            'Tipe',
            'Jadwal Mulai',
            'Jadwal Selesai',
            'Durasi (Menit)',
            'Nilai Anda',
            'Status Pengerjaan',
        ];
    }

    public function map($exam): array
    {
        // Cari attempt user ini
        $attempt = $exam->attempts->where('user_id', auth()->id())->first();
        $score = $attempt ? $attempt->score : '-';
        $status = $attempt ? ($attempt->status == 'submitted' ? 'Selesai' : 'Sedang Mengerjakan') : 'Belum Mengerjakan';

        return [
            $exam->title,
            $exam->subject->name ?? '-',
            ucfirst($exam->type),
            $exam->start_time->format('d/m/Y H:i'),
            $exam->end_time->format('d/m/Y H:i'),
            $exam->duration_minutes,
            $score,
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
