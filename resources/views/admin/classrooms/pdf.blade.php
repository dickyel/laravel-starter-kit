<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Kelas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 12px; margin-top: 0; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Data Kelas</h2>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Wali Kelas</th>
                <th>Kapasitas</th>
                <th>Jumlah Siswa</th>
                <th>Grid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classrooms as $classroom)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $classroom->name }}</td>
                    <td>{{ $classroom->homeroomTeacher->name ?? '-' }}</td>
                    <td class="text-center">{{ $classroom->max_students }}</td>
                    <td class="text-center">{{ $classroom->students_count }}</td>
                    <td class="text-center">{{ $classroom->grid_rows }} x {{ $classroom->grid_columns }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
