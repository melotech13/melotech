<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Farm;
use App\Models\PhotoAnalysis;
use App\Models\CropProgressUpdate;
use App\Observers\UserObserver;
use App\Observers\FarmObserver;
use App\Observers\PhotoAnalysisObserver;
use App\Observers\CropProgressUpdateObserver;
use App\Http\View\Composers\AdminLayoutComposer;
use App\Providers\PlainTextUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom plain text user provider
        Auth::provider('plaintext', function ($app, $config) {
            return new PlainTextUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for automatic notifications
        User::observe(UserObserver::class);
        Farm::observe(FarmObserver::class);
        PhotoAnalysis::observe(PhotoAnalysisObserver::class);
        CropProgressUpdate::observe(CropProgressUpdateObserver::class);
        
        // Register view composers
        View::composer('layouts.admin', AdminLayoutComposer::class);
    }
}
