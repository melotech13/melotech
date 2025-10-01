<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PhotoAnalysis;

class ClearPhotoRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo-analyses:clear-recommendations {--delete-all : Delete all photo analysis records instead of clearing recommendations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all stored recommendations from photo analyses, or delete all analyses with --delete-all';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('delete-all')) {
            if (!$this->confirm('This will DELETE all photo analysis records. Continue?')) {
                $this->info('Aborted.');
                return 0;
            }
            $count = PhotoAnalysis::count();
            PhotoAnalysis::query()->delete();
            $this->info("Deleted {$count} photo analysis record(s).");
            return 0;
        }

        $this->info('Clearing recommendations from all photo analyses...');
        $updated = PhotoAnalysis::whereNotNull('recommendations')->update(['recommendations' => null]);
        $this->info("Cleared recommendations on {$updated} record(s).");
        return 0;
    }
}


