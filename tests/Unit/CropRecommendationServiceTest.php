<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CropRecommendationService;

class CropRecommendationServiceTest extends TestCase
{
    private CropRecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CropRecommendationService();
    }

    public function test_generates_recommendations_for_healthy_crops()
    {
        $answers = [
            'plant_health' => 'excellent',
            'leaf_condition' => 'excellent',
            'growth_rate' => 'normal',
            'water_availability' => 'excellent',
            'pest_pressure' => 'none',
            'disease_issues' => 'none',
            'nutrient_deficiency' => 'none',
            'weather_impact' => 'positive',
            'stage_progression' => 'on_track',
            'overall_satisfaction' => 'very_satisfied'
        ];

        $recommendations = $this->service->generateRecommendations($answers);

        $this->assertArrayHasKey('immediate_actions', $recommendations);
        $this->assertArrayHasKey('weekly_plan', $recommendations);
        $this->assertArrayHasKey('long_term_tips', $recommendations);
        $this->assertArrayHasKey('priority_alerts', $recommendations);

        // Should have positive recommendations for healthy crops
        $this->assertContains('✅ Continue current good practices', $recommendations['immediate_actions']);
        $this->assertContains('✅ Maintain current excellent practices', $recommendations['long_term_tips']);
    }

    public function test_generates_urgent_recommendations_for_poor_health()
    {
        $answers = [
            'plant_health' => 'poor',
            'leaf_condition' => 'poor',
            'growth_rate' => 'stunted',
            'water_availability' => 'poor',
            'pest_pressure' => 'high',
            'disease_issues' => 'severe',
            'nutrient_deficiency' => 'severe',
            'weather_impact' => 'damaging',
            'stage_progression' => 'significantly_behind',
            'overall_satisfaction' => 'dissatisfied'
        ];

        $recommendations = $this->service->generateRecommendations($answers);

        // Should have urgent alerts
        $this->assertContains('⚠️ Plant health needs immediate attention', $recommendations['priority_alerts']);
        $this->assertContains('💧 Water management needs attention', $recommendations['priority_alerts']);
        $this->assertContains('🌱 Nutrient deficiency detected', $recommendations['priority_alerts']);
        $this->assertContains('🐛 Pest pressure requires immediate action', $recommendations['priority_alerts']);
        $this->assertContains('🦠 Disease issues detected', $recommendations['priority_alerts']);

        // Should have immediate actions
        $this->assertContains('🔍 Conduct thorough plant inspection to identify root causes', $recommendations['immediate_actions']);
        $this->assertContains('🚰 Check irrigation system for leaks or blockages', $recommendations['immediate_actions']);
    }

    public function test_generates_watermelon_specific_recommendations()
    {
        $answers = [
            'plant_health' => 'good',
            'leaf_condition' => 'good',
            'growth_rate' => 'normal',
            'water_availability' => 'good',
            'pest_pressure' => 'low',
            'disease_issues' => 'none',
            'nutrient_deficiency' => 'none',
            'weather_impact' => 'neutral',
            'stage_progression' => 'on_track',
            'overall_satisfaction' => 'satisfied'
        ];

        $recommendations = $this->service->generateRecommendations($answers, 'watermelon');

        // Should have watermelon-specific recommendations
        $this->assertContains('🍉 Monitor fruit development and adjust support as needed', $recommendations['weekly_plan']);
        $this->assertContains('🌱 Check for proper pollination and fruit set', $recommendations['weekly_plan']);
    }

    public function test_generates_appropriate_summary()
    {
        $answers = [
            'plant_health' => 'excellent',
            'leaf_condition' => 'excellent',
            'growth_rate' => 'normal',
            'water_availability' => 'excellent',
            'pest_pressure' => 'none',
            'disease_issues' => 'none',
            'nutrient_deficiency' => 'none',
            'weather_impact' => 'positive',
            'stage_progression' => 'on_track',
            'overall_satisfaction' => 'very_satisfied'
        ];

        $recommendations = $this->service->generateRecommendations($answers);
        $summary = $this->service->getRecommendationSummary($recommendations);

        $this->assertStringContainsString('Your crops are doing well', $summary);
    }

    public function test_handles_missing_answers_gracefully()
    {
        $answers = [
            'plant_health' => 'good',
            'leaf_condition' => 'good'
            // Missing other answers
        ];

        $recommendations = $this->service->generateRecommendations($answers);

        // Should still generate recommendations with defaults
        $this->assertArrayHasKey('immediate_actions', $recommendations);
        $this->assertArrayHasKey('weekly_plan', $recommendations);
        $this->assertArrayHasKey('long_term_tips', $recommendations);
        $this->assertArrayHasKey('priority_alerts', $recommendations);

        // Should have default recommendations
        $this->assertNotEmpty($recommendations['immediate_actions']);
        $this->assertNotEmpty($recommendations['weekly_plan']);
        $this->assertNotEmpty($recommendations['long_term_tips']);
        $this->assertNotEmpty($recommendations['priority_alerts']);
    }
}
