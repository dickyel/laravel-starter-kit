<?php

namespace App\Exports;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Exam::with(['subject', 'teacher', 'classroom', 'questions'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Judul Ujian',
            'Mata Pelajaran',
            'Guru Pembuat',
            'Kelas Target',
            'Tipe Ujian',
            'Waktu Mulai',
            'Durasi (Menit)',
            'Jumlah Soal',
            'Status',
        ];
    }

    public function map($exam): array
    {
        return [
            $exam->title,
            $exam->subject->name ?? '-',
            $exam->teacher->name ?? '-',
            $exam->classroom->name ?? 'Semua Kelas',
            ucfirst($exam->type),
            $exam->start_time->format('d/m/Y H:i'),
            $exam->duration_minutes,
            $exam->questions->count(),
            $exam->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
