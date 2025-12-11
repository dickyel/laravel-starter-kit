@extends('layouts.app')

@section('content')
    <div class="page-heading">
        <h3>Kelola Face Recognition</h3>
        <p class="text-muted">Proses foto user untuk menghasilkan face descriptor agar dapat dikenali di Kiosk Absensi</p>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4>Foto User yang Belum Diproses</h4>
                <button id="process-all-btn" class="btn btn-primary">
                    <i class="bi bi-lightning"></i> Proses Semua Foto
                </button>
            </div>
            <div class="card-body">
                <div id="status-message" class="alert alert-info" style="display: none;"></div>
                
                <div id="photo-grid" class="row g-3">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat foto...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Semua User & Foto Mereka</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Jumlah Foto</th>
                                <th>Foto dengan Descriptor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->photos->count() }}</td>
                                <td>{{ $user->photos->whereNotNull('face_descriptor')->count() }}</td>
                                <td>
                                    @if($user->photos->count() > 0)
                                        @if($user->photos->whereNotNull('face_descriptor')->count() > 0)
                                            <span class="badge bg-success">Terdaftar</span>
                                        @else
                                            <span class="badge bg-warning">Perlu Diproses</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Tidak ada foto</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden canvas for face processing -->
    <canvas id="hidden-canvas" style="display: none;"></canvas>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<script>
    let modelsLoaded = false;
    const statusMessage = document.getElementById('status-message');
    const photoGrid = document.getElementById('photo-grid');
    const processAllBtn = document.getElementById('process-all-btn');

    // Load face-api models
    async function loadModels() {
        statusMessage.style.display = 'block';
        statusMessage.className = 'alert alert-info';
        statusMessage.innerText = 'Memuat AI Models...';

        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
                faceapi.nets.faceLandmark68Net.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
                faceapi.nets.faceRecognitionNet.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights')
            ]);
            
            modelsLoaded = true;
            statusMessage.className = 'alert alert-success';
            statusMessage.innerText = 'AI Models berhasil dimuat! Siap memproses foto.';
            loadUnprocessedPhotos();
        } catch (error) {
            console.error(error);
            statusMessage.className = 'alert alert-danger';
            statusMessage.innerText = 'Gagal memuat AI Models. Periksa koneksi internet.';
        }
    }

    // Load photos that need processing
    async function loadUnprocessedPhotos() {
        try {
            const response = await fetch('/api/face-management/photos-needing-descriptors', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const photos = await response.json();
            
            if (photos.length === 0) {
                photoGrid.innerHTML = '<div class="col-12"><p class="text-center text-muted">Semua foto sudah diproses!</p></div>';
                processAllBtn.disabled = true;
                return;
            }

            photoGrid.innerHTML = '';
            photos.forEach(photo => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="card" data-photo-id="${photo.id}">
                        <img src="${photo.photo_url}" class="card-img-top" alt="${photo.user_name}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">${photo.user_name}</h6>
                            <button class="btn btn-sm btn-primary process-single-btn" data-photo-id="${photo.id}" data-photo-url="${photo.photo_url}">
                                <i class="bi bi-cpu"></i> Proses
                            </button>
                            <div class="photo-status text-muted small mt-2">Belum diproses</div>
                        </div>
                    </div>
                `;
                photoGrid.appendChild(col);
            });

            // Add event listeners to individual process buttons
            document.querySelectorAll('.process-single-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const photoId = this.getAttribute('data-photo-id');
                    const photoUrl = this.getAttribute('data-photo-url');
                    processSinglePhoto(photoId, photoUrl, this);
                });
            });
        } catch (error) {
            console.error(error);
            photoGrid.innerHTML = '<div class="col-12"><p class="text-center text-danger">Gagal memuat foto</p></div>';
        }
    }

    // Process a single photo
    async function processSinglePhoto(photoId, photoUrl, btnElement) {
        if (!modelsLoaded) {
            alert('AI Models belum dimuat penuh!');
            return;
        }

        const card = btnElement.closest('.card');
        const statusDiv = card.querySelector('.photo-status');
        
        btnElement.disabled = true;
        statusDiv.innerHTML = '<span class="text-warning">Memproses...</span>';

        try {
            const img = await faceapi.fetchImage(photoUrl);
            const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                statusDiv.innerHTML = '<span class="text-danger">Wajah tidak terdeteksi</span>';
                btnElement.disabled = false;
                return;
            }

            const descriptor = Array.from(detection.descriptor);
            
            // Save to database
            const response = await fetch('/api/face-management/store-descriptor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    photo_id: photoId,
                    face_descriptor: descriptor
                })
            });

            const result = await response.json();
            
            if (result.success) {
                statusDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Berhasil!</span>';
                btnElement.remove();
                setTimeout(() => {
                    card.closest('.col-md-3').remove();
                    if (document.querySelectorAll('.process-single-btn').length === 0) {
                        photoGrid.innerHTML = '<div class="col-12"><p class="text-center text-muted">Semua foto sudah diproses!</p></div>';
                        processAllBtn.disabled = true;
                    }
                }, 1500);
            } else {
                statusDiv.innerHTML = '<span class="text-danger">Gagal menyimpan</span>';
                btnElement.disabled = false;
            }
        } catch (error) {
            console.error(error);
            statusDiv.innerHTML = '<span class="text-danger">Error: ' + error.message + '</span>';
            btnElement.disabled = false;
        }
    }

    // Process all photos
    processAllBtn.addEventListener('click', async function() {
        if (!modelsLoaded) {
            alert('AI Models belum dimuat penuh!');
            return;
        }

        const buttons = document.querySelectorAll('.process-single-btn');
        if (buttons.length === 0) {
            alert('Tidak ada foto yang perlu diproses!');
            return;
        }

        this.disabled = true;
        statusMessage.className = 'alert alert-info';
        statusMessage.innerText = `Memproses ${buttons.length} foto...`;

        for (const btn of buttons) {
            const photoId = btn.getAttribute('data-photo-id');
            const photoUrl = btn.getAttribute('data-photo-url');
            await processSinglePhoto(photoId, photoUrl, btn);
            await new Promise(resolve => setTimeout(resolve, 500)); // Small delay between photos
        }

        statusMessage.className = 'alert alert-success';
        statusMessage.innerText = 'Semua foto berhasil diproses!';
        this.disabled = true;
    });

    // Initialize
    loadModels();
</script>
@endpush
