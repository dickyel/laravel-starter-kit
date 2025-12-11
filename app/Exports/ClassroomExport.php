<?php

namespace App\Exports;

use App\Models\Classroom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassroomExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Classroom::with(['homeroomTeacher'])->withCount('students')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'Wali Kelas',
            'Kapasitas',
            'Jumlah Siswa',
            'Sisa Kursi',
            'Ukuran Grid',
        ];
    }

    public function map($classroom): array
    {
        return [
            $classroom->name,
            $classroom->homeroomTeacher->name ?? '-',
            $classroom->max_students,
            $classroom->students_count,
            $classroom->max_students - $classroom->students_count,
            $classroom->grid_rows . ' x ' . $classroom->grid_columns,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
