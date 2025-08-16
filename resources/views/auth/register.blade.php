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

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
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

    /* Terms and Login */
    .terms-section {
        margin: 1.5rem 0;
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

    /* Modal Styling */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: var(--shadow-medium);
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
    }

    .modal-title {
        font-weight: 600;
        color: white;
    }

    .btn-close {
        filter: invert(1);
    }

    .modal-body {
        padding: 2rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .modal-body h6 {
        color: var(--primary-color);
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .modal-body h6:first-child {
        margin-top: 0;
    }

    .modal-body p {
        color: var(--text-dark);
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1rem 2rem;
    }

    .modal-footer .btn-secondary {
        background: var(--text-light);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
    }

    .modal-footer .btn-secondary:hover {
        background: #5a6268;
    }

    /* Terms and Privacy Links */
    .terms-link, .privacy-link {
        cursor: pointer;
        transition: opacity 0.2s ease;
    }

    .terms-link:hover, .privacy-link:hover {
        opacity: 0.8;
        text-decoration: underline !important;
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
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
                             <label for="field_size" class="form-label">Field Size</label>
                             <input type="number" class="form-control @error('field_size') is-invalid @enderror" 
                                    id="field_size" name="field_size" value="{{ old('field_size') }}" 
                                    step="0.1" min="0.1" required placeholder="0.0">
                             @error('field_size')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>
                         
                         <div class="col-md-3">
                             <label for="field_size_unit" class="form-label">Unit</label>
                             <select class="form-select @error('field_size_unit') is-invalid @enderror" 
                                     id="field_size_unit" name="field_size_unit" required>
                                 <option value="">Unit</option>
                                 <option value="acres" {{ old('field_size_unit') == 'acres' ? 'selected' : '' }}>Acres</option>
                                 <option value="hectares" {{ old('field_size_unit') == 'hectares' ? 'selected' : '' }}>Hectares</option>
                             </select>
                             @error('field_size_unit')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>
                     </div>
                </div>

                <!-- Terms and Submit Section -->
                <div class="form-section">
                    <div class="terms-section">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none terms-link" style="color: var(--primary-color);" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> and 
                                <a href="#" class="text-decoration-none privacy-link" style="color: var(--primary-color);" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

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

<!-- Terms and Privacy Policy Modals -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Acceptance of Terms</h6>
                <p>By accessing and using MeloTech, you accept and agree to be bound by the terms and provision of this agreement.</p>
                
                <h6>2. Use License</h6>
                <p>Permission is granted to temporarily download one copy of the materials (information or software) on MeloTech's website for personal, non-commercial transitory viewing only.</p>
                
                <h6>3. Disclaimer</h6>
                <p>The materials on MeloTech's website are provided on an 'as is' basis. MeloTech makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
                
                <h6>4. Limitations</h6>
                <p>In no event shall MeloTech or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on MeloTech's website.</p>
                
                <h6>5. Revisions and Errata</h6>
                <p>The materials appearing on MeloTech's website could include technical, typographical, or photographic errors. MeloTech does not warrant that any of the materials on its website are accurate, complete or current.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Information We Collect</h6>
                <p>We collect information you provide directly to us, such as when you create an account, complete a form, or contact us for support.</p>
                
                <h6>2. How We Use Your Information</h6>
                <p>We use the information we collect to provide, maintain, and improve our services, to communicate with you, and to develop new features.</p>
                
                <h6>3. Information Sharing</h6>
                <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>
                
                <h6>4. Data Security</h6>
                <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                
                <h6>5. Your Rights</h6>
                <p>You have the right to access, update, or delete your personal information. You can also opt out of certain communications from us.</p>
                
                <h6>6. Contact Us</h6>
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@melotech.com</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Load the SIMPLE preloaded locations module -->
<script src="{{ asset('js/locations-simple.js') }}?v={{ time() }}"></script>
<script>
// SIMPLE PASSWORD STRENGTH CHECKER - NO COMPLEX INITIALIZATION
console.log('🚀 Password strength checker starting...');

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM ready, setting up password features...');
    setupPasswordFeatures();
});

// Also try on window load as backup
window.addEventListener('load', function() {
    console.log('✅ Window loaded, setting up password features...');
    setupPasswordFeatures();
});

function setupPasswordFeatures() {
    console.log('🔐 Setting up password features...');
    
    // Get all the elements we need
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    console.log('Elements found:', {
        passwordInput: !!passwordInput,
        passwordConfirmInput: !!passwordConfirmInput,
        strengthFill: !!strengthFill,
        strengthText: !!strengthText
    });
    
    // Check if all elements exist
    if (!passwordInput || !passwordConfirmInput || !strengthFill || !strengthText) {
        console.error('❌ Some elements missing, retrying in 500ms...');
        setTimeout(setupPasswordFeatures, 500);
        return;
    }
    
    console.log('✅ All elements found! Setting up functionality...');
    
    // PASSWORD STRENGTH CHECKER
    function checkPasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/\d/.test(password)) score += 1;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 1;
        
        if (score === 0) return { strength: 'Very Weak', class: 'very-weak', width: '20%' };
        if (score === 1) return { strength: 'Weak', class: 'weak', width: '40%' };
        if (score === 2) return { strength: 'Fair', class: 'fair', width: '60%' };
        if (score === 3) return { strength: 'Good', class: 'good', width: '80%' };
        return { strength: 'Strong', class: 'strong', width: '100%' };
    }
    
    // UPDATE STRENGTH DISPLAY
    function updateStrength(password) {
        console.log('Updating strength for password:', password ? password.length + ' chars' : 'empty');
        
        if (!password || password.length === 0) {
            strengthFill.style.width = '0%';
            strengthFill.className = 'strength-fill';
            strengthText.textContent = 'Enter a password';
            strengthText.className = 'strength-text';
            console.log('Reset to empty state');
            return;
        }
        
        const result = checkPasswordStrength(password);
        strengthFill.style.width = result.width;
        strengthFill.className = `strength-fill ${result.class}`;
        strengthText.textContent = result.strength;
        strengthText.className = `strength-text ${result.class}`;
        
        console.log('Strength updated:', result.strength, result.class, result.width);
    }
    

    
    // PASSWORD INPUT EVENT LISTENER
    passwordInput.addEventListener('input', function() {
        console.log('Password input:', this.value);
        updateStrength(this.value);
    });
    
    // Also update on focus to ensure initial state
    passwordInput.addEventListener('focus', function() {
        console.log('Password field focused');
        updateStrength(this.value);
    });
    
    // PASSWORD CONFIRMATION CHECK
    passwordConfirmInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirm = this.value;
        
        if (confirm && password !== confirm) {
            this.classList.add('is-invalid');
            if (!document.getElementById('passwordMismatchError')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.id = 'passwordMismatchError';
                errorDiv.textContent = 'Passwords do not match';
                this.parentNode.appendChild(errorDiv);
            }
        } else {
            this.classList.remove('is-invalid');
            const errorDiv = document.getElementById('passwordMismatchError');
            if (errorDiv) errorDiv.remove();
        }
    });
    
    console.log('✅ Password features setup complete!');
    
    // Test it immediately
    console.log('🧪 Testing password strength checker...');
    // Don't test with a sample password - start with empty state
    updateStrength('');
}

