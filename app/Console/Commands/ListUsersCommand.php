<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'email_verified_at', 'email_verification_code', 'email_verification_code_expires_at']);
        
        if ($users->isEmpty()) {
            $this->info('No users found in the database.');
            return;
        }
        
        $this->info('Users in database:');
        $this->newLine();
        
        foreach ($users as $user) {
            $this->line("ID: {$user->id}");
            $this->line("Name: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("Email Verified: " . ($user->email_verified_at ? 'Yes (' . $user->email_verified_at . ')' : 'No'));
            $this->line("Verification Code: " . ($user->email_verification_code ?: 'Not set'));
            $this->line("Code Expires: " . ($user->email_verification_code_expires_at ?: 'Not set'));
            $this->line("Is Email Verified: " . ($user->isEmailVerified() ? 'Yes' : 'No'));
            $this->newLine();
        }
    }
}
