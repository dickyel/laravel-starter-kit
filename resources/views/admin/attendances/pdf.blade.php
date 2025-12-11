<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 12px; margin-top: 0; }
        .badge { padding: 3px 6px; border-radius: 4px; color: white; font-size: 10px; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <h2>Laporan Data Absensi</h2>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama User</th>
                <th>Role</th>
                <th>Masuk</th>
                <th>Status Check-in</th>
                <th>Pulang</th>
                <th>Status Check-out</th>
                <th>Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->user->roles->first()->name ?? '-' }}</td>
                    <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                    <td>
                        @if($attendance->check_in_status === 'on_time') <span class="badge bg-success">Tepat Waktu</span>
                        @elseif($attendance->check_in_status === 'late') <span class="badge bg-warning">Terlambat ({{ $attendance->late_minutes }}m)</span>
                        @else - @endif
                    </td>
                    <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                    <td>
                        @if($attendance->check_out_status === 'overtime') <span class="badge bg-success">Lembur ({{ $attendance->overtime_minutes }}m)</span>
                        @elseif($attendance->check_out_status === 'early_leave') <span class="badge bg-warning">Pulang Cepat</span>
                        @else - @endif
                    </td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
