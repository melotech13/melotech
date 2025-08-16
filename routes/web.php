<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
});
