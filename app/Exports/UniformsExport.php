<?php

namespace App\Exports;

use App\Models\Uniform;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UniformsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Uniform::all(['name', 'size', 'price', 'stock']);
    }

    public function headings(): array
    {
        return [
            'Nama Seragam',
            'Ukuran',
            'Harga',
            'Stok',
        ];
    }
}
