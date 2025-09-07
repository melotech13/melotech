<?php

namespace App\Services\Analysis;

use App\Models\PhotoAnalysis;
use Illuminate\Support\Facades\Log;

class GrowthAnalysisService
{
    /**
     * Determine growth stage based on analysis
     */
    public function determineGrowthStage(PhotoAnalysis $analysis): string
    {
        try {
            Log::info('Determining growth stage', [
                'analysis_id' => $analysis->id,
                'analysis_type' => $analysis->analysis_type
            ]);

            $daysOld = $analysis->created_at->diffInDays(now());
            $confidenceScore = (float) $analysis->confidence_score;
            $identifiedType = $analysis->identified_type;
            
            // Get growth stage based on analysis type and characteristics
            if ($analysis->analysis_type === 'leaves') {
                return $this->determineLeafGrowthStage($daysOld, $confidenceScore, $identifiedType);
            } elseif ($analysis->analysis_type === 'watermelon') {
                return $this->determineWatermelonGrowthStage($daysOld, $confidenceScore, $identifiedType);
            }
            
            // Default fallback
            return $this->getDefaultGrowthStage($daysOld);
            
        } catch (\Exception $e) {
            Log::error('Growth stage determination failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            return $this->getDefaultGrowthStage($analysis->created_at->diffInDays(now()));
        }
    }

    /**
     * Calculate growth progress percentage
     */
    public function calculateGrowthProgress(PhotoAnalysis $analysis): float
    {
        try {
            $daysOld = $analysis->created_at->diffInDays(now());
            $confidenceScore = (float) $analysis->confidence_score;
            $identifiedType = $analysis->identified_type;
            
            if ($analysis->analysis_type === 'leaves') {
                return $this->calculateLeafGrowthProgress($daysOld, $confidenceScore, $identifiedType);
            } elseif ($analysis->analysis_type === 'watermelon') {
                return $this->calculateWatermelonGrowthProgress($daysOld, $confidenceScore, $identifiedType);
            }
            
            // Default calculation
            return min(100, max(5, ($daysOld * 2)));
            
        } catch (\Exception $e) {
            Log::error('Growth progress calculation failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            return min(100, max(5, ($analysis->created_at->diffInDays(now()) * 2)));
        }
    }

    /**
     * Get recommended next steps
     */
    public function getNextSteps(PhotoAnalysis $analysis): array
    {
        try {
            $growthStage = $this->determineGrowthStage($analysis);
            $progress = $this->calculateGrowthProgress($analysis);
            $identifiedType = $analysis->identified_type;
            
            if ($analysis->analysis_type === 'leaves') {
                return $this->getLeafNextSteps($growthStage, $progress, $identifiedType);
            } elseif ($analysis->analysis_type === 'watermelon') {
                return $this->getWatermelonNextSteps($growthStage, $progress, $identifiedType);
            }
            
            return $this->getDefaultNextSteps();
            
        } catch (\Exception $e) {
            Log::error('Next steps generation failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            return $this->getDefaultNextSteps();
        }
    }

    /**
     * Determine leaf growth stage
     */
    protected function determineLeafGrowthStage(int $daysOld, float $confidenceScore, string $identifiedType): string
    {
        // Adjust days based on plant health
        $healthMultiplier = $this->getHealthMultiplier($identifiedType);
        $adjustedDays = $daysOld * $healthMultiplier;
        
        if ($adjustedDays < 7) {
            return 'Seedling Stage';
        } elseif ($adjustedDays < 21) {
            return 'Vegetative Stage';
        } elseif ($adjustedDays < 35) {
            return 'Flowering Stage';
        } elseif ($adjustedDays < 50) {
            return 'Fruiting Stage';
        } else {
            return 'Mature Stage';
        }
    }

    /**
     * Determine watermelon growth stage
     */
    protected function determineWatermelonGrowthStage(int $daysOld, float $confidenceScore, string $identifiedType): string
    {
        // Adjust days based on fruit condition
        $conditionMultiplier = $this->getConditionMultiplier($identifiedType);
        $adjustedDays = $daysOld * $conditionMultiplier;
        
        if ($adjustedDays < 10) {
            return 'Seedling Stage';
        } elseif ($adjustedDays < 25) {
            return 'Vegetative Stage';
        } elseif ($adjustedDays < 40) {
            return 'Flowering Stage';
        } elseif ($adjustedDays < 60) {
            return 'Fruiting Stage';
        } else {
            return 'Mature Stage';
        }
    }

    /**
     * Calculate leaf growth progress
     */
    protected function calculateLeafGrowthProgress(int $daysOld, float $confidenceScore, string $identifiedType): float
    {
        $baseProgress = min(100, max(5, ($daysOld * 2)));
        $healthMultiplier = $this->getHealthMultiplier($identifiedType);
        
        return min(100, $baseProgress * $healthMultiplier);
    }

    /**
     * Calculate watermelon growth progress
     */
    protected function calculateWatermelonGrowthProgress(int $daysOld, float $confidenceScore, string $identifiedType): float
    {
        $baseProgress = min(100, max(5, ($daysOld * 1.5)));
        $conditionMultiplier = $this->getConditionMultiplier($identifiedType);
        
        return min(100, $baseProgress * $conditionMultiplier);
    }

    /**
     * Get health multiplier based on identified type
     */
    protected function getHealthMultiplier(string $identifiedType): float
    {
        $multipliers = [
            'Healthy Green Leaves' => 1.0,
            'Yellowing Leaves' => 0.7,
            'Spotted/Diseased Leaves' => 0.5,
            'Wilted Leaves' => 0.3,
            'Nutrient Deficiency' => 0.6,
            'Pest Damage' => 0.4
        ];
        
        return $multipliers[$identifiedType] ?? 0.8;
    }

    /**
     * Get condition multiplier based on identified type
     */
    protected function getConditionMultiplier(string $identifiedType): float
    {
        $multipliers = [
            'Ripe Watermelon' => 1.0,
            'Nearly Ripe Watermelon' => 0.9,
            'Unripe Watermelon' => 0.6,
            'Overripe Watermelon' => 0.8,
            'Developing Watermelon' => 0.7,
            'Defective/Diseased Watermelon' => 0.3
        ];
        
        return $multipliers[$identifiedType] ?? 0.8;
    }

    /**
     * Get leaf-specific next steps
     */
    protected function getLeafNextSteps(string $growthStage, float $progress, string $identifiedType): array
    {
        $steps = [];
        
        // Base steps for growth stage
        switch ($growthStage) {
            case 'Seedling Stage':
                $steps = [
                    'Maintain consistent soil moisture',
                    'Provide gentle, filtered light',
                    'Monitor for first true leaves',
                    'Check soil temperature (70-75°F ideal)'
                ];
                break;
            case 'Vegetative Stage':
                $steps = [
                    'Ensure 6-8 hours of direct sunlight',
                    'Water when top inch of soil is dry',
                    'Begin light fertilization',
                    'Monitor for pest activity'
                ];
                break;
            case 'Flowering Stage':
                $steps = [
                    'Maintain consistent watering',
                    'Ensure adequate pollination',
                    'Monitor flower development',
                    'Reduce nitrogen, increase phosphorus'
                ];
                break;
            case 'Fruiting Stage':
                $steps = [
                    'Support developing fruits',
                    'Maintain consistent moisture',
                    'Monitor fruit development',
                    'Protect from extreme weather'
                ];
                break;
            case 'Mature Stage':
                $steps = [
                    'Harvest when ready',
                    'Prepare for next season',
                    'Collect seeds if desired',
                    'Clean up plant debris'
                ];
                break;
        }
        
        // Add health-specific steps
        $healthSteps = $this->getHealthSpecificSteps($identifiedType);
        $steps = array_merge($steps, $healthSteps);
        
        return $steps;
    }

    /**
     * Get watermelon-specific next steps
     */
    protected function getWatermelonNextSteps(string $growthStage, float $progress, string $identifiedType): array
    {
        $steps = [];
        
        // Base steps for growth stage
        switch ($growthStage) {
            case 'Seedling Stage':
                $steps = [
                    'Keep soil consistently moist',
                    'Provide warm temperatures (75-85°F)',
                    'Thin to strongest seedlings',
                    'Protect from cold snaps'
                ];
                break;
            case 'Vegetative Stage':
                $steps = [
                    'Space plants 6-8 feet apart',
                    'Provide trellis or ground space',
                    'Water deeply but infrequently',
                    'Apply balanced fertilizer'
                ];
                break;
            case 'Flowering Stage':
                $steps = [
                    'Ensure good pollination',
                    'Maintain consistent moisture',
                    'Watch for fruit set',
                    'Reduce nitrogen fertilizer'
                ];
                break;
            case 'Fruiting Stage':
                $steps = [
                    'Support developing fruits',
                    'Maintain even moisture',
                    'Rotate fruits for even ripening',
                    'Monitor for pests and diseases'
                ];
                break;
            case 'Mature Stage':
                $steps = [
                    'Check ripeness indicators',
                    'Harvest at optimal time',
                    'Store properly',
                    'Plan for next season'
                ];
                break;
        }
        
        // Add condition-specific steps
        $conditionSteps = $this->getConditionSpecificSteps($identifiedType);
        $steps = array_merge($steps, $conditionSteps);
        
        return $steps;
    }

    /**
     * Get health-specific steps for leaves
     */
    protected function getHealthSpecificSteps(string $identifiedType): array
    {
        $steps = [];
        
        switch ($identifiedType) {
            case 'Yellowing Leaves':
                $steps[] = 'Check soil pH and nutrient levels';
                $steps[] = 'Adjust watering frequency';
                break;
            case 'Spotted/Diseased Leaves':
                $steps[] = 'Remove affected leaves immediately';
                $steps[] = 'Apply appropriate fungicide';
                break;
            case 'Wilted Leaves':
                $steps[] = 'Check root health and drainage';
                $steps[] = 'Provide temporary shade';
                break;
            case 'Nutrient Deficiency':
                $steps[] = 'Test soil and apply specific nutrients';
                $steps[] = 'Consider foliar feeding';
                break;
            case 'Pest Damage':
                $steps[] = 'Identify and treat pest infestation';
                $steps[] = 'Apply preventive measures';
                break;
        }
        
        return $steps;
    }

    /**
     * Get condition-specific steps for watermelons
     */
    protected function getConditionSpecificSteps(string $identifiedType): array
    {
        $steps = [];
        
        switch ($identifiedType) {
            case 'Ripe Watermelon':
                $steps[] = 'Harvest within 1-2 days';
                $steps[] = 'Check ground spot color';
                break;
            case 'Nearly Ripe Watermelon':
                $steps[] = 'Wait 3-7 more days';
                $steps[] = 'Monitor tendril drying';
                break;
            case 'Unripe Watermelon':
                $steps[] = 'Continue regular care';
                $steps[] = 'Check again in 1-2 weeks';
                break;
            case 'Overripe Watermelon':
                $steps[] = 'Harvest immediately if quality is good';
                $steps[] = 'Use quickly after harvest';
                break;
            case 'Defective/Diseased Watermelon':
                $steps[] = 'Inspect for damage or disease';
                $steps[] = 'Consider removing if severely affected';
                break;
        }
        
        return $steps;
    }

    /**
     * Get default growth stage
     */
    protected function getDefaultGrowthStage(int $daysOld): string
    {
        if ($daysOld < 7) return 'Seedling Stage';
        if ($daysOld < 21) return 'Vegetative Stage';
        if ($daysOld < 35) return 'Flowering Stage';
        if ($daysOld < 50) return 'Fruiting Stage';
        return 'Mature Stage';
    }

    /**
     * Get default next steps
     */
    protected function getDefaultNextSteps(): array
    {
        return [
            'Monitor plant health regularly',
            'Maintain consistent care practices',
            'Check for signs of stress or disease',
            'Adjust care based on plant response'
        ];
    }
}
