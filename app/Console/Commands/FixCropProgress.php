<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Farm;
use Carbon\Carbon;

class FixCropProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crop:fix-progress {--farm-id= : Specific farm ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix crop progress for farms based on planting dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting crop progress fix...');
        
        $query = Farm::with('cropGrowth');
        
        if ($farmId = $this->option('farm-id')) {
            $query->where('id', $farmId);
            $this->info("Targeting specific farm ID: {$farmId}");
        }
        
        $farms = $query->get();
        
        if ($farms->isEmpty()) {
            $this->warn('No farms found to process.');
            return;
        }
        
        $this->info("Found {$farms->count()} farm(s) to process.");
        
        $bar = $this->output->createProgressBar($farms->count());
        $bar->start();
        
        $fixedCount = 0;
        $errors = [];
        
        foreach ($farms as $farm) {
            try {
                if ($this->fixFarmProgress($farm)) {
                    $fixedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Farm {$farm->id} ({$farm->farm_name}): " . $e->getMessage();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        if ($fixedCount > 0) {
            $this->info("✅ Successfully fixed {$fixedCount} farm(s).");
        } else {
            $this->warn("⚠️ No farms needed fixing.");
        }
        
        if (!empty($errors)) {
            $this->error("❌ Errors encountered:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        $this->info('🎯 Crop progress fix completed!');
    }
    
    /**
     * Fix progress for a specific farm
     */
    private function fixFarmProgress(Farm $farm): bool
    {
        $plantingDate = $farm->planting_date;
        $currentDate = now();
        
        // Skip if planting date is in the future
        if ($plantingDate->isAfter($currentDate)) {
            return false;
        }
        
        $cropGrowth = $farm->cropGrowth;
        
        // If no crop growth record exists, create one
        if (!$cropGrowth) {
            $farm->getOrCreateCropGrowth();
            $this->line("  Created crop growth record for farm: {$farm->farm_name}");
            return true;
        }
        
        // If progress is already correct, skip
        if ($cropGrowth->stage_progress > 0 && $cropGrowth->current_stage !== 'harvest') {
            // Force update anyway to ensure accuracy
            $this->line("  Forcing update for farm: {$farm->farm_name}");
        }
        
        // Calculate correct progress based on time elapsed
        $daysElapsed = $plantingDate->diffInDays($currentDate);
        
        // Define stage durations
        $stageDurations = [
            'seedling' => 20,
            'vegetative' => 25,
            'flowering' => 15,
            'fruiting' => 20
        ];
        
        $currentStage = 'seedling';
        $stageProgress = 0;
        $daysRemaining = $daysElapsed;
        
        // Calculate which stage we should be in and progress within that stage
        foreach ($stageDurations as $stage => $duration) {
            if ($daysRemaining >= $duration) {
                $daysRemaining -= $duration;
                $currentStage = $this->getNextStage($stage);
            } else {
                $stageProgress = ($daysRemaining / $duration) * 100;
                break;
            }
        }
        
        // If we've passed all stages, we're ready for harvest
        $totalStageDays = array_sum($stageDurations);
        if ($daysElapsed >= $totalStageDays) {
            $currentStage = 'harvest';
            $stageProgress = 100;
        }
        
        // Calculate overall progress
        $overallProgress = $this->calculateOverallProgress($currentStage, $stageProgress);
        
        // Update the crop growth record
        $cropGrowth->update([
            'current_stage' => $currentStage,
            'stage_progress' => $stageProgress,
            'overall_progress' => $overallProgress,
            'last_updated' => now(),
        ]);
        
        $this->line("  Fixed farm {$farm->farm_name}: Stage {$currentStage}, Progress {$stageProgress}%, Overall {$overallProgress}%");
        
        return true;
    }
    
    /**
     * Calculate overall progress based on current stage and progress
     */
    private function calculateOverallProgress($stage, $stageProgress): float
    {
        $stages = ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'];
        $currentIndex = array_search($stage, $stages);
        
        if ($currentIndex === false) {
            return 0;
        }
        
        $totalStages = count($stages);
        $stageWeight = 100 / $totalStages;
        
        return ($currentIndex * $stageWeight) + ($stageProgress * $stageWeight / 100);
    }
    
    /**
     * Get the next stage
     */
    private function getNextStage($currentStage): string
    {
        $stages = ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'];
        $currentIndex = array_search($currentStage, $stages);
        
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            return $stages[$currentIndex + 1];
        }
        
        return $currentStage;
    }
}
