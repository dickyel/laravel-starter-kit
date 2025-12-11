<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Ujian</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 10px; margin-top: 0; }
        .text-center { text-align: center; }
        .badge { padding: 2px 4px; border-radius: 3px; font-size: 9px; }
        .bg-success { background: #d1e7dd; color: #0f5132; }
        .bg-danger { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <h2>Laporan Data Ujian</h2>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Judul Ujian</th>
                <th>Mapel</th>
                <th>Guru</th>
                <th>Kelas</th>
                <th>Tipe</th>
                <th>Jadwal</th>
                <th>Durasi</th>
                <th>Soal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $exam->title }}</td>
                    <td>{{ $exam->subject->name ?? '-' }}</td>
                    <td>{{ $exam->teacher->name ?? '-' }}</td>
                    <td>{{ $exam->classroom->name ?? 'Semua' }}</td>
                    <td>{{ ucfirst($exam->type) }}</td>
                    <td>
                        {{ $exam->start_time->format('d/m/y H:i') }}<br>
                        sd<br>
                        {{ $exam->end_time->format('d/m/y H:i') }}
                    </td>
                    <td class="text-center">{{ $exam->duration_minutes }}m</td>
                    <td class="text-center">{{ $exam->questions->count() }}</td>
                    <td class="text-center">
                        @if($exam->is_active) 
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Non-Aktif</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
