<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:storage {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that verification codes are actually stored in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔍 COMPREHENSIVE VERIFICATION CODE STORAGE TEST");
        $this->info("=============================================");
        $this->newLine();
        
        // Check if user exists
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
        $this->info("📊 BEFORE CODE GENERATION:");
        $this->info("-------------------------");
        
        // Check database directly
        $dbUser = DB::table('users')->where('id', $user->id)->first();
        $this->line("Database ID: {$dbUser->id}");
        $this->line("Email: {$dbUser->email}");
        $this->line("Email Verified At: " . ($dbUser->email_verified_at ?: 'NULL'));
        $this->line("Verification Code: " . ($dbUser->email_verification_code ?: 'NULL'));
        $this->line("Code Expires At: " . ($dbUser->email_verification_code_expires_at ?: 'NULL'));
        
        $this->newLine();
        $this->info("🔄 GENERATING VERIFICATION CODE...");
        $this->info("----------------------------------");
        
        // Generate verification code
        $code = $user->generateVerificationCode();
        $this->info("Generated code: {$code}");
        
        $this->newLine();
        $this->info("📊 AFTER CODE GENERATION:");
        $this->info("------------------------");
        
        // Check database directly again
        $dbUserAfter = DB::table('users')->where('id', $user->id)->first();
        $this->line("Database ID: {$dbUserAfter->id}");
        $this->line("Email: {$dbUserAfter->email}");
        $this->line("Email Verified At: " . ($dbUserAfter->email_verified_at ?: 'NULL'));
        $this->line("Verification Code: " . ($dbUserAfter->email_verification_code ?: 'NULL'));
        $this->line("Code Expires At: " . ($dbUserAfter->email_verification_code_expires_at ?: 'NULL'));
        
        $this->newLine();
        $this->info("🧪 VALIDATION TESTS:");
        $this->info("-------------------");
        
        // Refresh user model
        $user->refresh();
        
        // Test model methods
        $this->line("Model isEmailVerified(): " . ($user->isEmailVerified() ? 'TRUE' : 'FALSE'));
        $this->line("Model verification code: " . ($user->email_verification_code ?: 'NULL'));
        $this->line("Model code expires: " . ($user->email_verification_code_expires_at ?: 'NULL'));
        
        // Test code validation
        $isValid = $user->isVerificationCodeValid($code);
        $this->line("Code validation test: " . ($isValid ? 'PASSED ✅' : 'FAILED ❌'));
        
        // Compare database vs model
        $dbCode = $dbUserAfter->email_verification_code;
        $modelCode = $user->email_verification_code;
        $this->line("Database code matches model: " . ($dbCode === $modelCode ? 'YES ✅' : 'NO ❌'));
        
        $this->newLine();
        if ($isValid && $dbCode === $modelCode) {
            $this->info("🎉 SUCCESS: Verification codes are being stored correctly!");
        } else {
            $this->error("❌ FAILURE: There's an issue with verification code storage!");
        }
        
        $this->newLine();
        $this->info("💡 To verify this in your database:");
        $this->line("Run: SELECT id, email, email_verified_at, email_verification_code, email_verification_code_expires_at FROM users WHERE email = '{$email}';");
    }
}
