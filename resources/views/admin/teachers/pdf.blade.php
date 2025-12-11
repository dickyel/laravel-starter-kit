<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 12px; margin-top: 0; }
        .text-center { text-align: center; }
        .badge { padding: 2px 4px; background: #e9ecef; border-radius: 3px; font-size: 10px; margin-right: 2px; }
    </style>
</head>
<body>
    <h2>Laporan Data Guru</h2>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $teacher)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->username }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td>
                        @foreach($teacher->teacherSubjects as $subject)
                            <span class="badge">{{ $subject->name }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