// FORM INITIALIZATION
function initializeForm() {
    console.log('📝 Initializing form...');
    
    // Philippine phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Philippine mobile number format: +63 9XX XXX XXXX
            if (value.length > 0) {
                if (value.startsWith('09')) {
                    // Convert 09 to +63 9
                    value = value.replace(/^09/, '+63 9');
                } else if (value.startsWith('9')) {
                    // Convert 9 to +63 9
                    value = value.replace(/^9/, '+63 9');
                } else if (value.startsWith('63')) {
                    // Convert 63 to +63
                    value = value.replace(/^63/, '+63');
                } else if (value.startsWith('0')) {
                    // Convert 0 to +63
                    value = value.replace(/^0/, '+63 ');
                } else {
                    // Add +63 prefix if no prefix exists
                    value = '+63 ' + value;
                }
                
                // Format the number with spaces
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
    
    // Planting date - no date restrictions
    const plantingDateInput = document.getElementById('planting_date');
    if (plantingDateInput) {
        // Remove any max date restrictions to allow any date selection
        plantingDateInput.removeAttribute('max');
    }
    
    // Form validation
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            
            if (passwordInput && passwordConfirmInput && passwordInput.value !== passwordConfirmInput.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Account...';
        });
    }

    // Modal functionality
    setupModals();
    
    console.log('✅ Form initialization complete!');
}

// Setup modal functionality
function setupModals() {
    console.log('🔍 Setting up modals...');
    
    // Terms modal
    const termsLink = document.querySelector('.terms-link');
    const termsModal = document.getElementById('termsModal');
    
    if (termsLink && termsModal) {
        termsLink.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(termsModal);
            modal.show();
        });
    }
    
    // Privacy modal
    const privacyLink = document.querySelector('.privacy-link');
    const privacyModal = document.getElementById('privacyModal');
    
    if (privacyLink && privacyModal) {
        privacyLink.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(privacyModal);
            modal.show();
        });
    }
    
    console.log('✅ Modal setup complete!');
}

// Initialize everything
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
});

console.log('🚀 Registration form JavaScript loaded!');
</script>
@endpush
