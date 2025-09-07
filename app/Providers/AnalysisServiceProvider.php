<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Analysis\GrowthAnalysisService;
use App\Services\Diagnosis\PhotoDiagnosisService;

class AnalysisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind new photo diagnosis service
        $this->app->singleton(PhotoDiagnosisService::class);

        // Keep growth analysis service
        $this->app->singleton(GrowthAnalysisService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}