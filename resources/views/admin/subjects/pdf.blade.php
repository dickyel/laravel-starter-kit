<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mata Pelajaran</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; color: #666; font-size: 12px; margin-top: 0; }
        .text-center { text-align: center; }
        .badge { padding: 2px 4px; background: #e9ecef; border-radius: 3px; font-size: 10px; margin-right: 2px; display: inline-block; margin-bottom: 2px; }
    </style>
</head>
<body>
    <h2>Laporan Data Mata Pelajaran</h2>
    <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Mata Pelajaran</th>
                <th>Guru Pengampu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>
                        @if($subject->teachers->count() > 0)
                            @foreach($subject->teachers as $teacher)
                                <span class="badge">{{ $teacher->name }}</span>
                            @endforeach
                        @else
                            <span style="color:#aaa; font-style:italic;">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
