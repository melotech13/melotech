<?php

namespace App\Services;

class CropRecommendationService
{
    /**
     * Generate AI recommendations based on crop progress answers
     */
    public function generateRecommendations(array $answers, string $cropVariety = 'watermelon'): array
    {
        $recommendations = [
            'immediate_actions' => [],
            'weekly_plan' => [],
            'long_term_tips' => [],
            'priority_alerts' => []
        ];

        // Analyze plant health
        $this->analyzePlantHealth($answers, $recommendations);
        
        // Analyze growth rate
        $this->analyzeGrowthRate($answers, $recommendations);
        
        // Analyze water and nutrient issues
        $this->analyzeWaterNutrients($answers, $recommendations);
        
        // Analyze pest and disease pressure
        $this->analyzePestDisease($answers, $recommendations);
        
        // Analyze weather impact
        $this->analyzeWeatherImpact($answers, $recommendations);
        
        // Generate crop-specific recommendations
        $this->generateCropSpecificTips($answers, $recommendations, $cropVariety);
        
        // Ensure we have recommendations in each category
        $this->ensureDefaultRecommendations($recommendations);

        return $recommendations;
    }

    /**
     * Analyze plant health and generate recommendations
     */
    private function analyzePlantHealth(array $answers, array &$recommendations): void
    {
        $plantHealth = $answers['plant_health'] ?? 'good';
        $leafCondition = $answers['leaf_condition'] ?? 'good';

        if (in_array($plantHealth, ['poor', 'fair']) || in_array($leafCondition, ['poor', 'fair'])) {
            $recommendations['priority_alerts'][] = '⚠️ Plant health needs immediate attention';
            
            if ($plantHealth === 'poor') {
                $recommendations['immediate_actions'][] = '🔍 Conduct thorough plant inspection to identify root causes';
                $recommendations['immediate_actions'][] = '🌱 Check soil conditions and drainage';
                $recommendations['immediate_actions'][] = '💧 Verify irrigation system is working properly';
            }
            
            if ($leafCondition === 'poor') {
                $recommendations['immediate_actions'][] = '🍃 Remove severely damaged leaves to prevent disease spread';
                $recommendations['immediate_actions'][] = '🔬 Check for signs of pests, diseases, or nutrient deficiencies';
            }
        }

        if ($plantHealth === 'excellent' && $leafCondition === 'excellent') {
            $recommendations['long_term_tips'][] = '✅ Maintain current excellent practices';
            $recommendations['long_term_tips'][] = '📊 Continue monitoring for early signs of issues';
        }
    }

    /**
     * Analyze growth rate and generate recommendations
     */
    private function analyzeGrowthRate(array $answers, array &$recommendations): void
    {
        $growthRate = $answers['growth_rate'] ?? 'normal';
        $stageProgression = $answers['stage_progression'] ?? 'on_track';

        if (in_array($growthRate, ['slower', 'stunted']) || in_array($stageProgression, ['slightly_behind', 'significantly_behind'])) {
            $recommendations['priority_alerts'][] = '📉 Growth rate below expectations - investigation needed';
            
            $recommendations['immediate_actions'][] = '🌡️ Check soil temperature and environmental conditions';
            $recommendations['immediate_actions'][] = '🔍 Review recent changes in farming practices';
            
            if ($growthRate === 'stunted') {
                $recommendations['immediate_actions'][] = '🚨 Consider soil testing for nutrient deficiencies';
                $recommendations['immediate_actions'][] = '💧 Check for root damage or waterlogging issues';
            }
        }

        if ($growthRate === 'faster' || $stageProgression === 'ahead') {
            $recommendations['weekly_plan'][] = '⚡ Monitor for potential overgrowth issues';
            $recommendations['weekly_plan'][] = '🌿 Adjust support structures if needed';
        }
    }

    /**
     * Analyze water and nutrient issues
     */
    private function analyzeWaterNutrients(array $answers, array &$recommendations): void
    {
        $waterAvailability = $answers['water_availability'] ?? 'good';
        $nutrientDeficiency = $answers['nutrient_deficiency'] ?? 'none';

        if (in_array($waterAvailability, ['poor', 'fair'])) {
            $recommendations['priority_alerts'][] = '💧 Water management needs attention';
            
            if ($waterAvailability === 'poor') {
                $recommendations['immediate_actions'][] = '🚰 Check irrigation system for leaks or blockages';
                $recommendations['immediate_actions'][] = '🌧️ Review recent rainfall patterns and adjust irrigation';
                $recommendations['immediate_actions'][] = '📊 Monitor soil moisture levels more frequently';
            }
        }

        if (in_array($nutrientDeficiency, ['moderate', 'severe'])) {
            $recommendations['priority_alerts'][] = '🌱 Nutrient deficiency detected';
            
            $recommendations['immediate_actions'][] = '🧪 Conduct soil test to identify specific deficiencies';
            $recommendations['immediate_actions'][] = '🌿 Apply appropriate fertilizers based on soil test results';
            
            if ($nutrientDeficiency === 'severe') {
                $recommendations['immediate_actions'][] = '⚠️ Consider foliar feeding for immediate relief';
            }
        }
    }

