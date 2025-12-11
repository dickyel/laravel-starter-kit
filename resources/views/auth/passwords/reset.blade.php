<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - School Management System</title>
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copied from Login Page Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: #5F9C9C; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .login-container { background: #5F9C9C; border: 15px solid #5F9C9C; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; max-width: 1200px; width: 100%; display: grid; grid-template-columns: 1fr 1fr; min-height: 600px; }
        .left-section { background: linear-gradient(135deg, #FFF8DC 0%, #FFEFD5 100%); padding: 3rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        .tagline { font-family: 'Playfair Display', serif; font-style: italic; color: #2C3E50; font-size: 1.1rem; margin-bottom: 1rem; }
        .main-heading { font-size: 3rem; font-weight: 700; color: #2C1A5B; line-height: 1.2; margin-bottom: 2rem; }
        .illustration { flex: 1; display: flex; align-items: center; justify-content: center; margin: 2rem 0; }
        .illustration img { max-width: 100%; height: auto; object-fit: contain; }
        .right-section { background: #FAFAFA; padding: 3rem; display: flex; flex-direction: column; justify-content: center; }
        .form-title { font-size: 2.5rem; font-weight: 700; color: #2C1A5B; text-align: center; margin-bottom: 2rem; }
        .login-form { background: white; border: 3px solid #2C1A5B; border-radius: 15px; padding: 2rem; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 500; color: #2C1A5B; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.8rem 1rem; border: 2px solid #E0E0E0; border-radius: 8px; font-size: 1rem; transition: all 0.3s; background: white; }
        .form-control:focus { outline: none; border-color: #2C1A5B; box-shadow: 0 0 0 3px rgba(44, 26, 91, 0.1); }
        .btn { padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s; border: none; width: 100%; }
        .btn-login { background: #FFD700; color: #2C1A5B; border: 2px solid #FFD700; }
        .btn-login:hover { background: #FFC700; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3); }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-auth-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .input-wrapper { position: relative; }
        .password-toggle { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; }
        
        @media (max-width: 968px) { .login-container { grid-template-columns: 1fr; } .left-section { display: none; } .right-section { padding: 2rem; } }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <div>
                <p class="tagline">School Management System</p>
                <h1 class="main-heading">New Password</h1>
            </div>
            <div class="illustration">
                <img src="{{ asset('assets/images/login_illustration.png') }}" alt="Illustration">
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2 class="form-title">Set New Password</h2>
            
            @if ($errors->any())
                <div class="alert alert-auth-error">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="login-form">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="{{ $email ?? old('email') }}" 
                            required
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                required
                                autocomplete="new-password"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm">Confirm Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password-confirm" 
                                name="password_confirmation" 
                                class="form-control" 
                                required
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
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
