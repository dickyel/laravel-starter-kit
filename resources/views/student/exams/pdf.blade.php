<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ujian Saya</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 12px; margin-top: 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 5px; border-radius: 4px; font-size: 10px; color: #fff; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <h2>Laporan Ujian Saya</h2>
    <p>Nama: {{ Auth::user()->name }} ({{ Auth::user()->username }})</p>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Mata Pelajaran</th>
                <th>Judul Ujian</th>
                <th>Tipe</th>
                <th>Waktu & Durasi</th>
                <th>Status</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
                @php
                    $attempt = $exam->attempts->where('user_id', Auth::id())->first();
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $exam->subject->name ?? '-' }}</td>
                    <td>{{ $exam->title }}</td>
                    <td class="text-center">{{ ucfirst($exam->type) }}</td>
                    <td>
                        {{ $exam->start_time->format('d/m/y H:i') }}<br>
                        <small>{{ $exam->duration_minutes }} Menit</small>
                    </td>
                    <td class="text-center">
                        @if($attempt)
                            @if($attempt->status == 'submitted')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-warning">Proses</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Belum</span>
                        @endif
                    </td>
                    <td class="text-center text-bold">
                        {{ $attempt && $attempt->status == 'submitted' ? $attempt->score : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
