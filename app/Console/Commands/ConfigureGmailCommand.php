<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureGmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:configure-gmail {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Gmail SMTP settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $this->info('🔧 Configuring Gmail SMTP settings...');
        
        // Read current .env file
        $envPath = base_path('.env');
        $envContent = File::get($envPath);
        
        // Email configuration
        $emailConfig = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.gmail.com',
            'MAIL_PORT=587',
            'MAIL_USERNAME=' . $email,
            'MAIL_PASSWORD=' . $password,
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS=' . $email,
            'MAIL_FROM_NAME="MeloTech"'
        ];
        
        // Update or add email configuration
        foreach ($emailConfig as $config) {
            $key = explode('=', $config)[0];
            
            if (strpos($envContent, $key . '=') !== false) {
                // Update existing setting
                $envContent = preg_replace('/^' . preg_quote($key) . '=.*$/m', $config, $envContent);
            } else {
                // Add new setting
                $envContent .= "\n" . $config;
            }
        }
        
        // Write updated .env file
        File::put($envPath, $envContent);
        
        $this->info('✅ Gmail SMTP configuration updated!');
        $this->newLine();
        
        $this->warn('⚠️  Important: Make sure you are using an App Password, not your regular Gmail password!');
        $this->newLine();
        
        $this->info('📧 To generate an App Password:');
        $this->line('1. Go to your Google Account settings');
        $this->line('2. Security → 2-Step Verification → App passwords');
        $this->line('3. Generate a new app password for "Mail"');
        $this->line('4. Use that 16-character password (not your regular password)');
        $this->newLine();
        
        $this->info('🔄 Now run: php artisan config:cache');
        $this->info('🧪 Then test: php artisan test:email ' . $email);
    }
}
