@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Scan Absensi</h3>
    <p class="text-subtitle text-muted">Arahkan wajah ke kamera untuk absen.</p>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div id="status-message" class="alert alert-info">Memuat Sistem...</div>
                    
                    <div class="position-relative d-inline-block">
                        <video id="video" width="640" height="480" autoplay muted style="border-radius: 10px; border: 2px solid #ddd;"></video>
                        <canvas id="overlay" style="position: absolute; top: 0; left: 0;"></canvas>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">Sistem akan otomatis mengambil gambar saat wajah terdeteksi.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const statusMsg = document.getElementById('status-message');

    let isProcessing = false;

    // Load Models (Tiny Face Detector for speed in realtime scanning)
    Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
        faceapi.nets.faceLandmark68Net.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
        faceapi.nets.faceRecognitionNet.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights')
    ]).then(startVideo).catch(err => {
        statusMsg.innerText = "Gagal memuat model.";
        statusMsg.classList.replace('alert-info', 'alert-danger');
    });

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: {} })
            .then(stream => {
                video.srcObject = stream;
                statusMsg.innerText = "Silakan menghadap kamera...";
            })
            .catch(err => console.error(err));
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.width, height: video.height }
        faceapi.matchDimensions(overlay, displaySize)
        
        setInterval(async () => {
            if (isProcessing) return;

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors()
            const resizedDetections = faceapi.resizeResults(detections, displaySize)
            
            overlay.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
            faceapi.draw.drawDetections(overlay, resizedDetections)

            if (detections.length > 0) {
                // Take the first face found
                const descriptor = Array.from(detections[0].descriptor);
                
                // Capture Image
                const canvasCap = document.createElement('canvas');
                canvasCap.width = video.videoWidth;
                canvasCap.height = video.videoHeight;
                canvasCap.getContext('2d').drawImage(video, 0, 0);
                const imageBase64 = canvasCap.toDataURL('image/png');

                performCheckIn(descriptor, imageBase64);
            }
        }, 2000) // Check every 2 seconds to avoid spamming
    })

    function performCheckIn(descriptor, image) {
        isProcessing = true;
        statusMsg.innerText = "Mengidentifikasi...";
        statusMsg.classList.replace('alert-info', 'alert-warning');

        fetch('{{ route("attendance.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                face_descriptor: descriptor,
                image: image
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                statusMsg.innerText = "Berhasil: " + data.message + " (" + data.user + ")";
                statusMsg.classList.replace('alert-warning', 'alert-success');
                // Play sound or visual cue
                setTimeout(() => {
                    statusMsg.innerText = "Silakan menghadap kamera...";
                    statusMsg.classList.replace('alert-success', 'alert-info');
                    isProcessing = false;
                }, 3000);
            } else {
                statusMsg.innerText = "Gagal: " + data.message;
                statusMsg.classList.replace('alert-warning', 'alert-danger');
                setTimeout(() => {
                    statusMsg.innerText = "Silakan menghadap kamera...";
                    statusMsg.classList.replace('alert-danger', 'alert-info');
                    isProcessing = false;
                }, 3000);
            }
        })
        .catch(err => {
            console.error(err);
            isProcessing = false;
        });
    }
</script>
@endpush
