<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Book::all(['title', 'author', 'publisher', 'price', 'stock']);
    }

    public function headings(): array
    {
        return [
            'Judul Buku',
            'Penulis',
            'Penerbit',
            'Harga',
            'Stok',
        ];
    }
}
