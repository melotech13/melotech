<?php

namespace App\Services;

class WatermelonRecommendationService
{
    /**
     * Generate AI-powered recommendations for watermelon farming based on weather and growth stage
     */
    public function generateRecommendations($weatherData, $growthStage = null)
    {
        $current = $weatherData['current'] ?? [];
        $forecast = $weatherData['forecast'] ?? [];
        
        // Determine growth stage if not provided
        if (!$growthStage) {
            $growthStage = $this->determineGrowthStage($current, $forecast);
        }
        
		$recommendations = [];
		$usedIds = [];
        
        // 1. Growth stage-specific recommendations
		$stageRecommendations = $this->getGrowthStageRecommendations($current, $forecast, $growthStage);
		if ($stageRecommendations) {
			$section = $this->sanitizeSection($stageRecommendations);
			if ($section && !in_array($section['id'], $usedIds, true)) {
				$recommendations[] = $section;
				$usedIds[] = $section['id'];
			}
		}
        
        // 2. Weather-based recommendations for watermelon
		$weatherRecommendations = $this->getWatermelonWeatherRecommendations($current, $forecast, $growthStage);
		if ($weatherRecommendations) {
			$section = $this->sanitizeSection($weatherRecommendations);
			if ($section && !in_array($section['id'], $usedIds, true)) {
				$recommendations[] = $section;
				$usedIds[] = $section['id'];
			}
		}
        
        // 3. Pest and disease management based on weather
		$pestRecommendations = $this->getPestDiseaseRecommendations($current, $forecast, $growthStage);
		if ($pestRecommendations) {
			$section = $this->sanitizeSection($pestRecommendations);
			if ($section && !in_array($section['id'], $usedIds, true)) {
				$recommendations[] = $section;
				$usedIds[] = $section['id'];
			}
		}
        
        // 4. Irrigation and nutrition recommendations
		$irrigationRecommendations = $this->getIrrigationNutritionRecommendations($current, $forecast, $growthStage);
		if ($irrigationRecommendations) {
			$section = $this->sanitizeSection($irrigationRecommendations);
			if ($section && !in_array($section['id'], $usedIds, true)) {
				$recommendations[] = $section;
				$usedIds[] = $section['id'];
			}
		}
        
		// Ensure we have exactly 4 unique sections; fill with distinct general tips
		while (count($recommendations) < 4) {
			$general = $this->getGeneralWatermelonRecommendation($growthStage);
			$general = $this->sanitizeSection($general);
			if ($general && !in_array($general['id'], $usedIds, true)) {
				$recommendations[] = $general;
				$usedIds[] = $general['id'];
			}
			// Prevent potential infinite loop: if both general tips are used, break
			if (count($usedIds) >= 10) {
				break;
			}
		}
        
		return array_slice($recommendations, 0, 4);
    }

	/**
	 * Normalize a section: ensure unique inner recommendations and required keys
	 */
	private function sanitizeSection(?array $section): ?array
	{
		if (!$section) {
			return null;
		}
		if (isset($section['recommendations']) && is_array($section['recommendations'])) {
			$unique = array_values(array_filter(array_unique($section['recommendations']), function ($line) {
				return is_string($line) && trim($line) !== '';
			}));
			$section['recommendations'] = $unique;
		}
		return $section;
	}
    
    /**
     * Determine watermelon growth stage based on weather and time patterns
     */
    private function determineGrowthStage($current, $forecast)
    {
        // This is a simplified approach - in a real system, you'd track actual planting dates
        $month = (int) date('n');
        $temp = $current['temperature'] ?? 25;
        
        // Basic growth stage estimation based on season and temperature
        if ($month >= 3 && $month <= 4) {
            return 'germination'; // March-April: Planting season
        } elseif ($month >= 5 && $month <= 6) {
            return 'vegetative'; // May-June: Vegetative growth
        } elseif ($month >= 7 && $month <= 8) {
            return 'flowering'; // July-August: Flowering and fruit set
        } elseif ($month >= 9 && $month <= 10) {
            return 'fruit_development'; // September-October: Fruit development
        } elseif ($month >= 11 && $month <= 12) {
            return 'harvest'; // November-December: Harvest season
        } else {
            return 'dormant'; // January-February: Dormant period
        }
    }
    
