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
        $admins = [
            [
                'name' => 'Admin1 Administrator',
                'email' => 'admin1@melotech.com',
                'phone' => '+639171234500',
                'password' => 'admin123'
            ],
            [
                'name' => 'Admin2 Administrator',
                'email' => 'admin2@melotech.com',
                'phone' => '+639171234501',
                'password' => 'admin123'
            ],
            [
                'name' => 'Admin3 Administrator',
                'email' => 'admin3@melotech.com',
                'phone' => '+639171234502',
                'password' => 'admin123'
            ],
            [
                'name' => 'Admin4 Administrator',
                'email' => 'admin4@melotech.com',
                'phone' => '+639171234503',
                'password' => 'admin123'
            ],
            [
                'name' => 'Admin5 Administrator',
                'email' => 'admin5@melotech.com',
                'phone' => '+639171234504',
                'password' => 'admin123'
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'role' => 'admin',
                    'phone' => $admin['phone'],
                    'email_verified_at' => now(),
                    'password' => $admin['password'], // Store as plain text
                ]
            );
        }

        $this->command->info('5 admin users created successfully.');
    }
}
