@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Pembelian Kebutuhan Sekolah</h3>
    <p class="text-subtitle text-muted">Beli buku pelajaran dan seragam sekolah dengan mudah.</p>
</div>
<div class="page-content">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('student.purchase.store') }}" method="POST" id="purchaseForm">
        @csrf
        <section class="row">
            <!-- Books Section -->
             <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-white mb-0"><i class="bi bi-book-half me-2"></i> Buku Pelajaran</h4>
                    </div>
                    <div class="card-body p-4">
                         <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            @foreach($books as $book)
                                <div class="col">
                                    <div class="card h-100 shadow-sm border item-card">
                                        <div class="position-relative">
                                            @if($book->image)
                                                <img src="{{ asset('storage/' . $book->image) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center text-secondary" style="height: 200px;">
                                                    <i class="bi bi-book fs-1"></i>
                                                </div>
                                            @endif
                                            <span class="position-absolute top-0 end-0 badge bg-info m-2">Stok: {{ $book->stock }}</span>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">{{ $book->title }}</h5>
                                            <p class="card-text text-muted small mb-1">{{ $book->author }}</p>
                                            <h6 class="text-primary mt-auto">Rp {{ number_format($book->price, 0, ',', '.') }}</h6>
                                            
                                            <div class="mt-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Qty</span>
                                                    <input type="number" class="form-control quantity-input" min="0" max="{{ $book->stock }}" value="0" data-id="{{ $book->id }}" data-type="book" data-price="{{ $book->price }}" data-name="{{ $book->title }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if($books->isEmpty())
                                <div class="col-12 text-center text-muted py-4">Tidak ada buku tersedia saat ini.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uniforms Section -->
             <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="text-white mb-0"><i class="bi bi-person-badge me-2"></i> Seragam Sekolah</h4>
                    </div>
                    <div class="card-body p-4">
                         <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                            @foreach($uniforms as $uniform)
                                <div class="col">
                                    <div class="card h-100 shadow-sm border item-card">
                                        <div class="position-relative">
                                            @if($uniform->image)
                                                <img src="{{ asset('storage/' . $uniform->image) }}" class="card-img-top" alt="{{ $uniform->name }}" style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center text-secondary" style="height: 200px;">
                                                    <i class="bi bi-person fs-1"></i>
                                                </div>
                                            @endif
                                            <span class="position-absolute top-0 end-0 badge bg-secondary m-2">Stok: {{ $uniform->stock }}</span>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">{{ $uniform->name }}</h5>
                                            <p class="card-text text-muted small mb-1">Ukuran: <strong>{{ $uniform->size }}</strong></p>
                                            <h6 class="text-success mt-auto">Rp {{ number_format($uniform->price, 0, ',', '.') }}</h6>
                                            
                                            <div class="mt-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Qty</span>
                                                    <input type="number" class="form-control quantity-input" min="0" max="{{ $uniform->stock }}" value="0" data-id="{{ $uniform->id }}" data-type="uniform" data-price="{{ $uniform->price }}" data-name="{{ $uniform->name }} ({{ $uniform->size }})">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                             @if($uniforms->isEmpty())
                                <div class="col-12 text-center text-muted py-4">Tidak ada seragam tersedia saat ini.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Cart Fixed Bottom -->
            <div class="fixed-bottom bg-white shadow-lg border-top p-3" style="z-index: 1030;" id="cartSummary" hidden>
                <div class="container d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Total: <span class="text-primary fw-bold" id="totalPrice">Rp 0</span></h5>
                        <small class="text-muted" id="totalItems">0 Item dipilih</small>
                    </div>
                    <button type="button" class="btn btn-primary px-5 rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmPurchaseModal">
                        Checkout <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Checkout Modal -->
        <div class="modal fade" id="confirmPurchaseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Pembelian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membeli item berikut?</p>
                        <ul class="list-group list-group-flush mb-3" id="purchaseList">
                            <!-- JS will populate this -->
                        </ul>
                         <div class="d-flex justify-content-between fw-bold fs-5 mt-3 border-top pt-2">
                             <span>Total Bayar:</span>
                             <span class="text-primary" id="modalTotal">Rp 0</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Beli Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hidden Inputs Container -->
        <div id="hiddenInputs"></div>

    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const cartSummary = document.getElementById('cartSummary');
        const totalPriceEl = document.getElementById('totalPrice');
        const totalItemsEl = document.getElementById('totalItems');
        const purchaseListEl = document.getElementById('purchaseList');
        const modalTotalEl = document.getElementById('modalTotal');
        const hiddenInputsContainer = document.getElementById('hiddenInputs');
        const form = document.getElementById('purchaseForm');

        function updateSummary() {
            let total = 0;
            let itemCount = 0;
            let items = [];

            hiddenInputsContainer.innerHTML = '';
            purchaseListEl.innerHTML = '';

            quantityInputs.forEach((input, index) => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    const price = parseFloat(input.dataset.price);
                    const name = input.dataset.name;
                    const id = input.dataset.id;
                    const type = input.dataset.type;
                    
                    total += price * qty;
                    itemCount += qty;

                    items.push({ name, qty, price: price * qty });

                    // Create hidden inputs for form submission
                    // items[index][type]
                    // items[index][id]
                    // items[index][quantity]
                    
                    // Since we just need a flat array of items mainly, let's use a counter
                    const inputIdx = hiddenInputsContainer.children.length / 3; 
                    
                    hiddenInputsContainer.insertAdjacentHTML('beforeend', `
                        <input type="hidden" name="items[${itemCount}][type]" value="${type}">
                        <input type="hidden" name="items[${itemCount}][id]" value="${id}">
                        <input type="hidden" name="items[${itemCount}][quantity]" value="${qty}">
                    `);
                    
                     purchaseListEl.insertAdjacentHTML('beforeend', `
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                ${name} <span class="badge bg-secondary rounded-pill ms-2">x${qty}</span>
                            </div>
                            <span>Rp ${(price * qty).toLocaleString('id-ID')}</span>
                         </li>
                     `);
                }
            });

            totalPriceEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            modalTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            totalItemsEl.textContent = itemCount + ' Item dipilih';

            if (itemCount > 0) {
                cartSummary.hidden = false;
                // Add padding to body so fixed bottom doesn't cover content
                document.body.style.paddingBottom = '80px';
            } else {
                cartSummary.hidden = true;
                document.body.style.paddingBottom = '0';
            }
        }

        quantityInputs.forEach(input => {
            input.addEventListener('input', updateSummary);
            input.addEventListener('change', updateSummary);
        });
        
        // Prevent form submission if no items (though hidden button handles this visually)
        form.addEventListener('submit', function(e) {
             const hasItems = document.querySelectorAll('#hiddenInputs input').length > 0;
             if (!hasItems) {
                 e.preventDefault();
                 alert('Pilih minimal satu item untuk dibeli.');
             }
        });
    });
</script>
@endpush
@endsection