    /**
     * Get growth stage-specific recommendations
     */
    private function getGrowthStageRecommendations($current, $forecast, $growthStage)
    {
        $temp = $current['temperature'] ?? 25;
        $humidity = $current['humidity'] ?? 60;
        
        switch ($growthStage) {
            case 'germination':
                return [
                    'id' => 'germination_stage',
                    'title' => 'Germination & Early Growth',
                    'icon' => 'fas fa-seedling',
                    'color' => '#10b981',
                    'bg_color' => '#ecfdf5',
                    'border_color' => '#10b981',
                    'priority' => 'high',
                    'recommendations' => [
                        'Maintain soil temperature between 24-30°C for optimal germination',
                        'Keep soil consistently moist but not waterlogged',
                        'Protect young seedlings from strong winds and heavy rain',
                        'Apply light mulch to retain soil moisture and warmth',
                        'Monitor for damping-off disease in humid conditions'
                    ],
                    'reasoning' => 'Watermelon seeds require warm, moist conditions for successful germination. Proper temperature and moisture management are critical during this stage.'
                ];
                
            case 'vegetative':
                return [
                    'id' => 'vegetative_stage',
                    'title' => 'Vegetative Growth Management',
                    'icon' => 'fas fa-leaf',
                    'color' => '#059669',
                    'bg_color' => '#f0fdf4',
                    'border_color' => '#059669',
                    'priority' => 'medium',
                    'recommendations' => [
                        'Ensure adequate spacing (2-3 feet between plants)',
                        'Apply balanced fertilizer (N-P-K 10-10-10) every 2 weeks',
                        'Train vines to grow in desired direction',
                        'Remove early flowers to promote vegetative growth',
                        'Monitor for powdery mildew in humid conditions'
                    ],
                    'reasoning' => 'Strong vegetative growth is essential for healthy watermelon plants. Proper nutrition and vine management during this stage determine future fruit production.'
                ];
                
            case 'flowering':
                return [
                    'id' => 'flowering_stage',
                    'title' => 'Flowering & Pollination',
                    'icon' => 'fas fa-flower',
                    'color' => '#ec4899',
                    'bg_color' => '#fdf2f8',
                    'border_color' => '#ec4899',
                    'priority' => 'high',
                    'recommendations' => [
                        'Ensure adequate bee activity for pollination',
                        'Avoid overhead watering during flowering',
                        'Apply phosphorus-rich fertilizer to promote flowering',
                        'Protect flowers from heavy rain and strong winds',
                        'Hand-pollinate if bee activity is low'
                    ],
                    'reasoning' => 'Successful pollination is crucial for fruit set. Weather conditions during flowering directly impact fruit development and yield.'
                ];
                
            case 'fruit_development':
                return [
                    'id' => 'fruit_development_stage',
                    'title' => 'Fruit Development & Growth',
                    'icon' => 'fas fa-apple-alt',
                    'color' => '#f59e0b',
                    'bg_color' => '#fffbeb',
                    'border_color' => '#f59e0b',
                    'priority' => 'high',
                    'recommendations' => [
                        'Maintain consistent soil moisture (1-2 inches per week)',
                        'Apply potassium-rich fertilizer for fruit development',
                        'Support heavy fruits with slings or supports',
                        'Rotate fruits regularly to prevent sunburn',
                        'Monitor for fruit rot in humid conditions'
                    ],
                    'reasoning' => 'Fruit development requires consistent moisture and nutrition. Weather stress during this stage can cause fruit cracking or poor quality.'
                ];
                
            case 'harvest':
                return [
                    'id' => 'harvest_stage',
                    'title' => 'Harvest Preparation',
                    'icon' => 'fas fa-harvest',
                    'color' => '#dc2626',
                    'bg_color' => '#fef2f2',
                    'border_color' => '#dc2626',
                    'priority' => 'high',
                    'recommendations' => [
                        'Check for harvest readiness (yellow ground spot, hollow sound)',
                        'Harvest in early morning when temperatures are cool',
                        'Stop watering 3-5 days before harvest for better flavor',
                        'Handle fruits carefully to prevent bruising',
                        'Store in cool, dry place immediately after harvest'
                    ],
                    'reasoning' => 'Proper harvest timing and handling are crucial for fruit quality and shelf life. Weather conditions affect harvest timing and fruit quality.'
                ];
                
            default:
                return null;
        }
    }
    
