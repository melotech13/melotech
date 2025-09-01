<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Farm;
use App\Models\CropProgressUpdate;
use Carbon\Carbon;

class CheckCropProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crop:check-status {--user-id= : Check specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check crop progress update status for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $users = User::where('id', $userId)->get();
        } else {
            $users = User::all();
        }
        
        $this->info('🔍 Checking crop progress status...');
        $this->newLine();
        
        foreach ($users as $user) {
            $this->info("👤 User: {$user->name} (ID: {$user->id})");
            
            $farms = $user->farms;
            if ($farms->isEmpty()) {
                $this->warn("  ❌ No farms found for this user");
                continue;
            }
            
            foreach ($farms as $farm) {
                $this->info("  🚜 Farm: {$farm->farm_name} (ID: {$farm->id})");
                $this->info("  📅 Planting Date: {$farm->planting_date->format('Y-m-d')}");
                
                // Check last update
                $lastUpdate = CropProgressUpdate::where('user_id', $user->id)
                    ->where('farm_id', $farm->id)
                    ->where('status', 'completed')
                    ->latest('update_date')
                    ->first();
                
                if ($lastUpdate) {
                    $this->info("  📝 Last Update: {$lastUpdate->update_date->format('Y-m-d H:i:s')}");
                    $this->info("  🆔 Session ID: {$lastUpdate->session_id}");
                    
                    // Calculate next update date
                    $nextUpdateDate = $lastUpdate->update_date->startOfDay()->addDays(6);
                    $this->info("  📅 Next Update Date: {$nextUpdateDate->format('Y-m-d H:i:s')}");
                    
                    // Check if can access now
                    $canAccess = CropProgressUpdate::canAccessNewQuestions($user, $farm);
                    $this->info("  🔓 Can Access New Questions: " . ($canAccess ? '✅ YES' : '❌ NO'));
                    
                    // Show current date comparison
                    $currentDate = Carbon::now()->startOfDay();
                    $lastUpdateDate = $lastUpdate->update_date->startOfDay();
                    $daysSinceLastUpdate = $currentDate->diffInDays($lastUpdateDate, false);
                    
                    $this->info("  📊 Days Since Last Update: {$daysSinceLastUpdate}");
                    $this->info("  🕐 Current Date: {$currentDate->format('Y-m-d H:i:s')}");
                    
                    if ($daysSinceLastUpdate < 0) {
                        $this->info("  ✅ Current date is AFTER last update date");
                        $this->info("  📈 Days passed: " . abs($daysSinceLastUpdate));
                    } else {
                        $this->info("  ⏳ Current date is BEFORE last update date");
                        $this->info("  ⏰ Days remaining: {$daysSinceLastUpdate}");
                    }
                    
                } else {
                    $this->info("  📝 No previous updates found");
                    
                    // Check if can access based on planting date using the model method
                    $canAccess = CropProgressUpdate::canAccessNewQuestions($user, $farm);
                    $this->info("  🔓 Can Access New Questions: " . ($canAccess ? '✅ YES' : '❌ NO'));
                    
                    // Show detailed calculation for debugging
                    $plantingDate = $farm->planting_date->startOfDay();
                    $currentDate = Carbon::now()->startOfDay();
                    $daysSincePlanting = $currentDate->diffInDays($plantingDate, false);
                    
                    if ($daysSincePlanting < 0) {
                        $this->info("  📊 Days Since Planting: " . abs($daysSincePlanting) . " (current date is after planting)");
                    } else {
                        $this->info("  📊 Days Since Planting: {$daysSincePlanting} (current date is before planting)");
                    }
                }
                
                $this->newLine();
            }
        }
        
        $this->info('🎯 Status check completed!');
    }
}
