<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - MeloTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .verification-icon {
            background: linear-gradient(135deg, #2c5530, #4a7c59);
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 32px;
        }
        .verification-code-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        .verification-code-input:focus {
            border-color: #2c5530;
            box-shadow: 0 0 0 0.2rem rgba(44, 85, 48, 0.25);
        }
        .btn-verify {
            background: linear-gradient(135deg, #2c5530, #4a7c59);
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(44, 85, 48, 0.3);
        }
        .btn-verify:disabled {
            background: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-resend {
            background: transparent;
            border: 2px solid #2c5530;
            color: #2c5530;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-resend:hover {
            background: #2c5530;
            color: white;
        }
        .alert {
            border-radius: 15px;
            border: none;
        }
        .countdown {
            color: #dc3545;
            font-weight: bold;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: white;
        }
        .step.active {
            background: linear-gradient(135deg, #2c5530, #4a7c59);
        }
        .step.completed {
            background: #28a745;
        }
        .step.pending {
            background: #e9ecef;
            color: #6c757d;
        }
        .step-line {
            width: 60px;
            height: 2px;
            background: #e9ecef;
            margin-top: 19px;
        }
        .step-line.completed {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100">
            <div class="col-md-6 col-lg-4 mx-auto">
                <div class="verification-container p-5">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-line completed"></div>
                        <div class="step active">
                            <span>2</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step pending">
                            <span>3</span>
                        </div>
                    </div>

                    <!-- Verification Icon -->
                    <div class="verification-icon">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>

                    <!-- Title -->
                    <h2 class="text-center mb-4" style="color: #2c5530; font-weight: 700;">
                        Verify Your Email
                    </h2>

                    <!-- Description -->
                    <p class="text-center text-muted mb-4">
                        We've sent a 6-digit verification code to:
                        <br>
                        <strong>{{ session('email', 'your email address') }}</strong>
                    </p>

                    <!-- Alerts -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('verification.verify') }}" id="verificationForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', session('email')) }}" 
                                   required
                                   readonly>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="verification_code" class="form-label">Verification Code</label>
                            <input type="text" 
                                   class="form-control verification-code-input @error('verification_code') is-invalid @enderror" 
                                   id="verification_code" 
                                   name="verification_code" 
                                   value="{{ old('verification_code') }}" 
                                   maxlength="6"
                                   placeholder="000000"
                                   required
                                   autocomplete="off">
                            @error('verification_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter the 6-digit code sent to your email
                            </div>
                        </div>

                        <!-- Countdown Timer -->
                        <div class="text-center mb-4">
                            <small class="text-muted">
                                Code expires in: <span class="countdown" id="countdown">15:00</span>
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-secondary btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-check me-2"></i>
                                Verify Email
                            </button>
                        </div>

                        <!-- Resend Button -->
                        <div class="text-center">
                            <button type="button" class="btn btn-resend" id="resendBtn" disabled>
                                <i class="fas fa-redo me-2"></i>
                                Resend Code
                            </button>
                        </div>
                    </form>

                    <!-- Help Text -->
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-question-circle me-1"></i>
                            Didn't receive the email? Check your spam folder or 
                            <a href="#" id="resendLink" style="color: #2c5530; text-decoration: none;">
                                resend verification code
                            </a>
                        </small>
                    </div>

                    <!-- Back to Login -->
                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format verification code input
        const verificationInput = document.getElementById('verification_code');
        const submitBtn = document.getElementById('submitBtn');
        
        function updateSubmitButton() {
            if (verificationInput.value.length === 6) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-verify');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-verify');
                submitBtn.classList.add('btn-secondary');
            }
        }
        
        verificationInput.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            updateSubmitButton();
        });
        
        // Handle paste event
        verificationInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                this.value = this.value.replace(/[^0-9]/g, '');
                updateSubmitButton();
            }, 10);
        });

        // Countdown timer
        let timeLeft = 15 * 60; // 15 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        const resendLink = document.getElementById('resendLink');

        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                countdownElement.textContent = 'Expired';
                resendBtn.disabled = false;
                resendLink.style.pointerEvents = 'auto';
                resendLink.style.opacity = '1';
            } else {
                timeLeft--;
            }
        }

        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call

        // Resend verification code
        function resendVerification() {
            const email = document.getElementById('email').value;
            const resendBtn = document.getElementById('resendBtn');
            const resendLink = document.getElementById('resendLink');
            
            // Show loading state
            const originalText = resendBtn.innerHTML;
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            fetch('{{ route("verification.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Reset countdown
                    timeLeft = 15 * 60;
                    resendBtn.disabled = true;
                    resendLink.style.pointerEvents = 'none';
                    resendLink.style.opacity = '0.5';
                    
                    // Clear verification code input
                    document.getElementById('verification_code').value = '';
                    document.getElementById('submitBtn').disabled = true;
                } else {
                    showAlert('error', data.message || 'Failed to send verification code. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            })
            .finally(() => {
                // Restore button state
                resendBtn.innerHTML = originalText;
            });
        }

        // Show alert function
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            `;
            
            // Insert after the description paragraph
            const description = document.querySelector('p.text-center.text-muted.mb-4');
            description.parentNode.insertBefore(alertDiv, description.nextSibling);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        resendBtn.addEventListener('click', resendVerification);
        resendLink.addEventListener('click', function(e) {
            e.preventDefault();
            resendVerification();
        });

        // Focus on verification code input
        document.getElementById('verification_code').focus();

        // Form submission handling
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const verificationCode = document.getElementById('verification_code').value;
            
            if (verificationCode.length !== 6) {
                e.preventDefault();
                alert('Please enter a complete 6-digit verification code.');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
        });
    </script>
</body>
</html>
