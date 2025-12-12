<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student - School Management System</title>
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #5F9C9C;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            background: #5F9C9C;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* Removed fixed height to prevent clipping */
            min-height: 600px; 
            height: auto;
        }

        /* Left Side - Illustration */
        .left-section {
            background: linear-gradient(135deg, #FFF8DC 0%, #FFEFD5 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .tagline {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            color: #2C3E50;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .main-heading {
            font-size: 3rem;
            font-weight: 700;
            color: #2C1A5B;
            line-height: 1.2;
            margin-bottom: 2rem;
        }

        .illustration {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2rem 0;
        }

        .illustration img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* Right Side - Form */
        .right-section {
            background: #FAFAFA;
            padding: 2rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2C1A5B;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-form {
            background: white;
            border: 3px solid #2C1A5B;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #2C1A5B;
            margin-bottom: 0.3rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #2C1A5B;
            box-shadow: 0 0 0 3px rgba(44, 26, 91, 0.1);
        }

        .btn-register {
            background: #FFD700;
            color: #2C1A5B;
            border: 2px solid #FFD700;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-register:hover {
            background: #FFC700;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-secondary-action {
            background: #e2e6ea;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            margin-top: 5px;
            display: inline-block;
        }
        .btn-secondary-action:hover {
            background: #dbe0e5;
        }

        .text-center {
            text-align: center;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            body {
                padding: 1rem;
                align-items: flex-start; /* Allow scroll on mobile */
            }
            .login-container {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
            }

            .left-section {
                display: none;
            }

            .right-section {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <div>
                <p class="tagline">Join Us Today</p>
                <h1 class="main-heading">Start Your Journey With Us</h1>
            </div>
            
            <div class="illustration">
                <img src="{{ asset('assets/images/login_illustration.png') }}" alt="Recruitment Illustration">
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2 class="form-title">Pendaftaran Siswa Baru</h2>
            
            @if(session('success'))
                <div class="alert" style="background: #d4edda; color: #155724; border-color: #c3e6cb;">
                     {{ session('success') }}
                </div>
            @endif

            <div class="login-form">
                <form action="{{ route('register-student.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row" style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group" style="flex: 1;">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat Lengkap</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Contoh: Jl. Jendral Sudirman No. 1, Jakarta" required>{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row" style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="latitude">Latitude</label>
                            <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-6.200000" required>
                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="longitude">Longitude</label>
                            <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="106.816666" required>
                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <small style="display:block; margin-bottom: 1rem; color: #666;">Hint: Gunakan Google Maps untuk mendapatkan koordinat lokasi (klik kanan pada peta > ada angka koordinat).</small>

                    <button type="submit" class="btn-register">Daftar Sekarang</button>
                    
                    <div class="text-center mt-3" style="margin-top: 1rem;">
                        <a href="{{ route('login') }}" style="color: #2C1A5B; text-decoration: none;">Sudah punya akun? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
