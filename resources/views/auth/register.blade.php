@extends('layouts.app')

@section('title', 'Create Account - MeloTech')

{{--
    Farm Registration Form
    - Simple and clean registration interface
    - Farm information collection
    - User-friendly design
    - Working password strength checker
--}}

@push('styles')
<style>
    /* Clean Professional Color Palette */
    :root {
        --primary-color: #2E8B57;
        --primary-dark: #1e5f3f;
        --primary-light: #90EE90;
        --accent-color: #FF6B9D;
        --text-dark: #2C3E50;
        --text-light: #6C757D;
        --background-light: #F8F9FA;
        --border-color: #E9ECEF;
        --success-color: #28A745;
        --error-color: #DC3545;
        --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.1);
        --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    /* Clean Hero Section */
    .register-hero {
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
    .register-form-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-medium);
        margin: 2rem auto;
        max-width: 800px;
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
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
    }

    .section-icon {
        width: 20px;
        height: 20px;
        margin-right: 0.75rem;
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
    }

    .form-label::after {
        content: ' *';
        color: var(--error-color);
        font-weight: 600;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
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

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 0.5rem;
    }

    .strength-bar {
        height: 4px;
        background: var(--border-color);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-fill.very-weak {
        width: 20%;
        background: #DC3545;
    }

    .strength-fill.weak {
        width: 40%;
        background: #FD7E14;
    }

    .strength-fill.fair {
        width: 60%;
        background: #FFC107;
    }

    .strength-fill.good {
        width: 80%;
        background: #20C997;
    }

    .strength-fill.strong {
        width: 100%;
        background: #28A745;
    }

    .strength-text {
        font-size: 0.8rem;
        color: var(--text-light);
        font-weight: 500;
    }

    .strength-text.very-weak {
        color: #DC3545;
    }

    .strength-text.weak {
        color: #FD7E14;
    }

    .strength-text.fair {
        color: #FFC107;
    }

    .strength-text.good {
        color: #20C997;
    }

    .strength-text.strong {
        color: #28A745;
    }

    /* Clean Submit Button */
    .btn-register {
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

    .btn-register:hover {
        background: #246B4A;
        transform: translateY(-1px);
        box-shadow: var(--shadow-light);
    }

    .btn-register:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-secondary {
        background: transparent;
        border: 1px solid var(--text-light);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-outline-secondary:hover {
        background: var(--text-light);
        color: white;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .login-link a {
        color: var(--primary-color);
        font-weight: 500;
        text-decoration: none;
    }

    .login-link a:hover {
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

    .text-success {
        color: var(--success-color) !important;
    }

    .text-danger {
        color: var(--error-color) !important;
    }

    /* Form Check Styling */
    .form-check {
        margin-bottom: 0.5rem;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        color: var(--text-dark);
        font-weight: 500;
        line-height: 1.5;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .form-body {
            padding: 1.5rem;
        }

        .register-hero {
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

        .section-title {
            font-size: 1.1rem;
        }
    }

    /* Terms and Conditions Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        overflow: hidden;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-dialog {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        margin: auto;
    }

    .modal-overlay.active .modal-dialog {
        transform: scale(1);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 2rem;
        font-weight: 300;
        color: var(--text-light);
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .modal-close-btn:hover {
        color: var(--text-dark);
    }

    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        line-height: 1.6;
    }

    .modal-body h6 {
        font-weight: 700;
        color: var(--primary-color);
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
    }

    #termsLink {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    #termsLink:hover {
        text-decoration: underline;
    }

    /* Enhanced Modal Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    @keyframes slideInUp {
        from {
            transform: translateY(30px) scale(0.95);
            opacity: 0;
        }

        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    @keyframes slideOutDown {
        from {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        to {
            transform: translateY(30px) scale(0.95);
            opacity: 0;
        }
    }

    .modal-overlay.active .modal-dialog {
        animation: slideInUp 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .modal-overlay:not(.active) .modal-dialog {
        animation: slideOutDown 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Enhanced Modal Content */
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-bottom: none;
    }

    .modal-title {
        color: white;
        font-weight: 700;
    }

    .modal-close-btn {
        color: white;
        opacity: 0.8;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        color: white;
        opacity: 1;
        transform: scale(1.1);
    }

    .modal-body {
        background: #fafafa;
        color: var(--text-dark);
        line-height: 1.7;
    }

    .modal-body h6 {
        color: var(--primary-color);
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
    }

    .modal-body p {
        margin-bottom: 1rem;
        color: var(--text-dark);
    }

    .modal-footer {
        background: white;
        border-top: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .modal-footer .btn-register {
        background: var(--primary-color);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .modal-footer .btn-register:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
    }

    /* Terms Accepted State */
    .terms-accepted .form-check-input:checked {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .terms-accepted .form-check-label {
        color: var(--success-color);
        font-weight: 600;
    }

    /* Responsive Modal */
    @media (max-width: 768px) {
        .modal-dialog {
            width: 95%;
            margin: 1rem;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .modal-header {
            padding: 1rem;
        }
        
        .modal-footer {
            padding: 1rem;
        }
    }

    /* Enhanced Modal Positioning and Scrolling */
    .modal-overlay.active {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active .modal-dialog {
        position: relative;
        top: auto;
        left: auto;
        transform: scale(1);
        max-height: 90vh;
        overflow-y: auto;
        margin: 20px;
    }

    /* Ensure modal content is always visible */
    .modal-content {
        position: relative;
        z-index: 1;
        max-height: 100%;
        overflow: hidden;
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
        height: 100%;
    }

    /* Additional responsive fixes */
    @media (max-height: 600px) {
        .modal-dialog {
            max-height: 95vh;
            margin: 10px;
        }
    }

    @media (max-width: 480px) {
        .modal-dialog {
            width: 98%;
            margin: 5px;
        }
        
        .modal-body {
            max-height: 60vh;
        }
    }

    /* Terms Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-dialog {
        width: 100%;
        max-width: 500px;
        margin: 1rem;
        transform: translateY(20px);
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0;
    }

    .modal-overlay.active .modal-dialog {
        transform: translateY(0);
        opacity: 1;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        max-height: 80vh;
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        position: relative;
    }

    .modal-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .modal-icon svg {
        width: 20px;
        height: 20px;
        color: white;
    }

    .modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.75rem;
        color: white;
        opacity: 0.8;
        cursor: pointer;
        padding: 0 0.5rem;
        margin-left: auto;
        line-height: 1;
        transition: opacity 0.2s ease;
    }

    .modal-close-btn:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }

    .terms-content {
        font-size: 0.9375rem;
        line-height: 1.6;
        color: var(--text-dark);
    }

    .terms-intro {
        margin-bottom: 1.5rem;
        color: var(--text-light);
        font-size: 0.9375rem;
    }

    .terms-section {
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }

    .terms-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .terms-section h6 {
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        font-size: 0.9375rem;
    }

    .term-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        font-size: 0.75rem;
        margin-right: 0.75rem;
        font-weight: 600;
    }

    .terms-section p {
        margin: 0 0 0 2rem;
        color: var(--text-light);
        font-size: 0.9375rem;
    }

    .modal-footer {
        padding: 1.25rem 1.5rem;
        background: var(--background-light);
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .btn-outline-secondary {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-light);
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn-outline-secondary:hover {
        background: #f1f3f5;
        border-color: #dee2e6;
    }

    #acceptTermsBtn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.5rem;
    }

    #acceptTermsBtn svg {
        width: 18px;
        height: 18px;
        transition: transform 0.2s ease;
    }

    #acceptTermsBtn:hover svg {
        transform: translateX(3px);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.5rem;
            max-height: 90vh;
        }
        
        .modal-content {
            max-height: 90vh;
        }
        
        .modal-header {
            padding: 1rem 1.25rem;
        }
        
        .modal-body {
            padding: 1.25rem;
        }
        
        .terms-section {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
        
        .terms-section h6 {
            font-size: 0.9375rem;
        }
        
        .terms-section p {
            font-size: 0.875rem;
            margin-left: 1.75rem;
        }
        
        .modal-footer {
            padding: 1rem;
            flex-direction: column;
        }
        
        .btn-outline-secondary,
        #acceptTermsBtn {
            width: 100%;
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Clean Hero Section -->
<section class="register-hero">
    <div class="container">
        <h1 class="hero-title">Create Your Account</h1>
        <p class="hero-subtitle">Join MeloTech and start your smart melon farming journey with AI-powered insights</p>
    </div>
</section>

<!-- Registration Form -->
<div class="container">
    <div class="register-form-container">
        <!-- Form Header -->
        <div class="form-header">
            <h2>Melon Farmer Registration</h2>
            <p>Complete your melon farming registration in a few simple steps</p>
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

            <form method="POST" action="{{ route('register') }}" id="registrationForm">
                @csrf

                <!-- Account Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Account Information
                    </h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" required
                                placeholder="Enter your full name">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required
                                placeholder="Enter your email address">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone') }}"
                                placeholder="+63 9XX XXX XXXX" required>
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required
                                placeholder="Enter your password">
                            <div class="password-strength" id="passwordStrength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill" style="width: 0%;"></div>
                                </div>
                                <div class="strength-text" id="strengthText">Enter a password</div>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-12">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" required
                                placeholder="Confirm your password">
                            @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Location
                    </h3>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="province" class="form-label">Province</label>
                            <select class="form-select" id="province" name="province_name">
                                <option value="">Loading provinces...</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="city_municipality" class="form-label">Municipality/City</label>
                            <select class="form-select" id="city_municipality" name="city_municipality_name">
                                <option value="">Select Province first</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select class="form-select" id="barangay" name="barangay_name">
                                <option value="">Select Municipality first</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Farm Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Melon Farm Information
                    </h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="farm_name" class="form-label">Farm Name</label>
                            <input type="text" class="form-control @error('farm_name') is-invalid @enderror"
                                id="farm_name" name="farm_name" value="{{ old('farm_name') }}"
                                placeholder="e.g., Green Valley Farm" required>
                            @error('farm_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="watermelon_variety" class="form-label">Melon Variety</label>
                            <select class="form-select @error('watermelon_variety') is-invalid @enderror"
                                id="watermelon_variety" name="watermelon_variety" required>
                                <option value="">Select variety</option>
                                <option value="Cantaloupe / Muskmelon" {{ old('watermelon_variety') == 'Cantaloupe / Muskmelon' ? 'selected' : '' }}>🍈 Cantaloupe / Muskmelon (Melon)</option>
                                <option value="Honeydew Melon" {{ old('watermelon_variety') == 'Honeydew Melon' ? 'selected' : '' }}>🍈 Honeydew Melon (Melon Verde)</option>
                                <option value="Watermelon" {{ old('watermelon_variety') == 'Watermelon' ? 'selected' : '' }}>🍉 Watermelon (Pakwan)</option>
                                <option value="Winter Melon" {{ old('watermelon_variety') == 'Winter Melon' ? 'selected' : '' }}>🥒 Winter Melon (Kundol)</option>
                                <option value="Bitter Melon" {{ old('watermelon_variety') == 'Bitter Melon' ? 'selected' : '' }}>🥒 Bitter Melon (Ampalaya)</option>
                                <option value="Snake Melon" {{ old('watermelon_variety') == 'Snake Melon' ? 'selected' : '' }}>🥒 Snake Melon (Kundol-haba)</option>
                            </select>
                            @error('watermelon_variety')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="planting_date" class="form-label">Planting Date</label>
                            <input type="date" class="form-control @error('planting_date') is-invalid @enderror"
                                id="planting_date" name="planting_date" value="{{ old('planting_date') }}" required>
                            @error('planting_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="land_size" class="form-label">Land Size</label>
                            <input type="number" class="form-control @error('land_size') is-invalid @enderror"
                                id="land_size" name="land_size" value="{{ old('land_size') }}"
                                step="0.1" min="0.1" required placeholder="0.0">
                            @error('land_size')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="land_size_unit" class="form-label">Unit</label>
                            <select class="form-select @error('land_size_unit') is-invalid @enderror"
                                id="land_size_unit" name="land_size_unit" required>
                                <option value="">Unit</option>
                                <option value="m2" {{ old('land_size_unit') == 'm2' ? 'selected' : '' }}>Square Meters (m²)</option>
                                <option value="ha" {{ old('land_size_unit') == 'ha' ? 'selected' : '' }}>Hectares (ha)</option>
                            </select>
                            @error('land_size_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="form-section">
                    <div class="form-check">
                        <input class="form-check-input @error('terms_accepted') is-invalid @enderror" type="checkbox" id="terms_accepted" name="terms_accepted" required>
                        <label class="form-check-label" for="terms_accepted">
                            I agree to the <a href="#" id="termsLink">Terms and Conditions</a>
                        </label>
                        @error('terms_accepted')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="form-section">
                    <button type="submit" class="btn btn-register" id="submitBtn">
                        Create Account
                    </button>
                </div>

                <div class="login-link">
                    <p class="mb-0">
                        Already have an account?
                        <a href="{{ route('login') }}">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal-overlay" id="termsModalOverlay">
    <div class="modal-dialog" id="termsModalDialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                </div>
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="modal-close-btn" id="modalCloseBtn" aria-label="Close">
                    &times;
                </button>
            </div>
            
            <div class="modal-body">
                <div class="terms-content">
                    <p class="terms-intro">By registering, you agree to our Terms and Conditions. Please read them carefully.</p>
                    
                    <div class="terms-section">
                        <h6><span class="term-number">1</span> Account Security</h6>
                        <p>You are responsible for keeping your account secure. Use a strong password and report any unauthorized access.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h6><span class="term-number">2</span> Acceptable Use</h6>
                        <p>Use our services only for authorized purposes. Misuse or harmful content is not allowed.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h6><span class="term-number">3</span> Privacy</h6>
                        <p>We respect your data and handle it according to our privacy policy.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h6><span class="term-number">4</span> Your Content</h6>
                        <p>You own your content. MeloTech does not claim your rights.</p>
                    </div>
                    
                    <div class="terms-section">
                        <h6><span class="term-number">5</span> Service Availability</h6>
                        <p>We strive for reliable service but cannot guarantee uninterrupted access at all times.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-register" id="acceptTermsBtn">
                    <span>I Agree to Terms</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ENHANCED PASSWORD STRENGTH SYSTEM
    class PasswordStrengthChecker {
        constructor() {
            this.passwordInput = document.getElementById('password');
            this.confirmInput = document.getElementById('password_confirmation');
            this.strengthFill = document.getElementById('strengthFill');
            this.strengthText = document.getElementById('strengthText');

            this.init();
        }

        init() {
            if (!this.passwordInput || !this.strengthFill || !this.strengthText) {
                console.error('❌ Password strength elements not found');
                return;
            }

            console.log('🔐 Initializing enhanced password strength checker...');
            this.bindEvents();
            console.log('✅ Password strength checker initialized');
        }

        bindEvents() {
            this.passwordInput.addEventListener('input', (e) => {
                this.checkStrength(e.target.value);
            });

            this.passwordInput.addEventListener('focus', (e) => {
                this.checkStrength(e.target.value);
            });

            if (this.confirmInput) {
                this.confirmInput.addEventListener('input', () => {
                    this.checkPasswordMatch();
                });
            }
        }

        checkStrength(password) {
            const result = this.calculateStrength(password);

            // Update visual indicators with smooth animation
            this.strengthFill.style.width = result.width;
            this.strengthFill.className = `strength-fill ${result.class}`;
            this.strengthText.textContent = result.text;
            this.strengthText.className = `strength-text ${result.class}`;

            // Add pulse animation for strong passwords
            if (result.class === 'strong') {
                this.strengthFill.style.animation = 'pulse 0.5s ease-in-out';
                setTimeout(() => {
                    this.strengthFill.style.animation = '';
                }, 500);
            }

            console.log(`Password strength: ${result.text} (${result.score}/5)`);
        }

        calculateStrength(password) {
            if (!password) {
                return {
                    score: 0,
                    text: 'Enter a password',
                    class: '',
                    width: '0%'
                };
            }

            let score = 0;
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                numbers: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Calculate score
            Object.values(checks).forEach(check => {
                if (check) score++;
            });

            // Determine strength level
            const levels = [{
                    min: 0,
                    text: 'Very Weak',
                    class: 'very-weak',
                    width: '20%'
                },
                {
                    min: 1,
                    text: 'Weak',
                    class: 'weak',
                    width: '40%'
                },
                {
                    min: 2,
                    text: 'Fair',
                    class: 'fair',
                    width: '60%'
                },
                {
                    min: 3,
                    text: 'Good',
                    class: 'good',
                    width: '80%'
                },
                {
                    min: 4,
                    text: 'Strong',
                    class: 'strong',
                    width: '100%'
                }
            ];

            const level = levels.reverse().find(l => score >= l.min) || levels[0];
            return {
                ...level,
                score
            };
        }

        checkPasswordMatch() {
            const password = this.passwordInput.value;
            const confirm = this.confirmInput.value;

            if (confirm && password !== confirm) {
                this.showPasswordMismatch();
            } else {
                this.hidePasswordMismatch();
            }
        }

        showPasswordMismatch() {
            this.confirmInput.classList.add('is-invalid');

            let errorDiv = document.getElementById('passwordMismatchError');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.id = 'passwordMismatchError';
                errorDiv.textContent = 'Passwords do not match';
                this.confirmInput.parentNode.appendChild(errorDiv);
            }
        }

        hidePasswordMismatch() {
            this.confirmInput.classList.remove('is-invalid');
            const errorDiv = document.getElementById('passwordMismatchError');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }

    // ENHANCED FORM SYSTEM
    class EnhancedRegistrationForm {
        constructor() {
            this.form = document.getElementById('registrationForm');
            this.submitBtn = document.getElementById('submitBtn');
            this.phoneInput = document.getElementById('phone');

            this.init();
        }

        init() {
            if (!this.form) {
                console.error('❌ Registration form not found');
                return;
            }

            console.log('📝 Initializing enhanced registration form...');
            this.bindEvents();
            this.setupPhoneFormatting();
            console.log('✅ Registration form initialized');
        }

        bindEvents() {
            this.form.addEventListener('submit', (e) => {
                this.handleSubmit(e);
            });
        }

        setupPhoneFormatting() {
            if (!this.phoneInput) return;

            this.phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');

                // Philippine mobile number formatting
                if (value.length > 0) {
                    if (value.startsWith('09')) {
                        value = value.replace(/^09/, '+63 9');
                    } else if (value.startsWith('9')) {
                        value = value.replace(/^9/, '+63 9');
                    } else if (value.startsWith('63')) {
                        value = value.replace(/^63/, '+63');
                    } else if (value.startsWith('0')) {
                        value = value.replace(/^0/, '+63 ');
                    } else {
                        value = '+63 ' + value;
                    }

                    // Add spacing
                    if (value.length > 6) {
                        value = value.slice(0, 6) + ' ' + value.slice(6);
                    }
                    if (value.length > 10) {
                        value = value.slice(0, 10) + ' ' + value.slice(10);
                    }
                    if (value.length > 15) {
                        value = value.slice(0, 15) + ' ' + value.slice(15);
                    }
                }

                e.target.value = value;
            });
        }

        handleSubmit(e) {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');

            // Password validation
            if (passwordInput && confirmInput && passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                this.showError('Passwords do not match!');
                return;
            }

            // Show loading state
            this.showLoadingState();
        }

        showError(message) {
            // Create enhanced error notification
            const errorNotification = document.createElement('div');
            errorNotification.innerHTML = `⚠️ ${message}`;
            errorNotification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            z-index: 10001;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            animation: slideInRight 0.3s ease-out;
        `;

            // Add animation styles
            if (!document.getElementById('errorAnimation')) {
                const style = document.createElement('style');
                style.id = 'errorAnimation';
                style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
                document.head.appendChild(style);
            }

            document.body.appendChild(errorNotification);

            // Remove after 4 seconds
            setTimeout(() => {
                errorNotification.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => errorNotification.remove(), 300);
            }, 4000);
        }

        showLoadingState() {
            if (this.submitBtn) {
                this.submitBtn.disabled = true;
                this.submitBtn.innerHTML = `
                <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid white; border-radius: 50%; animation: spin 1s linear infinite;"></span>
                    Creating Account...
                </span>
            `;

                // Add spin animation
                if (!document.getElementById('spinAnimation')) {
                    const style = document.createElement('style');
                    style.id = 'spinAnimation';
                    style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                    document.head.appendChild(style);
                }
            }
        }
    }

    // INITIALIZE ALL SYSTEMS
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Initializing enhanced registration systems...');

        // Initialize all enhanced systems
        new PasswordStrengthChecker();
        new EnhancedRegistrationForm();

        // Initialize Terms and Conditions Modal
        initializeTermsModal();

        console.log('✅ All enhanced systems initialized successfully!');
    });

    // TERMS AND CONDITIONS MODAL SYSTEM
    function initializeTermsModal() {
        const termsLink = document.getElementById('termsLink');
        const termsModalOverlay = document.getElementById('termsModalOverlay');
        const modalCloseBtn = document.getElementById('modalCloseBtn');
        const acceptTermsBtn = document.getElementById('acceptTermsBtn');
        const termsCheckbox = document.getElementById('terms_accepted');

        if (!termsLink || !termsModalOverlay || !modalCloseBtn || !acceptTermsBtn || !termsCheckbox) {
            console.error('❌ Terms modal elements not found');
            return;
        }

        console.log('📋 Initializing Terms and Conditions modal...');

        // Show modal function
        const showModal = () => {
            termsModalOverlay.classList.add('active');
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        };

        // Hide modal function
        const hideModal = () => {
            termsModalOverlay.classList.remove('active');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = ''; // Restore scrolling
        };

        // Event Listeners
        termsLink.addEventListener('click', (e) => {
            e.preventDefault();
            showModal();
        });

        modalCloseBtn.addEventListener('click', hideModal);

        // Close when clicking outside modal content
        termsModalOverlay.addEventListener('click', (e) => {
            if (e.target === termsModalOverlay) {
                hideModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && termsModalOverlay.classList.contains('active')) {
                hideModal();
            }
        });

        // Accept terms and close modal
        acceptTermsBtn.addEventListener('click', () => {
            termsCheckbox.checked = true;

            // Add visual feedback
            termsCheckbox.parentElement.classList.add('terms-accepted');

            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'alert alert-success mt-3';
            successMessage.style.animation = 'fadeIn 0.3s ease-out';
            successMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>Terms and conditions accepted!';

            const termsContainer = termsCheckbox.closest('.form-check');
            termsContainer.appendChild(successMessage);

            hideModal();

            // Remove message after 3 seconds
            setTimeout(() => {
                successMessage.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => successMessage.remove(), 300);
            }, 3000);
        });

        console.log('✅ Terms and Conditions modal initialized');
    }

    // No need for window.load initialization since we're already using DOMContentLoaded
    // which is more appropriate for this case

    // PASSWORD STRENGTH INDICATOR ANIMATION
</script>
@endsection

@push('scripts')
<!-- Load the SIMPLE preloaded locations module -->
<script src="{{ asset('js/locations-simple.js') }}?v={{ time() }}"></script>
</push>