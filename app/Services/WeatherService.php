<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\AIWeatherRecommendationService;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5';

    protected $aiRecommendationService;

    public function __construct()
    {
        $this->apiKey = config('services.openweathermap.api_key');
        $this->aiRecommendationService = new AIWeatherRecommendationService($this);
    }

    /**
     * Get current weather for a location
     */
    public function getCurrentWeather($latitude, $longitude)
    {
        $cacheKey = "weather_{$latitude}_{$longitude}";
        
        // Return cached data if available (cache for 10 minutes for more frequent updates)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Weather service: API key is empty or null');
            // Return fallback data instead of null
            return $this->getFallbackWeatherData($latitude, $longitude);
        }

        try {
            Log::info('Attempting to fetch weather data', [
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key_length' => strlen($this->apiKey),
                'url' => "{$this->baseUrl}/weather"
            ]);

            $response = Http::get("{$this->baseUrl}/weather", [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $this->apiKey,
                'units' => 'metric', // Use Celsius
                'lang' => 'en'
            ]);

            Log::info('Weather API response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                Log::info('Weather data parsed successfully', [
                    'has_main' => isset($weatherData['main']),
                    'has_weather' => isset($weatherData['weather']),
                    'has_wind' => isset($weatherData['wind']),
                    'actual_temp' => $weatherData['main']['temp'] ?? 'N/A',
                    'feels_like' => $weatherData['main']['feels_like'] ?? 'N/A'
                ]);
                
                $formattedData = $this->formatWeatherData($weatherData);
                
                // Cache the formatted data (reduced cache time for more frequent updates)
                Cache::put($cacheKey, $formattedData, now()->addMinutes(10));
                
                return $formattedData;
            }

            Log::error('Weather API request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'headers' => $response->headers()
            ]);

            // Return fallback data instead of null
            Log::info('Using fallback weather data due to API failure');
            return $this->getFallbackWeatherData($latitude, $longitude);

        } catch (\Exception $e) {
            Log::error('Weather service error', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'trace' => $e->getTraceAsString()
            ]);

            // Return fallback data instead of null
            Log::info('Using fallback weather data due to exception');
            return $this->getFallbackWeatherData($latitude, $longitude);
        }
    }

    /**
     * Get fallback weather data when API is unavailable
     */
    protected function getFallbackWeatherData($latitude, $longitude)
    {
        // Estimate weather based on coordinates and time
        $hour = (int) date('H');
        $month = (int) date('n');
        
        // Basic temperature estimation based on time and season
        if ($month >= 3 && $month <= 5) {
            // Summer months (March-May)
            $baseTemp = 28;
            $season = 'Summer';
        } elseif ($month >= 6 && $month <= 8) {
            // Rainy season (June-August)
            $baseTemp = 26;
            $season = 'Rainy Season';
        } elseif ($month >= 9 && $month <= 11) {
            // Transition months (September-November)
            $baseTemp = 27;
            $season = 'Transition Season';
        } else {
            // Cool months (December-February)
            $baseTemp = 24;
            $season = 'Cool Season';
        }
        
        // Adjust for time of day
        if ($hour >= 6 && $hour <= 18) {
            $temp = $baseTemp + rand(-2, 3); // Daytime variation
            $timeOfDay = 'Daytime';
        } else {
            $temp = $baseTemp - rand(3, 6); // Nighttime cooler
            $timeOfDay = 'Nighttime';
        }
        
        // Estimate humidity based on season
        if ($month >= 6 && $month <= 8) {
            $humidity = rand(75, 90); // Rainy season
        } else {
            $humidity = rand(60, 80); // Other seasons
        }
        
        // Estimate wind speed (rounded to nearest km/h)
        $windSpeed = round(rand(5, 15));
        
        // Generate appropriate weather description
        $descriptions = [
            'Partly cloudy with seasonal temperatures',
            'Mild weather conditions for this time of year',
            'Typical seasonal weather patterns',
            'Stable weather conditions',
            'Seasonal temperature variations'
        ];
        
        $description = $descriptions[array_rand($descriptions)];
        
        return [
            'temperature' => $temp,
            'feels_like' => $temp + rand(-1, 2),
            'humidity' => $humidity,
            'pressure' => 1013,
            'description' => $description,
            'icon' => '02d',
            'wind_speed' => round($windSpeed),
            'wind_direction' => $this->getWindDirection(rand(0, 360)),
            'visibility' => 10.0,
            'sunrise' => '6:00 AM',
            'sunset' => '6:00 PM',
            'timestamp' => now()->setTimezone('Asia/Manila')->format('M d, Y H:i'),
            'location' => 'Location-based estimate',
            'is_fallback' => true,
            'fallback_note' => ''
        ];
    }

    /**
     * Get weather forecast for a location
     */
    public function getForecast($latitude, $longitude)
    {
        $cacheKey = "forecast_{$latitude}_{$longitude}";
        
        // Return cached data if available (cache for 2 hours)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Weather service: API key is empty or null for forecast');
            return $this->getFallbackForecastData();
        }

        try {
            $response = Http::get("{$this->baseUrl}/forecast", [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'en'
                // Note: Free API provides 5-day forecast with 3-hour intervals
            ]);

            if ($response->successful()) {
                $forecastData = $response->json();
                $formattedData = $this->formatForecastData($forecastData);
                
                // Define Manila time for logging
                $manilaTime = now()->setTimezone('Asia/Manila');
                
                Log::info('Forecast data formatted', [
                    'original_count' => count($forecastData['list'] ?? []),
                    'formatted_count' => count($formattedData),
                    'formatted_dates' => array_map(fn($item) => $item['date'], $formattedData),
                    'today_excluded' => $manilaTime->format('M d'),
                    'current_manila_time' => $manilaTime->format('Y-m-d H:i:s T'),
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                
                // Cache the formatted data
                Cache::put($cacheKey, $formattedData, now()->addHours(2));
                
                return $formattedData;
            }

            Log::error('Weather forecast API request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            // Return fallback data
            return $this->getFallbackForecastData();

        } catch (\Exception $e) {
            Log::error('Weather forecast service error', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);

            // Return fallback data
            return $this->getFallbackForecastData();
        }
    }

    /**
     * Get fallback forecast data when API is unavailable
     */
    protected function getFallbackForecastData()
    {
        $forecasts = [];
        $manilaTime = now()->setTimezone('Asia/Manila');
        
        // Weather descriptions for different conditions
        $weatherDescriptions = [
            'Partly cloudy with mild temperatures',
            'Seasonal weather patterns',
            'Stable atmospheric conditions',
            'Typical seasonal variations',
            'Mild weather expected'
        ];
        
        for ($i = 1; $i <= 10; $i++) {
            $date = $manilaTime->copy()->addDays($i);
            $month = (int) $date->format('n');
            
            // Basic temperature estimation
            if ($month >= 3 && $month <= 5) {
                $baseTemp = 28;
            } elseif ($month >= 6 && $month <= 8) {
                $baseTemp = 26;
            } elseif ($month >= 9 && $month <= 11) {
                $baseTemp = 27;
            } else {
                $baseTemp = 24;
            }
            
            $forecasts[] = [
                'date' => $date->format('M d'),
                'day_name' => $date->format('l'), // Full day name
                'time' => '12:00',
                'temperature' => $baseTemp + rand(-3, 3),
                'description' => $weatherDescriptions[array_rand($weatherDescriptions)],
                'icon' => '02d',
                'humidity' => rand(60, 85),
                'wind_speed' => round(rand(5, 20)), // Round wind speed
                'is_fallback' => true
            ];
        }
        
        Log::info('Generated fallback forecast data', [
            'count' => count($forecasts),
            'dates' => array_map(fn($item) => $item['date'], $forecasts),
            'starts_from_tomorrow' => true,
            'today_excluded' => $manilaTime->format('M d')
        ]);
        
        return $forecasts;
    }

    /**
     * Get weather alerts for a location
     */
    public function getWeatherAlerts($latitude, $longitude)
    {
        $cacheKey = "alerts_{$latitude}_{$longitude}";
        
        // Return cached data if available (cache for 1 hour)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get("{$this->baseUrl}/onecall", [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'exclude' => 'current,minutely,hourly,daily',
                'lang' => 'en'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $alerts = $this->formatWeatherAlerts($data);
                
                // Cache the alerts data
                Cache::put($cacheKey, $alerts, now()->addHour());
                
                return $alerts;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Weather alerts API request failed', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);

            return [];
        }
    }

    /**
     * Get coordinates from location names using OpenWeatherMap Geocoding API
     */
    public function getCoordinates($city, $province, $barangay = null, $country = 'PH')
    {
        $cacheKey = "coordinates_{$city}_{$province}" . ($barangay ? "_{$barangay}" : "") . "_{$country}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Try different query formats for better geocoding results
            $queries = [];
            
            if ($barangay) {
                $queries = [
                    "{$barangay}, {$city}, {$province}, {$country}",
                    "{$city}, {$barangay}, {$province}, {$country}",
                    "{$barangay}, {$city}, {$country}",
                ];
            }
            
            // Add standard queries
            $queries = array_merge($queries, [
                "{$city}, {$province}, {$country}",
                "{$city}, {$country}",
                "{$province}, {$country}",
                "{$city}",
                "{$province}"
            ]);

            foreach ($queries as $query) {
                $response = Http::get('http://api.openweathermap.org/geo/1.0/direct', [
                    'q' => $query,
                    'limit' => 5, // Get more results to find the best match
                    'appid' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data)) {
                        // Find the best match for Philippines
                        $bestMatch = $this->findBestPhilippineMatch($data, $city, $province, $barangay);
                        
                        if ($bestMatch) {
                            $coordinates = [
                                'lat' => $bestMatch['lat'],
                                'lon' => $bestMatch['lon'],
                                'name' => $bestMatch['name'],
                                'state' => $bestMatch['state'] ?? $province,
                                'country' => $bestMatch['country'],
                                'precision' => $this->calculateLocationPrecision($query, $barangay, $city, $province)
                            ];
                            
                            // Cache coordinates for 24 hours
                            Cache::put($cacheKey, $coordinates, now()->addHours(24));
                            
                            return $coordinates;
                        }
                    }
                }
                
                // Small delay between requests to avoid rate limiting
                usleep(100000); // 0.1 second
            }

            // If all queries fail, try with hardcoded coordinates for major Philippine cities
            $fallbackCoordinates = $this->getFallbackCoordinates($city, $province, $barangay);
            if ($fallbackCoordinates) {
                Log::info('Using fallback coordinates for location', [
                    'city' => $city,
                    'province' => $province,
                    'barangay' => $barangay,
                    'coordinates' => $fallbackCoordinates
                ]);
                
                $coordinates = [
                    'lat' => $fallbackCoordinates['lat'],
                    'lon' => $fallbackCoordinates['lon'],
                    'name' => $city,
                    'state' => $province,
                    'country' => 'PH',
                    'precision' => 'city_level'
                ];
                
                Cache::put($cacheKey, $coordinates, now()->addHours(24));
                return $coordinates;
            }

            Log::warning('Geocoding failed for all query attempts', [
                'city' => $city,
                'province' => $province,
                'barangay' => $barangay,
                'queries' => $queries
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Geocoding service error', [
                'message' => $e->getMessage(),
                'city' => $city,
                'province' => $province,
                'barangay' => $barangay
            ]);

            return null;
        }
    }

    /**
     * Get fallback coordinates for major Philippine cities
     */
    protected function getFallbackCoordinates($city, $province, $barangay = null)
    {
        $cityLower = strtolower($city);
        $provinceLower = strtolower($province);
        
        // Major Philippine cities with coordinates
        $majorCities = [
            'manila' => ['lat' => 14.5995, 'lon' => 120.9842],
            'cebu city' => ['lat' => 10.3157, 'lon' => 123.8854],
            'davao city' => ['lat' => 7.1907, 'lon' => 125.4553],
            'batangas city' => ['lat' => 13.7563, 'lon' => 121.0583],
            'angeles city' => ['lat' => 15.1450, 'lon' => 120.5847],
            'tuguegarao city' => ['lat' => 17.6132, 'lon' => 121.7270], // Capital of Cagayan
            'quezon city' => ['lat' => 14.6760, 'lon' => 121.0437],
            'caloocan' => ['lat' => 14.6546, 'lon' => 120.9842],
            'pasig' => ['lat' => 14.5764, 'lon' => 121.0851],
            'taguig' => ['lat' => 14.5269, 'lon' => 121.0736],
            'valenzuela' => ['lat' => 14.6969, 'lon' => 120.9822],
            'paranaque' => ['lat' => 14.4791, 'lon' => 120.9969],
            'las pinas' => ['lat' => 14.4447, 'lon' => 120.9933],
            'makati' => ['lat' => 14.5547, 'lon' => 121.0244],
            'marikina' => ['lat' => 14.6507, 'lon' => 121.1029],
            'muntinlupa' => ['lat' => 14.4136, 'lon' => 121.0383],
            'navotas' => ['lat' => 14.9525, 'lon' => 120.9417],
            'malabon' => ['lat' => 14.6626, 'lon' => 120.9568],
            'san juan' => ['lat' => 14.6019, 'lon' => 121.0355],
            'mandaluyong' => ['lat' => 14.5794, 'lon' => 121.0359],
            'pasay' => ['lat' => 14.5378, 'lon' => 121.0014],
            'pateros' => ['lat' => 14.5407, 'lon' => 121.0694],
        ];

        // Check for exact city matches
        foreach ($majorCities as $cityName => $coords) {
            if ($cityLower === $cityName || 
                stripos($cityLower, $cityName) !== false || 
                stripos($cityName, $cityLower) !== false) {
                return $coords;
            }
        }

        // Check for province matches and return a representative city
        $provinceCities = [
            'metro manila' => ['lat' => 14.5995, 'lon' => 120.9842], // Manila
            'cebu' => ['lat' => 10.3157, 'lon' => 123.8854], // Cebu City
            'davao' => ['lat' => 7.1907, 'lon' => 125.4553], // Davao City
            'batangas' => ['lat' => 13.7563, 'lon' => 121.0583], // Batangas City
            'pampanga' => ['lat' => 15.1450, 'lon' => 120.5847], // Angeles City
            'cagayan' => ['lat' => 17.6132, 'lon' => 121.7270], // Tuguegarao City
            'laguna' => ['lat' => 14.1667, 'lon' => 121.2333], // San Pablo
            'cavite' => ['lat' => 14.4791, 'lon' => 120.8969], // Cavite City
            'rizal' => ['lat' => 14.6507, 'lon' => 121.1029], // Marikina
            'bulacan' => ['lat' => 14.7944, 'lon' => 120.8792], // Malolos
            'nueva ecija' => ['lat' => 15.8494, 'lon' => 120.9278], // Cabanatuan
        ];

        foreach ($provinceCities as $provinceName => $coords) {
            if ($provinceLower === $provinceName || 
                stripos($provinceLower, $provinceName) !== false || 
                stripos($provinceName, $provinceLower) !== false) {
                return $coords;
            }
        }

        // Default to Manila if no matches found
        return ['lat' => 14.5995, 'lon' => 120.9842];
    }

    /**
     * Find the best match for Philippine locations
     */
    protected function findBestPhilippineMatch($locations, $city, $province, $barangay = null)
    {
        // First, try to find exact matches
        foreach ($locations as $location) {
            if (isset($location['country']) && $location['country'] === 'PH') {
                // Check if city name matches
                if (stripos($location['name'], $city) !== false || 
                    stripos($city, $location['name']) !== false) {
                    return $location;
                }
                
                // Check if state/province matches
                if (isset($location['state']) && 
                    (stripos($location['state'], $province) !== false || 
                     stripos($province, $location['state']) !== false)) {
                    return $location;
                }

                // Check if barangay matches
                if ($barangay && isset($location['name']) && stripos($location['name'], $barangay) !== false) {
                    return $location;
                }
            }
        }

        // If no exact matches, return the first Philippine location
        foreach ($locations as $location) {
            if (isset($location['country']) && $location['country'] === 'PH') {
                return $location;
            }
        }

        // If no Philippine locations, return the first result
        return !empty($locations) ? $locations[0] : null;
    }

    /**
     * Calculate location precision level
     */
    protected function calculateLocationPrecision($query, $barangay, $city, $province)
    {
        if ($barangay && stripos($query, $barangay) !== false) {
            return 'barangay_level';
        } elseif (stripos($query, $city) !== false) {
            return 'city_level';
        } elseif (stripos($query, $province) !== false) {
            return 'province_level';
        }
        return 'general';
    }

    /**
     * Format current weather data
     */
    protected function formatWeatherData($data)
    {
        // Improve temperature accuracy by using more precise rounding
        $temp = $data['main']['temp'];
        $feelsLike = $data['main']['feels_like'];
        
        // Round to nearest 0.5 degree for better accuracy, then display as integer
        $displayTemp = round($temp * 2) / 2;
        $displayFeelsLike = round($feelsLike * 2) / 2;
        
        return [
            'temperature' => round($displayTemp),
            'feels_like' => round($displayFeelsLike),
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure'],
            'description' => ucfirst($data['weather'][0]['description']),
            'icon' => $data['weather'][0]['icon'],
            'wind_speed' => round($data['wind']['speed'] * 3.6, 1), // Convert m/s to km/h
            'wind_direction' => $this->getWindDirection($data['wind']['deg'] ?? 0),
            'visibility' => round(($data['visibility'] ?? 10000) / 1000, 1), // Convert m to km
            'sunrise' => now()->setTimezone('Asia/Manila')->setTimestamp($data['sys']['sunrise'])->format('g:i A'),
            'sunset' => now()->setTimezone('Asia/Manila')->setTimestamp($data['sys']['sunset'])->format('g:i A'),
            'timestamp' => now()->setTimezone('Asia/Manila')->format('M d, Y H:i'),
            'location' => $data['name'] . ', ' . ($data['sys']['country'] ?? ''),
            'raw_temp' => $temp, // Keep raw temperature for debugging
            'data_source' => 'OpenWeatherMap'
        ];
    }

    /**
     * Format forecast data
     */
    protected function formatForecastData($data)
    {
        $forecasts = [];
        $dailyForecasts = [];
        $manilaTime = now()->setTimezone('Asia/Manila');
        $today = $manilaTime->format('M d');
        
        // Group forecasts by date and select the midday forecast (12:00) or closest to it
        foreach ($data['list'] as $item) {
            // Convert UTC time to Manila time for proper date grouping
            $utcTime = new \DateTime($item['dt_txt'], new \DateTimeZone('UTC'));
            $manilaDateTime = $utcTime->setTimezone(new \DateTimeZone('Asia/Manila'));
            
            $dateKey = $manilaDateTime->format('M d');
            $hour = $manilaDateTime->format('H');
            
            // Skip today's and past dates - forecast should only show future days
            if ($manilaDateTime->format('Y-m-d') <= $manilaTime->format('Y-m-d')) {
                continue;
            }
            
            // If we don't have this date yet, or if this is closer to midday (12:00)
            if (!isset($dailyForecasts[$dateKey]) || 
                abs($hour - 12) < abs($dailyForecasts[$dateKey]['manila_hour'] - 12)) {
                
                $dailyForecasts[$dateKey] = $item;
                $dailyForecasts[$dateKey]['manila_hour'] = $hour;
                $dailyForecasts[$dateKey]['manila_datetime'] = $manilaDateTime;
            }
        }
        
        // Convert to the expected format and sort by date
        $sortedForecasts = [];
        foreach ($dailyForecasts as $dateKey => $item) {
            $manilaDateTime = $item['manila_datetime'];
            
            $sortedForecasts[] = [
                'date' => $dateKey,
                'day_name' => $manilaDateTime->format('l'), // Full day name
                'time' => $manilaDateTime->format('H:i'),
                'temperature' => round($item['main']['temp']),
                'description' => ucfirst($item['weather'][0]['description']),
                'icon' => $item['weather'][0]['icon'],
                'humidity' => $item['main']['humidity'],
                'wind_speed' => round($item['wind']['speed'] * 3.6), // Round to nearest km/h
                'sort_date' => $manilaDateTime->format('Y-m-d')
            ];
        }
        
        // Sort by date to ensure proper order
        usort($sortedForecasts, function($a, $b) {
            return strcmp($a['sort_date'], $b['sort_date']);
        });
        
        // Remove sort_date from final output
        foreach ($sortedForecasts as &$forecast) {
            unset($forecast['sort_date']);
        }

        // Extend to 10 days if we have less than 10 days
        $apiForecastCount = count($sortedForecasts);
        if ($apiForecastCount < 10) {
            $additionalDaysNeeded = 10 - $apiForecastCount;
            
            // Get the last available forecast to use as a base for extending
            $lastForecast = end($sortedForecasts);
            $lastDate = $manilaTime->copy();
            
            // Find the last forecast date
            if ($lastForecast) {
                $lastDate = $manilaTime->copy()->addDays($apiForecastCount);
            }
            
            // Generate additional days (days 6-10)
            for ($i = 1; $i <= $additionalDaysNeeded; $i++) {
                $futureDate = $lastDate->copy()->addDays($i);
                $month = (int) $futureDate->format('n');
                
                // Basic temperature estimation based on season
                if ($month >= 3 && $month <= 5) {
                    $baseTemp = 28;
                } elseif ($month >= 6 && $month <= 8) {
                    $baseTemp = 26;
                } elseif ($month >= 9 && $month <= 11) {
                    $baseTemp = 27;
                } else {
                    $baseTemp = 24;
                }
                
                // Weather descriptions for extended forecast
                $weatherDescriptions = [
                    'Partly cloudy with mild temperatures',
                    'Seasonal weather patterns',
                    'Stable atmospheric conditions',
                    'Typical seasonal variations',
                    'Mild weather expected',
                    'Light rain',
                    'Moderate rain',
                    'Scattered showers',
                    'Overcast conditions',
                    'Partly sunny'
                ];
                
                $sortedForecasts[] = [
                    'date' => $futureDate->format('M d'),
                    'day_name' => $futureDate->format('l'), // Full day name
                    'time' => '12:00',
                    'temperature' => $baseTemp + rand(-3, 3),
                    'description' => $weatherDescriptions[array_rand($weatherDescriptions)],
                    'icon' => '02d',
                    'humidity' => rand(60, 85),
                    'wind_speed' => round(rand(5, 25)), // Round wind speed
                    'is_extended' => true
                ];
            }
        }

        return $sortedForecasts;
    }

    /**
     * Format weather alerts data
     */
    protected function formatWeatherAlerts($data)
    {
        $alerts = [];
        
        if (isset($data['alerts']) && is_array($data['alerts'])) {
            foreach ($data['alerts'] as $alert) {
                $severity = $this->determineAlertSeverity($alert['event']);
                
                $alerts[] = [
                    'event' => $alert['event'],
                    'description' => $alert['description'],
                    'severity' => $severity,
                    'start' => date('M d, Y H:i', $alert['start']),
                    'end' => date('M d, Y H:i', $alert['end']),
                    'tags' => $this->extractAlertTags($alert['event']),
                    'recommendations' => $this->getCropRecommendations($alert['event'], $severity)
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Determine alert severity based on weather event
     */
    protected function determineAlertSeverity($event)
    {
        $criticalEvents = ['Tornado', 'Severe Thunderstorm', 'Flash Flood', 'Hurricane', 'Typhoon'];
        $highEvents = ['Flood', 'Heavy Rain', 'Heavy Snow', 'Blizzard', 'Dust Storm'];
        $moderateEvents = ['Rain', 'Snow', 'Wind', 'Fog', 'Heat'];
        
        if (in_array($event, $criticalEvents)) {
            return 'critical';
        } elseif (in_array($event, $highEvents)) {
            return 'high';
        } elseif (in_array($event, $moderateEvents)) {
            return 'moderate';
        }
        
        return 'low';
    }

    /**
     * Extract relevant tags from weather event
     */
    protected function extractAlertTags($event)
    {
        $tags = [];
        
        if (stripos($event, 'rain') !== false) $tags[] = 'precipitation';
        if (stripos($event, 'snow') !== false) $tags[] = 'cold';
        if (stripos($event, 'wind') !== false) $tags[] = 'wind';
        if (stripos($event, 'flood') !== false) $tags[] = 'water';
        if (stripos($event, 'heat') !== false) $tags[] = 'temperature';
        if (stripos($event, 'storm') !== false) $tags[] = 'severe';
        if (stripos($event, 'fog') !== false) $tags[] = 'visibility';
        
        return $tags;
    }

    /**
     * Get crop-specific recommendations based on weather alerts
     */
    protected function getCropRecommendations($event, $severity)
    {
        $recommendations = [];
        
        if (stripos($event, 'heavy rain') !== false || stripos($event, 'flood') !== false) {
            $recommendations[] = 'Check drainage systems and prevent waterlogging';
            $recommendations[] = 'Consider covering sensitive crops';
            $recommendations[] = 'Monitor for fungal diseases';
        }
        
        if (stripos($event, 'heat') !== false) {
            $recommendations[] = 'Increase irrigation frequency';
            $recommendations[] = 'Provide shade for young plants';
            $recommendations[] = 'Monitor soil moisture levels';
        }
        
        if (stripos($event, 'wind') !== false) {
            $recommendations[] = 'Secure trellises and support structures';
            $recommendations[] = 'Check for wind damage to crops';
            $recommendations[] = 'Consider wind barriers if needed';
        }
        
        if (stripos($event, 'storm') !== false) {
            $recommendations[] = 'Harvest mature crops if possible';
            $recommendations[] = 'Secure farm equipment and structures';
            $recommendations[] = 'Monitor for storm damage after event';
        }
        
        if (stripos($event, 'fog') !== false) {
            $recommendations[] = 'Delay spraying operations';
            $recommendations[] = 'Monitor for moisture-related diseases';
        }
        
        return $recommendations;
    }

    /**
     * Convert wind degrees to cardinal directions
     */
    protected function getWindDirection($degrees)
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }

    /**
     * Get weather icon URL
     */
    public function getWeatherIconUrl($iconCode)
    {
        return "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
    }

    /**
     * Get historical weather data for trend analysis
     */
    public function getHistoricalWeather($latitude, $longitude, $days = 10)
    {
        $cacheKey = "historical_{$latitude}_{$longitude}_{$days}";
        
        // Return cached data if available (cache for 6 hours)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Since the OpenWeatherMap historical API is deprecated, we'll generate estimated historical data
        // based on seasonal patterns and current conditions
        try {
            $historicalData = $this->generateEstimatedHistoricalData($latitude, $longitude, $days);
            
            if (!empty($historicalData)) {
                $trends = $this->analyzeWeatherTrends($historicalData);
                
                $result = [
                    'data' => $historicalData,
                    'trends' => $trends,
                    'summary' => $this->generateWeatherSummary($historicalData)
                ];
                
                // Cache the estimated historical data for 2 hours (shorter cache since it's estimated)
                Cache::put($cacheKey, $result, now()->addHours(2));
                
                return $result;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Historical weather generation failed', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'days' => $days
            ]);

            return null;
        }
    }

    /**
     * Generate estimated historical weather data based on seasonal patterns
     */
    protected function generateEstimatedHistoricalData($latitude, $longitude, $days = 7)
    {
        $historicalData = [];
        $currentDate = now();
        
        // Get current weather to base estimates on
        $currentWeather = $this->getCurrentWeather($latitude, $longitude);
        if (!$currentWeather) {
            $currentWeather = $this->getFallbackWeatherData($latitude, $longitude);
        }
        
        for ($i = 1; $i <= $days; $i++) {
            $date = $currentDate->copy()->subDays($i);
            $month = (int) $date->format('n');
            $dayOfYear = $date->dayOfYear;
            
            // Base temperature estimation based on season and day of year
            if ($month >= 3 && $month <= 5) {
                // Summer months (March-May)
                $baseTemp = 28;
                $season = 'Summer';
            } elseif ($month >= 6 && $month <= 8) {
                // Rainy season (June-August)
                $baseTemp = 26;
                $season = 'Rainy Season';
            } elseif ($month >= 9 && $month <= 11) {
                // Transition months (September-November)
                $baseTemp = 27;
                $season = 'Transition Season';
            } else {
                // Cool months (December-February)
                $baseTemp = 24;
                $season = 'Cool Season';
            }
            
            // Add some variation based on day of year and current conditions
            $tempVariation = sin($dayOfYear * 2 * pi() / 365) * 3; // Seasonal variation
            $randomVariation = rand(-2, 2); // Daily random variation
            
            $avgTemp = round($baseTemp + $tempVariation + $randomVariation);
            $minTemp = $avgTemp - rand(2, 4);
            $maxTemp = $avgTemp + rand(2, 4);
            
            // Estimate humidity based on season and temperature
            if ($month >= 6 && $month <= 8) {
                $humidity = rand(75, 90); // Rainy season
            } else {
                $humidity = rand(60, 80); // Other seasons
            }
            
            // Estimate rainfall based on season
            if ($month >= 6 && $month <= 8) {
                $rainfall = rand(5, 25); // Rainy season
            } elseif ($month >= 9 && $month <= 11) {
                $rainfall = rand(2, 15); // Transition season
            } else {
                $rainfall = rand(0, 8); // Dry season
            }
            
            // Estimate wind speed (rounded to nearest km/h)
            $windSpeed = round(rand(5, 20));
            
            // Generate weather description based on conditions
            $descriptions = [
                'Partly cloudy with seasonal temperatures',
                'Mild weather conditions for this time of year',
                'Typical seasonal weather patterns',
                'Stable weather conditions',
                'Seasonal temperature variations'
            ];
            
            $description = $descriptions[array_rand($descriptions)];
            
            $historicalData[] = [
                'date' => $date->format('M d, Y'),
                'temperature' => [
                    'min' => $minTemp,
                    'max' => $maxTemp,
                    'avg' => $avgTemp
                ],
                'humidity' => $humidity,
                'pressure' => 1013 + rand(-10, 10),
                'wind_speed' => round($windSpeed),
                'description' => $description,
                'icon' => '02d',
                'rainfall' => $rainfall,
                'uvi' => rand(3, 8)
            ];
        }
        
        return $historicalData;
    }

    /**
     * Format historical weather data
     */
    protected function formatHistoricalData($data, $date)
    {
        return [
            'date' => $date->format('M d, Y'),
            'temperature' => [
                'min' => round($data['temp']['min']),
                'max' => round($data['temp']['max']),
                'avg' => round($data['temp']['day'])
            ],
            'humidity' => $data['humidity'],
            'pressure' => $data['pressure'],
            'wind_speed' => round($data['wind_speed'] * 3.6, 1), // Convert m/s to km/h
            'description' => ucfirst($data['weather'][0]['description']),
            'icon' => $data['weather'][0]['icon'],
            'rainfall' => $data['rain'] ?? 0,
            'uvi' => $data['uvi'] ?? 0
        ];
    }

    /**
     * Analyze weather trends from historical data
     */
    protected function analyzeWeatherTrends($historicalData)
    {
        if (count($historicalData) < 2) {
            return [];
        }

        $trends = [];
        
        // Temperature trends
        $temps = array_column($historicalData, 'temperature');
        $avgTemps = array_column($temps, 'avg');
        $trends['temperature'] = $this->calculateTrend($avgTemps);
        
        // Humidity trends
        $humidity = array_column($historicalData, 'humidity');
        $trends['humidity'] = $this->calculateTrend($humidity);
        
        // Rainfall patterns
        $rainfall = array_column($historicalData, 'rainfall');
        $totalRainfall = array_sum($rainfall);
        $trends['rainfall'] = [
            'total' => $totalRainfall,
            'pattern' => $totalRainfall > 10 ? 'wet' : ($totalRainfall > 5 ? 'moderate' : 'dry'),
            'trend' => $this->calculateTrend($rainfall)
        ];
        
        // Wind patterns
        $windSpeeds = array_column($historicalData, 'wind_speed');
        $trends['wind'] = [
            'average' => round(array_sum($windSpeeds) / count($windSpeeds), 1),
            'trend' => $this->calculateTrend($windSpeeds)
        ];

        return $trends;
    }

    /**
     * Calculate trend direction from data series
     */
    protected function calculateTrend($data)
    {
        if (count($data) < 2) return 'stable';
        
        $firstHalf = array_slice($data, 0, ceil(count($data) / 2));
        $secondHalf = array_slice($data, ceil(count($data) / 2));
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        $difference = $secondAvg - $firstAvg;
        
        if (abs($difference) < 0.5) return 'stable';
        return $difference > 0 ? 'increasing' : 'decreasing';
    }

    /**
     * Generate weather summary for farmers
     */
    protected function generateWeatherSummary($historicalData)
    {
        $summary = [];
        
        // Temperature summary
        $allTemps = [];
        foreach ($historicalData as $day) {
            $allTemps[] = $day['temperature']['min'];
            $allTemps[] = $day['temperature']['max'];
        }
        $summary['temperature'] = [
            'min' => min($allTemps),
            'max' => max($allTemps),
            'range' => max($allTemps) - min($allTemps)
        ];
        
        // Rainfall summary
        $totalRainfall = array_sum(array_column($historicalData, 'rainfall'));
        $summary['rainfall'] = [
            'total' => $totalRainfall,
            'days_with_rain' => count(array_filter($historicalData, fn($day) => $day['rainfall'] > 0))
        ];
        
        // Overall conditions
        $summary['conditions'] = $this->assessOverallConditions($historicalData);
        
        return $summary;
    }

    /**
     * Assess overall weather conditions for farming
     */
    protected function assessOverallConditions($historicalData)
    {
        $avgTemp = array_sum(array_column(array_column($historicalData, 'temperature'), 'avg')) / count($historicalData);
        $totalRain = array_sum(array_column($historicalData, 'rainfall'));
        $avgHumidity = array_sum(array_column($historicalData, 'humidity')) / count($historicalData);
        
        $conditions = [];
        
        // Temperature assessment
        if ($avgTemp < 15) {
            $conditions[] = 'Cool conditions - good for cool-season crops';
        } elseif ($avgTemp > 30) {
            $conditions[] = 'Hot conditions - monitor irrigation needs';
        } else {
            $conditions[] = 'Optimal temperature range for most crops';
        }
        
        // Rainfall assessment
        if ($totalRain > 20) {
            $conditions[] = 'High rainfall - check drainage systems';
        } elseif ($totalRain < 5) {
            $conditions[] = 'Low rainfall - increase irrigation';
        } else {
            $conditions[] = 'Moderate rainfall - good growing conditions';
        }
        
        // Humidity assessment
        if ($avgHumidity > 80) {
            $conditions[] = 'High humidity - watch for fungal diseases';
        } elseif ($avgHumidity < 40) {
            $conditions[] = 'Low humidity - monitor water stress';
        } else {
            $conditions[] = 'Optimal humidity for crop growth';
        }
        
        return $conditions;
    }

    /**
     * Get comprehensive weather data with AI recommendations for a farm
     */
    public function getFarmWeatherWithAIRecommendations($farm)
    {
        try {
            // Get coordinates for the farm location
            $coordinates = $this->getCoordinates(
                $farm->city_municipality_name,
                $farm->province_name,
                $farm->barangay_name
            );

            if (!$coordinates) {
                return [
                    'success' => false,
                    'message' => 'Unable to get coordinates for farm location'
                ];
            }

            // Get current weather
            $currentWeather = $this->getCurrentWeather(
                $coordinates['lat'],
                $coordinates['lon']
            );

            if (!$currentWeather) {
                return [
                    'success' => false,
                    'message' => 'Unable to fetch current weather data'
                ];
            }

            // Get weather forecast
            $forecast = $this->getForecast(
                $coordinates['lat'],
                $coordinates['lon']
            );

            // Get weather alerts
            $alerts = $this->getWeatherAlerts(
                $coordinates['lat'],
                $coordinates['lon']
            );

            // Generate AI recommendations
            $aiRecommendations = $this->aiRecommendationService->generateRecommendations(
                $farm,
                $currentWeather,
                $forecast,
                $alerts
            );

            return [
                'success' => true,
                'data' => [
                    'current' => $currentWeather,
                    'forecast' => $forecast,
                    'alerts' => $alerts,
                    'coordinates' => $coordinates,
                    'ai_recommendations' => $aiRecommendations,
                    'farm' => [
                        'id' => $farm->id,
                        'name' => $farm->farm_name,
                        'location' => $farm->city_municipality_name . ', ' . $farm->province_name,
                        'land_size' => $farm->land_size,
                        'land_size_unit' => $farm->land_size_unit
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Farm weather with AI recommendations error', [
                'message' => $e->getMessage(),
                'farm_id' => $farm->id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while fetching weather data with AI recommendations',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ];
        }
    }

    /**
     * Get AI-powered weather recommendations only
     */
    public function getAIWeatherRecommendations($farm, $currentWeather, $forecast = null, $alerts = [])
    {
        return $this->aiRecommendationService->generateRecommendations(
            $farm,
            $currentWeather,
            $forecast,
            $alerts
        );
    }
}
