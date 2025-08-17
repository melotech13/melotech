<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Location endpoints (PSGC proxy)
Route::get('/locations/provinces', [LocationController::class, 'provinces']);
Route::get('/locations/cities-municipalities', [LocationController::class, 'citiesMunicipalities']);
Route::get('/locations/barangays', [LocationController::class, 'barangays']);

// Preloaded locations JSON (fast client-side cascading)
Route::get('/locations.json', [LocationController::class, 'locationsJson'])->name('locations.json');



// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Weather routes
    Route::get('/weather/farm/{farmId}', [App\Http\Controllers\WeatherController::class, 'getFarmWeather'])->name('weather.farm');
    Route::get('/weather/user-farm', [App\Http\Controllers\WeatherController::class, 'getUserFarmWeather'])->name('weather.user-farm');
    Route::post('/weather/refresh/{farmId}', [App\Http\Controllers\WeatherController::class, 'refreshWeather'])->name('weather.refresh');
    Route::get('/weather/historical/{farmId}', [App\Http\Controllers\WeatherController::class, 'getHistoricalWeather'])->name('weather.historical');
    Route::get('/weather/test-connection', [App\Http\Controllers\WeatherController::class, 'testConnection'])->name('weather.test');
    
    // Debug route (only in debug mode)
    if (config('app.debug')) {
        Route::get('/weather/debug/geocoding', [App\Http\Controllers\WeatherController::class, 'debugGeocoding'])->name('weather.debug.geocoding');
    }
});
