<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;

class SetupEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:setup {--test-email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup and test email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 MeloTech Email Configuration Setup');
        $this->newLine();

        // Check current configuration
        $this->info('Current Configuration:');
        $this->line('Mail Driver: ' . config('mail.default'));
        $this->line('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->line('SMTP Port: ' . config('mail.mailers.smtp.port'));
        $this->line('From Address: ' . config('mail.from.address'));
        $this->line('From Name: ' . config('mail.from.name'));
        $this->newLine();

        // Test email if provided
        $testEmail = $this->option('test-email');
        if ($testEmail) {
            $this->info("Testing email to: {$testEmail}");
            
            $emailService = app(EmailService::class);
            $result = $emailService->testEmailConfiguration($testEmail);
            
            if ($result['success']) {
                $this->info('✅ ' . $result['message']);
                if (isset($result['recommendation'])) {
                    $this->warn('💡 ' . $result['recommendation']);
                }
            } else {
                $this->error('❌ ' . $result['message']);
            }
        }

        $this->newLine();
        $this->info('📧 Email Configuration Options:');
        $this->newLine();

        // Development setup
        $this->line('1. Development (Current - Log Driver):');
        $this->line('   - Emails are written to storage/logs/laravel.log');
        $this->line('   - Good for testing without real email sending');
        $this->line('   - To use: No changes needed');
        $this->newLine();

        // Gmail SMTP setup
        $this->line('2. Gmail SMTP (Recommended for production):');
        $this->line('   Add these to your .env file:');
        $this->line('   MAIL_MAILER=smtp');
        $this->line('   MAIL_HOST=smtp.gmail.com');
        $this->line('   MAIL_PORT=587');
        $this->line('   MAIL_USERNAME=your-email@gmail.com');
        $this->line('   MAIL_PASSWORD=your-app-password');
        $this->line('   MAIL_ENCRYPTION=tls');
        $this->line('   MAIL_FROM_ADDRESS=your-email@gmail.com');
        $this->line('   MAIL_FROM_NAME="MeloTech"');
        $this->newLine();

        // Other SMTP providers
        $this->line('3. Other SMTP Providers:');
        $this->line('   - Mailgun: Use MAILGUN_* environment variables');
        $this->line('   - SendGrid: Use SENDGRID_* environment variables');
        $this->line('   - Amazon SES: Use AWS_* environment variables');
        $this->newLine();

        // Fallback mechanism
        $this->line('4. Fallback Mechanism:');
        $this->line('   - If email sending fails, verification codes are stored in cache');
        $this->line('   - Users can still verify their email using the displayed code');
        $this->line('   - Codes expire after 15 minutes for security');
        $this->newLine();

        $this->info('🚀 To test email sending:');
        $this->line('php artisan email:setup --test-email=your-email@example.com');
        $this->newLine();

        $this->info('📝 To clear configuration cache:');
        $this->line('php artisan config:cache');
    }
}
