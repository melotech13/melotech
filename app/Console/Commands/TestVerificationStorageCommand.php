<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestVerificationStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:test-storage {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test verification code storage in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing verification code storage for: {$email}");
        
        // Find or create user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->info("Creating new user...");
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => 'user'
            ]);
            $this->info("User created with ID: {$user->id}");
        } else {
            $this->info("User found with ID: {$user->id}");
        }
        
        $this->newLine();
        $this->info("Before generating code:");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        
        $this->newLine();
        $this->info("Generating verification code...");
        
        // Generate verification code
        $code = $user->generateVerificationCode();
        
        $this->info("Code generated: {$code}");
        
        // Refresh user from database
        $user->refresh();
        
        $this->newLine();
        $this->info("After generating code:");
        $this->line("Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        
        // Test if code is valid
        $isValid = $user->isVerificationCodeValid($code);
        $this->line("Code is valid: " . ($isValid ? 'Yes' : 'No'));
        
        if (!$isValid && $user->email_verification_code_expires_at) {
            $this->warn("Code expired: " . $user->email_verification_code_expires_at);
            $this->line("Current time: " . now());
        }
    }
}
