<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CropGrowthController;
use App\Http\Controllers\PhotoDiagnosisController;

use App\Models\User;

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
    Route::get('/weather/farm/{farmId}/refresh', [App\Http\Controllers\WeatherController::class, 'refreshWeather'])->name('weather.refresh');
    Route::get('/weather/historical/{farmId}', [App\Http\Controllers\WeatherController::class, 'getHistoricalWeather'])->name('weather.historical');
    Route::get('/weather/test-connection', [App\Http\Controllers\WeatherController::class, 'testConnection'])->name('weather.test');
    Route::get('/weather', [App\Http\Controllers\WeatherController::class, 'showWeatherPage'])->name('weather.index');
    
    // Crop Growth routes
    Route::get('/crop-growth', [CropGrowthController::class, 'index'])->name('crop-growth.index');
    Route::post('/crop-growth/farm', [CropGrowthController::class, 'store'])->name('crop-growth.store');
    Route::post('/crop-growth/farm/{farm}/progress', [CropGrowthController::class, 'updateProgress'])->name('crop-growth.progress');
    Route::post('/crop-growth/farm/{farm}/advance', [CropGrowthController::class, 'advanceStage'])->name('crop-growth.advance');
    Route::post('/crop-growth/farm/{farm}/quick-update', [CropGrowthController::class, 'quickUpdate'])->name('crop-growth.quick-update');
    Route::get('/crop-growth/dashboard-data', [CropGrowthController::class, 'getDashboardData'])->name('crop-growth.dashboard-data');
    Route::post('/crop-growth/farm/{farm}/force-update', [CropGrowthController::class, 'forceUpdateProgress'])->name('crop-growth.force-update');
    
                 // Crop Progress Update routes
             Route::get('/crop-progress', [App\Http\Controllers\CropProgressController::class, 'index'])->name('crop-progress.index');
             Route::get('/crop-progress/questions', [App\Http\Controllers\CropProgressController::class, 'showQuestions'])->name('crop-progress.questions');

             Route::post('/crop-progress/questions', [App\Http\Controllers\CropProgressController::class, 'storeQuestions'])->name('crop-progress.store-questions');

             Route::get('/crop-progress/export', [App\Http\Controllers\CropProgressController::class, 'exportProgress'])->name('crop-progress.export');
             Route::get('/crop-progress/export/{id}', [App\Http\Controllers\CropProgressController::class, 'exportSingleUpdate'])->name('crop-progress.export-single');
             Route::get('/crop-progress/{id}/recommendations', [App\Http\Controllers\CropProgressController::class, 'getRecommendations'])->name('crop-progress.recommendations');
    
    // Photo Diagnosis routes
    Route::get('/photo-diagnosis', [PhotoDiagnosisController::class, 'index'])->name('photo-diagnosis.index');
    Route::get('/photo-diagnosis/create', [PhotoDiagnosisController::class, 'create'])->name('photo-diagnosis.create');
    Route::post('/photo-diagnosis', [PhotoDiagnosisController::class, 'store'])->name('photo-diagnosis.store');
    Route::get('/photo-diagnosis/{photoAnalysis}', [PhotoDiagnosisController::class, 'show'])->name('photo-diagnosis.show');
    
    // Debug route for photo upload testing
    Route::post('/photo-diagnosis/debug', function(Request $request) {
        try {
            $user = Auth::user();
            \Log::info('Debug upload attempt', [
                'user_id' => $user->id ?? 'not authenticated',
                'has_file' => $request->hasFile('photo'),
                'analysis_type' => $request->get('analysis_type'),
                'file_info' => $request->hasFile('photo') ? [
                    'size' => $request->file('photo')->getSize(),
                    'mime' => $request->file('photo')->getMimeType(),
                    'extension' => $request->file('photo')->getClientOriginalExtension(),
                ] : 'No file'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Debug info logged',
                'user_authenticated' => !!$user,
                'has_file' => $request->hasFile('photo'),
                'analysis_type' => $request->get('analysis_type')
            ]);
        } catch (\Exception $e) {
            \Log::error('Debug upload error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('photo-diagnosis.debug');
    
    // Debug route for testing crop data
    Route::get('/debug/crop-data', function() {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $farms = $user->farms()->with('cropGrowth')->get();
        $debugData = [];
        
        foreach ($farms as $farm) {
            $cropGrowth = $farm->getOrCreateCropGrowth();
            $debugData[] = [
                'farm_id' => $farm->id,
                'farm_name' => $farm->farm_name,
                'planting_date' => $farm->planting_date,
                'crop_growth_id' => $cropGrowth->id,
                'current_stage' => $cropGrowth->current_stage,
                'stage_progress' => $cropGrowth->stage_progress,
                'overall_progress' => $cropGrowth->overall_progress,
                'has_nutrient_predictions' => method_exists(app(CropGrowthController::class), 'getNutrientPredictions'),
                'has_harvest_countdown' => method_exists(app(CropGrowthController::class), 'getHarvestCountdown'),
            ];
        }
        
        return response()->json([
            'success' => true,
            'debug_data' => $debugData,
            'user_id' => $user->id,
            'farms_count' => $farms->count()
        ]);
    })->name('debug.crop-data');
    
    // Test the dashboard data endpoint directly
    Route::get('/test/dashboard-data', function() {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        try {
            $controller = app(CropGrowthController::class);
            $response = $controller->getDashboardData();
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.dashboard-data');
    
    // Simple test endpoint for crop insights
    Route::get('/test/crop-insights', function() {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $farms = $user->farms()->with('cropGrowth')->first();
        if (!$farms) {
            return response()->json(['error' => 'No farms found']);
        }
        
        $cropGrowth = $farms->getOrCreateCropGrowth();
        
        // Test basic data structure
        $testData = [
            'farm_id' => $farms->id,
            'farm_name' => $farms->farm_name,
            'current_stage' => $cropGrowth->current_stage,
            'stage_name' => 'Test Stage',
            'nutrient_predictions' => [
                'nitrogen' => 'Test Nitrogen',
                'phosphorus' => 'Test Phosphorus',
                'potassium' => 'Test Potassium',
                'recommendations' => ['Test recommendation']
            ],
            'harvest_countdown' => [
                'status' => 'test',
                'message' => 'Test message',
                'days' => 10,
                'color' => 'primary',
                'icon' => 'fas fa-test'
            ]
        ];
        
        return response()->json([
            'success' => true,
            'test_data' => $testData
        ]);
    })->name('test.crop-insights');
    
    // Debug route (only in debug mode)
if (config('app.debug')) {
    Route::get('/weather/debug/geocoding', [App\Http\Controllers\WeatherController::class, 'debugGeocoding'])->name('weather.debug.geocoding');

}
});
