@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Registrasi Wajah</h3>
    <p class="text-subtitle text-muted">Daftarkan wajah Anda untuk keperluan absensi.</p>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div id="loading" class="alert alert-info">Memuat Model Wajah... Mohon Tunggu.</div>
                    
                    <div class="position-relative d-inline-block">
                        <video id="video" width="640" height="480" autoplay muted style="border-radius: 10px; border: 2px solid #ddd;"></video>
                        <canvas id="overlay" style="position: absolute; top: 0; left: 0;"></canvas>
                    </div>

                    <div class="mt-4">
                        <button id="btn-capture" class="btn btn-primary btn-lg" disabled>
                            <i class="bi bi-camera-fill"></i> Ambil Data Wajah
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Load Face API from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const btnCapture = document.getElementById('btn-capture');
    const loading = document.getElementById('loading');

    let isModelLoaded = false;

    // Load Models
    Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
        faceapi.nets.faceLandmark68Net.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
        faceapi.nets.faceRecognitionNet.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
        faceapi.nets.ssdMobilenetv1.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights') // Better accuracy
    ]).then(startVideo).catch(err => {
        console.error(err);
        loading.innerText = "Gagal memuat model. Periksa koneksi internet.";
        loading.classList.replace('alert-info', 'alert-danger');
    });

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: {} })
            .then(stream => {
                video.srcObject = stream;
                isModelLoaded = true;
                loading.style.display = 'none';
                btnCapture.disabled = false;
            })
            .catch(err => console.error("Camera Error:", err));
    }

    btnCapture.addEventListener('click', async () => {
        if (!isModelLoaded) return;

        btnCapture.disabled = true;
        btnCapture.innerText = "Processing...";

        // Detect Face
        // Use SSD MobileNet for better registration accuracy
        const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options()).withFaceLandmarks().withFaceDescriptor();

        if (detection) {
            // Visualize (Optional)
            const dims = faceapi.matchDimensions(overlay, video, true);
            const resized = faceapi.resizeResults(detection, dims);
            faceapi.draw.drawDetections(overlay, resized);

            // Send Descriptor to Backend
            // Descriptor is a Float32Array, convert to normal array
            const descriptorArray = Array.from(detection.descriptor);

            saveFaceAttributes(descriptorArray);
        } else {
            alert("Wajah tidak terdeteksi! Pastikan wajah terlihat jelas dan pencahayaan cukup.");
            btnCapture.disabled = false;
            btnCapture.innerText = "Ambil Data Wajah";
        }
    });

    function saveFaceAttributes(descriptor) {
        fetch('{{ route("face.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: '{{ Auth::id() }}',
                face_descriptor: descriptor
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Registrasi wajah berhasil!');
                window.location.href = '/dashboard';
            } else {
                alert('Gagal: ' + data.message);
                btnCapture.disabled = false;
                btnCapture.innerText = "Ambil Data Wajah";
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan server.');
            btnCapture.disabled = false;
            btnCapture.innerText = "Ambil Data Wajah";
        });
    }
</script>
@endpush
