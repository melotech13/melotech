<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailVerificationService
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send verification email to user.
     *
     * @param User $user
     * @return array
     */
    public function sendVerificationEmail(User $user): array
    {
        return $this->emailService->sendVerificationEmail($user);
    }

    /**
     * Verify user's email with the provided code.
     *
     * @param User $user
     * @param string $code
     * @return array
     */
    public function verifyEmail(User $user, string $code): array
    {
        Log::info('EmailVerificationService::verifyEmail called', [
            'user_id' => $user->id,
            'email' => $user->email,
            'provided_code' => $code,
            'stored_code' => $user->email_verification_code,
            'code_expires_at' => $user->email_verification_code_expires_at,
            'is_email_verified' => $user->isEmailVerified()
        ]);

        // First check the database verification code
        if ($user->isVerificationCodeValid($code)) {
            Log::info('Database verification code is valid', ['user_id' => $user->id]);
            
            // Set audit trail fields before marking as verified
            $user->update([
                'used_verification_code' => $code,
                'verification_completed_at' => now(),
            ]);
            
            $result = $user->markEmailAsVerified();
            $this->emailService->clearVerificationCodeFromCache($user);
            
            Log::info('Email marked as verified', [
                'user_id' => $user->id,
                'result' => $result,
                'verification_code' => $user->email_verification_code,
                'used_verification_code' => $user->used_verification_code,
                'verification_completed_at' => $user->verification_completed_at,
                'email_verified_at' => $user->fresh()->email_verified_at
            ]);
            
            return [
                'success' => true,
                'message' => 'Email verified successfully!'
            ];
        }

        // Fallback: Check cache for verification code
        $cachedCode = $this->emailService->getVerificationCodeFromCache($user);
        Log::info('Checking cached verification code', [
            'user_id' => $user->id,
            'cached_code' => $cachedCode,
            'provided_code' => $code
        ]);
        
        if ($cachedCode === $code) {
            Log::info('Cached verification code is valid', ['user_id' => $user->id]);
            
            // Set audit trail fields before marking as verified
            $user->update([
                'used_verification_code' => $code,
                'verification_completed_at' => now(),
            ]);
            
            $result = $user->markEmailAsVerified();
            $this->emailService->clearVerificationCodeFromCache($user);
            
            Log::info('Email marked as verified via cache', [
                'user_id' => $user->id,
                'result' => $result,
                'verification_code' => $user->email_verification_code,
                'used_verification_code' => $user->used_verification_code,
                'verification_completed_at' => $user->verification_completed_at,
                'email_verified_at' => $user->fresh()->email_verified_at
            ]);
            
            return [
                'success' => true,
                'message' => 'Email verified successfully!'
            ];
        }

        Log::warning('Verification failed - invalid or expired code', [
            'user_id' => $user->id,
            'provided_code' => $code,
            'stored_code' => $user->email_verification_code,
            'cached_code' => $cachedCode
        ]);

        return [
            'success' => false,
            'message' => 'Invalid or expired verification code.'
        ];
    }

    /**
     * Resend verification email.
     *
     * @param User $user
     * @return array
     */
    public function resendVerificationEmail(User $user): array
    {
        return $this->sendVerificationEmail($user);
    }
}

