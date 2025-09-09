<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications (older than 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting notification cleanup...');
        
        try {
            NotificationService::cleanupOldNotifications();
            $this->info('Notification cleanup completed successfully.');
        } catch (\Exception $e) {
            $this->error('Notification cleanup failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
