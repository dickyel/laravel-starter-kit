<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::whereHas('roles', function($q){
            $q->where('slug', 'teacher')->orWhere('slug', 'guru');
        })->with('teacherSubjects')->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Username',
            'Email',
            'Mata Pelajaran',
        ];
    }

    public function map($teacher): array
    {
        return [
            $teacher->name,
            $teacher->username,
            $teacher->email,
            $teacher->teacherSubjects->pluck('name')->implode(', '),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
