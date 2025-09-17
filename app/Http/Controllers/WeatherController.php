<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\User;
use App\Models\CropGrowth;
use App\Services\WeatherService;
use App\Services\WeatherRecommendationService;
use App\Services\WatermelonRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Added for Http facade
use Illuminate\Support\Facades\Auth;

class WeatherController extends Controller
{
    protected $weatherService;
    protected $recommendationService;
    protected $watermelonRecommendationService;

    public function __construct(
        WeatherService $weatherService, 
        WeatherRecommendationService $recommendationService,
        WatermelonRecommendationService $watermelonRecommendationService
    ) {
        $this->weatherService = $weatherService;
        $this->recommendationService = $recommendationService;
        $this->watermelonRecommendationService = $watermelonRecommendationService;
    }

    /**
     * Get weather data for a specific farm
     */
    public function getFarmWeather(Request $request, $farmId): JsonResponse
    {
        try {
            $farm = Farm::where('id', $farmId)
                       ->where('user_id', Auth::id())
                       ->first();

            if (!$farm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farm not found or access denied'
                ], 404);
            }

            // Log the attempt for debugging
            Log::info('Weather request for farm', [
                'farm_id' => $farmId,
                'farm_name' => $farm->farm_name,
                'city' => $farm->city_municipality_name,
                'province' => $farm->province_name
            ]);

