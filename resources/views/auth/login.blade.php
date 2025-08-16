@extends('layouts.app')

@section('title', 'Login - MeloTech')

@push('styles')
<style>
    /* Clean Professional Color Palette */
    :root {
        --primary-color: #2E8B57;
        --primary-light: #90EE90;
        --accent-color: #FF6B9D;
        --text-dark: #2C3E50;
        --text-light: #6C757D;
        --background-light: #F8F9FA;
        --border-color: #E9ECEF;
        --success-color: #28A745;
        --error-color: #DC3545;
        --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
        --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
    }

    /* Clean Hero Section */
    .login-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 4rem 0 2rem;
        text-align: center;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Simple Form Container */
    .login-form-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-medium);
        margin: 2rem auto;
        max-width: 500px;
        overflow: hidden;
    }

    .form-header {
        background: var(--primary-color);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .form-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        opacity: 0.9;
        margin: 0;
        font-size: 1rem;
    }

    .form-body {
        padding: 2.5rem;
    }

    /* Clean Form Styling */
    .form-section {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
    }

    .form-control {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
        width: 100%;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--error-color);
    }

    .invalid-feedback {
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Password Input Group */
    .input-group {
        position: relative;
    }

    .input-group-text {
        background: var(--background-light);
        border: 1px solid var(--border-color);
        border-left: none;
        color: var(--text-light);
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        transition: background-color 0.2s ease;
        padding: 0.75rem 1rem;
    }

    .input-group-text:hover {
        background: var(--border-color);
    }

    .input-group .form-control {
        border-right: none;
        border-radius: 8px 0 0 8px;
    }

    /* Clean Submit Button */
    .btn-login {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        width: 100%;
        cursor: pointer;
    }

    .btn-login:hover {
        background: #246B4A;
        transform: translateY(-1px);
        box-shadow: var(--shadow-light);
    }

    .btn-login:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Remember Me Checkbox */
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        color: var(--text-dark);
        font-size: 0.9rem;
    }

    /* Register Link */
    .register-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .register-link a {
        color: var(--primary-color);
        font-weight: 500;
        text-decoration: none;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: #F8D7DA;
        color: #721C24;
    }

    .alert-success {
        background: #D4EDDA;
        color: #155724;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        .login-hero {
            padding: 3rem 0 1rem;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }

        .form-header h2 {
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Clean Hero Section -->
<section class="login-hero">
    <div class="container">
        <h1 class="hero-title">Welcome Back</h1>
        <p class="hero-subtitle">Sign in to your MeloTech account and continue your smart farming journey</p>
    </div>
</section>

<!-- Login Form -->
<div class="container">
    <div class="login-form-container">
        <!-- Form Header -->
        <div class="form-header">
            <h2>Sign In</h2>
            <p>Access your account with your credentials</p>
        </div>

        <!-- Form Body -->
        <div class="form-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Email Section -->
                <div class="form-section">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter your email address">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Section -->
                <div class="form-section">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required
                               placeholder="Enter your password">
                        <button class="btn input-group-text" type="button" id="togglePassword">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me Section -->
                <div class="form-section">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-section">
                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>
                </div>

                <!-- Register Link -->
                <div class="register-link">
                    <p class="mb-0">
                        Don't have an account? 
                        <a href="{{ route('register') }}">Register here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const svg = this.querySelector('svg');
            if (svg) {
                svg.innerHTML = type === 'password' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
            }
        });
    }

    // Form submission handling
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
        });
    }
});
</script>
@endpush