    /**
     * Analyze pest and disease pressure
     */
    private function analyzePestDisease(array $answers, array &$recommendations): void
    {
        $pestPressure = $answers['pest_pressure'] ?? 'none';
        $diseaseIssues = $answers['disease_issues'] ?? 'none';

        if (in_array($pestPressure, ['moderate', 'high'])) {
            $recommendations['priority_alerts'][] = '🐛 Pest pressure requires immediate action';
            
            $recommendations['immediate_actions'][] = '🔍 Identify pest types and assess damage levels';
            $recommendations['immediate_actions'][] = '🌿 Apply appropriate pest control measures';
            
            if ($pestPressure === 'high') {
                $recommendations['immediate_actions'][] = '🚨 Consider professional pest management consultation';
            }
        }

        if (in_array($diseaseIssues, ['moderate', 'severe'])) {
            $recommendations['priority_alerts'][] = '🦠 Disease issues detected';
            
            $recommendations['immediate_actions'][] = '🔬 Identify disease symptoms and affected plants';
            $recommendations['immediate_actions'][] = '🌱 Remove and destroy severely infected plants';
            $recommendations['immediate_actions'][] = '💊 Apply appropriate fungicides or treatments';
        }
    }

    /**
     * Analyze weather impact
     */
    private function analyzeWeatherImpact(array $answers, array &$recommendations): void
    {
        $weatherImpact = $answers['weather_impact'] ?? 'neutral';

        if (in_array($weatherImpact, ['negative', 'damaging'])) {
            $recommendations['priority_alerts'][] = '🌦️ Weather conditions affecting crop health';
            
            if ($weatherImpact === 'damaging') {
                $recommendations['immediate_actions'][] = '🛡️ Implement protective measures for extreme weather';
                $recommendations['immediate_actions'][] = '📊 Assess damage and plan recovery strategies';
            }
            
            $recommendations['weekly_plan'][] = '🌤️ Monitor weather forecasts for planning';
            $recommendations['weekly_plan'][] = '🌱 Adjust farming activities based on weather conditions';
        }
    }

    /**
     * Generate crop-specific recommendations for watermelon
     */
    private function generateCropSpecificTips(array $answers, array &$recommendations, string $cropVariety): void
    {
        if (strtolower($cropVariety) === 'watermelon') {
            $overallSatisfaction = $answers['overall_satisfaction'] ?? 'satisfied';
            
            if (in_array($overallSatisfaction, ['somewhat_satisfied', 'dissatisfied'])) {
                $recommendations['long_term_tips'][] = '🍉 Watermelon-specific: Ensure proper spacing (6-8 feet between plants)';
                $recommendations['long_term_tips'][] = '🌱 Watermelon-specific: Maintain consistent soil moisture, especially during fruit development';
                $recommendations['long_term_tips'][] = '🌿 Watermelon-specific: Provide adequate support for developing fruits';
            }
            
            $recommendations['weekly_plan'][] = '🍉 Monitor fruit development and adjust support as needed';
            $recommendations['weekly_plan'][] = '🌱 Check for proper pollination and fruit set';
        }
    }

    /**
     * Ensure we have default recommendations in each category
     */
    private function ensureDefaultRecommendations(array &$recommendations): void
    {
        if (empty($recommendations['immediate_actions'])) {
            $recommendations['immediate_actions'][] = '✅ Continue current good practices';
        }
        
        if (empty($recommendations['weekly_plan'])) {
            $recommendations['weekly_plan'][] = '📅 Maintain regular monitoring schedule';
            $recommendations['weekly_plan'][] = '🌱 Continue with planned farming activities';
        }
        
        if (empty($recommendations['long_term_tips'])) {
            $recommendations['long_term_tips'][] = '🎯 Focus on maintaining optimal growing conditions';
            $recommendations['long_term_tips'][] = '📊 Keep detailed records for future planning';
        }
        
        if (empty($recommendations['priority_alerts'])) {
            $recommendations['priority_alerts'][] = '✅ No immediate concerns detected';
        }
    }

    /**
     * Get a summary of the recommendations
     */
    public function getRecommendationSummary(array $recommendations): string
    {
        $priorityCount = count(array_filter($recommendations['priority_alerts'], function($alert) {
            return !str_contains($alert, '✅');
        }));
        
        if ($priorityCount === 0) {
            return "Your crops are doing well! Continue with current practices and regular monitoring.";
        } elseif ($priorityCount <= 2) {
            return "Minor attention needed. Review the recommendations below for optimal crop health.";
        } else {
            return "Several areas need attention. Prioritize the immediate actions below for best results.";
        }
    }
}
