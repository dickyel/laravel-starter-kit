@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Detail Transaksi</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Invoice: {{ $order->invoice_number }}</h4>
             <span class="badge {{ $order->status == 'completed' ? 'bg-success' : 'bg-warning' }} fs-5">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-muted">Informasi Siswa</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="100">Nama</td>
                            <td>: {{ $order->user->name ?? 'User Deleted' }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: {{ $order->user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ $order->created_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="mb-3">Item Pembelian</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="bg-light">
                            <th>No</th>
                            <th>Nama Item</th>
                            <th>Tipe</th>
                            <th>Harga Satuan</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $item->item->title ?? ($item->item->name ?? 'Item Unknown/Deleted') }}
                                @if(isset($item->item->size))
                                    ({{ $item->item->size }})
                                @endif
                            </td>
                            <td>
                                @if($item->item_type == 'App\Models\Book')
                                    <span class="badge bg-primary">Buku</span>
                                @else
                                    <span class="badge bg-success">Seragam</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold fs-5">
                            <td colspan="5" class="text-end">Total Bayar</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('orders.print', $order->id) }}" class="btn btn-primary" target="_blank"><i class="bi bi-printer"></i> Cetak Invoice</a>
            </div>
        </div>
    </div>
</div>
@endsection
