<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\EmailVerificationService;

class TestRealVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:test-real {email} {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the real verification flow exactly as it happens in the web interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $code = $this->argument('code');
        
        $this->info("🧪 TESTING REAL VERIFICATION FLOW");
        $this->info("=================================");
        $this->newLine();
        
        // Step 1: Find user (like in AuthController)
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User not found!");
            return;
        }
        
        $this->info("✅ User found: {$user->name} (ID: {$user->id})");
        $this->line("Email: {$user->email}");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        
        $this->newLine();
        
        // Step 2: Check if already verified (like in AuthController)
        if ($user->isEmailVerified()) {
            $this->warn("⚠️  User is already verified!");
            $this->line("In the web interface, this would redirect to login page.");
            $this->line("This is the expected behavior for already verified users.");
            return;
        }
        
        // Step 3: Test verification (like in AuthController)
        $this->info("🔄 Testing verification with code: {$code}");
        
        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->verifyEmail($user, $code);
        
        $this->newLine();
        $this->info("📊 VERIFICATION RESULT:");
        $this->line("Success: " . ($result['success'] ? 'Yes' : 'No'));
        $this->line("Message: " . $result['message']);
        
        if ($result['success']) {
            // Refresh user
            $user->refresh();
            $this->newLine();
            $this->info("✅ AFTER VERIFICATION:");
            $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
            $this->line("Verification Code: " . ($user->email_verification_code ?: 'Cleared'));
            $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Cleared'));
        }
        
        $this->newLine();
        $this->info("💡 SUMMARY:");
        if ($user->isEmailVerified()) {
            $this->line("This user is already verified, so verification attempts will redirect to login.");
            $this->line("This is the correct behavior!");
        } else {
            $this->line("This user needs verification, and the process " . ($result['success'] ? 'worked' : 'failed') . ".");
        }
    }
}
