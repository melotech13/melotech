<?php

namespace App\Services;

class WeatherRecommendationService
{
    /**
     * Generate AI-powered recommendations based on current weather conditions
     */
    public function generateRecommendations($weatherData)
    {
        $current = $weatherData['current'] ?? [];
        $forecast = $weatherData['forecast'] ?? [];
        
        $recommendations = [];
        
        // 1. Temperature-based recommendations
        $tempRecommendations = $this->getTemperatureRecommendations($current, $forecast);
        if ($tempRecommendations) {
            $recommendations[] = $tempRecommendations;
        }
        
        // 2. Humidity-based recommendations
        $humidityRecommendations = $this->getHumidityRecommendations($current, $forecast);
        if ($humidityRecommendations) {
            $recommendations[] = $humidityRecommendations;
        }
        
        // 3. Wind-based recommendations
        $windRecommendations = $this->getWindRecommendations($current, $forecast);
        if ($windRecommendations) {
            $recommendations[] = $windRecommendations;
        }
        
        // 4. Rain-based recommendations
        $rainRecommendations = $this->getRainRecommendations($current, $forecast);
        if ($rainRecommendations) {
            $recommendations[] = $rainRecommendations;
        }
        
        // Ensure we have exactly 4 recommendations
        while (count($recommendations) < 4) {
            $recommendations[] = $this->getGeneralRecommendation();
        }
        
        return array_slice($recommendations, 0, 4);
    }
    
