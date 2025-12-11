<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@1,400;1,500&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5F9C9C;      /* Login Background */
            --secondary: #FFF8DC;    /* Login Left Section */
            --dark-text: #2C1A5B;    /* Login Heading */
            --accent: #FFD700;       /* Login Button */
            --bg-light: #FAFAFA;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background-color: #fff;
            color: #333;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            background-color: var(--primary);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff !important;
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
        }
        .btn-nav-login {
            background-color: var(--accent);
            color: var(--dark-text);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid var(--accent);
        }
        .btn-nav-login:hover {
            background-color: #FFC700;
            color: var(--dark-text);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px 0;
            /* Gradient mirip Left Section Login Page */
            background: linear-gradient(135deg, #FFF8DC 0%, #FFEFD5 100%); 
            position: relative;
            overflow: hidden;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--dark-text);
            line-height: 1.2;
            margin-bottom: 20px;
            font-family: 'Poppins', sans-serif;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 30px;
            max-width: 550px;
        }
        .hero-img-container {
            position: relative;
            z-index: 2;
        }
        .hero-img-container img {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(44, 26, 91, 0.15);
            border: 10px solid rgba(255,255,255,0.5);
            transform: rotate(-2deg);
            transition: transform 0.5s ease;
        }
        .hero-img-container:hover img {
            transform: rotate(0deg) scale(1.02);
        }

        /* Floating Shapes (Decorations) */
        .shape {
            position: absolute;
            z-index: 1;
            opacity: 0.6;
        }
        .shape-circle { top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(95, 156, 156, 0.1); border-radius: 50%; }
        .shape-dots { bottom: 20px; left: 20px; width: 100px; opacity: 0.3; }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background-color: #fff;
        }
        .section-title {
            text-align: center;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 10px;
            font-size: 2.2rem;
        }
        .section-subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 60px;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            padding: 40px 30px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
            border-bottom: 5px solid transparent;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-bottom-color: var(--primary);
        }
        .feature-icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(95, 156, 156, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            color: var(--primary);
            font-size: 2rem;
            transition: all 0.3s;
        }
        .feature-card:hover .feature-icon-wrapper {
            background: var(--primary);
            color: #fff;
        }
        .feature-card h4 {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 15px;
        }

        /* Contact & Map Section */
        .contact-section {
            background-color: var(--primary);
            color: #fff;
            padding: 80px 0 40px 0;
            position: relative;
        }
        .contact-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 40px;
        }
        .contact-info-item {
            display: flex;
            margin-bottom: 25px;
            align-items: flex-start;
        }
        .contact-info-icon {
            width: 40px;
            height: 40px;
            background: var(--accent);
            color: var(--dark-text);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .map-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            height: 100%;
            min-height: 400px;
            border: 5px solid rgba(255,255,255,0.2);
        }
        iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Footer */
        footer {
            background-color: #4b8080; /* Darker Primary */
            padding: 20px 0;
            text-align: center;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i> EduSystem</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-nav-login">Dashboard <i class="fas fa-arrow-right ms-1"></i></a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-nav-login">Login Portal</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <!-- Decoration -->
        <div class="shape shape-circle"></div>

        <div class="container" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <p class="text-uppercase fw-bold text-primary mb-2" style="letter-spacing: 2px;">Welcome to Future Education</p>
                    <h1 class="hero-title">Sistem Sekolah <br>Terintegrasi & Modern</h1>
                    <p class="hero-subtitle">Kelola absensi wajah, akademik, ujian online, dan laporan sekolah dalam satu platform yang mudah digunakan.</p>
                    
                    <div class="d-flex gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-lg btn-nav-login px-4 py-3 shadow">Akses Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-lg btn-nav-login px-4 py-3 shadow">Mulai Sekarang</a>
                        @endauth
                        <a href="#features" class="btn btn-lg btn-outline-dark px-4 py-3 rounded-pill">Pelajari Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-img-container">
                        <!-- Hero Image from Login Page -->
                        <img src="{{ asset('assets/images/login_illustration.png') }}" alt="School Activity" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Fitur Unggulan</h2>
            <p class="section-subtitle">Teknologi terbaik untuk mendukung operasional sekolah Anda.</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-camera-retro"></i>
                        </div>
                        <h4>Face Attendance</h4>
                        <p class="text-muted">Absensi siswa dan guru menggunakan pengenalan wajah yang cepat dan akurat.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4>Manajemen Guru</h4>
                        <p class="text-muted">Kelola data guru, jadwal mengajar, dan mata pelajaran dalam satu dashboard.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h4>Ujian Online (CBT)</h4>
                        <p class="text-muted">Pelaksanaan ujian berbasis komputer dengan penilaian otomatis yang efisien.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h4>Laporan Real-time</h4>
                        <p class="text-muted">Export data absensi, nilai, dan aktivitas sekolah ke PDF & Excel kapan saja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Map Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="contact-card">
                        <h2 class="fw-bold mb-4">Hubungi Kami</h2>
                        <p class="mb-5 opacity-75">Kami siap membantu Anda. Kunjungi lokasi kami atau hubungi kontak di bawah ini.</p>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h5 class="mb-1">Alamat</h5>
                                <p class="mb-0 opacity-75">Jl. Jendral Sudirman No. Kav 1, Jakarta Pusat, DKI Jakarta</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="mb-0 opacity-75">info@sekolah-juara.sch.id</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h5 class="mb-1">Telepon</h5>
                                <p class="mb-0 opacity-75">(021) 123-4567</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="map-container">
                        <!-- Google Maps Embed -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126920.24706560413!2d106.76219277054664!3d-6.229746461962354!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta%2C%20Special%20Capital%20Region%20of%20Jakarta!5e0!3m2!1sen!2sid!4v1679034293847!5m2!1sen!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} School Management System. All Rights Reserved. Built with <i class="fas fa-heart text-danger"></i> by Admin.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
