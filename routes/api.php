<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Analysis related routes
Route::prefix('analysis')->group(function () {
    // Get growth progress for a specific analysis
    Route::get('/{id}/growth-progress', [AnalysisController::class, 'getGrowthProgress'])
        ->where('id', '[0-9]+')
        ->name('api.analysis.growth-progress');
});

// Weather information route
Route::get('/weather', [WeatherController::class, 'getWeatherInfo'])
    ->name('api.weather.get');