    /**
     * Get temperature-based recommendations
     */
    private function getTemperatureRecommendations($current, $forecast)
    {
        $temp = $current['temperature'] ?? 25;
        $feelsLike = $current['feels_like'] ?? $temp;
        
        if ($feelsLike > 35) {
            return [
                'id' => 'temp_hot',
                'title' => 'Heat Management',
                'icon' => 'fas fa-thermometer-full',
                'color' => '#f59e0b',
                'bg_color' => '#fef3c7',
                'border_color' => '#f59e0b',
                'priority' => 'high',
                'recommendations' => [
                    'Increase irrigation frequency to prevent heat stress',
                    'Apply mulch to retain soil moisture and cool roots',
                    'Water crops early morning (5-7 AM) for maximum absorption',
                    'Consider temporary shade structures for sensitive crops',
                    'Monitor soil moisture levels more frequently'
                ],
                'reasoning' => 'High temperatures above 35°C can cause heat stress, wilting, and reduced crop yields. Immediate action is needed to protect your crops.'
            ];
        } elseif ($feelsLike < 15) {
            return [
                'id' => 'temp_cold',
                'title' => 'Cold Protection',
                'icon' => 'fas fa-thermometer-empty',
                'color' => '#3b82f6',
                'bg_color' => '#eff6ff',
                'border_color' => '#3b82f6',
                'priority' => 'medium',
                'recommendations' => [
                    'Cover sensitive crops with frost cloth or row covers',
                    'Reduce irrigation to prevent root rot in cold conditions',
                    'Apply organic mulch to insulate soil and roots',
                    'Consider cold-tolerant crop varieties for future planting',
                    'Monitor for frost damage and adjust protection accordingly'
                ],
                'reasoning' => 'Cool temperatures below 15°C can slow plant growth and increase frost risk. Protective measures will help maintain crop health.'
            ];
        } elseif ($feelsLike >= 20 && $feelsLike <= 30) {
            return [
                'id' => 'temp_optimal',
                'title' => 'Optimal Growing Conditions',
                'icon' => 'fas fa-seedling',
                'color' => '#10b981',
                'bg_color' => '#ecfdf5',
                'border_color' => '#10b981',
                'priority' => 'low',
                'recommendations' => [
                    'Perfect weather for planting and transplanting',
                    'Ideal conditions for applying fertilizers and nutrients',
                    'Excellent time for crop monitoring and maintenance',
                    'Consider planting heat-sensitive crops now',
                    'Take advantage of optimal growth conditions'
                ],
                'reasoning' => 'Temperatures between 20-30°C provide ideal growing conditions for most crops. This is the perfect time for various farming activities.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get humidity-based recommendations
     */
    private function getHumidityRecommendations($current, $forecast)
    {
        $humidity = $current['humidity'] ?? 60;
        
        if ($humidity > 80) {
            return [
                'id' => 'humidity_high',
                'title' => 'Disease Prevention',
                'icon' => 'fas fa-shield-virus',
                'color' => '#dc2626',
                'bg_color' => '#fef2f2',
                'border_color' => '#dc2626',
                'priority' => 'high',
                'recommendations' => [
                    'Apply fungicide to prevent fungal diseases',
                    'Improve air circulation around crops',
                    'Avoid overhead watering to reduce leaf wetness',
                    'Remove any diseased plant material immediately',
                    'Consider crop rotation to break disease cycles'
                ],
                'reasoning' => 'High humidity above 80% creates ideal conditions for fungal diseases. Preventive measures are crucial to protect crop health.'
            ];
        } elseif ($humidity < 40) {
            return [
                'id' => 'humidity_low',
                'title' => 'Moisture Management',
                'icon' => 'fas fa-tint',
                'color' => '#0891b2',
                'bg_color' => '#f0f9ff',
                'border_color' => '#0891b2',
                'priority' => 'medium',
                'recommendations' => [
                    'Increase irrigation frequency and duration',
                    'Apply mulch to retain soil moisture',
                    'Water deeply and less frequently for better root development',
                    'Consider drip irrigation for efficient water delivery',
                    'Monitor soil moisture levels daily'
                ],
                'reasoning' => 'Low humidity below 40% increases water stress. Proper irrigation management is essential to maintain crop health.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get wind-based recommendations
     */
    private function getWindRecommendations($current, $forecast)
    {
        $windSpeed = $current['wind_speed'] ?? 10;
        
        if ($windSpeed > 25) {
            return [
                'id' => 'wind_strong',
                'title' => 'Wind Protection',
                'icon' => 'fas fa-wind',
                'color' => '#8b5cf6',
                'bg_color' => '#faf5ff',
                'border_color' => '#8b5cf6',
                'priority' => 'high',
                'recommendations' => [
                    'Secure all crop covers and protective structures',
                    'Stake tall plants to prevent wind damage',
                    'Avoid spraying pesticides in strong winds',
                    'Check and reinforce trellises and support systems',
                    'Consider windbreaks for future protection'
                ],
                'reasoning' => 'Strong winds above 25 km/h can cause physical damage to crops and structures. Immediate protective measures are needed.'
            ];
        } elseif ($windSpeed < 5) {
            return [
                'id' => 'wind_calm',
                'title' => 'Air Circulation',
                'icon' => 'fas fa-fan',
                'color' => '#6b7280',
                'bg_color' => '#f9fafb',
                'border_color' => '#6b7280',
                'priority' => 'low',
                'recommendations' => [
                    'Ideal conditions for pesticide application',
                    'Good time for detailed crop inspection',
                    'Perfect weather for planting and transplanting',
                    'Consider pruning to improve air circulation',
                    'Monitor for pest activity in calm conditions'
                ],
                'reasoning' => 'Calm winds below 5 km/h provide stable conditions for various farming activities, especially chemical applications.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get rain-based recommendations
     */
    private function getRainRecommendations($current, $forecast)
    {
        $description = strtolower($current['description'] ?? '');
        $humidity = $current['humidity'] ?? 60;
        
        if (strpos($description, 'rain') !== false || strpos($description, 'shower') !== false) {
            return [
                'id' => 'rain_active',
                'title' => 'Rain Management',
                'icon' => 'fas fa-cloud-rain',
                'color' => '#0ea5e9',
                'bg_color' => '#f0f9ff',
                'border_color' => '#0ea5e9',
                'priority' => 'medium',
                'recommendations' => [
                    'Reduce or stop irrigation to prevent overwatering',
                    'Check drainage systems for proper water flow',
                    'Avoid working in wet fields to prevent soil compaction',
                    'Monitor for waterlogged areas and standing water',
                    'Apply nutrients after rain for better absorption'
                ],
                'reasoning' => 'Active rainfall requires adjusting irrigation and monitoring drainage. Proper water management prevents crop damage.'
            ];
        } elseif ($humidity > 70 && strpos($description, 'cloud') !== false) {
            return [
                'id' => 'rain_likely',
                'title' => 'Rain Preparation',
                'icon' => 'fas fa-cloud',
                'color' => '#6366f1',
                'bg_color' => '#eef2ff',
                'border_color' => '#6366f1',
                'priority' => 'medium',
                'recommendations' => [
                    'Prepare drainage systems for incoming rain',
                    'Harvest any ready crops before heavy rain',
                    'Secure loose materials and equipment',
                    'Check weather forecast for rain intensity',
                    'Consider delaying fertilizer application'
                ],
                'reasoning' => 'High humidity and cloudy conditions suggest rain is likely. Preparation will help protect your crops and equipment.'
            ];
        }
        
        return null;
    }
    
    /**
     * Get general recommendations when specific conditions don't apply
     */
    private function getGeneralRecommendation()
    {
        $generalTips = [
            [
                'id' => 'general_monitoring',
                'title' => 'Crop Monitoring',
                'icon' => 'fas fa-search',
                'color' => '#059669',
                'bg_color' => '#ecfdf5',
                'border_color' => '#059669',
                'priority' => 'low',
                'recommendations' => [
                    'Regular crop inspection for pests and diseases',
                    'Monitor soil moisture and nutrient levels',
                    'Check for signs of stress or nutrient deficiency',
                    'Document crop growth and development stages',
                    'Plan upcoming farming activities'
                ],
                'reasoning' => 'Regular monitoring helps identify issues early and ensures optimal crop health and productivity.'
            ],
            [
                'id' => 'general_maintenance',
                'title' => 'Farm Maintenance',
                'icon' => 'fas fa-tools',
                'color' => '#7c3aed',
                'bg_color' => '#faf5ff',
                'border_color' => '#7c3aed',
                'priority' => 'low',
                'recommendations' => [
                    'Maintain irrigation systems and equipment',
                    'Clean and organize farm tools and machinery',
                    'Check and repair fences and structures',
                    'Update farm records and documentation',
                    'Plan for upcoming seasonal activities'
                ],
                'reasoning' => 'Regular maintenance ensures equipment reliability and prepares the farm for optimal productivity.'
            ],
            [
                'id' => 'general_planning',
                'title' => 'Strategic Planning',
                'icon' => 'fas fa-calendar-alt',
                'color' => '#d97706',
                'bg_color' => '#fffbeb',
                'border_color' => '#d97706',
                'priority' => 'low',
                'recommendations' => [
                    'Review crop rotation schedules',
                    'Plan for upcoming planting seasons',
                    'Analyze weather patterns and trends',
                    'Update farming strategies based on conditions',
                    'Prepare for seasonal weather changes'
                ],
                'reasoning' => 'Strategic planning helps optimize farm operations and adapt to changing weather conditions.'
            ]
        ];
        
        return $generalTips[array_rand($generalTips)];
    }
}
