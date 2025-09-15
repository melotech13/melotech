<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmailService
{
    /**
     * Send verification email to user with fallback mechanisms.
     *
     * @param User $user
     * @return array
     */
    public function sendVerificationEmail(User $user): array
    {
        try {
            // Generate verification code
            $verificationCode = $user->generateVerificationCode();
            
            // Try to send email using configured mail driver
            $emailSent = $this->attemptEmailSending($user, $verificationCode);
            
            if ($emailSent) {
                return [
                    'success' => true,
                    'message' => 'Verification email sent successfully!',
                    'method' => 'email',
                    'code' => null // Don't expose code in production
                ];
            }
            
            // Fallback: Store code in cache for manual retrieval
            $this->storeVerificationCodeInCache($user, $verificationCode);
            
            return [
                'success' => true,
                'message' => 'Email service unavailable. Your verification code has been generated and is available for 15 minutes.',
                'method' => 'cache',
                'code' => $verificationCode // Show code as fallback
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send verification email. Please contact support.',
                'method' => 'error',
                'code' => null
            ];
        }
    }

    /**
     * Attempt to send email using the configured mail driver.
     *
     * @param User $user
     * @param string $verificationCode
     * @return bool
     */
    private function attemptEmailSending(User $user, string $verificationCode): bool
    {
        try {
            // Check if we're using log driver (development mode)
            if (config('mail.default') === 'log') {
                Log::info('Email would be sent (log driver)', [
                    'to' => $user->email,
                    'subject' => 'Verify Your Email Address - MeloTech',
                    'verification_code' => $verificationCode
                ]);
                return true; // Consider log driver as successful for development
            }

            // Send actual email
            Mail::send('emails.verification', [
                'user' => $user,
                'verificationCode' => $verificationCode,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Verify Your Email Address - MeloTech');
            });

            Log::info('Verification email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'driver' => config('mail.default')
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'driver' => config('mail.default'),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Store verification code in cache as fallback.
     *
     * @param User $user
     * @param string $verificationCode
     * @return void
     */
    private function storeVerificationCodeInCache(User $user, string $verificationCode): void
    {
        $cacheKey = "verification_code_{$user->id}";
        Cache::put($cacheKey, $verificationCode, now()->addMinutes(15));
        
        Log::info('Verification code stored in cache as fallback', [
            'user_id' => $user->id,
            'email' => $user->email,
            'cache_key' => $cacheKey
        ]);
    }

    /**
     * Get verification code from cache.
     *
     * @param User $user
     * @return string|null
     */
    public function getVerificationCodeFromCache(User $user): ?string
    {
        $cacheKey = "verification_code_{$user->id}";
        return Cache::get($cacheKey);
    }

    /**
     * Clear verification code from cache.
     *
     * @param User $user
     * @return void
     */
    public function clearVerificationCodeFromCache(User $user): void
    {
        $cacheKey = "verification_code_{$user->id}";
        Cache::forget($cacheKey);
    }

    /**
     * Test email configuration.
     *
     * @param string $testEmail
     * @return array
     */
    public function testEmailConfiguration(string $testEmail): array
    {
        try {
            $driver = config('mail.default');
            
            if ($driver === 'log') {
                return [
                    'success' => true,
                    'message' => 'Email system is configured for development (log driver). Emails are written to log files.',
                    'driver' => $driver,
                    'recommendation' => 'Configure SMTP settings for production email sending.'
                ];
            }

            // Test sending email
            Mail::raw('This is a test email from MeloTech', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email from MeloTech');
            });

            return [
                'success' => true,
                'message' => 'Test email sent successfully!',
                'driver' => $driver
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage(),
                'driver' => config('mail.default'),
                'error' => $e->getMessage()
            ];
        }
    }
}
