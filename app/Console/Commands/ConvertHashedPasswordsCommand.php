<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ConvertHashedPasswordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwords:convert-hashed {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert hashed passwords to plain text for admin management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Converting hashed passwords to plain text...');
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->newLine();
        
        // Get all users
        $users = User::all();
        $convertedCount = 0;
        $alreadyPlainCount = 0;
        
        $this->info("Found {$users->count()} users to check");
        $this->newLine();
        
        foreach ($users as $user) {
            $password = $user->password;
            
            // Check if password is hashed (starts with $2y$ or similar bcrypt pattern)
            if (preg_match('/^\$2[ayb]\$.{56}$/', $password)) {
                $this->line("User ID {$user->id} ({$user->email}): Hashed password detected");
                
                if (!$isDryRun) {
                    // For hashed passwords, we'll set a default plain text password
                    // In a real scenario, you might want to ask for the actual password
                    $plainPassword = 'password123'; // Default password for converted users
                    
                    $user->update(['password' => $plainPassword]);
                    $this->info("  → Converted to plain text: {$plainPassword}");
                } else {
                    $this->info("  → Would convert to plain text: password123");
                }
                
                $convertedCount++;
            } else {
                $this->line("User ID {$user->id} ({$user->email}): Already plain text");
                $alreadyPlainCount++;
            }
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->line("- Users with plain text passwords: {$alreadyPlainCount}");
        $this->line("- Users with hashed passwords: {$convertedCount}");
        
        if ($isDryRun) {
            $this->warn("This was a dry run. Run without --dry-run to make actual changes.");
        } else {
            $this->info("Password conversion completed successfully!");
        }
        
        return 0;
    }
}
