<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateVerificationCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:generate {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new verification code for a user';

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
        
        $this->info("Generating new verification code for: {$email}");
        
        // Generate new verification code
        $code = $user->generateVerificationCode();
        
        $this->info("✅ New verification code generated!");
        $this->line("Code: {$code}");
        $this->line("Expires at: {$user->email_verification_code_expires_at}");
        $this->line("User ID: {$user->id}");
        
        $this->newLine();
        $this->info("You can now use this code to verify the email address.");
    }
}
