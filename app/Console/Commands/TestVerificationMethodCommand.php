<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestVerificationMethodCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:test-method {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the markEmailAsVerified method';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('User not found!');
            return;
        }
        
        $this->info("Testing markEmailAsVerified for: {$email}");
        $this->newLine();
        
        $this->line("Before verification:");
        $this->line("Email Verified At: " . ($user->email_verified_at ?: 'Not verified'));
        $this->line("Is Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
        $this->line("Code Expires At: " . ($user->email_verification_code_expires_at ?: 'Not set'));
        $this->newLine();
        
        // Test markEmailAsVerified
        $this->info("Calling markEmailAsVerified()...");
        $result = $user->markEmailAsVerified();
        
        $this->line("Result: " . ($result ? 'Success' : 'Failed'));
        $this->newLine();
        
        // Refresh and check again
        $user->refresh();
        
        $this->line("After verification:");
        $this->line("Email Verified At: " . ($user->email_verified_at ?: 'Not verified'));
        $this->line("Is Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
        $this->line("Verification Code: " . ($user->email_verification_code ?: 'Cleared'));
        $this->line("Code Expires At: " . ($user->email_verification_code_expires_at ?: 'Cleared'));
    }
}
