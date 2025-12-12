@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

@section('content')
<div class="page-heading">
    <h3>Catat Transaksi Penjualan</h3>
</div>
<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <!-- Student Selection -->
                        <div class="form-group mb-4">
                            <label for="user_id" class="form-label fs-5">Pilih Siswa</label>
                            <select class="choices form-select" name="user_id" id="user_id" required>
                                <option value="">Cari Nama Siswa...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->email }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <!-- Item Selection Section -->
                        <h4 class="mb-3">Item Pembelian</h4>
                        <div class="row g-3 mb-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Pilih Item (Buku / Seragam)</label>
                                <select class="choices form-select" id="itemSelector">
                                    <option value="">Pilih Item...</option>
                                    <optgroup label="Buku Pelajaran">
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" data-type="book" data-price="{{ $book->price }}" data-limit="{{ $book->stock }}" {{ $book->stock <= 0 ? 'disabled' : '' }}>
                                                [Buku] {{ $book->title }} (Stok: {{ $book->stock }}) - Rp {{ number_format($book->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Seragam Sekolah">
                                        @foreach($uniforms as $uniform)
                                            <option value="{{ $uniform->id }}" data-type="uniform" data-price="{{ $uniform->price }}" data-limit="{{ $uniform->stock }}" {{ $uniform->stock <= 0 ? 'disabled' : '' }}>
                                                [Seragam] {{ $uniform->name }} - {{ $uniform->size }} (Stok: {{ $uniform->stock }}) - Rp {{ number_format($uniform->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" id="itemQty" value="1" min="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary w-100" id="addItemBtn">Tambah</button>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Item</th>
                                        <th width="150">Harga Satuan</th>
                                        <th width="100">Qty</th>
                                        <th width="200">Subtotal</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsBody">
                                    <!-- Items will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold fs-5">
                                        <td colspan="3" class="text-end">Total Bayar:</td>
                                        <td colspan="2" id="grandTotal">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Hidden Inputs Container -->
                         <div id="hiddenInputs"></div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('orders.index') }}" class="btn btn-light me-2">Batal</a>
                            <button type="submit" class="btn btn-success px-4" id="submitBtn" disabled>Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Choices
        const userSelect = new Choices('#user_id');
        const itemSelect = new Choices('#itemSelector');

        const addItemBtn = document.getElementById('addItemBtn');
        const itemsBody = document.getElementById('orderItemsBody');
        const grandTotalEl = document.getElementById('grandTotal');
        const hiddenInputs = document.getElementById('hiddenInputs');
        const submitBtn = document.getElementById('submitBtn');

        let cart = [];

        addItemBtn.addEventListener('click', function() {
            // Get selected option directly from DOM element since Choices hides it
            const selectEl = document.getElementById('itemSelector');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            
            if (!selectedOption || !selectedOption.value) {
                alert('Pilih item terlebih dahulu');
                return;
            }

            const id = selectedOption.value;
            const type = selectedOption.dataset.type;
            const price = parseFloat(selectedOption.dataset.price);
            const stock = parseInt(selectedOption.dataset.limit);
            const text = selectedOption.text;
            const qty = parseInt(document.getElementById('itemQty').value);

            if (qty <= 0) {
                alert('Jumlah harus minimal 1');
                return;
            }

            if (qty > stock) {
                alert('Stok tidak mencukupi! Sisa stok: ' + stock);
                return;
            }

            // Check if item exists in cart
            const existingItemIndex = cart.findIndex(i => i.id === id && i.type === type);
            if (existingItemIndex > -1) {
                if (cart[existingItemIndex].qty + qty > stock) {
                     alert('Total jumlah melebihi stok tersedia!');
                     return;
                }
                cart[existingItemIndex].qty += qty;
            } else {
                cart.push({ id, type, price, text, qty });
            }

            renderCart();
            // Reset fields
            document.getElementById('itemQty').value = 1;
        });

        window.removeItem = function(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            itemsBody.innerHTML = '';
            hiddenInputs.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                total += subtotal;

                // Table Row
                const row = `
                    <tr>
                        <td>${item.text}</td>
                        <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                        <td>${item.qty}</td>
                        <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
                itemsBody.insertAdjacentHTML('beforeend', row);

                // Hidden Inputs
                const inputs = `
                    <input type="hidden" name="items[${index}][type]" value="${item.type}">
                    <input type="hidden" name="items[${index}][id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                `;
                hiddenInputs.insertAdjacentHTML('beforeend', inputs);
            });

            grandTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            submitBtn.disabled = cart.length === 0;
        }
    });
</script>
@endpush
@endsection
