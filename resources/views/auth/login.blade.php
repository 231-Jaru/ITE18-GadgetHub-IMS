<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --light-bg: #f8fafc;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .login-header i {
            position: relative;
            z-index: 1;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .login-header h2 {
            position: relative;
            z-index: 1;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .login-header p {
            position: relative;
            z-index: 1;
            opacity: 0.9;
            margin: 0;
            font-size: 0.95rem;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-label i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .input-group .btn {
            border-left: none;
            border-color: #e5e7eb;
        }

        .input-group .form-control:focus + .btn {
            border-color: var(--primary-color);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .form-text {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .info-text {
            background: #f0f9ff;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1e40af;
        }

        .info-text i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #10b981;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-warehouse"></i>
                        <h2>Inventory System</h2>
                        <p>Admin Login</p>
                    </div>
                    <div class="login-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(isset($errors) && $errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" autocomplete="on" id="loginForm">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-user"></i>Username
                                </label>
                                <input type="text" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       maxlength="255"
                                       placeholder="Enter your username">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">
                                    Enter your admin username to access the system
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text">
                                    Enter your password to login
                                </small>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-login" id="loginButton">
                                    <span class="spinner-border spinner-border-sm d-none me-2" id="loginSpinner" role="status" aria-hidden="true"></span>
                                    <i class="fas fa-sign-in-alt me-2" id="loginIcon"></i>
                                    <span id="loginButtonText">Log In</span>
                                </button>
                            </div>
                        </form>

                        <div class="info-text">
                            <i class="fas fa-shield-alt"></i>
                            <strong>Admin Access Only</strong><br>
                            <small>This system is restricted to authorized administrators. Contact your system administrator for account access.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear any cached form data
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (toggleIcon) {
                        toggleIcon.classList.toggle('fa-eye');
                        toggleIcon.classList.toggle('fa-eye-slash');
                    }
                });
            }

            // Form submission handling
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const loginSpinner = document.getElementById('loginSpinner');
            const loginIcon = document.getElementById('loginIcon');
            const loginButtonText = document.getElementById('loginButtonText');

            function resetButtonState() {
                if (loginButton) {
                    loginButton.disabled = false;
                    if (loginSpinner) loginSpinner.classList.add('d-none');
                    if (loginIcon) loginIcon.classList.remove('d-none');
                    if (loginButtonText) loginButtonText.textContent = 'Log In';
                }
            }

            resetButtonState();

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    if (loginButton) {
                        loginButton.disabled = true;
                        if (loginSpinner) loginSpinner.classList.remove('d-none');
                        if (loginIcon) loginIcon.classList.add('d-none');
                        if (loginButtonText) loginButtonText.textContent = 'Logging in...';
                    }

                    setTimeout(function() {
                        resetButtonState();
                    }, 10000);
                });
            }

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
        
        // Prevent caching of the login page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
