<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\EmailService;

class TestVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:verification {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email verification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email verification system for: {$email}");
        
        // Create a test user
        $user = new User();
        $user->id = 999; // Test ID
        $user->email = $email;
        $user->name = 'Test User';
        
        $emailService = app(EmailService::class);
        
        // Test sending verification email
        $this->info('Sending verification email...');
        $result = $emailService->sendVerificationEmail($user);
        
        $this->info('Result:');
        $this->line('Success: ' . ($result['success'] ? 'Yes' : 'No'));
        $this->line('Message: ' . $result['message']);
        $this->line('Method: ' . $result['method']);
        
        if (isset($result['code'])) {
            $this->line('Verification Code: ' . $result['code']);
        }
        
        // Test verification code retrieval from cache
        if ($result['method'] === 'cache') {
            $this->info('Testing cache retrieval...');
            $cachedCode = $emailService->getVerificationCodeFromCache($user);
            $this->line('Cached Code: ' . ($cachedCode ?: 'Not found'));
        }
        
        $this->newLine();
        $this->info('✅ Email verification system test completed!');
    }
}
