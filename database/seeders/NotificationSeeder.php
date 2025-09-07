<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user (assuming user ID 1 is admin)
        $adminUser = User::first();
        
        if (!$adminUser) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample notifications
        $notifications = [
            [
                'user_id' => $adminUser->id,
                'type' => 'system',
                'title' => 'System Maintenance Scheduled',
                'message' => 'Scheduled maintenance will occur on Sunday at 2:00 AM. Expected downtime: 30 minutes.',
                'read' => false,
                'action_url' => '/admin/settings',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'user',
                'title' => 'New User Registration',
                'message' => 'John Doe has registered a new account and created their first farm.',
                'read' => false,
                'action_url' => '/admin/users',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'farm',
                'title' => 'Farm Progress Update',
                'message' => 'Green Valley Farm has updated their crop progress to flowering stage.',
                'read' => true,
                'action_url' => '/admin/farms',
                'created_at' => Carbon::now()->subDay(),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'system',
                'title' => 'Database Backup Completed',
                'message' => 'Daily database backup has been completed successfully.',
                'read' => true,
                'action_url' => null,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'user',
                'title' => 'User Profile Updated',
                'message' => 'Jane Smith has updated their profile information.',
                'read' => true,
                'action_url' => '/admin/users',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'farm',
                'title' => 'New Farm Registration',
                'message' => 'Sunrise Farm has been registered by Mike Johnson.',
                'read' => true,
                'action_url' => '/admin/farms',
                'created_at' => Carbon::now()->subWeek(),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'system',
                'title' => 'Weather API Updated',
                'message' => 'Weather service has been updated with new data sources.',
                'read' => false,
                'action_url' => '/admin/settings',
                'created_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'user_id' => $adminUser->id,
                'type' => 'farm',
                'title' => 'Crop Analysis Complete',
                'message' => 'Photo analysis completed for 5 farms. Check results in farm management.',
                'read' => false,
                'action_url' => '/admin/farms',
                'created_at' => Carbon::now()->subMinutes(15),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
