<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Subject::with('teachers')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Mata Pelajaran',
            'Guru Pengampu',
        ];
    }

    public function map($subject): array
    {
        return [
            $subject->name,
            $subject->teachers->pluck('name')->implode(', '),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
