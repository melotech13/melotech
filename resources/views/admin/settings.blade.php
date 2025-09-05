@extends('layouts.admin')

@section('title', 'Settings - MeloTech Admin')

@section('page-title', 'Settings')

@section('content')
<div class="settings-container">
    <!-- Settings Header -->
    <div class="settings-header mb-4">
        <div class="settings-header-content">
            <div class="settings-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="settings-title-section">
                <h2 class="settings-title">Account Settings</h2>
                <p class="settings-subtitle">Manage your personal information and account preferences</p>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="settings-content">
        <div class="settings-grid">
            <!-- Profile Information Card -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="settings-card-title">
                        <h3>Profile Information</h3>
                        <p>Update your personal details and contact information</p>
                    </div>
                </div>
                
                <div class="settings-card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>
                                Full Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', auth()->user()->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>
                                Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', auth()->user()->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-2"></i>
                                Phone Number
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', auth()->user()->phone) }}" 
                                   placeholder="Enter your phone number">
                            @error('phone')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Update Profile
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="settings-card-title">
                        <h3>Change Password</h3>
                        <p>Update your account password for better security</p>
                    </div>
                </div>
                
                <div class="settings-card-body">
                    <form method="POST" action="{{ route('admin.settings.password') }}" class="settings-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-2"></i>
                                Current Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>
                                Change Password
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetPasswordForm()">
                                <i class="fas fa-undo me-2"></i>
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Information Card -->
        <div class="settings-card account-info-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="settings-card-title">
                    <h3>Account Information</h3>
                    <p>View your account details and activity</p>
                </div>
            </div>
            
            <div class="settings-card-body">
                <div class="account-info-grid">
                    <div class="account-info-item">
                        <div class="account-info-label">
                            <i class="fas fa-user-shield me-2"></i>
                            Role
                        </div>
                        <div class="account-info-value">
                            <span class="badge badge-admin">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                    
                    <div class="account-info-item">
                        <div class="account-info-label">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Member Since
                        </div>
                        <div class="account-info-value">
                            {{ auth()->user()->created_at->format('F j, Y') }}
                        </div>
                    </div>
                    
                    <div class="account-info-item">
                        <div class="account-info-label">
                            <i class="fas fa-clock me-2"></i>
                            Last Login
                        </div>
                        <div class="account-info-value">
                            {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('F j, Y g:i A') : 'Never' }}
                        </div>
                    </div>
                    
                    <div class="account-info-item">
                        <div class="account-info-label">
                            <i class="fas fa-database me-2"></i>
                            User ID
                        </div>
                        <div class="account-info-value">
                            #{{ auth()->user()->id }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.settings-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            updatePasswordStrength(strength);
        });
    }
});

function resetForm() {
    const form = document.querySelector('form[action="{{ route('admin.settings.update') }}"]');
    if (form) {
        form.reset();
        // Reset to original values
        document.getElementById('name').value = '{{ auth()->user()->name }}';
        document.getElementById('email').value = '{{ auth()->user()->email }}';
        document.getElementById('phone').value = '{{ auth()->user()->phone ?? '' }}';
    }
}

function resetPasswordForm() {
    const form = document.querySelector('form[action="{{ route('admin.settings.password') }}"]');
    if (form) {
        form.reset();
    }
}

function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    return strength;
}

function updatePasswordStrength(strength) {
    // This could be enhanced with a visual strength indicator
    console.log('Password strength:', strength);
}
</script>
@endpush

<style>
.settings-container {
    max-width: 1200px;
    margin: 0 auto;
}

.settings-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.settings-header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.settings-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.settings-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.settings-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
}

.settings-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.settings-card {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: var(--transition);
}

.settings-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.settings-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.settings-card-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.settings-card-title h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.settings-card-title p {
    margin: 0.25rem 0 0 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.settings-card-body {
    padding: 2rem;
}

.settings-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.form-control {
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
    background: var(--bg-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.invalid-feedback {
    color: var(--danger-color);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-outline-secondary {
    background: transparent;
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
}

.btn-outline-secondary:hover {
    background: var(--bg-secondary);
    border-color: var(--text-secondary);
}

.account-info-card {
    grid-column: 1 / -1;
}

.account-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.account-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.account-info-label {
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
}

.account-info-value {
    color: var(--text-secondary);
    font-weight: 500;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-admin {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .settings-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .settings-icon {
        font-size: 2.5rem;
    }
    
    .settings-title {
        font-size: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .account-info-grid {
        grid-template-columns: 1fr;
    }
    
    .account-info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

/* Animation for form submission */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    animation: pulse 1s infinite;
}
</style>
@endsection