            // Get coordinates for the farm location
            $coordinates = $this->weatherService->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if (!$coordinates) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to get coordinates for farm location'
                ], 400);
            }

            // Get current weather
            $currentWeather = $this->weatherService->getCurrentWeather(
                $coordinates['lat'],
                $coordinates['lon']
            );

            if (!$currentWeather) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch current weather data'
                ], 500);
            }

            // Get weather forecast
            $forecast = $this->weatherService->getForecast(
                $coordinates['lat'],
                $coordinates['lon']
            );

            // Get weather alerts
            $alerts = $this->weatherService->getWeatherAlerts(
                $coordinates['lat'],
                $coordinates['lon']
            );

            // Get crop growth stage for watermelon-specific recommendations
            $cropGrowth = CropGrowth::where('farm_id', $farmId)->first();
            $growthStage = null;
            
            if ($cropGrowth) {
                $growthStage = $cropGrowth->current_stage;
                Log::info('Crop growth stage found', [
                    'farm_id' => $farmId,
                    'current_stage' => $growthStage,
                    'stage_progress' => $cropGrowth->stage_progress,
                    'overall_progress' => $cropGrowth->overall_progress
                ]);
            }

            // Generate AI-powered recommendations (watermelon-specific)
            try {
                $recommendations = $this->watermelonRecommendationService->generateRecommendations([
                    'current' => $currentWeather,
                    'forecast' => $forecast
                ], $growthStage);
                
                Log::info('Watermelon AI recommendations generated', [
                    'farm_id' => $farmId,
                    'growth_stage' => $growthStage,
                    'recommendations_count' => count($recommendations),
                    'recommendation_titles' => array_column($recommendations, 'title')
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to generate watermelon AI recommendations', [
                    'farm_id' => $farmId,
                    'growth_stage' => $growthStage,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Fallback to general recommendations if watermelon-specific fails
                try {
                    $recommendations = $this->recommendationService->generateRecommendations([
                        'current' => $currentWeather,
                        'forecast' => $forecast
                    ]);
                    
                    Log::info('Fallback to general recommendations', [
                        'farm_id' => $farmId,
                        'recommendations_count' => count($recommendations)
                    ]);
                } catch (\Exception $fallbackError) {
                    Log::error('Both watermelon and general recommendations failed', [
                        'farm_id' => $farmId,
                        'watermelon_error' => $e->getMessage(),
                        'general_error' => $fallbackError->getMessage()
                    ]);
                    
                    $recommendations = [];
                }
            }

            Log::info('Weather data fetched successfully', [
                'farm_id' => $farmId,
                'has_current' => !empty($currentWeather),
                'has_forecast' => !empty($forecast),
                'has_alerts' => !empty($alerts),
                'has_recommendations' => !empty($recommendations)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => $currentWeather,
                    'forecast' => $forecast,
                    'alerts' => $alerts,
                    'recommendations' => $recommendations,
                    'coordinates' => $coordinates,
                    'growth_stage' => $growthStage,
                    'crop_growth' => $cropGrowth ? [
                        'current_stage' => $cropGrowth->current_stage,
                        'stage_progress' => $cropGrowth->stage_progress,
                        'overall_progress' => $cropGrowth->overall_progress,
                        'days_elapsed' => $cropGrowth->getDaysElapsedAttribute(),
                        'stage_info' => $cropGrowth->getStageInfo()
                    ] : null,
                    'farm' => [
                        'id' => $farm->id,
                        'name' => $farm->farm_name,
                        'location' => $farm->city_municipality_name . ', ' . $farm->province_name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Weather service error', [
                'message' => $e->getMessage(),
                'farm_id' => $farmId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching weather data. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get weather data for user's primary farm
     */
    public function getUserFarmWeather(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $farm = $user->farms()->first();

            if (!$farm) {
                return response()->json([
                    'success' => false,
                    'message' => 'No farm found for this user'
                ], 404);
            }

            // Get coordinates for the farm location
            $coordinates = $this->weatherService->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if (!$coordinates) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to get coordinates for farm location'
                ], 400);
            }

            // Get current weather
            $currentWeather = $this->weatherService->getCurrentWeather(
                $coordinates['lat'],
                $coordinates['lon']
            );

            if (!$currentWeather) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch weather data'
                ], 500);
            }

            // Get weather forecast
            $forecast = $this->weatherService->getForecast(
                $coordinates['lat'],
                $coordinates['lon']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => $currentWeather,
                    'forecast' => $forecast,
                    'coordinates' => $coordinates,
                    'farm' => [
                        'name' => $farm->farm_name,
                        'location' => $farm->city_municipality_name . ', ' . $farm->province_name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching weather data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh weather data (bypass cache)
     */
    public function refreshWeather(Request $request, $farmId): JsonResponse
    {
        try {
            $farm = Farm::where('id', $farmId)
                       ->where('user_id', Auth::id())
                       ->first();

            if (!$farm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farm not found or access denied'
                ], 404);
            }

            // Clear cache for this location
            $coordinates = $this->weatherService->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if ($coordinates) {
                $cacheKey = "weather_{$coordinates['lat']}_{$coordinates['lon']}";
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                
                $forecastCacheKey = "forecast_{$coordinates['lat']}_{$coordinates['lon']}";
                \Illuminate\Support\Facades\Cache::forget($forecastCacheKey);
            }

            // Get fresh weather data
            return $this->getFarmWeather($request, $farmId);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while refreshing weather data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint for testing geocoding (only in debug mode)
     */
    public function debugGeocoding(Request $request): JsonResponse
    {
        if (!config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Debug endpoint only available in debug mode'
            ], 403);
        }

        $city = $request->get('city', 'Manila');
        $province = $request->get('province', 'Metro Manila');

        try {
            $coordinates = $this->weatherService->getCoordinates($city, $province);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'input' => [
                        'city' => $city,
                        'province' => $province
                    ],
                    'coordinates' => $coordinates
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geocoding failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint to check API key and basic connectivity
     */
    public function testConnection(): JsonResponse
    {
        try {
            $apiKey = config('services.openweathermap.api_key');
            
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key is not configured',
                    'debug_info' => [
                        'config_path' => 'config/services.php',
                        'env_variable' => 'OPENWEATHERMAP_API_KEY',
                        'current_value' => $apiKey
                    ]
                ], 400);
            }

            // Test a simple API call to Manila coordinates
            $testLat = 14.5995;
            $testLon = 120.9842;
            
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $testLat,
                'lon' => $testLon,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'API connection successful',
                    'debug_info' => [
                        'api_key_length' => strlen($apiKey),
                        'api_key_preview' => substr($apiKey, 0, 8) . '...',
                        'test_response_status' => $response->status(),
                        'test_response_body_length' => strlen($response->body())
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API call failed',
                    'debug_info' => [
                        'api_key_length' => strlen($apiKey),
                        'api_key_preview' => substr($apiKey, 0, 8) . '...',
                        'response_status' => $response->status(),
                        'response_body' => $response->body()
                    ]
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed with exception',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get historical weather data for trend analysis
     */
    public function getHistoricalWeather(Request $request, $farmId): JsonResponse
    {
        try {
            $farm = Farm::where('id', $farmId)
                       ->where('user_id', Auth::id())
                       ->first();

            if (!$farm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farm not found or access denied'
                ], 404);
            }

            $days = $request->get('days', 10); // Default to 10 days

            // Get coordinates for the farm location
            $coordinates = $this->weatherService->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if (!$coordinates) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to get coordinates for farm location'
                ], 400);
            }

            // Get historical weather data
            $historicalData = $this->weatherService->getHistoricalWeather(
                $coordinates['lat'],
                $coordinates['lon'],
                $days
            );

            if (!$historicalData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch historical weather data'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'historical' => $historicalData,
                    'coordinates' => $coordinates,
                    'farm' => [
                        'name' => $farm->farm_name,
                        'location' => $farm->city_municipality_name . ', ' . $farm->province_name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Historical weather service error', [
                'message' => $e->getMessage(),
                'farm_id' => $farmId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching historical weather data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Show the dedicated weather information page
     */
    public function showWeatherPage()
    {
        /** @var User $user */
        $user = Auth::user();
        $farms = $user->farms;
        
        return view('user.weather.index', compact('farms'));
    }


    /**
     * Get weather information for API requests
     */
    public function getWeatherInfo(Request $request): JsonResponse
    {
        try {
            $location = $request->get('location');
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location parameter is required'
                ], 400);
            }

            // For now, return a simple response since the location parsing is complex
            // This can be enhanced later with proper location parsing
            $html = '<div class="weather-info"><p>Weather information for: ' . htmlspecialchars($location) . '</p><p class="text-muted">Weather data will be available soon.</p></div>';

            return response()->json([
                'success' => true,
                'html' => $html,
                'location' => $location
            ]);

        } catch (\Exception $e) {
            Log::error('Weather API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch weather information'
            ], 500);
        }
    }
}
