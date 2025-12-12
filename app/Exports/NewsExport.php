<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return \App\Models\News::all();
    }

    public function headings(): array
    {
        return [
            'Judul Berita',
            'Konten',
            'Status Publikasi',
            'Tanggal Dibuat',
        ];
    }

    public function map($news): array
    {
        return [
            $news->title,
            strip_tags($news->content), // Remove HTML tags
            $news->is_published ? 'Published' : 'Draft',
            $news->created_at->format('d/m/Y H:i'),
        ];
    }
}
