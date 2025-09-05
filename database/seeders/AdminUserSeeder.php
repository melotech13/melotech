<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@melotech.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'System Administrator',
                'email' => 'admin@melotech.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@melotech.com');
            $this->command->info('Password: Admin@123');
            $this->command->warn('Please change the password after first login for security!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
