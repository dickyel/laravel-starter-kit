<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .details {
            margin-bottom: 20px;
            width: 100%;
        }
        .details td {
            vertical-align: top;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items th, table.items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.items th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p><strong>{{ $order->invoice_number }}</strong></p>
    </div>

    <table class="details">
        <tr>
            <td>
                <strong>Penerbit:</strong><br>
                Sekolah Kami<br>
                Jl. Pendidikan No. 1<br>
                Jakarta, Indonesia
            </td>
            <td style="text-align: right;">
                <strong>Kepada:</strong><br>
                {{ $order->user->name ?? 'N/A' }}<br>
                {{ $order->user->email ?? '' }}<br>
                Tanggal: {{ $order->created_at->format('d M Y') }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>Tipe</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $item->item->title ?? ($item->item->name ?? 'Item Removed') }}
                    @if(isset($item->item->size))
                        ({{ $item->item->size }})
                    @endif
                </td>
                <td>{{ $item->item_type == 'App\Models\Book' ? 'Buku' : 'Seragam' }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="total">Total Bayar</td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda.</p>
        <p><i>Dicetak otomatis oleh sistem.</i></p>
    </div>
</body>
</html>
