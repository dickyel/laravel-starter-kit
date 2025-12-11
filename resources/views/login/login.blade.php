<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management System</title>
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
            padding: 1.5rem;
        }

        .login-container {
            background: #5F9C9C;
            border: 15px solid #5F9C9C;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
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

        .social-links {
            font-size: 0.9rem;
            color: #2C3E50;
        }

        .social-links a {
            color: #2C3E50;
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #5F9C9C;
        }

        /* Right Side - Form */
        .right-section {
            background: #FAFAFA;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2C1A5B;
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-form {
            background: white;
            border: 3px solid #2C1A5B;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #2C1A5B;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #2C1A5B;
            box-shadow: 0 0 0 3px rgba(44, 26, 91, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-register {
            background: white;
            color: #2C1A5B;
            border: 2px solid #2C1A5B;
        }

        .btn-register:hover {
            background: #2C1A5B;
            color: white;
        }

        .btn-login {
            background: #FFD700;
            color: #2C1A5B;
            border: 2px solid #FFD700;
        }

        .btn-login:hover {
            background: #FFC700;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .forgot-password a {
            color: #2C1A5B;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #E0E0E0;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .btn-google {
            width: 100%;
            background: #4285F4;
            color: white;
            padding: 0.8rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-google:hover {
            background: #357AE8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(66, 133, 244, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .left-section {
                display: none;
            }

            .right-section {
                padding: 2rem;
            }

            .main-heading {
                font-size: 2rem;
            }

            .form-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .login-container {
                border-width: 10px;
            }

            .right-section {
                padding: 1.5rem;
            }

            .login-form {
                padding: 1.5rem;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .form-title {
                font-size: 1.8rem;
            }
        }

        @media (min-width: 969px) and (max-width: 1200px) {
            .main-heading {
                font-size: 2.5rem;
            }

            .left-section {
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
                <p class="tagline">School Management System</p>
                <h1 class="main-heading">We make it easy research</h1>
            </div>
            
            <div class="illustration">
                <img src="{{ asset('assets/images/login_illustration.png') }}" alt="Research Illustration">
            </div>
            
            <!-- <div class="social-links">
                <a href="#" target="_blank">Facebook</a> | <a href="#" target="_blank">Twitter</a>
            </div> -->
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2 class="form-title">Sign in</h2>
            
            @error('login')
                <div class="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror

            <div class="login-form">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="login" 
                            class="form-control @error('login') is-invalid @enderror" 
                            value="{{ old('login') }}" 
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="button-group">
                        <!-- <button type="button" class="btn btn-register" onclick="window.location.href='#'">Register</button> -->
                        <button type="submit" class="btn btn-login">Login</button>
                    </div>

                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                </form>

                <!-- <div class="divider">or</div> -->

                <!-- <button type="button" class="btn-google" onclick="alert('Google Sign-in coming soon!')">
                    <i class="fab fa-google"></i>
                    Google Sign in
                </button> -->
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
