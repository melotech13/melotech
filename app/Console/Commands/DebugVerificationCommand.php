<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\EmailVerificationService;

class DebugVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:verification {email} {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug email verification process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $code = $this->argument('code');
        
        $this->info("🔍 Debugging verification for: {$email}");
        $this->info("Code: {$code}");
        $this->newLine();
        
        // Find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('❌ User not found!');
            return;
        }
        
        $this->info('👤 User found:');
        $this->line('ID: ' . $user->id);
        $this->line('Name: ' . $user->name);
        $this->line('Email: ' . $user->email);
        $this->line('Email Verified At: ' . ($user->email_verified_at ?: 'Not verified'));
        $this->line('Verification Code: ' . ($user->email_verification_code ?: 'Not set'));
        $this->line('Code Expires At: ' . ($user->email_verification_code_expires_at ?: 'Not set'));
        $this->line('Is Email Verified: ' . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->newLine();
        
        // Test verification code validation
        $this->info('🔐 Testing verification code validation:');
        $isValid = $user->isVerificationCodeValid($code);
        $this->line('Code is valid: ' . ($isValid ? 'Yes' : 'No'));
        
        if ($user->email_verification_code) {
            $this->line('Stored code: ' . $user->email_verification_code);
            $this->line('Provided code: ' . $code);
            $this->line('Codes match: ' . ($user->email_verification_code === $code ? 'Yes' : 'No'));
        }
        
        if ($user->email_verification_code_expires_at) {
            $this->line('Code expires: ' . $user->email_verification_code_expires_at);
            $this->line('Is future: ' . ($user->email_verification_code_expires_at->isFuture() ? 'Yes' : 'No'));
        }
        $this->newLine();
        
        // Test verification service
        $this->info('🛠️ Testing EmailVerificationService:');
        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->verifyEmail($user, $code);
        
        $this->line('Verification result:');
        $this->line('Success: ' . ($result['success'] ? 'Yes' : 'No'));
        $this->line('Message: ' . $result['message']);
        $this->newLine();
        
        // Refresh user and check again
        $user->refresh();
        $this->info('🔄 After verification attempt:');
        $this->line('Email Verified At: ' . ($user->email_verified_at ?: 'Not verified'));
        $this->line('Is Email Verified: ' . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line('Verification Code: ' . ($user->email_verification_code ?: 'Cleared'));
    }
}
