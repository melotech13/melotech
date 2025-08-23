<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Added for Http facade

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Get weather data for a specific farm
     */
    public function getFarmWeather(Request $request, $farmId): JsonResponse
    {
        try {
            $farm = Farm::where('id', $farmId)
                       ->where('user_id', auth()->id())
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

            // Debug: Check if API key is loaded
            $apiKey = config('services.openweathermap.api_key');
            Log::info('API Key check', [
                'has_api_key' => !empty($apiKey),
                'api_key_length' => strlen($apiKey ?? ''),
                'api_key_preview' => $apiKey ? substr($apiKey, 0, 8) . '...' : 'null'
            ]);

            // Get coordinates for the farm location
            $coordinates = $this->weatherService->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if (!$coordinates) {
                Log::warning('Failed to get coordinates for farm location', [
                    'farm_id' => $farmId,
                    'city' => $farm->city_municipality_name,
                    'province' => $farm->province_name
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to get coordinates for farm location. Please check your farm location details.',
                    'debug_info' => [
                        'city' => $farm->city_municipality_name,
                        'province' => $farm->province_name,
                        'suggestion' => 'Try updating your farm location with standard Philippine city/province names'
                    ]
                ], 400);
            }

            Log::info('Coordinates obtained successfully', [
                'farm_id' => $farmId,
                'coordinates' => $coordinates
            ]);

            // Get current weather
            $currentWeather = $this->weatherService->getCurrentWeather(
                $coordinates['lat'],
                $coordinates['lon']
            );

            if (!$currentWeather) {
                Log::error('Failed to fetch current weather data', [
                    'farm_id' => $farmId,
                    'coordinates' => $coordinates,
                    'api_key_available' => !empty($apiKey)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch current weather data. Please try again later.',
                    'debug_info' => [
                        'coordinates' => $coordinates,
                        'suggestion' => 'Weather service may be temporarily unavailable',
                        'api_key_status' => !empty($apiKey) ? 'Available' : 'Missing or Invalid'
                    ]
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

            if (!$forecast) {
                Log::warning('Failed to fetch forecast data, but current weather is available', [
                    'farm_id' => $farmId,
                    'coordinates' => $coordinates
                ]);
                
                // Return current weather only if forecast fails
                return response()->json([
                    'success' => true,
                    'data' => [
                        'current' => $currentWeather,
                        'forecast' => null,
                        'alerts' => $alerts,
                        'coordinates' => $coordinates,
                        'farm' => [
                            'name' => $farm->farm_name,
                            'location' => $farm->city_municipality_name . ', ' . $farm->province_name
                        ]
                    ],
                    'message' => 'Current weather available, but forecast data is temporarily unavailable'
                ]);
            }

            Log::info('Weather data fetched successfully', [
                'farm_id' => $farmId,
                'has_current' => !empty($currentWeather),
                'has_forecast' => !empty($forecast),
                'has_alerts' => !empty($alerts)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => $currentWeather,
                    'forecast' => $forecast,
                    'alerts' => $alerts,
                    'coordinates' => $coordinates,
                    'farm' => [
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
            $user = auth()->user();
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
                       ->where('user_id', auth()->id())
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
                       ->where('user_id', auth()->id())
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
        $user = auth()->user();
        $farms = $user->farms;
        
        return view('weather.index', compact('farms'));
    }
}
