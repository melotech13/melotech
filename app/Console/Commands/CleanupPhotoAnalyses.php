<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PhotoAnalysis;
use Illuminate\Support\Facades\Storage;

class CleanupPhotoAnalyses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo-analyses:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up photo analysis records with empty or null values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting photo analysis cleanup...');

        // Find records with empty or null values
        $emptyRecords = PhotoAnalysis::where(function($query) {
            $query->whereNull('identified_type')
                  ->orWhere('identified_type', '')
                  ->orWhereNull('photo_path')
                  ->orWhere('photo_path', '')
                  ->orWhereNull('confidence_score');
        })->get();

        if ($emptyRecords->count() === 0) {
            $this->info('No problematic records found. Database is clean!');
            return 0;
        }

        $this->warn("Found {$emptyRecords->count()} record(s) with empty or null values:");

        foreach ($emptyRecords as $record) {
            $this->line("ID: {$record->id}, User: {$record->user_id}, Type: {$record->identified_type}, Confidence: {$record->confidence_score}");
        }

        if ($this->confirm('Do you want to delete these problematic records?')) {
            $deletedCount = $emptyRecords->count();
            $emptyRecords->each(function($record) {
                // Also delete the associated photo file if it exists
                if ($record->photo_path && Storage::disk('public')->exists($record->photo_path)) {
                    Storage::disk('public')->delete($record->photo_path);
                }
                $record->delete();
            });

            $this->info("Successfully deleted {$deletedCount} problematic record(s).");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
