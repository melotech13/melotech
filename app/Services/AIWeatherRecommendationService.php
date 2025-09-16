<?php

namespace App\Services;

use App\Models\Farm;
use App\Models\CropGrowth;
use App\Models\PhotoAnalysis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIWeatherRecommendationService
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Generate AI-powered weather recommendations for a specific farm
     */
    public function generateRecommendations($farm, $currentWeather, $forecast = null, $alerts = [])
    {
        $cacheKey = "ai_recommendations_{$farm->id}_{$currentWeather['timestamp']}";
        
        // Return cached data if available (cache for 30 minutes)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $recommendations = [
                'immediate_actions' => $this->getImmediateActions($farm, $currentWeather, $alerts),
                'short_term_planning' => $this->getShortTermPlanning($farm, $currentWeather, $forecast),
                'crop_specific_advice' => $this->getCropSpecificAdvice($farm, $currentWeather, $forecast),
                'risk_assessment' => $this->getRiskAssessment($farm, $currentWeather, $forecast, $alerts),
                'optimization_tips' => $this->getOptimizationTips($farm, $currentWeather, $forecast),
                'seasonal_insights' => $this->getSeasonalInsights($farm, $currentWeather),
                'ai_confidence' => $this->calculateAIConfidence($farm, $currentWeather, $forecast)
            ];

            // Cache the recommendations
            Cache::put($cacheKey, $recommendations, now()->addMinutes(30));

            return $recommendations;

        } catch (\Exception $e) {
            Log::error('AI Weather Recommendation generation failed', [
                'message' => $e->getMessage(),
                'farm_id' => $farm->id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getFallbackRecommendations($farm, $currentWeather);
        }
    }

    /**
     * Get immediate actions based on current weather conditions
     */
    protected function getImmediateActions($farm, $currentWeather, $alerts = [])
    {
        $actions = [];
        $temperature = $currentWeather['temperature'];
        $humidity = $currentWeather['humidity'];
        $windSpeed = $currentWeather['wind_speed'];
        $description = strtolower($currentWeather['description']);

        // Temperature-based immediate actions
        if ($temperature > 35) {
            $actions[] = [
                'priority' => 'critical',
                'category' => 'heat_management',
                'icon' => 'fas fa-thermometer-full',
                'color' => '#dc2626',
                'title' => '🚨 CRITICAL: Extreme Heat Protection Required',
                'description' => "Temperature is {$temperature}°C - Your crops are at high risk of heat stress and wilting.",
                'urgency' => 'Immediate (within 2 hours)',
                'actions' => [
                    'Water immediately: 2-3 gallons per plant (morning 6-7 AM)',
                    'Apply 3-4 inch mulch layer around all plants to retain moisture',
                    'Install 50-70% shade cloth over sensitive crops (tomatoes, peppers)',
                    'Water again at 6-7 PM with 1-2 gallons per plant',
                    'Check soil moisture every 2 hours - if dry 1 inch deep, water again',
                    'Spray leaves with water mist to cool plants (avoid flowers)'
                ],
                'mitigation' => [
                    'Set up temporary shade structures using bamboo and cloth',
                    'Move potted plants to shaded areas immediately',
                    'Apply organic mulch (rice straw, coconut coir) 3-4 inches thick',
                    'Use drip irrigation for consistent moisture delivery'
                ],
                'ai_reasoning' => "At {$temperature}°C, plants lose water 3x faster. Without immediate action, you could lose 30-50% of your harvest."
            ];
        } elseif ($temperature > 30) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'heat_management',
                'icon' => 'fas fa-thermometer-three-quarters',
                'color' => '#f59e0b',
                'title' => '⚠️ HIGH: Hot Weather Care Required',
                'description' => "Temperature is {$temperature}°C - Monitor crops closely for heat stress signs.",
                'urgency' => 'Within 4 hours',
                'actions' => [
                    'Water deeply: 1-2 gallons per plant (morning 6-8 AM)',
                    'Apply 2-3 inch mulch layer to conserve soil moisture',
                    'Provide partial shade for young or sensitive plants',
                    'Water again at 5-6 PM if soil feels dry',
                    'Check for wilting leaves every 3-4 hours'
                ],
                'mitigation' => [
                    'Install 30-50% shade cloth for delicate crops',
                    'Use white plastic mulch to reflect heat',
                    'Group plants together for mutual shade'
                ],
                'ai_reasoning' => "At {$temperature}°C, plants need extra water and protection to maintain healthy growth."
            ];
        } elseif ($temperature < 10) {
            $actions[] = [
                'priority' => 'critical',
                'category' => 'cold_protection',
                'icon' => 'fas fa-thermometer-quarter',
                'color' => '#3b82f6',
                'title' => '❄️ CRITICAL: Frost Protection Required',
                'description' => "Temperature is {$temperature}°C - Your crops are at risk of frost damage.",
                'urgency' => 'Immediate (within 1 hour)',
                'actions' => [
                    'Cover all plants with frost cloth or plastic sheets immediately',
                    'Add 4-6 inch mulch layer around plant roots for insulation',
                    'Water soil thoroughly in the evening to retain heat',
                    'Use row covers for vegetables and young plants',
                    'Check covers are secure and not touching plant leaves',
                    'Inspect for damage at sunrise (6-7 AM)'
                ],
                'mitigation' => [
                    'Set up temporary greenhouse using plastic and bamboo',
                    'Use heat lamps or candles in covered areas (safely)',
                    'Move potted plants indoors or to protected areas',
                    'Apply anti-transpirant spray to reduce water loss'
                ],
                'ai_reasoning' => "At {$temperature}°C, frost can kill sensitive plants within hours. Immediate protection is essential."
            ];
        }

        // Humidity-based actions
        if ($humidity > 85) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'disease_prevention',
                'icon' => 'fas fa-tint',
                'color' => '#8b5cf6',
                'title' => '🦠 HIGH: Disease Prevention Required',
                'description' => "Humidity is {$humidity}% - Perfect conditions for fungal diseases to spread rapidly.",
                'urgency' => 'Within 6 hours',
                'actions' => [
                    'Spray copper fungicide (2-3 tablespoons per gallon) on all plants',
                    'Ensure 2-3 feet spacing between plants for air circulation',
                    'Water only at soil level - never wet the leaves',
                    'Remove any yellow, spotted, or moldy leaves immediately',
                    'Check under leaves for white powdery mildew every 2 hours',
                    'Apply neem oil spray (1 tablespoon per quart) as preventive measure'
                ],
                'mitigation' => [
                    'Install fans to improve air circulation around plants',
                    'Use drip irrigation to keep leaves dry',
                    'Apply baking soda spray (1 tsp per quart water) on affected areas',
                    'Remove and destroy severely infected plants to prevent spread'
                ],
                'ai_reasoning' => "At {$humidity}% humidity, fungal spores germinate 5x faster. Without treatment, you could lose 40-60% of your crop to disease."
            ];
        } elseif ($humidity < 30) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'moisture_management',
                'icon' => 'fas fa-sun',
                'color' => '#f59e0b',
                'title' => '🌵 HIGH: Drought Stress Prevention',
                'description' => "Humidity is only {$humidity}% - Plants are losing water rapidly through transpiration.",
                'urgency' => 'Within 4 hours',
                'actions' => [
                    'Water deeply: 3-4 gallons per plant (morning 5-7 AM)',
                    'Install drip irrigation system for consistent moisture',
                    'Apply 4-6 inch organic mulch layer around all plants',
                    'Water again at 6-7 PM with 2 gallons per plant',
                    'Check soil moisture every 3 hours - water if dry 2 inches deep',
                    'Use shade cloth (30-50%) to reduce water loss'
                ],
                'mitigation' => [
                    'Group plants together to create micro-humidity zones',
                    'Use water-absorbing crystals in soil to retain moisture',
                    'Install misting system for immediate humidity boost',
                    'Apply anti-transpirant spray to reduce water loss'
                ],
                'ai_reasoning' => "At {$humidity}% humidity, plants lose water 4x faster than normal. Without intervention, plants will wilt and die within 24-48 hours."
            ];
        }

        // Wind-based actions
        if ($windSpeed > 25) {
            $actions[] = [
                'priority' => 'critical',
                'category' => 'wind_protection',
                'icon' => 'fas fa-wind',
                'color' => '#6b7280',
                'title' => '💨 CRITICAL: Wind Damage Prevention',
                'description' => "Wind speed is {$windSpeed} km/h - Strong winds can cause severe damage to crops and structures.",
                'urgency' => 'Immediate (within 1 hour)',
                'actions' => [
                    'Secure all trellises with 12-gauge wire and stakes every 3 feet',
                    'Tie down greenhouse plastic with rope every 2 feet around perimeter',
                    'Harvest all ripe crops immediately to prevent wind damage',
                    'Install wind barriers using bamboo screens (6-8 feet high)',
                    'Stake all plants taller than 2 feet with 4-foot stakes',
                    'Move potted plants to protected areas or lay them on their sides'
                ],
                'mitigation' => [
                    'Create windbreaks using fast-growing trees (banana, bamboo)',
                    'Use netting to protect delicate crops from wind abrasion',
                    'Install temporary wind barriers using plastic sheets and posts',
                    'Group plants together to provide mutual wind protection'
                ],
                'ai_reasoning' => "At {$windSpeed} km/h, wind can break plant stems, damage fruit, and destroy greenhouse structures. Immediate action prevents 70-90% of potential damage."
            ];
        } elseif ($windSpeed > 15) {
            $actions[] = [
                'priority' => 'medium',
                'category' => 'wind_protection',
                'icon' => 'fas fa-wind',
                'color' => '#f59e0b',
                'title' => '💨 MEDIUM: Wind Protection Recommended',
                'description' => "Wind speed is {$windSpeed} km/h - Moderate winds may affect sensitive crops.",
                'urgency' => 'Within 3 hours',
                'actions' => [
                    'Check all plant supports and trellises are secure',
                    'Stake any plants that are leaning or swaying',
                    'Avoid spraying pesticides or fertilizers (wind will carry them away)',
                    'Harvest delicate crops like lettuce and herbs',
                    'Check greenhouse doors and vents are properly closed'
                ],
                'mitigation' => [
                    'Install temporary wind barriers for young plants',
                    'Use row covers to protect sensitive seedlings',
                    'Group potted plants together for stability'
                ],
                'ai_reasoning' => "At {$windSpeed} km/h, wind can cause minor damage to sensitive plants and reduce pesticide effectiveness."
            ];
        }

        // Weather condition specific actions
        if (strpos($description, 'rain') !== false || strpos($description, 'storm') !== false) {
            $actions[] = [
                'priority' => 'high',
                'category' => 'rain_management',
                'icon' => 'fas fa-cloud-rain',
                'color' => '#3b82f6',
                'title' => '🌧️ HIGH: Rain/Storm Preparation Required',
                'description' => 'Rain or storm is approaching - Prepare your farm for wet weather conditions.',
                'urgency' => 'Within 2 hours',
                'actions' => [
                    'Clear all drainage ditches and channels (minimum 6 inches deep)',
                    'Cover delicate crops with plastic sheets or row covers',
                    'Apply fungicide spray to all plants (copper-based, 2 tbsp per gallon)',
                    'Harvest all ripe crops to prevent water damage',
                    'Check low-lying areas for proper drainage (slope 2-3%)',
                    'Secure loose items that could be blown around'
                ],
                'mitigation' => [
                    'Install raised beds (6-8 inches high) for vegetables',
                    'Use water-permeable landscape fabric to prevent soil erosion',
                    'Create temporary drainage channels using shovels',
                    'Set up collection barrels for rainwater harvesting'
                ],
                'ai_reasoning' => 'Proper preparation prevents soil erosion, disease spread, and crop damage. Without preparation, you could lose 20-40% of your harvest to water damage.'
            ];
        }

        // Alert-based actions
        foreach ($alerts as $alert) {
            if ($alert['severity'] === 'critical' || $alert['severity'] === 'high') {
                $actions[] = [
                    'priority' => 'critical',
                    'category' => 'weather_alert',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => '#dc2626',
                    'title' => '🚨 CRITICAL: ' . $alert['event'],
                    'description' => $alert['description'] . ' - Official weather warning requires immediate action.',
                    'urgency' => 'Immediate (within 30 minutes)',
                    'actions' => $alert['recommendations'] ?? [
                        'Listen to official weather updates every 15 minutes',
                        'Secure all farm equipment and tools in storage',
                        'Move all animals to protected areas immediately',
                        'Harvest all ripe crops that can be saved',
                        'Turn off all electrical equipment and irrigation systems',
                        'Have emergency supplies ready (flashlight, first aid, food)'
                    ],
                    'mitigation' => [
                        'Follow official evacuation orders if issued',
                        'Document farm damage with photos for insurance',
                        'Keep emergency contact numbers readily available',
                        'Have backup power source ready if possible'
                    ],
                    'ai_reasoning' => 'Official weather warning - This is a serious threat that requires immediate protective action to prevent loss of life and property.'
                ];
            }
        }

        return $actions;
    }

    /**
     * Get short-term planning recommendations based on forecast
     */
    protected function getShortTermPlanning($farm, $currentWeather, $forecast = null)
    {
        $planning = [];

        if (!$forecast || count($forecast) < 3) {
            return [
                [
                    'category' => 'planning',
                    'icon' => 'fas fa-calendar-alt',
                    'color' => '#6b7280',
                    'title' => '📅 Limited Forecast - Plan Carefully',
                    'description' => 'Weather forecast is limited. Plan based on today\'s weather conditions.',
                    'urgency' => 'Daily planning required',
                    'recommendations' => [
                        'Check weather updates every 4 hours',
                        'Plan farm work based on current weather patterns',
                        'Be ready to change plans if weather changes suddenly',
                        'Monitor soil moisture levels twice daily',
                        'Have backup plans for different weather scenarios'
                    ],
                    'ai_reasoning' => 'Limited forecast data requires more careful monitoring and flexible planning.'
                ]
            ];
        }

        // Analyze next 3 days
        $next3Days = array_slice($forecast, 0, 3);
        $temperatures = array_column($next3Days, 'temperature');
        $descriptions = array_column($next3Days, 'description');
        $humidity = array_column($next3Days, 'humidity');

        // Temperature trend analysis
        $avgTemp = array_sum($temperatures) / count($temperatures);
        $tempRange = max($temperatures) - min($temperatures);
        $maxTemp = max($temperatures);
        $minTemp = min($temperatures);

        if ($tempRange > 10) {
            $planning[] = [
                'category' => 'temperature_planning',
                'icon' => 'fas fa-thermometer-half',
                'color' => '#f59e0b',
                'title' => '🌡️ HIGH: Extreme Temperature Swings Expected',
                'description' => "Temperature will swing from {$minTemp}°C to {$maxTemp}°C (range: {$tempRange}°C) - Your crops need special protection.",
                'urgency' => 'Plan for next 3 days',
                'recommendations' => [
                    'Day 1: Prepare protective covers for cold nights (below 15°C)',
                    'Day 2: Install shade cloth for hot days (above 30°C)',
                    'Day 3: Adjust watering schedule - more water on hot days, less on cool days',
                    'Check soil temperature daily with thermometer (optimal: 20-25°C)',
                    'Apply mulch 3-4 inches thick to buffer temperature changes',
                    'Use row covers that can be easily removed during hot periods'
                ],
                'mitigation' => [
                    'Install automatic temperature-controlled ventilation systems',
                    'Use thermal mass (water barrels) to stabilize greenhouse temperatures',
                    'Plant heat-tolerant varieties in exposed areas',
                    'Create microclimates using strategic plant placement'
                ],
                'ai_reasoning' => "Temperature swings of {$tempRange}°C can cause plant stress, reduced yields, and increased disease susceptibility. Proper planning prevents 50-70% of temperature-related crop damage."
            ];
        }

        // Rain pattern analysis
        $rainyDays = 0;
        $heavyRainDays = 0;
        foreach ($descriptions as $desc) {
            $descLower = strtolower($desc);
            if (strpos($descLower, 'rain') !== false || strpos($descLower, 'shower') !== false) {
                $rainyDays++;
                if (strpos($descLower, 'heavy') !== false || strpos($descLower, 'storm') !== false) {
                    $heavyRainDays++;
                }
            }
        }

        if ($rainyDays >= 2) {
            $planning[] = [
                'category' => 'rain_planning',
                'icon' => 'fas fa-cloud-rain',
                'color' => '#3b82f6',
                'title' => '🌧️ HIGH: Multi-Day Rain Event Expected',
                'description' => "Rain expected for {$rainyDays} days with {$heavyRainDays} heavy rain days - Plan for wet weather management.",
                'urgency' => 'Prepare within 6 hours',
                'recommendations' => [
                    'Day 1: Complete all outdoor work before rain starts',
                    'Day 2: Clear drainage ditches (6 inches deep, 2-3% slope)',
                    'Day 3: Apply fungicide spray (copper-based, 2 tbsp per gallon)',
                    'Install raised beds (6-8 inches) for all vegetables',
                    'Cover delicate crops with plastic sheets or row covers',
                    'Set up rainwater collection barrels (50-100 gallon capacity)',
                    'Plan indoor work: seed starting, tool maintenance, record keeping'
                ],
                'mitigation' => [
                    'Install French drains around low-lying areas',
                    'Use water-permeable landscape fabric to prevent erosion',
                    'Create temporary drainage channels with shovels',
                    'Apply gypsum to improve soil drainage (50 lbs per 1000 sq ft)'
                ],
                'ai_reasoning' => "Extended rain periods increase disease risk by 300% and can cause 20-40% crop loss due to waterlogging. Proper preparation reduces these risks by 60-80%."
            ];
        } elseif ($rainyDays === 0) {
            $planning[] = [
                'category' => 'dry_planning',
                'icon' => 'fas fa-sun',
                'color' => '#f59e0b',
                'title' => '☀️ HIGH: Extended Dry Period Expected',
                'description' => 'No rain expected for 3+ days - Implement water conservation and drought management strategies.',
                'urgency' => 'Plan irrigation schedule',
                'recommendations' => [
                    'Day 1: Deep water all crops (3-4 gallons per plant)',
                    'Day 2: Apply 4-6 inch mulch layer around all plants',
                    'Day 3: Install drip irrigation system for consistent moisture',
                    'Water early morning (5-7 AM) and evening (6-8 PM)',
                    'Check soil moisture every 6 hours - water if dry 2 inches deep',
                    'Use shade cloth (30-50%) to reduce water loss',
                    'Group plants by water needs for efficient irrigation'
                ],
                'mitigation' => [
                    'Install water-absorbing crystals in soil (1 cup per plant)',
                    'Use ollas (clay pots) for slow-release irrigation',
                    'Create swales to capture and store rainwater',
                    'Plant drought-resistant varieties for future seasons'
                ],
                'ai_reasoning' => 'Extended dry periods can cause 40-60% crop loss without proper water management. Strategic irrigation and mulching can maintain 80-90% of normal yields.'
            ];
        }

        // Humidity trend analysis
        $avgHumidity = array_sum($humidity) / count($humidity);
        if ($avgHumidity > 80) {
            $planning[] = [
                'category' => 'humidity_planning',
                'icon' => 'fas fa-tint',
                'color' => '#8b5cf6',
                'title' => '💧 MEDIUM: High Humidity Period Expected',
                'description' => "Average humidity will be {$avgHumidity}% - High disease risk period requires preventive measures.",
                'urgency' => 'Apply preventive treatments',
                'recommendations' => [
                    'Apply copper fungicide spray every 3 days (2 tbsp per gallon)',
                    'Ensure 3-foot spacing between plants for air circulation',
                    'Water only at soil level - never wet the leaves',
                    'Remove any diseased leaves immediately',
                    'Install fans to improve air circulation',
                    'Apply neem oil spray as preventive measure (1 tbsp per quart)'
                ],
                'mitigation' => [
                    'Use drip irrigation to keep leaves dry',
                    'Apply baking soda spray weekly (1 tsp per quart water)',
                    'Plant disease-resistant varieties',
                    'Improve soil drainage to reduce humidity'
                ],
                'ai_reasoning' => "High humidity ({$avgHumidity}%) increases fungal disease risk by 400%. Preventive treatment reduces disease incidence by 70-85%."
            ];
        }

        return $planning;
    }

    /**
     * Get crop-specific advice based on farm's crops
     */
    protected function getCropSpecificAdvice($farm, $currentWeather, $forecast = null)
    {
        $advice = [];
        
        // Get current crops from the farm
        $crops = $this->getFarmCrops($farm);
        
        if (empty($crops)) {
            return [
                [
                    'category' => 'crop_advice',
                    'title' => '🌱 General Crop Care',
                    'description' => 'No specific crop information. Here are general tips.',
                    'recommendations' => [
                        'Add your crop information for better advice',
                        'Check your plants every day',
                        'Follow basic farming practices'
                    ]
                ]
            ];
        }

        foreach ($crops as $crop) {
            $cropAdvice = $this->getCropSpecificRecommendations($crop, $currentWeather, $forecast);
            if (!empty($cropAdvice)) {
                $advice[] = $cropAdvice;
            }
        }

        return $advice;
    }

    /**
     * Get specific recommendations for a crop
     */
    protected function getCropSpecificRecommendations($crop, $currentWeather, $forecast = null)
    {
        $cropType = strtolower($crop['crop_type'] ?? '');
        $growthStage = strtolower($crop['growth_stage'] ?? '');
        $temperature = $currentWeather['temperature'];
        $humidity = $currentWeather['humidity'];
        $windSpeed = $currentWeather['wind_speed'];

        $recommendations = [];
        $mitigation = [];
        $icon = 'fas fa-seedling';
        $color = '#10b981';
        $priority = 'medium';

        // Rice-specific recommendations
        if (strpos($cropType, 'rice') !== false) {
            $icon = 'fas fa-wheat-awn';
            $color = '#059669';
            
            if ($growthStage === 'vegetative' || $growthStage === 'tillering') {
                if ($temperature > 30) {
                    $priority = 'high';
                    $recommendations[] = 'Maintain 3-5cm water depth in rice field to cool plants';
                    $recommendations[] = 'Apply nitrogen fertilizer (20-30 kg/ha) during early morning (6-8 AM)';
                    $recommendations[] = 'Check water temperature - if above 35°C, add cooler water';
                    $recommendations[] = 'Monitor for heat stress symptoms: yellowing leaves, stunted growth';
                }
                if ($humidity > 80) {
                    $priority = 'high';
                    $recommendations[] = 'Apply fungicide spray (tricyclazole 75% WP, 1.5g per liter) for rice blast prevention';
                    $recommendations[] = 'Ensure proper drainage - water should flow through field, not stagnate';
                    $recommendations[] = 'Remove infected plant debris immediately to prevent disease spread';
                    $recommendations[] = 'Space plants 20x20cm for better air circulation';
                }
            } elseif ($growthStage === 'flowering' || $growthStage === 'grain_filling') {
                if ($temperature > 35) {
                    $priority = 'critical';
                    $recommendations[] = 'CRITICAL: Maintain 5-7cm water depth during flowering stage';
                    $recommendations[] = 'Apply potassium fertilizer (40-50 kg/ha) to improve heat tolerance';
                    $recommendations[] = 'Water early morning (5-6 AM) to cool panicles';
                    $recommendations[] = 'Monitor for spikelet sterility - check 10 panicles per 100 sq meters';
                }
            }
        }

        // Corn-specific recommendations
        if (strpos($cropType, 'corn') !== false || strpos($cropType, 'maize') !== false) {
            $icon = 'fas fa-corn';
            $color = '#f59e0b';
            
            if ($growthStage === 'vegetative' || $growthStage === 'tasseling') {
                if ($temperature > 32) {
                    $priority = 'high';
                    $recommendations[] = 'Water corn every 2-3 days during tasseling (2-3 gallons per plant)';
                    $recommendations[] = 'Apply potassium fertilizer (50-60 kg/ha) to improve heat stress tolerance';
                    $recommendations[] = 'Install shade cloth (30% shade) over corn rows during peak heat';
                    $recommendations[] = 'Monitor for tassel blasting - check 10 plants per 100 sq meters';
                }
                if ($humidity > 85) {
                    $priority = 'high';
                    $recommendations[] = 'Apply fungicide (propiconazole 25% EC, 1ml per liter) for corn rust prevention';
                    $recommendations[] = 'Ensure 75cm row spacing and 25cm plant spacing for air circulation';
                    $recommendations[] = 'Remove lower leaves that touch the ground to prevent disease';
                }
            }
        }

        // Tomato recommendations
        if (strpos($cropType, 'tomato') !== false) {
            $icon = 'fas fa-apple-alt';
            $color = '#dc2626';
            
            if ($temperature > 30) {
                $priority = 'high';
                $recommendations[] = 'Install 50-70% shade cloth over tomato plants during 10 AM - 4 PM';
                $recommendations[] = 'Water deeply every morning (1-2 gallons per plant) and evening (1 gallon)';
                $recommendations[] = 'Apply 3-4 inch mulch layer (straw or plastic) around plants';
                $recommendations[] = 'Monitor for blossom drop - maintain soil moisture at 70-80%';
                $recommendations[] = 'Apply calcium nitrate (2-3 tbsp per gallon) to prevent blossom end rot';
            }
            if ($humidity > 80) {
                $priority = 'high';
                $recommendations[] = 'Spray copper fungicide (2 tbsp per gallon) every 7-10 days';
                $recommendations[] = 'Ensure 60cm spacing between plants for air circulation';
                $recommendations[] = 'Remove suckers and lower leaves up to first fruit cluster';
                $recommendations[] = 'Use drip irrigation to keep leaves dry';
            }
        }

        // Pepper recommendations
        if (strpos($cropType, 'pepper') !== false) {
            $icon = 'fas fa-pepper-hot';
            $color = '#f59e0b';
            
            if ($temperature > 30) {
                $priority = 'high';
                $recommendations[] = 'Provide 40-60% shade during hottest hours (11 AM - 3 PM)';
                $recommendations[] = 'Water 1-1.5 gallons per plant twice daily (morning and evening)';
                $recommendations[] = 'Apply white plastic mulch to reflect heat and retain moisture';
                $recommendations[] = 'Monitor for flower drop - maintain consistent soil moisture';
            }
            if ($humidity > 80) {
                $priority = 'high';
                $recommendations[] = 'Apply neem oil spray (1 tbsp per quart) every 5-7 days';
                $recommendations[] = 'Ensure 45cm spacing between plants';
                $recommendations[] = 'Remove any diseased leaves immediately';
            }
        }

        // Cucumber recommendations
        if (strpos($cropType, 'cucumber') !== false) {
            $icon = 'fas fa-seedling';
            $color = '#10b981';
            
            if ($temperature > 30) {
                $priority = 'high';
                $recommendations[] = 'Install trellis system and provide 30-50% shade';
                $recommendations[] = 'Water 1-2 gallons per plant daily (morning)';
                $recommendations[] = 'Apply organic mulch 2-3 inches thick';
                $recommendations[] = 'Monitor for bitter fruit - maintain consistent watering';
            }
            if ($humidity > 80) {
                $priority = 'high';
                $recommendations[] = 'Spray copper fungicide (1.5 tbsp per gallon) every 7 days';
                $recommendations[] = 'Ensure 30cm spacing between plants';
                $recommendations[] = 'Remove lower leaves up to 30cm from ground';
            }
        }

        // General vegetable recommendations
        if (in_array($cropType, ['lettuce', 'spinach', 'cabbage', 'broccoli'])) {
            $icon = 'fas fa-leaf';
            $color = '#10b981';
            
            if ($temperature > 25) {
                $priority = 'medium';
                $recommendations[] = 'Provide 50-70% shade cloth during hot periods';
                $recommendations[] = 'Water 0.5-1 gallon per plant daily';
                $recommendations[] = 'Apply 2-3 inch mulch layer to keep soil cool';
                $recommendations[] = 'Harvest in early morning to prevent wilting';
            }
        }

        if (empty($recommendations)) {
            return null;
        }

        return [
            'category' => 'crop_advice',
            'icon' => $icon,
            'color' => $color,
            'priority' => $priority,
            'title' => '🌾 ' . ucfirst($cropType) . ' (' . ucfirst($growthStage) . ') - Specific Care',
            'description' => "Specialized care instructions for your {$cropType} plants in current weather conditions.",
            'urgency' => $priority === 'critical' ? 'Immediate action required' : ($priority === 'high' ? 'Within 4 hours' : 'Within 8 hours'),
            'recommendations' => $recommendations,
            'mitigation' => $mitigation,
            'ai_reasoning' => "Based on {$cropType} growth stage '{$growthStage}' and current weather (temp: {$temperature}°C, humidity: {$humidity}%, wind: {$windSpeed} km/h)."
        ];
    }

    /**
     * Get risk assessment based on weather conditions
     */
    protected function getRiskAssessment($farm, $currentWeather, $forecast = null, $alerts = [])
    {
        $risks = [];
        $temperature = $currentWeather['temperature'];
        $humidity = $currentWeather['humidity'];
        $windSpeed = $currentWeather['wind_speed'];

        // Heat stress risk
        if ($temperature > 35) {
            $risks[] = [
                'level' => 'critical',
                'type' => 'heat_stress',
                'icon' => 'fas fa-thermometer-full',
                'color' => '#dc2626',
                'title' => '🌡️ CRITICAL: Extreme Heat Stress Risk',
                'description' => "Temperature {$temperature}°C poses severe risk - Crops can suffer irreversible damage within 2-4 hours.",
                'urgency' => 'Immediate action required',
                'risk_factors' => [
                    'Plant wilting and leaf burn (visible within 1-2 hours)',
                    'Blossom drop and fruit abortion (30-50% yield loss)',
                    'Root damage from overheated soil (permanent damage)',
                    'Increased pest activity due to stressed plants'
                ],
                'mitigation' => [
                    'Install 70% shade cloth immediately over all crops',
                    'Apply 4-6 inch organic mulch to cool soil temperature',
                    'Water every 2 hours with 2-3 gallons per plant',
                    'Use misting system to cool plant canopies',
                    'Move potted plants to shaded areas',
                    'Apply anti-transpirant spray to reduce water loss'
                ],
                'monitoring' => [
                    'Check soil temperature every hour (should be below 30°C)',
                    'Monitor leaf temperature with infrared thermometer',
                    'Watch for wilting, yellowing, or curling leaves',
                    'Check for blossom drop and fruit damage'
                ],
                'ai_reasoning' => "At {$temperature}°C, plants experience severe heat stress. Without immediate intervention, you could lose 50-80% of your harvest within 24-48 hours."
            ];
        } elseif ($temperature > 30) {
            $risks[] = [
                'level' => 'high',
                'type' => 'heat_stress',
                'icon' => 'fas fa-thermometer-three-quarters',
                'color' => '#f59e0b',
                'title' => '🌡️ HIGH: Heat Stress Risk',
                'description' => "Temperature {$temperature}°C creates moderate heat stress - Monitor crops closely for stress symptoms.",
                'urgency' => 'Within 4 hours',
                'risk_factors' => [
                    'Reduced photosynthesis efficiency (20-30% decrease)',
                    'Increased water consumption (2-3x normal)',
                    'Potential blossom drop in flowering plants',
                    'Slower growth and delayed maturity'
                ],
                'mitigation' => [
                    'Provide 50% shade cloth during peak hours (10 AM - 4 PM)',
                    'Water deeply every morning (1-2 gallons per plant)',
                    'Apply 3-4 inch mulch layer around all plants',
                    'Use drip irrigation for consistent moisture',
                    'Group plants together for mutual shade'
                ],
                'monitoring' => [
                    'Check soil moisture every 4 hours',
                    'Monitor for wilting or drooping leaves',
                    'Watch for early flowering or bolting in leafy crops',
                    'Check fruit quality and size development'
                ],
                'ai_reasoning' => "At {$temperature}°C, plants experience moderate heat stress. Proper management can maintain 80-90% of normal yields."
            ];
        }

        // Disease risk
        if ($humidity > 85) {
            $risks[] = [
                'level' => 'high',
                'type' => 'disease',
                'icon' => 'fas fa-virus',
                'color' => '#8b5cf6',
                'title' => '🦠 HIGH: Fungal Disease Risk',
                'description' => "Humidity {$humidity}% creates ideal conditions for fungal diseases - High risk of crop infection.",
                'urgency' => 'Within 6 hours',
                'risk_factors' => [
                    'Powdery mildew (white powdery coating on leaves)',
                    'Downy mildew (yellow spots with fuzzy undersides)',
                    'Bacterial leaf spot (dark spots with yellow halos)',
                    'Root rot and stem cankers in wet soil'
                ],
                'mitigation' => [
                    'Apply copper fungicide spray (2 tbsp per gallon) immediately',
                    'Ensure 3-foot spacing between plants for air circulation',
                    'Install fans to improve air movement around plants',
                    'Water only at soil level - never wet the leaves',
                    'Remove any diseased plant material immediately',
                    'Apply neem oil spray (1 tbsp per quart) as preventive'
                ],
                'monitoring' => [
                    'Check under leaves for white powdery growth',
                    'Look for yellow or brown spots on leaves',
                    'Monitor for wilting despite adequate moisture',
                    'Check for stunted growth or distorted leaves'
                ],
                'ai_reasoning' => "At {$humidity}% humidity, fungal spores germinate 5x faster. Without treatment, disease can spread to 60-80% of your crop within 3-5 days."
            ];
        }

        // Wind damage risk
        if ($windSpeed > 25) {
            $risks[] = [
                'level' => 'critical',
                'type' => 'wind_damage',
                'icon' => 'fas fa-wind',
                'color' => '#6b7280',
                'title' => '💨 CRITICAL: Wind Damage Risk',
                'description' => "Wind speed {$windSpeed} km/h poses severe risk - Structural damage and crop loss likely.",
                'urgency' => 'Immediate action required',
                'risk_factors' => [
                    'Plant stem breakage and lodging (permanent damage)',
                    'Fruit abrasion and quality loss (30-50% damage)',
                    'Greenhouse structure failure (complete crop loss)',
                    'Soil erosion and root exposure'
                ],
                'mitigation' => [
                    'Secure all trellises with 12-gauge wire every 3 feet',
                    'Tie down greenhouse plastic with rope every 2 feet',
                    'Install wind barriers using bamboo screens (6-8 feet high)',
                    'Stake all plants taller than 2 feet with 4-foot stakes',
                    'Harvest all ripe crops immediately',
                    'Move potted plants to protected areas'
                ],
                'monitoring' => [
                    'Check plant supports every 2 hours',
                    'Monitor for leaning or swaying plants',
                    'Watch for torn or damaged greenhouse covers',
                    'Check for soil erosion around plant bases'
                ],
                'ai_reasoning' => "At {$windSpeed} km/h, wind can cause severe structural damage. Immediate action prevents 70-90% of potential crop and equipment damage."
            ];
        } elseif ($windSpeed > 15) {
            $risks[] = [
                'level' => 'medium',
                'type' => 'wind_damage',
                'icon' => 'fas fa-wind',
                'color' => '#f59e0b',
                'title' => '💨 MEDIUM: Wind Damage Risk',
                'description' => "Wind speed {$windSpeed} km/h may cause minor damage - Secure loose items and monitor crops.",
                'urgency' => 'Within 3 hours',
                'risk_factors' => [
                    'Minor stem bending and leaf damage',
                    'Reduced pesticide effectiveness due to drift',
                    'Potential trellis or support loosening',
                    'Soil drying and increased water needs'
                ],
                'mitigation' => [
                    'Check and tighten all plant supports',
                    'Avoid spraying pesticides or fertilizers',
                    'Harvest delicate crops like lettuce and herbs',
                    'Install temporary wind barriers for young plants',
                    'Group potted plants together for stability'
                ],
                'monitoring' => [
                    'Check plant supports every 4 hours',
                    'Monitor for leaning or swaying plants',
                    'Watch for leaf damage or abrasion',
                    'Check soil moisture more frequently'
                ],
                'ai_reasoning' => "At {$windSpeed} km/h, wind can cause minor damage to sensitive plants. Proper preparation prevents most potential issues."
            ];
        }

        return $risks;
    }

    /**
     * Get optimization tips for better farm management
     */
    protected function getOptimizationTips($farm, $currentWeather, $forecast = null)
    {
        $tips = [];
        $temperature = $currentWeather['temperature'];
        $humidity = $currentWeather['humidity'];
        $windSpeed = $currentWeather['wind_speed'];

        // Water management optimization
        if ($humidity < 50 && $temperature > 25) {
            $tips[] = [
                'category' => 'water_optimization',
                'icon' => 'fas fa-tint',
                'color' => '#3b82f6',
                'title' => '💧 HIGH: Water Conservation Required',
                'description' => "Low humidity ({$humidity}%) and high temperature ({$temperature}°C) - Implement water-saving strategies immediately.",
                'urgency' => 'Within 6 hours',
                'tips' => [
                    'Install drip irrigation system to save 40-60% water usage',
                    'Water early morning (5-7 AM) for 30% better absorption',
                    'Apply 4-6 inch organic mulch to reduce evaporation by 50%',
                    'Use soil moisture sensors to water only when needed',
                    'Group plants by water needs (high, medium, low) for efficient irrigation',
                    'Install ollas (clay pots) for slow-release irrigation',
                    'Use water-absorbing crystals (1 cup per plant) to retain moisture'
                ],
                'mitigation' => [
                    'Create swales to capture and store rainwater',
                    'Install rain barrels (50-100 gallon capacity) for water storage',
                    'Use greywater from household for non-edible plants',
                    'Plant drought-resistant varieties for future seasons'
                ],
                'ai_reasoning' => "Current conditions increase water needs by 200-300%. Proper water management can reduce usage by 40-60% while maintaining crop health."
            ];
        }

        // Energy optimization
        if ($temperature > 30) {
            $tips[] = [
                'category' => 'energy_optimization',
                'icon' => 'fas fa-bolt',
                'color' => '#f59e0b',
                'title' => '⚡ HIGH: Energy Cost Reduction',
                'description' => "High temperature ({$temperature}°C) increases energy costs - Implement energy-saving strategies.",
                'urgency' => 'Within 8 hours',
                'tips' => [
                    'Use 50-70% shade cloth instead of cooling systems (saves 80% energy)',
                    'Water during cooler hours (5-7 AM, 6-8 PM) to reduce evaporation',
                    'Install solar-powered irrigation pumps (saves 100% electricity)',
                    'Use white plastic mulch to reflect heat and reduce soil temperature',
                    'Install automatic ventilation systems for greenhouses',
                    'Use LED grow lights instead of traditional bulbs (saves 60% energy)',
                    'Group heat-sensitive plants in shaded areas'
                ],
                'mitigation' => [
                    'Install solar panels for farm electricity needs',
                    'Use thermal mass (water barrels) to stabilize temperatures',
                    'Plant windbreaks to reduce cooling needs',
                    'Use energy-efficient irrigation timers'
                ],
                'ai_reasoning' => "High temperatures increase energy costs by 150-200%. These strategies can reduce energy usage by 60-80% while maintaining optimal growing conditions."
            ];
        }

        // Wind optimization
        if ($windSpeed > 15) {
            $tips[] = [
                'category' => 'wind_optimization',
                'icon' => 'fas fa-wind',
                'color' => '#6b7280',
                'title' => '💨 MEDIUM: Wind Energy Utilization',
                'description' => "Wind speed {$windSpeed} km/h - Consider using wind energy for farm operations.",
                'urgency' => 'Consider for future planning',
                'tips' => [
                    'Install small wind turbines for irrigation pumps',
                    'Use wind to improve air circulation in greenhouses',
                    'Position fans strategically to maximize natural airflow',
                    'Consider wind-powered water pumps for remote areas',
                    'Use wind to dry harvested crops naturally',
                    'Install wind chimes to monitor wind direction and speed'
                ],
                'mitigation' => [
                    'Plant windbreaks to create microclimates',
                    'Use wind to power simple farm equipment',
                    'Position structures to maximize wind benefits',
                    'Consider wind as free energy source for future expansion'
                ],
                'ai_reasoning' => "Wind speed {$windSpeed} km/h can be harnessed for energy. Small wind systems can provide 20-40% of farm energy needs."
            ];
        }

        // General optimization tips
        $tips[] = [
            'category' => 'general_optimization',
            'icon' => 'fas fa-lightbulb',
            'color' => '#10b981',
            'title' => '💡 GENERAL: Farm Efficiency Tips',
            'description' => 'General optimization strategies to improve farm efficiency and reduce costs.',
            'urgency' => 'Ongoing implementation',
            'tips' => [
                'Use companion planting to reduce pest problems (marigolds with tomatoes)',
                'Implement crop rotation to improve soil health and reduce disease',
                'Use organic fertilizers to reduce chemical costs by 30-50%',
                'Install weather stations for accurate local weather data',
                'Keep detailed farm records to identify patterns and optimize practices',
                'Use cover crops to improve soil structure and reduce erosion',
                'Implement integrated pest management to reduce pesticide use'
            ],
            'mitigation' => [
                'Join farmer cooperatives for bulk purchasing discounts',
                'Use mobile apps for farm management and record keeping',
                'Attend agricultural extension workshops for new techniques',
                'Network with other farmers to share knowledge and resources'
            ],
            'ai_reasoning' => 'These general optimization strategies can improve farm efficiency by 20-40% and reduce operating costs by 15-30% over time.'
        ];

        return $tips;
    }

    /**
     * Get seasonal insights based on current date and weather
     */
    protected function getSeasonalInsights($farm, $currentWeather)
    {
        $month = (int) date('n');
        $temperature = $currentWeather['temperature'];
        $humidity = $currentWeather['humidity'];
        $insights = [];

        // Philippine seasonal patterns
        if ($month >= 3 && $month <= 5) {
            // Summer (March-May)
            $insights[] = [
                'category' => 'seasonal',
                'icon' => 'fas fa-sun',
                'color' => '#f59e0b',
                'title' => '☀️ SUMMER SEASON: Hot Weather Management',
                'description' => 'March-May is peak summer - Implement heat management strategies for optimal harvest.',
                'urgency' => 'Seasonal planning required',
                'insights' => [
                    'Plant heat-tolerant crops: okra, eggplant, bitter gourd, ampalaya',
                    'Water crops 2-3 times daily (5-7 AM, 11 AM, 6-8 PM)',
                    'Apply 4-6 inch mulch layer to reduce soil temperature by 5-10°C',
                    'Choose drought-resistant varieties: drought-tolerant rice, heat-resistant tomatoes',
                    'Plant shade crops between rows: corn, sunflower, or tall legumes',
                    'Install 50-70% shade cloth over sensitive crops during peak heat',
                    'Harvest crops early morning (5-7 AM) to prevent wilting'
                ],
                'mitigation' => [
                    'Use raised beds (6-8 inches) to improve drainage and root cooling',
                    'Install drip irrigation with timers for consistent moisture',
                    'Create windbreaks using fast-growing trees (banana, bamboo)',
                    'Use white plastic mulch to reflect heat and reduce soil temperature'
                ],
                'ai_reasoning' => 'Summer temperatures can reach 35-40°C. Proper heat management can maintain 80-90% of normal yields while preventing crop stress.'
            ];
        } elseif ($month >= 6 && $month <= 8) {
            // Rainy season (June-August)
            $insights[] = [
                'category' => 'seasonal',
                'icon' => 'fas fa-cloud-rain',
                'color' => '#3b82f6',
                'title' => '🌧️ RAINY SEASON: Wet Weather Management',
                'description' => 'June-August is peak rainy season - Implement flood and disease prevention strategies.',
                'urgency' => 'Seasonal planning required',
                'insights' => [
                    'Plant water-loving crops: rice, taro, water spinach, kangkong',
                    'Install raised beds (8-12 inches) for all vegetables',
                    'Apply fungicide every 7-10 days (copper-based, 2 tbsp per gallon)',
                    'Choose flood-resistant varieties: submergence-tolerant rice',
                    'Create drainage ditches (6-8 inches deep, 2-3% slope)',
                    'Use water-permeable landscape fabric to prevent soil erosion',
                    'Plant cover crops to prevent soil loss during heavy rains'
                ],
                'mitigation' => [
                    'Install French drains around low-lying areas',
                    'Use floating gardens for vegetables in flood-prone areas',
                    'Create swales to capture and store rainwater',
                    'Plant flood-tolerant trees as windbreaks and erosion control'
                ],
                'ai_reasoning' => 'Rainy season brings 70-80% of annual rainfall. Proper water management prevents 30-50% crop loss from flooding and disease.'
            ];
        } elseif ($month >= 9 && $month <= 11) {
            // Transition season (September-November)
            $insights[] = [
                'category' => 'seasonal',
                'icon' => 'fas fa-leaf',
                'color' => '#10b981',
                'title' => '🍂 TRANSITION SEASON: Variable Weather Management',
                'description' => 'September-November is transition period - Prepare for changing weather patterns.',
                'urgency' => 'Flexible planning required',
                'insights' => [
                    'Plant both wet and dry season crops for continuous harvest',
                    'Monitor weather changes every 2-3 days and adjust plans accordingly',
                    'Use adaptable irrigation systems (drip + overhead)',
                    'Plant quick-maturing crops: lettuce, radish, spinach (30-45 days)',
                    'Prepare for typhoon season: secure structures, harvest early',
                    'Use row covers that can be easily removed or added',
                    'Plan crop rotation for next season based on soil conditions'
                ],
                'mitigation' => [
                    'Install modular greenhouse systems for weather protection',
                    'Use weather-resistant crop varieties',
                    'Create microclimates using strategic plant placement',
                    'Maintain flexible irrigation and drainage systems'
                ],
                'ai_reasoning' => 'Transition season brings unpredictable weather. Flexible management allows 60-80% success rate across different weather conditions.'
            ];
        } else {
            // Cool season (December-February)
            $insights[] = [
                'category' => 'seasonal',
                'icon' => 'fas fa-snowflake',
                'color' => '#3b82f6',
                'title' => '❄️ COOL SEASON: Cold Weather Management',
                'description' => 'December-February is cool season - Implement cold protection strategies.',
                'urgency' => 'Cold protection required',
                'insights' => [
                    'Plant cool-season vegetables: lettuce, cabbage, broccoli, carrots',
                    'Use greenhouses or cold frames to control temperature (15-25°C)',
                    'Plan for shorter daylight hours (10-11 hours) - use grow lights if needed',
                    'Focus on root crops and leafy vegetables that tolerate cool weather',
                    'Apply frost protection: row covers, plastic sheets, or cloth',
                    'Water less frequently but deeply to prevent root rot',
                    'Use black plastic mulch to warm soil temperature'
                ],
                'mitigation' => [
                    'Install heating systems in greenhouses for extreme cold',
                    'Use thermal mass (water barrels) to stabilize temperatures',
                    'Plant cold-tolerant varieties and hybrids',
                    'Create windbreaks to reduce cold wind exposure'
                ],
                'ai_reasoning' => 'Cool season temperatures (15-25°C) are ideal for many crops. Proper cold management can achieve 90-95% of optimal yields.'
            ];
        }

        // Add current weather-specific seasonal insights
        if ($temperature > 30 && $month >= 3 && $month <= 5) {
            $insights[] = [
                'category' => 'seasonal',
                'icon' => 'fas fa-thermometer-full',
                'color' => '#dc2626',
                'title' => '🌡️ SUMMER HEAT ALERT: Extra Protection Needed',
                'description' => "Current temperature {$temperature}°C during summer season - Implement additional heat protection measures.",
                'urgency' => 'Immediate action required',
                'insights' => [
                    'Increase watering frequency to 3-4 times daily',
                    'Apply additional mulch layer (6-8 inches) around all plants',
                    'Install temporary shade structures over all crops',
                    'Harvest crops 2-3 hours earlier than usual to prevent heat damage',
                    'Use misting systems to cool plant canopies',
                    'Apply anti-transpirant spray to reduce water loss'
                ],
                'ai_reasoning' => 'Summer heat above 30°C requires extra protection beyond normal seasonal practices to prevent crop damage.'
            ];
        }

        return $insights;
    }

    /**
     * Calculate AI confidence level for recommendations
     */
    protected function calculateAIConfidence($farm, $currentWeather, $forecast = null)
    {
        $confidence = 70; // Base confidence

        // Increase confidence based on data quality
        if ($forecast && count($forecast) >= 5) {
            $confidence += 10;
        }

        if ($farm->crop_growths()->count() > 0) {
            $confidence += 10;
        }

        if (isset($currentWeather['data_source']) && $currentWeather['data_source'] === 'OpenWeatherMap') {
            $confidence += 10;
        }

        return min($confidence, 95); // Cap at 95%
    }

    /**
     * Get farm crops for analysis
     */
    protected function getFarmCrops($farm)
    {
        return $farm->crop_growths()
            ->where('status', 'active')
            ->get(['crop_type', 'growth_stage', 'planting_date'])
            ->toArray();
    }

    /**
     * Get fallback recommendations when AI analysis fails
     */
    protected function getFallbackRecommendations($farm, $currentWeather)
    {
        return [
            'immediate_actions' => [
                [
                    'priority' => 'medium',
                    'category' => 'general',
                    'title' => '🌱 General Farm Care',
                    'description' => 'Basic tips based on today\'s weather.',
                    'actions' => [
                        'Check your crops every day',
                        'Make sure they get enough water',
                        'Look for pests and diseases',
                        'Follow basic farming practices'
                    ],
                    'ai_reasoning' => 'Basic tips provided due to limited data.'
                ]
            ],
            'short_term_planning' => [],
            'crop_specific_advice' => [],
            'risk_assessment' => [],
            'optimization_tips' => [],
            'seasonal_insights' => [],
            'ai_confidence' => 50
        ];
    }
}
