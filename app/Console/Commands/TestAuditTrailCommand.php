<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\EmailVerificationService;

class TestAuditTrailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:test-audit {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test verification code audit trail functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔍 VERIFICATION CODE AUDIT TRAIL TEST");
        $this->info("=====================================");
        $this->newLine();
        
        // Find or create user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->info("Creating new user for testing...");
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => bcrypt('password'),
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
        $this->line("Used Verification Code: " . ($user->used_verification_code ?: 'Not set'));
        $this->line("Verification Completed At: " . ($user->verification_completed_at ?: 'Not set'));
        
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
        $this->info("🧪 TESTING VERIFICATION WITH AUDIT TRAIL:");
        $this->info("----------------------------------------");
        
        $emailVerificationService = app(EmailVerificationService::class);
        $result = $emailVerificationService->verifyEmail($user, $code);
        
        $this->line("Verification result: " . ($result['success'] ? 'SUCCESS' : 'FAILED'));
        $this->line("Message: " . $result['message']);
        
        // Refresh user and show audit trail
        $user->refresh();
        
        $this->newLine();
        $this->info("📊 AFTER VERIFICATION (AUDIT TRAIL):");
        $this->info("-----------------------------------");
        $this->line("Email: {$user->email}");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Email Verified At: " . ($user->email_verified_at ?: 'Not set'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Cleared (for security)'));
        $this->line("Used Verification Code: " . ($user->used_verification_code ?: 'Not set'));
        $this->line("Verification Completed At: " . ($user->verification_completed_at ?: 'Not set'));
        
        $this->newLine();
        if ($user->used_verification_code) {
            $this->info("🎉 SUCCESS: Audit trail is working!");
            $this->line("✅ Used verification code: {$user->used_verification_code}");
            $this->line("✅ Verification completed at: {$user->verification_completed_at}");
            $this->line("✅ This provides evidence for panelists!");
        } else {
            $this->error("❌ Audit trail not working properly");
        }
        
        $this->newLine();
        $this->info("💡 EVIDENCE FOR PANELISTS:");
        $this->line("• User: {$user->email}");
        $this->line("• Verification Code Used: " . ($user->used_verification_code ?: 'N/A'));
        $this->line("• Verification Date: " . ($user->verification_completed_at ?: 'N/A'));
        $this->line("• Status: " . ($user->isEmailVerified() ? 'VERIFIED' : 'NOT VERIFIED'));
    }
}
