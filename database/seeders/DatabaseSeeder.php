<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\NotificationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed an initial admin user
        $this->call(AdminUserSeeder::class);

        // Seed admin and user accounts with farms
        $this->call(UsersSeeder::class);

        // Seed sample notifications
        $this->call(NotificationSeeder::class);
    }
}
