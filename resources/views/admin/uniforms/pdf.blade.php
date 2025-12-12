<!DOCTYPE html>
<html>
<head>
    <title>Data Seragam Sekolah</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Data Seragam Sekolah</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Seragam</th>
                <th>Ukuran</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uniforms as $uniform)
            <tr>
                <td>{{ $uniform->name }}</td>
                <td>{{ $uniform->size }}</td>
                <td>Rp {{ number_format($uniform->price, 0, ',', '.') }}</td>
                <td>{{ $uniform->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
