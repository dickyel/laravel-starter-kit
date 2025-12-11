<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Attendance::with('user');

        // Apply filters (sama logic dengan controller)
        if ($this->request->month) {
            $query->whereMonth('date', date('m', strtotime($this->request->month)))
                  ->whereYear('date', date('Y', strtotime($this->request->month)));
        }

        if ($this->request->date_from) {
            $query->whereDate('date', '>=', $this->request->date_from);
        }

        if ($this->request->date_to) {
            $query->whereDate('date', '<=', $this->request->date_to);
        }

        if ($this->request->role) {
            $query->whereHas('user.roles', function($q) {
                $q->where('slug', $this->request->role);
            });
        }

        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->check_in_status) {
            $query->where('check_in_status', $this->request->check_in_status);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama User',
            'Role',
            'Jam Masuk',
            'Status Masuk',
            'Telat (Menit)',
            'Jam Pulang',
            'Status Pulang',
            'Lembur (Menit)',
            'Status Kehadiran',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->user->name ?? '-',
            $attendance->user->roles->first()->name ?? '-',
            $attendance->check_in_time ?? '-',
            $attendance->check_in_status ?? '-',
            $attendance->late_minutes ?? '0',
            $attendance->check_out_time ?? '-',
            $attendance->check_out_status ?? '-',
            $attendance->overtime_minutes ?? '0',
            $attendance->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