    /**
     * Get weather-specific recommendations for watermelon
     */
    private function getWatermelonWeatherRecommendations($current, $forecast, $growthStage)
    {
        $temp = $current['temperature'] ?? 25;
        $humidity = $current['humidity'] ?? 60;
        $windSpeed = $current['wind_speed'] ?? 10;
        $description = strtolower($current['description'] ?? '');
        
        if ($temp > 35) {
            return [
                'id' => 'heat_stress_watermelon',
                'title' => 'Heat Stress Management',
                'icon' => 'fas fa-thermometer-full',
                'color' => '#f59e0b',
                'bg_color' => '#fef3c7',
                'border_color' => '#f59e0b',
                'priority' => 'high',
                'recommendations' => [
                    'Increase irrigation frequency to prevent heat stress',
                    'Apply mulch to keep soil cool and retain moisture',
                    'Provide temporary shade for young plants',
                    'Water early morning (5-7 AM) for maximum absorption',
                    'Monitor for blossom drop and fruit abortion'
                ],
                'reasoning' => 'Watermelons are sensitive to extreme heat. Temperatures above 35°C can cause flower drop, poor fruit set, and reduced sugar content.'
            ];
        } elseif ($temp < 15) {
            return [
                'id' => 'cold_protection_watermelon',
                'title' => 'Cold Protection for Watermelons',
                'icon' => 'fas fa-thermometer-empty',
                'color' => '#3b82f6',
                'bg_color' => '#eff6ff',
                'border_color' => '#3b82f6',
                'priority' => 'high',
                'recommendations' => [
                    'Cover plants with row covers or frost cloth',
                    'Use black plastic mulch to warm soil',
                    'Avoid planting until soil temperature reaches 18°C',
                    'Consider using hot caps for individual plants',
                    'Monitor for cold damage and adjust protection'
                ],
                'reasoning' => 'Watermelons are warm-season crops that cannot tolerate frost. Cold temperatures below 15°C can stunt growth or kill plants.'
            ];
        } elseif ($humidity > 85) {
            return [
                'id' => 'humidity_management_watermelon',
                'title' => 'High Humidity Management',
                'icon' => 'fas fa-tint',
                'color' => '#dc2626',
                'bg_color' => '#fef2f2',
                'border_color' => '#dc2626',
                'priority' => 'high',
                'recommendations' => [
                    'Improve air circulation around plants',
                    'Apply fungicide to prevent powdery mildew',
                    'Avoid overhead watering to reduce leaf wetness',
                    'Space plants properly for better ventilation',
                    'Remove diseased leaves immediately'
                ],
                'reasoning' => 'High humidity above 85% creates ideal conditions for fungal diseases like powdery mildew and anthracnose in watermelons.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get pest and disease management recommendations
     */
    private function getPestDiseaseRecommendations($current, $forecast, $growthStage)
    {
        $temp = $current['temperature'] ?? 25;
        $humidity = $current['humidity'] ?? 60;
        $description = strtolower($current['description'] ?? '');
        
        $recommendations = [];
        
        // Powdery mildew risk
        if ($humidity > 70 && $temp > 20) {
            $recommendations[] = 'Apply sulfur-based fungicide for powdery mildew prevention';
        }
        
        // Anthracnose risk
        if ($humidity > 80 && strpos($description, 'rain') !== false) {
            $recommendations[] = 'Monitor for anthracnose and apply copper-based fungicide';
        }
        
        // Aphid risk
        if ($temp > 25 && $humidity < 60) {
            $recommendations[] = 'Check for aphids and apply neem oil if needed';
        }
        
        // Cucumber beetle risk
        if ($temp > 20 && $growthStage === 'vegetative') {
            $recommendations[] = 'Monitor for cucumber beetles and use row covers';
        }
        
        if (empty($recommendations)) {
            return null;
        }
        
        return [
            'id' => 'pest_disease_management',
            'title' => 'Pest & Disease Management',
            'icon' => 'fas fa-shield-virus',
            'color' => '#8b5cf6',
            'bg_color' => '#faf5ff',
            'border_color' => '#8b5cf6',
            'priority' => 'medium',
            'recommendations' => $recommendations,
            'reasoning' => 'Weather conditions significantly impact pest and disease pressure in watermelons. Early detection and prevention are key to healthy crops.'
        ];
    }
    
    /**
     * Get irrigation and nutrition recommendations
     */
    private function getIrrigationNutritionRecommendations($current, $forecast, $growthStage)
    {
        $temp = $current['temperature'] ?? 25;
        $humidity = $current['humidity'] ?? 60;
        $description = strtolower($current['description'] ?? '');
        
        $isRaining = strpos($description, 'rain') !== false;
        $rainyDays = $this->countRainyDays($forecast);
        
        if ($isRaining || $rainyDays > 3) {
            return [
                'id' => 'irrigation_rainy',
                'title' => 'Rainy Weather Irrigation',
                'icon' => 'fas fa-cloud-rain',
                'color' => '#0ea5e9',
                'bg_color' => '#f0f9ff',
                'border_color' => '#0ea5e9',
                'priority' => 'medium',
                'recommendations' => [
                    'Reduce or stop irrigation during rainy periods',
                    'Ensure proper drainage to prevent waterlogging',
                    'Apply nitrogen fertilizer after heavy rain',
                    'Monitor for root rot in waterlogged conditions',
                    'Resume normal irrigation when rain stops'
                ],
                'reasoning' => 'Excessive moisture can cause root rot and reduce fruit quality. Proper drainage and irrigation management are essential during rainy periods.'
            ];
        } elseif ($temp > 30 && $humidity < 50) {
            return [
                'id' => 'irrigation_dry',
                'title' => 'Dry Weather Irrigation',
                'icon' => 'fas fa-tint',
                'color' => '#0891b2',
                'bg_color' => '#f0f9ff',
                'border_color' => '#0891b2',
                'priority' => 'high',
                'recommendations' => [
                    'Increase irrigation frequency to 2-3 times per week',
                    'Water deeply to encourage deep root growth',
                    'Apply mulch to retain soil moisture',
                    'Monitor soil moisture levels daily',
                    'Consider drip irrigation for efficiency'
                ],
                'reasoning' => 'Hot, dry conditions require increased irrigation to maintain healthy watermelon growth and prevent fruit cracking.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get general watermelon recommendations
     */
    private function getGeneralWatermelonRecommendation($growthStage)
    {
        $generalTips = [
            [
                'id' => 'general_watermelon_care',
                'title' => 'General Watermelon Care',
                'icon' => 'fas fa-seedling',
                'color' => '#059669',
                'bg_color' => '#ecfdf5',
                'border_color' => '#059669',
                'priority' => 'low',
                'recommendations' => [
                    'Maintain soil pH between 6.0-6.8',
                    'Ensure adequate spacing for vine growth',
                    'Regularly check for signs of stress or disease',
                    'Keep detailed records of weather and crop response',
                    'Plan for next season based on current results'
                ],
                'reasoning' => 'Consistent care and monitoring are essential for successful watermelon production. Regular observation helps identify issues early.'
            ],
            [
                'id' => 'watermelon_quality',
                'title' => 'Fruit Quality Management',
                'icon' => 'fas fa-apple-alt',
                'color' => '#7c3aed',
                'bg_color' => '#faf5ff',
                'border_color' => '#7c3aed',
                'priority' => 'low',
                'recommendations' => [
                    'Monitor sugar content development',
                    'Check for uniform fruit size and shape',
                    'Ensure proper fruit support to prevent damage',
                    'Harvest at optimal ripeness for best flavor',
                    'Store harvested fruits properly'
                ],
                'reasoning' => 'Quality management throughout the growing season ensures premium watermelon production and market value.'
            ]
        ];
        
        return $generalTips[array_rand($generalTips)];
    }
    
    /**
     * Count rainy days in forecast
     */
    private function countRainyDays($forecast)
    {
        if (!$forecast) return 0;
        
        $rainyDays = 0;
        foreach ($forecast as $day) {
            $description = strtolower($day['description'] ?? '');
            if (strpos($description, 'rain') !== false || strpos($description, 'shower') !== false) {
                $rainyDays++;
            }
        }
        
        return $rainyDays;
    }
}
