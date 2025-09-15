<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email sending to: {$email}");
        $this->info("Current mail driver: " . config('mail.default'));
        
        $emailService = app(EmailService::class);
        $result = $emailService->testEmailConfiguration($email);
        
        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            
            if (isset($result['recommendation'])) {
                $this->warn('💡 ' . $result['recommendation']);
            }
        } else {
            $this->error('❌ ' . $result['message']);
        }
    }
}