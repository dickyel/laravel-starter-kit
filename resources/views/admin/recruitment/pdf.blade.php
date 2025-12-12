<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekrutmen Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .badge { padding: 3px 5px; border-radius: 3px; font-size: 8pt; color: white; display: inline-block;}
        .bg-success { background-color: #198754; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black !important; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekrutmen Calon Siswa Baru</h2>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Alamat</th>
                <th>Jarak</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($candidates as $index => $candidate)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <b>{{ $candidate->name }}</b><br>
                    <small>{{ $candidate->email }}</small>
                </td>
                <td>{{ $candidate->username }}</td>
                <td>{{ Str::limit($candidate->address, 50) }}</td>
                <td>{{ number_format($candidate->distance_to_school, 2) }} km</td>
                <td>
                    @if(str_contains($candidate->recruitment_status, 'Tidak') || $candidate->recruitment_status == 'Gagal')
                        <span class="badge bg-danger">{{ $candidate->recruitment_status }}</span>
                    @elseif(str_contains($candidate->recruitment_status, 'Masih'))
                        <span class="badge bg-warning">{{ $candidate->recruitment_status }}</span>
                    @else
                        <span class="badge bg-success">{{ $candidate->recruitment_status }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
