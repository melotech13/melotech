<?php

namespace App\Services;

class CropRecommendationService
{
    /**
     * Generate AI recommendations based on crop progress answers and research papers
     */
    public function generateRecommendations(array $answers, string $cropVariety = 'watermelon', array $researchPapers = []): array
    {
        $recommendations = [
            'general_status' => '',
            'priority_alerts' => [],
            'immediate_actions' => [],
            'weekly_plan' => [],
            'long_term_tips' => []
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
        
        // Generate general status summary
        $this->generateGeneralStatus($answers, $recommendations);
        
        // Enhance recommendations with research paper insights
        if (!empty($researchPapers)) {
            $this->enhanceWithResearchInsights($recommendations, $researchPapers, $answers);
        }
        
        // Ensure we have recommendations in each category
        $this->ensureDefaultRecommendations($recommendations);

        return $recommendations;
    }

    /**
     * Analyze plant health and generate recommendations (NEW STRUCTURE)
     */
    private function analyzePlantHealth(array $answers, array &$recommendations): void
    {
        // Analyze leaf color (1-5 rating)
        $leafColor = $answers['leaf_color'] ?? 5;
        if (is_numeric($leafColor) && $leafColor <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Severe leaf discoloration detected - likely nutrient deficiency';
            $recommendations['immediate_actions'][] = 'Conduct soil test to identify specific nutrient deficiencies';
            $recommendations['immediate_actions'][] = 'Apply appropriate fertilizers based on soil test results';
            $recommendations['immediate_actions'][] = 'Check for signs of nitrogen, phosphorus, or potassium deficiency';
        } elseif (is_numeric($leafColor) && $leafColor <= 3) {
            $recommendations['priority_alerts'][] = '⚠️ Leaf yellowing observed - possible nutrient issue';
            $recommendations['immediate_actions'][] = 'Monitor leaf color changes over the next week';
            $recommendations['immediate_actions'][] = 'Consider applying balanced fertilizer if yellowing persists';
        }
        
        // Analyze leaf spots/damage
        $leafSpots = $answers['leaf_spots'] ?? 'no';
        if ($leafSpots === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ Leaf spots or damage detected - potential disease or pest issue';
            $recommendations['immediate_actions'][] = 'Identify the type of spots (fungal, bacterial, or pest damage)';
            $recommendations['immediate_actions'][] = 'Remove severely affected leaves to prevent spread';
            $recommendations['immediate_actions'][] = 'Apply appropriate fungicide or pesticide if needed';
        }
        
        // Analyze leaf texture/wilting
        $leafTexture = $answers['leaf_texture'] ?? 5;
        if (is_numeric($leafTexture) && $leafTexture <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Severe wilting detected - critical water stress';
            $recommendations['immediate_actions'][] = 'Check irrigation system immediately';
            $recommendations['immediate_actions'][] = 'Increase watering frequency if soil is dry';
            $recommendations['immediate_actions'][] = 'Monitor plants during hottest part of day';
        } elseif (is_numeric($leafTexture) && $leafTexture <= 3) {
            $recommendations['priority_alerts'][] = '⚠️ Leaf wilting observed - water stress developing';
            $recommendations['immediate_actions'][] = 'Adjust irrigation schedule to prevent further stress';
        }
        
        // Analyze leaf health (for flowering/fruiting stages)
        $leafHealth = $answers['leaf_health'] ?? 5;
        if (is_numeric($leafHealth) && $leafHealth <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Poor overall leaf health - multiple issues detected';
            $recommendations['immediate_actions'][] = 'Comprehensive plant inspection required';
            $recommendations['immediate_actions'][] = 'Address water, nutrient, and pest issues immediately';
        }
        
        // Analyze old leaf condition (senescence)
        $olderLeafCondition = $answers['older_leaf_condition'] ?? 'no';
        $leafCurl = $answers['leaf_curl'] ?? 'no';
        if ($leafCurl === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ Leaf curling detected - possible viral disease or pest damage';
            $recommendations['immediate_actions'][] = 'Inspect for aphids, whiteflies, or other sap-sucking insects';
            $recommendations['immediate_actions'][] = 'Test for viral infections if curling is severe';
        }
    }

    /**
     * Analyze growth rate and generate recommendations (NEW STRUCTURE)
     */
    private function analyzeGrowthRate(array $answers, array &$recommendations): void
    {
        // Analyze stem strength (seedling stage)
        $stemStrength = $answers['stem_strength'] ?? 5;
        if (is_numeric($stemStrength) && $stemStrength <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Weak stem structure - plants at risk of falling over';
            $recommendations['immediate_actions'][] = 'Provide support stakes for weak seedlings';
            $recommendations['immediate_actions'][] = 'Reduce nitrogen if stems are thin and elongated';
            $recommendations['immediate_actions'][] = 'Improve light exposure to strengthen stems';
        }
        
        // Analyze growth speed
        $growthSpeed = $answers['growth_speed'] ?? 5;
        if (is_numeric($growthSpeed) && $growthSpeed <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Very slow growth rate - development delayed';
            $recommendations['immediate_actions'][] = 'Check soil temperature (optimal: 21-29°C for melons)';
            $recommendations['immediate_actions'][] = 'Verify adequate sunlight exposure (6-8 hours daily)';
            $recommendations['immediate_actions'][] = 'Test soil for nutrient deficiencies';
        }
        
        // Analyze seedling uniformity
        $uniformity = $answers['seedling_uniformity'] ?? 5;
        if (is_numeric($uniformity) && $uniformity <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Highly uneven growth across seedlings';
            $recommendations['immediate_actions'][] = 'Check for uneven watering patterns';
            $recommendations['immediate_actions'][] = 'Identify and separate weak seedlings for special care';
        }
        
        // Analyze vine length (vegetative stage)
        $vineLength = $answers['vine_length'] ?? null;
        if ($vineLength !== null && is_numeric($vineLength)) {
            if ($vineLength < 50) {
                $recommendations['priority_alerts'][] = '⚠️ Vine growth below expected - development delayed';
                $recommendations['immediate_actions'][] = 'Increase nitrogen application to promote vine growth';
                $recommendations['weekly_plan'][] = 'Monitor vine length weekly to track improvement';
            }
        }
        
        // Analyze overall vigor
        $overallVigor = $answers['overall_vigor'] ?? 5;
        if (is_numeric($overallVigor) && $overallVigor <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Poor plant vigor - multiple factors affecting growth';
            $recommendations['immediate_actions'][] = 'Comprehensive assessment of water, nutrients, and pests needed';
            $recommendations['immediate_actions'][] = 'Consider consulting agricultural extension service';
        }
    }

    /**
     * Analyze water and nutrient issues (NEW STRUCTURE)
     */
    private function analyzeWaterNutrients(array $answers, array &$recommendations): void
    {
        // Analyze water stress rating
        $waterStress = $answers['water_stress'] ?? 5;
        if (is_numeric($waterStress) && $waterStress <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Severe water stress - plants experiencing extreme wilting';
            $recommendations['immediate_actions'][] = 'Irrigate immediately if soil is dry';
            $recommendations['immediate_actions'][] = 'Check for irrigation system failures';
            $recommendations['immediate_actions'][] = 'Increase watering frequency during hot periods';
        } elseif (is_numeric($waterStress) && $waterStress <= 3) {
            $recommendations['priority_alerts'][] = '⚠️ Moderate water stress observed';
            $recommendations['immediate_actions'][] = 'Adjust irrigation schedule to reduce stress';
            $recommendations['weekly_plan'][] = 'Monitor soil moisture levels daily';
        }
        
        // Analyze nutrient deficiency symptoms
        $leafNutrientSigns = $answers['leaf_nutrient_signs'] ?? 0;
        if (is_numeric($leafNutrientSigns) && $leafNutrientSigns > 30) {
            $recommendations['priority_alerts'][] = '⚠️ Significant nutrient deficiency symptoms visible';
            $recommendations['immediate_actions'][] = 'Conduct soil test to identify specific deficiencies';
            $recommendations['immediate_actions'][] = 'Apply balanced fertilizer (N-P-K) immediately';
            $recommendations['weekly_plan'][] = 'Monitor leaf recovery after fertilizer application';
        } elseif (is_numeric($leafNutrientSigns) && $leafNutrientSigns > 15) {
            $recommendations['priority_alerts'][] = '⚠️ Minor nutrient deficiency symptoms observed';
            $recommendations['immediate_actions'][] = 'Consider supplemental fertilizer application';
        }
        
        // Analyze root development
        $rootDevelopment = $answers['root_development'] ?? 'yes';
        if ($rootDevelopment === 'no') {
            $recommendations['priority_alerts'][] = '⚠️ Poor root development - may affect nutrient uptake';
            $recommendations['immediate_actions'][] = 'Check for waterlogging or compacted soil';
            $recommendations['immediate_actions'][] = 'Ensure adequate drainage';
        }
        
        // Analyze photosynthetic capacity
        $leafPhotosynthesis = $answers['leaf_photosynthesis'] ?? 5;
        if (is_numeric($leafPhotosynthesis) && $leafPhotosynthesis <= 2) {
            $recommendations['priority_alerts'][] = '⚠️ Very poor photosynthetic capacity - severe yellowing';
            $recommendations['immediate_actions'][] = 'Address nutrient deficiencies urgently';
            $recommendations['immediate_actions'][] = 'Improve light exposure if plants are shaded';
        }
    }

    /**
     * Analyze pest and disease pressure (NEW STRUCTURE)
     */
    private function analyzePestDisease(array $answers, array &$recommendations): void
    {
        // Analyze pest damage percentage
        $pestDamage = $answers['pest_damage'] ?? 0;
        if (is_numeric($pestDamage) && $pestDamage > 20) {
            $recommendations['priority_alerts'][] = '⚠️ High pest damage - ' . $pestDamage . '% of seedlings affected';
            $recommendations['immediate_actions'][] = 'Identify pest species immediately (aphids, caterpillars, etc.)';
            $recommendations['immediate_actions'][] = 'Apply appropriate insecticide or organic pest control';
            $recommendations['immediate_actions'][] = 'Remove heavily damaged plants to prevent pest spread';
        } elseif (is_numeric($pestDamage) && $pestDamage > 5) {
            $recommendations['priority_alerts'][] = '⚠️ Moderate pest pressure detected (' . $pestDamage . '% affected)';
            $recommendations['immediate_actions'][] = 'Monitor pest populations daily';
            $recommendations['immediate_actions'][] = 'Consider integrated pest management strategies';
        }
        
        // Analyze leaf damage from insects
        $leafDamage = $answers['leaf_damage'] ?? 0;
        if (is_numeric($leafDamage) && $leafDamage > 25) {
            $recommendations['priority_alerts'][] = '⚠️ Severe insect damage to leaves (' . $leafDamage . '% affected)';
            $recommendations['immediate_actions'][] = 'Implement immediate pest control measures';
            $recommendations['immediate_actions'][] = 'Inspect undersides of leaves for eggs or larvae';
        }
        
        // Analyze disease presence
        $leafDisease = $answers['leaf_disease'] ?? 'no';
        if ($leafDisease === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ Disease symptoms detected on leaves';
            $recommendations['immediate_actions'][] = 'Identify disease type (powdery mildew, downy mildew, or fungal spots)';
            $recommendations['immediate_actions'][] = 'Remove infected leaves immediately';
            $recommendations['immediate_actions'][] = 'Apply appropriate fungicide based on disease type';
            $recommendations['weekly_plan'][] = 'Monitor disease spread daily and apply preventive treatments';
        }
        
        // Analyze disease spread
        $diseaseSpread = $answers['leaf_disease_spread'] ?? 'no';
        if ($diseaseSpread === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ CRITICAL: Disease is actively spreading';
            $recommendations['immediate_actions'][] = 'Isolate affected plants if possible';
            $recommendations['immediate_actions'][] = 'Apply systemic fungicide immediately';
            $recommendations['immediate_actions'][] = 'Improve air circulation by pruning and spacing plants';
            $recommendations['immediate_actions'][] = 'Consider professional agricultural consultation';
        }
        
        // Analyze disease impact on fruit quality
        $diseaseFinal = $answers['leaf_disease_final'] ?? 'no';
        if ($diseaseFinal === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ Disease affecting fruit quality at harvest';
            $recommendations['immediate_actions'][] = 'Harvest unaffected fruits immediately';
            $recommendations['immediate_actions'][] = 'Separate diseased plants from healthy ones';
        }
        
        // Analyze pest activity at harvest
        $pestFinal = $answers['leaf_pest_final'] ?? 'no';
        if ($pestFinal === 'yes') {
            $recommendations['priority_alerts'][] = '⚠️ Pest activity still present at harvest stage';
            $recommendations['immediate_actions'][] = 'Apply final pest control treatment before harvest';
            $recommendations['weekly_plan'][] = 'Inspect harvested fruits for pest damage';
        }
    }

    /**
     * Analyze weather impact (NEW STRUCTURE)
     */
    private function analyzeWeatherImpact(array $answers, array &$recommendations): void
    {
        // Most weather-related impacts are captured in water stress and leaf texture
        // Add general weather-related advice
        $waterStress = $answers['water_stress'] ?? 5;
        if (is_numeric($waterStress) && $waterStress <= 3) {
            $recommendations['weekly_plan'][] = 'Monitor weather forecasts for rainfall and adjust irrigation';
            $recommendations['long_term_tips'][] = 'Consider installing rain gauges to track precipitation';
        }
    }

    /**
     * Generate crop-specific recommendations for watermelon (NEW STRUCTURE)
     */
    private function generateCropSpecificTips(array $answers, array &$recommendations, string $cropVariety): void
    {
        if (strtolower($cropVariety) === 'watermelon' || str_contains(strtolower($cropVariety), 'melon')) {
            // Pollination stage recommendations
            $pollinatorActivity = $answers['pollinator_activity'] ?? null;
            if ($pollinatorActivity === 'no') {
                $recommendations['weekly_plan'][] = 'Consider hand pollination if bee activity remains low';
                $recommendations['long_term_tips'][] = 'Plant bee-attracting flowers near melon fields';
            }
            
            // Fruit development recommendations
            $fruitCount = $answers['fruit_count'] ?? null;
            if ($fruitCount !== null && is_numeric($fruitCount) && $fruitCount < 2) {
                $recommendations['weekly_plan'][] = 'Monitor new flower formation for additional fruit set';
            } elseif ($fruitCount !== null && is_numeric($fruitCount) && $fruitCount > 4) {
                $recommendations['immediate_actions'][] = 'Consider thinning fruits to 2-4 per plant for better size and quality';
            }
            
            // Harvest readiness
            $harvestReadiness = $answers['harvest_readiness'] ?? null;
            $groundSpot = $answers['ground_spot'] ?? null;
            if ($groundSpot === 'yes' && is_numeric($harvestReadiness) && $harvestReadiness >= 4) {
                $recommendations['immediate_actions'][] = 'Fruits are approaching optimal harvest time - monitor daily';
                $recommendations['weekly_plan'][] = 'Prepare for harvest within next 2-7 days';
            }
            
            // General melon tips
            $recommendations['long_term_tips'][] = 'Maintain consistent soil moisture throughout fruit development';
            $recommendations['long_term_tips'][] = 'Ensure proper vine spacing (1.5-2m between plants) for air circulation';
        }
    }

    /**
     * Generate general status summary (IMPROVED ACCURACY)
     */
    private function generateGeneralStatus(array $answers, array &$recommendations): void
    {
        $healthIndicators = $this->analyzeHealthIndicators($answers);
        
        $concernCount = count(array_filter($recommendations['priority_alerts'], function($alert) {
            return str_contains($alert, '⚠️');
        }));
        
        $criticalConcerns = count(array_filter($recommendations['priority_alerts'], function($alert) {
            return str_contains($alert, 'CRITICAL') || str_contains($alert, 'Severe');
        }));
        
        // Generate realistic status based on actual conditions
        if ($criticalConcerns > 0) {
            $recommendations['general_status'] = 'URGENT: Your crops are experiencing severe problems that require immediate intervention. Multiple critical issues have been detected that could significantly impact yield if not addressed within 24-48 hours.';
        } elseif ($concernCount >= 5 || $healthIndicators['overall_score'] < 2.5) {
            $recommendations['general_status'] = 'Your crops are showing signs of stress and poor health across multiple indicators. Immediate action is required to prevent further deterioration. Focus on the priority alerts below.';
        } elseif ($concernCount >= 3 || $healthIndicators['overall_score'] < 3.5) {
            $recommendations['general_status'] = 'Your crops are experiencing moderate stress with several issues detected. While not critical, these problems should be addressed promptly to prevent escalation and maintain productive growth.';
        } elseif ($concernCount >= 1 || $healthIndicators['overall_score'] < 4.0) {
            $recommendations['general_status'] = 'Your crops are generally developing well, but some minor concerns have been identified. Address these issues proactively to maintain optimal health and prevent potential problems.';
        } else {
            $recommendations['general_status'] = 'Excellent! Your crops are healthy and showing strong, vigorous growth. All indicators are within optimal ranges. Maintain current practices and continue regular monitoring.';
        }
    }

    /**
     * Analyze health indicators from answers (IMPROVED LOGIC)
     */
    private function analyzeHealthIndicators(array $answers): array
    {
        $scores = [];
        $negativeIndicators = 0;
        
        // Extract numeric ratings and assess negative indicators
        foreach ($answers as $key => $value) {
            if (is_numeric($value)) {
                $numValue = (int)$value;
                
                // Check if this is a percentage slider (pest, damage, etc.)
                if (str_contains($key, 'damage') || str_contains($key, 'pest') || str_contains($key, 'nutrient_signs')) {
                    // Higher percentage = worse (invert the score)
                    if ($numValue > 50) {
                        $scores[] = 1;
                        $negativeIndicators++;
                    } elseif ($numValue > 25) {
                        $scores[] = 2;
                        $negativeIndicators++;
                    } elseif ($numValue > 10) {
                        $scores[] = 3;
                    } else {
                        $scores[] = 5;
                    }
                } else {
                    // Regular 1-5 rating
                    $scores[] = $numValue;
                    if ($numValue <= 2) {
                        $negativeIndicators++;
                    }
                }
            } elseif ($value === 'yes') {
                // Context matters - 'yes' to disease/problems is bad, 'yes' to good things is good
                if (str_contains($key, 'disease') || str_contains($key, 'spots') || str_contains($key, 'curl') || str_contains($key, 'pest')) {
                    $scores[] = 1;
                    $negativeIndicators++;
                } else {
                    $scores[] = 5;
                }
            } elseif ($value === 'no') {
                // Context matters - 'no' to problems is good, 'no' to good things is bad
                if (str_contains($key, 'disease') || str_contains($key, 'spots') || str_contains($key, 'curl') || str_contains($key, 'pest')) {
                    $scores[] = 5;
                } else {
                    $scores[] = 3;
                }
            }
        }
        
        $avgScore = !empty($scores) ? array_sum($scores) / count($scores) : 3;
        
        return [
            'overall_score' => $avgScore,
            'total_responses' => count($answers),
            'negative_indicators' => $negativeIndicators,
            'health_level' => $avgScore >= 4 ? 'excellent' : ($avgScore >= 3 ? 'good' : 'needs_attention')
        ];
    }

    /**
     * Ensure we have default recommendations in each category
     */
    private function ensureDefaultRecommendations(array &$recommendations): void
    {
        // Only add defaults if there are truly no issues detected
        $hasRealAlerts = count(array_filter($recommendations['priority_alerts'], function($alert) {
            return str_contains($alert, '⚠️');
        })) > 0;
        
        if (empty($recommendations['immediate_actions'])) {
            $recommendations['immediate_actions'][] = '✅ Continue current farming practices - no urgent actions needed';
            $recommendations['immediate_actions'][] = 'Maintain regular watering and fertilization schedule';
        }
        
        if (empty($recommendations['weekly_plan'])) {
            $recommendations['weekly_plan'][] = 'Conduct weekly visual inspection of plants';
            $recommendations['weekly_plan'][] = 'Monitor for early signs of pests or diseases';
            $recommendations['weekly_plan'][] = 'Check soil moisture levels regularly';
        }
        
        if (empty($recommendations['long_term_tips'])) {
            $recommendations['long_term_tips'][] = 'Maintain detailed crop records for future reference';
            $recommendations['long_term_tips'][] = 'Plan crop rotation for next season';
            $recommendations['long_term_tips'][] = 'Consider soil health improvement strategies';
        }
        
        if (empty($recommendations['priority_alerts'])) {
            $recommendations['priority_alerts'][] = '✅ No immediate concerns detected - all indicators within healthy ranges';
        }
        
        if (empty($recommendations['general_status'])) {
            $recommendations['general_status'] = 'Your crops are developing normally with no significant issues detected. Continue monitoring and maintain current practices.';
        }
    }

    /**
     * Enhance recommendations with insights from research papers
     */
    private function enhanceWithResearchInsights(array &$recommendations, array $researchPapers, array $answers): void
    {
        // Extract key insights from research papers
        $researchInsights = $this->extractResearchInsights($researchPapers, $answers);
        
        // Add research-backed actions to immediate actions
        if (!empty($researchInsights['immediate'])) {
            foreach ($researchInsights['immediate'] as $action) {
                if (!in_array($action, $recommendations['immediate_actions'])) {
                    $recommendations['immediate_actions'][] = $action;
                }
            }
        }
        
        // Add research-backed weekly plan items
        if (!empty($researchInsights['weekly'])) {
            foreach ($researchInsights['weekly'] as $plan) {
                if (!in_array($plan, $recommendations['weekly_plan'])) {
                    $recommendations['weekly_plan'][] = $plan;
                }
            }
        }
        
        // Add research-backed long-term tips
        if (!empty($researchInsights['longterm'])) {
            foreach ($researchInsights['longterm'] as $tip) {
                if (!in_array($tip, $recommendations['long_term_tips'])) {
                    $recommendations['long_term_tips'][] = $tip;
                }
            }
        }
    }

    /**
     * Extract actionable insights from research papers based on user's answers
     */
    private function extractResearchInsights(array $researchPapers, array $answers): array
    {
        $insights = [
            'immediate' => [],
            'weekly' => [],
            'longterm' => []
        ];
        
        foreach ($researchPapers as $paper) {
            $fullAbstract = strtolower($paper['full_abstract'] ?? $paper['abstract'] ?? '');
            $title = strtolower($paper['title'] ?? '');
            
            // Leaf health issues from answers
            $leafColorRating = $this->extractNumericRating($answers, 'leaf_color');
            if ($leafColorRating <= 3 && (str_contains($fullAbstract, 'nitrogen') || str_contains($title, 'nitrogen'))) {
                $insights['immediate'][] = '📖 Research shows: Nitrogen deficiency causes leaf yellowing. Apply nitrogen fertilizer to restore green color (based on ' . ($paper['authors'] ?? 'research') . ', ' . ($paper['year'] ?? 'N/A') . ')';
                $insights['weekly'][] = 'Monitor leaf color changes after nitrogen application - improvement should be visible within 5-7 days';
            }
            
            // Water stress issues
            if ($this->detectWaterStress($answers) && str_contains($fullAbstract, 'irrigation')) {
                $insights['immediate'][] = '📖 Research shows: Precision irrigation reduces water stress. Monitor soil moisture at root zone depth (based on ' . ($paper['authors'] ?? 'research') . ', ' . ($paper['year'] ?? 'N/A') . ')';
                $insights['weekly'][] = 'Check soil moisture levels daily during fruit development to prevent cracking';
            }
            
            // Pollination issues
            if ($this->detectPollinationIssues($answers) && (str_contains($fullAbstract, 'pollination') || str_contains($fullAbstract, 'bee'))) {
                $insights['immediate'][] = '📖 Research shows: Adequate bee populations (2-3 hives/hectare) increase fruit set by 20-25%. Check for bee activity during morning hours (based on ' . ($paper['authors'] ?? 'research') . ', ' . ($paper['year'] ?? 'N/A') . ')';
                $insights['weekly'][] = 'Monitor pollinator activity between 8-11 AM when bee activity is highest';
                $insights['longterm'][] = 'Consider maintaining bee-friendly plants near fields to support natural pollinator populations';
            }
            
            // Disease management
            if ($this->detectDiseaseIssues($answers) && str_contains($fullAbstract, 'disease')) {
                $insights['immediate'][] = '📖 Research shows: Early removal of infected leaves prevents disease spread. Improve air circulation through proper spacing (based on ' . ($paper['authors'] ?? 'research') . ', ' . ($paper['year'] ?? 'N/A') . ')';
                $insights['weekly'][] = 'Apply preventive fungicides during humid conditions when disease pressure is high';
            }
            
            // General sustainable practices
            if (str_contains($fullAbstract, 'sustainable') || str_contains($title, 'sustainable')) {
                $insights['longterm'][] = '📖 Research shows: Integrated pest management and optimal irrigation enhance yield while maintaining environmental sustainability (based on ' . ($paper['authors'] ?? 'research') . ', ' . ($paper['year'] ?? 'N/A') . ')';
            }
        }
        
        // Remove duplicates
        $insights['immediate'] = array_unique($insights['immediate']);
        $insights['weekly'] = array_unique($insights['weekly']);
        $insights['longterm'] = array_unique($insights['longterm']);
        
        return $insights;
    }

    /**
     * Extract numeric rating from answers
     */
    private function extractNumericRating(array $answers, string $key): int
    {
        $value = $answers[$key] ?? null;
        return is_numeric($value) ? (int)$value : 5;
    }

    /**
     * Detect water stress from answers
     */
    private function detectWaterStress(array $answers): bool
    {
        // Check for wilting, water stress indicators
        foreach ($answers as $key => $value) {
            if (str_contains($key, 'water') || str_contains($key, 'texture')) {
                if (is_numeric($value) && $value <= 2) {
                    return true;
                }
                if ($value === 'poor' || $value === 'wilted') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Detect pollination issues from answers
     */
    private function detectPollinationIssues(array $answers): bool
    {
        // Check flowering and fruit set
        $pollinatorActivity = $answers['pollinator_activity'] ?? 'yes';
        $fruitSet = $answers['fruit_set'] ?? 50;
        
        if ($pollinatorActivity === 'no') {
            return true;
        }
        if (is_numeric($fruitSet) && $fruitSet < 40) {
            return true;
        }
        return false;
    }

    /**
     * Detect disease issues from answers
     */
    private function detectDiseaseIssues(array $answers): bool
    {
        // Check for disease indicators
        $leafDisease = $answers['leaf_disease'] ?? 'no';
        $diseaseSpread = $answers['leaf_disease_spread'] ?? 'no';
        
        return $leafDisease === 'yes' || $diseaseSpread === 'yes';
    }

    /**
     * Get a summary of the recommendations
     */
    public function getRecommendationSummary(array $recommendations): string
    {
        $priorityCount = count(array_filter($recommendations['priority_alerts'], function($alert) {
            return !str_contains($alert, 'No immediate concerns detected');
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
