<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CropGrowthController;
use App\Http\Controllers\PhotoDiagnosisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController; // Added AdminController

use App\Models\User;

// Favicon routes
Route::get('/favicon.ico', function () {
    return response()->file(public_path('favicon.ico'));
});

Route::get('/favicon-{size}.png', function ($size) {
    $validSizes = ['16x16', '32x32'];
    if (in_array($size, $validSizes)) {
        return response()->file(public_path("favicon-{$size}.png"));
    }
    abort(404);
});

Route::get('/apple-touch-icon.png', function () {
    return response()->file(public_path('apple-touch-icon.png'));
});

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

// Email verification routes
Route::get('/verify-email', [AuthController::class, 'showVerification'])->name('verification.show');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');

// Location endpoints (PSGC proxy)
Route::get('/locations/provinces', [LocationController::class, 'provinces']);
Route::get('/locations/cities-municipalities', [LocationController::class, 'citiesMunicipalities']);
Route::get('/locations/barangays', [LocationController::class, 'barangays']);

// Preloaded locations JSON (fast client-side cascading)
Route::get('/locations.json', [LocationController::class, 'locationsJson'])->name('locations.json');



// Protected routes
Route::middleware(['auth', 'verified.email'])->group(function () {
	Route::get('/dashboard', function () {
		if (Auth::user() && Auth::user()->role === 'admin') {
			return redirect()->route('admin.dashboard');
		}
		
		// Get daily farming activity data for the last 7 days
		$dailyActivity = [];
		$user = Auth::user();
		
		for ($i = 6; $i >= 0; $i--) {
			$date = \Carbon\Carbon::now()->subDays($i);
			$startOfDay = $date->copy()->startOfDay();
			$endOfDay = $date->copy()->endOfDay();
			
			// Count user's farms created on this day
			$newFarms = $user->farms()
				->whereBetween('created_at', [$startOfDay, $endOfDay])
				->count();
			
			// Count user's photo analyses created on this day
			$newAnalyses = \App\Models\PhotoAnalysis::where('user_id', $user->id)
				->whereBetween('created_at', [$startOfDay, $endOfDay])
				->count();
			
			// Count user's progress updates created on this day
			$newUpdates = \App\Models\CropProgressUpdate::where('user_id', $user->id)
				->whereBetween('created_at', [$startOfDay, $endOfDay])
				->count();
			
			$dailyActivity[] = [
				'date' => $date->format('M d'),
				'date_full' => $date->format('Y-m-d'),
				'new_farms' => $newFarms,
				'new_analyses' => $newAnalyses,
				'new_updates' => $newUpdates,
				'total_activity' => $newFarms + $newAnalyses + $newUpdates,
			];
		}
		
		return view('user.dashboard.dashboard', compact('dailyActivity'));
	})->name('dashboard');
	
	
	
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
	
	// Profile routes
	Route::get('/profile', [ProfileController::class, 'index'])->name('profile.settings');
	Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
	
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
			 Route::get('/crop-progress/export-pdf', [App\Http\Controllers\CropProgressController::class, 'exportPDF'])->name('crop-progress.export-pdf');
Route::get('/crop-progress/print', [App\Http\Controllers\CropProgressController::class, 'printReport'])->name('crop-progress.print');
Route::get('/crop-progress/print-test', function() {
	return view('user.crop-progress.pdf-report', [
		'farm' => (object)[
			'farm_name' => 'Test Farm',
			'watermelon_variety' => 'Test Variety',
			'farm_size' => '1.0',
			'province' => 'Test Province',
			'municipality' => 'Test Municipality'
		],
		'progressUpdates' => collect([]),
		'exportDate' => now()->format('M d, Y H:i:s')
	]);
})->name('crop-progress.print-test');
			 Route::get('/crop-progress/{id}/recommendations', [App\Http\Controllers\CropProgressController::class, 'getRecommendations'])->name('crop-progress.recommendations');
			 Route::get('/crop-progress/{id}/summary', [App\Http\Controllers\CropProgressController::class, 'getSummary'])->name('crop-progress.summary');
	
	// Photo Diagnosis routes
	Route::get('/photo-diagnosis', [PhotoDiagnosisController::class, 'index'])->name('photo-diagnosis.index');
	Route::get('/photo-diagnosis/create', [PhotoDiagnosisController::class, 'create'])->name('photo-diagnosis.create');
	Route::post('/photo-diagnosis', [PhotoDiagnosisController::class, 'store'])->name('photo-diagnosis.store');
	
	// Parameterized routes (must come after specific routes)
	Route::get('/photo-diagnosis/{photoAnalysis}', [PhotoDiagnosisController::class, 'show'])->name('photo-diagnosis.show');
	Route::delete('/photo-diagnosis/{photoAnalysis}', [PhotoDiagnosisController::class, 'destroy'])->name('photo-diagnosis.destroy');
	
	
	// Debug route for photo upload testing
	Route::post('/photo-diagnosis/debug', function(Request $request) {
		try {
			$user = Auth::user();
			Log::info('Debug upload attempt', [
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
			Log::error('Debug upload error: ' . $e->getMessage());
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

// Admin routes (protected by admin middleware)
Route::middleware(['auth', 'verified.email', 'admin'])->prefix('admin')->name('admin.')->group(function () {
	Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
	Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
	
	// User management
	Route::get('/users', [AdminController::class, 'users'])->name('users.index');
	Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
	Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
	
	// User management exports (must be before parameterized routes)
	Route::get('/users/export-pdf', [AdminController::class, 'exportUsersPDF'])->name('users.export-pdf');
	Route::get('/users/print', [AdminController::class, 'printUsers'])->name('users.print');
	
	// User parameterized routes (must be after specific routes)
	Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
	Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
	Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
	Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
	Route::post('/users/{user}/convert-password', [AdminController::class, 'convertPassword'])->name('users.convert-password');
	
	// Farm management exports (must be before parameterized routes)
	Route::get('/farms/export-pdf', [AdminController::class, 'exportFarmsPDF'])->name('farms.export-pdf');
	Route::get('/farms/print', [AdminController::class, 'printFarms'])->name('farms.print');
	
	// Farm management
	Route::get('/farms', [AdminController::class, 'farms'])->name('farms.index');
	Route::get('/farms/{farm}', [AdminController::class, 'showFarm'])->name('farms.show');
	Route::get('/farms/{farm}/edit', [AdminController::class, 'editFarm'])->name('farms.edit');
	Route::put('/farms/{farm}', [AdminController::class, 'updateFarm'])->name('farms.update');
	Route::delete('/farms/{farm}', [AdminController::class, 'deleteFarm'])->name('farms.delete');
	
	// Admin settings
	Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
	Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
	Route::put('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');
	
	// Notifications
	Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
	Route::post('/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
	Route::get('/notifications/unread-count', [AdminController::class, 'getUnreadCount'])->name('notifications.unread-count');
	Route::get('/notifications/dropdown', [AdminController::class, 'getDropdownNotifications'])->name('notifications.dropdown');
});
