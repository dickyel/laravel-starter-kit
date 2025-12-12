<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CandidatesExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return User::whereNotNull('recruitment_status')->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Username',
            'No. Telepon',
            'Alamat',
            'Jarak (km)',
            'Status Pendaftaran',
            'Latitude',
            'Longitude',
            'Tanggal Daftar',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->username,
            $user->phone_number,
            $user->address,
            $user->distance_to_school,
            $user->recruitment_status,
            $user->latitude,
            $user->longitude,
            $user->created_at->format('d-m-Y H:i'),
        ];
    }
}
