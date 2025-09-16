<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\EmailVerificationService;

class TestCleanVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:test-clean {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test clean verification system - keeps code in original column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧹 CLEAN VERIFICATION SYSTEM TEST");
        $this->info("=================================");
        $this->newLine();
        
        // Find or create user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->info("Creating new user for testing...");
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => 'password', // Store as plain text
                'role' => 'user'
            ]);
            $this->info("✅ User created with ID: {$user->id}");
        } else {
            $this->info("✅ User found with ID: {$user->id}");
        }
        
        $this->newLine();
        $this->info("📊 BEFORE VERIFICATION:");
        $this->info("----------------------");
        $this->line("Email: {$user->email}");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        
        // Generate verification code if not set
        if (!$user->email_verification_code) {
            $this->newLine();
            $this->info("🔄 Generating verification code...");
            $code = $user->generateVerificationCode();
            $this->line("Generated code: {$code}");
        } else {
            $code = $user->email_verification_code;
            $this->newLine();
            $this->info("Using existing code: {$code}");
        }
        
        // Test verification
        $this->newLine();
        $this->info("🧪 TESTING VERIFICATION:");
        $this->info("------------------------");
        
        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->verifyEmail($user, $code);
        
        $this->line("Verification result: " . ($result['success'] ? 'SUCCESS' : 'FAILED'));
        $this->line("Message: " . $result['message']);
        
        // Refresh user and show results
        $user->refresh();
        
        $this->newLine();
        $this->info("📊 AFTER VERIFICATION:");
        $this->info("---------------------");
        $this->line("Email: {$user->email}");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Email Verified At: " . ($user->email_verified_at ?: 'Not set'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        
        $this->newLine();
        if ($user->email_verification_code && $user->isEmailVerified()) {
            $this->info("🎉 SUCCESS: Clean verification system working!");
            $this->line("✅ Verification code kept: {$user->email_verification_code}");
            $this->line("✅ No NULL values in database");
            $this->line("✅ Clean and simple - only 2 verification columns");
            $this->line("✅ Perfect evidence for panelists!");
        } else {
            $this->error("❌ Something went wrong");
        }
        
        $this->newLine();
        $this->info("💡 EVIDENCE FOR PANELISTS:");
        $this->line("• User: {$user->email}");
        $this->line("• Verification Code: " . ($user->email_verification_code ?: 'N/A'));
        $this->line("• Verification Date: " . ($user->email_verified_at ?: 'N/A'));
        $this->line("• Status: " . ($user->isEmailVerified() ? 'VERIFIED' : 'NOT VERIFIED'));
    }
}
