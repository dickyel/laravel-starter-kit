<!DOCTYPE html>
<html>
<head>
    <title>Data Buku Mata Pelajaran</title>
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
    <h2>Data Buku Mata Pelajaran</h2>
    <table>
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->publisher }}</td>
                <td>Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                <td>{{ $book->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
