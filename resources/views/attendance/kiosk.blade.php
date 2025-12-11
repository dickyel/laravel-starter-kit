<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Absensi Wajah - School Management System</title>
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #435ebe 0%, #25396f 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .kiosk-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 1000px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .kiosk-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
            background: #fff;
        }

        .kiosk-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        #video-container {
            position: relative;
            width: 640px;
            max-width: 100%;
            height: 480px;
            max-height: 60vh;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: #000;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .status-badge {
            margin-top: 20px;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            width: 80%;
        }

        .status-idle {
            background: #e2e8f0;
            color: #475569;
        }

        .status-processing {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-success {
            background: #dcfce7;
            color: #166534;
            transform: scale(1.05);
        }

        .status-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .clock {
            font-size: 1.2rem;
            color: #64748b;
            margin-top: 10px;
        }
        
        /* Dark mode overrides if needed, but intended for bright kiosk */
    </style>
</head>

<body>
    <div class="kiosk-card">
        <div class="kiosk-header">
            <h2 class="mb-0 text-primary">Absensi Wajah</h2>
            <div id="clock" class="clock">Loading time...</div>
        </div>
        
        <div class="kiosk-body">
            <div id="video-container">
                <video id="video" autoplay muted playsinline></video>
                <canvas id="overlay"></canvas>
            </div>

            <div id="status-display" class="status-badge status-idle">
                Silakan menghadap kamera...
            </div>
            
            <div id="user-info" class="mt-3 text-center" style="display: none;">
                <h3 id="user-name" class="fw-bold text-dark"></h3>
                <p class="text-muted">Absen Tercatat</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <script>
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const statusDisplay = document.getElementById('status-display');
        const userInfo = document.getElementById('user-info');
        const userName = document.getElementById('user-name');
        const clockElement = document.getElementById('clock');

        let isProcessing = false;
        let lastProcessTime = 0; // Track waktu terakhir proses
        let COOLDOWN_PERIOD = 8000; // 8 detik cooldown setelah proses terakhir
        let detectionInterval;
        let consecutiveDetections = 0; // Hitung berapa kali wajah terdeteksi berturut-turut
        let REQUIRED_DETECTIONS = 2; // Butuh minimal 2 deteksi berturut sebelum proses

        // Clock
        setInterval(() => {
            const now = new Date();
            clockElement.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('id-ID');
        }, 1000);

        // Audio Player - Gunakan audio file jika tersedia, fallback ke TTS
        let audioPlayer = null;
        const audioFiles = {
            'success': '/assets/audio/absen-berhasil.mp3',
            'already': '/assets/audio/sudah-absen.mp3',
            'failed': '/assets/audio/wajah-tidak-dikenali.mp3'
        };

        function playAudio(type, userName = '') {
            // Stop audio yang sedang berjalan
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
            }

            // Coba gunakan audio file terlebih dahulu
            const audioFile = audioFiles[type];
            if (audioFile) {
                audioPlayer = new Audio(audioFile);
                audioPlayer.play().catch(err => {
                    console.log('Audio file tidak tersedia, gunakan TTS:', err);
                    // Fallback ke TTS jika audio file tidak ada
                    speakWithTTS(type, userName);
                });
            } else {
                // Fallback ke TTS
                speakWithTTS(type, userName);
            }
        }

        // TTS Helper - Improved dengan voice selection
        function speakWithTTS(type, userName = '') {
            window.speechSynthesis.cancel(); // Stop previous
            
            let text = '';
            if (type === 'success') {
                text = `Terima kasih ${userName}, Anda berhasil absen.`;
            } else if (type === 'already') {
                text = `${userName}, Anda sudah melakukan absen hari ini.`;
            } else if (type === 'failed') {
                text = `Maaf, wajah tidak dikenali.`;
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.9; // Sedikit lebih lambat
            utterance.pitch = 1.1; // Sedikit lebih tinggi
            utterance.volume = 1.0;

            // Coba cari voice Indonesia yang lebih natural
            const voices = window.speechSynthesis.getVoices();
            const indonesianVoice = voices.find(voice => 
                voice.lang.startsWith('id') || 
                voice.name.includes('Indonesia') ||
                voice.name.includes('Damayanti') || // Google Indonesian voice
                voice.name.includes('Andika')
            );
            
            if (indonesianVoice) {
                utterance.voice = indonesianVoice;
            }

            window.speechSynthesis.speak(utterance);
        }

        // Load voices (untuk TTS)
        window.speechSynthesis.onvoiceschanged = () => {
            const voices = window.speechSynthesis.getVoices();
            console.log('Available voices:', voices.map(v => v.name + ' (' + v.lang + ')'));
        };

        // Init
        async function run() {
            statusDisplay.innerText = "Memuat Model AI...";
            
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights')
                ]);

                startVideo();
            } catch (e) {
                console.error(e);
                statusDisplay.innerText = "Gagal memuat model. Periksa koneksi internet.";
                statusDisplay.className = "status-badge status-error";
            }
        }

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    video.srcObject = stream;
                    statusDisplay.innerText = "Siap. Silakan menghadap kamera.";
                    statusDisplay.className = "status-badge status-idle";
                })
                .catch(err => {
                    console.error(err);
                    statusDisplay.innerText = "Kamera tidak ditemukan.";
                    statusDisplay.className = "status-badge status-error";
                });
        }

        video.addEventListener('play', () => {
            const displaySize = { width: video.clientWidth, height: video.clientHeight };
            faceapi.matchDimensions(overlay, displaySize);

            detectionInterval = setInterval(async () => {
                // Cek apakah masih dalam cooldown period
                const now = Date.now();
                const timeSinceLastProcess = now - lastProcessTime;
                
                if (isProcessing || timeSinceLastProcess < COOLDOWN_PERIOD) {
                    // Masih cooldown, gambar overlay saja tapi jangan proses
                    const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
                    faceapi.draw.drawDetections(overlay, resizedDetections);
                    
                    // Reset consecutive detections jika dalam cooldown
                    consecutiveDetections = 0;
                    return;
                }

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                // Draw
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
                faceapi.draw.drawDetections(overlay, resizedDetections);

                if (detections.length > 0) {
                    // Wajah terdeteksi
                    const face = detections[0];
                    
                    if (face.detection.score > 0.6) { // Tingkatkan threshold sedikit
                        consecutiveDetections++;
                        
                        // Hanya proses jika sudah terdeteksi beberapa kali berturut-turut
                        // Ini mencegah orang yang cuma lewat dari di-proses
                        if (consecutiveDetections >= REQUIRED_DETECTIONS) {
                            const descriptor = Array.from(face.descriptor);
                            
                            // Pause detection
                            isProcessing = true;
                            lastProcessTime = Date.now(); // Update waktu terakhir proses
                            consecutiveDetections = 0; // Reset counter
                            
                            statusDisplay.innerText = "Mencocokkan Wajah...";
                            statusDisplay.className = "status-badge status-processing";
                            
                            // Capture Image for Evidence
                            const canvasCap = document.createElement('canvas');
                            canvasCap.width = video.videoWidth;
                            canvasCap.height = video.videoHeight;
                            canvasCap.getContext('2d').drawImage(video, 0, 0);
                            const imageBase64 = canvasCap.toDataURL('image/png');

                            checkIn(descriptor, imageBase64);
                        }
                    } else {
                        // Score terlalu rendah, reset counter
                        consecutiveDetections = 0;
                    }
                } else {
                    // Tidak ada wajah, reset counter
                    consecutiveDetections = 0;
                }
            }, 1000); // Scan every 1s
        });

        function checkIn(descriptor, imageBase64) {
            fetch('/api/attendance-kiosk/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    face_descriptor: descriptor,
                    image: imageBase64
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Success
                    statusDisplay.innerText = "Berhasil!";
                    statusDisplay.className = "status-badge status-success";
                    userName.innerText = data.user_name;
                    userInfo.style.display = 'block';
                    
                    // Audio feedback dalam Bahasa Indonesia - hanya jika BERHASIL
                    if (data.already_present) {
                        playAudio('already', data.user_name);
                    } else {
                        playAudio('success', data.user_name);
                    }

                    setTimeout(() => {
                        resetState();
                    }, 5000); // 5 detik tampilkan hasil
                } else {
                    // Failed - TIDAK ada suara untuk yang gagal/lewat
                    statusDisplay.innerText = "Wajah tidak dikenali";
                    statusDisplay.className = "status-badge status-error";
                    // speak("Maaf, wajah tidak dikenali. Silakan coba lagi."); // DIMATIKAN

                    setTimeout(() => {
                        resetState();
                    }, 3000);
                }
            })
            .catch(err => {
                console.error(err);
                statusDisplay.innerText = "Error Sistem.";
                statusDisplay.className = "status-badge status-error";
                setTimeout(() => {
                    resetState();
                }, 3000);
            });
        }

        function resetState() {
            userInfo.style.display = 'none';
            statusDisplay.innerText = "Silakan menghadap kamera...";
            statusDisplay.className = "status-badge status-idle";
            isProcessing = false;
            consecutiveDetections = 0;
        }

        run();
    </script>
</body>
</html>
