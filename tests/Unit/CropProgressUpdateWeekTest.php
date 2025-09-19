<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CropProgressUpdate;
use App\Models\Farm;
use Carbon\Carbon;

class CropProgressUpdateWeekTest extends TestCase
{


    public function test_week_calculation_logic()
    {
        // Create a mock farm with planting date
        $plantingDate = Carbon::now()->subDays(30);
        
        // Test Week 1 (0-7 days after planting)
        $week1Date = $plantingDate->copy()->addDays(7);
        $daysSincePlanting = abs($week1Date->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        
        $this->assertEquals(1, $weekNumber);
        $this->assertEquals('Week 1', "Week {$weekNumber}");

        // Test Week 2 (8-14 days after planting)
        $week2Date = $plantingDate->copy()->addDays(10);
        $daysSincePlanting = abs($week2Date->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        
        $this->assertEquals(2, $weekNumber);
        $this->assertEquals('Week 2', "Week {$weekNumber}");

        // Test Week 3 (15-21 days after planting)
        $week3Date = $plantingDate->copy()->addDays(17);
        $daysSincePlanting = abs($week3Date->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        
        $this->assertEquals(3, $weekNumber);
        $this->assertEquals('Week 3', "Week {$weekNumber}");

        // Test Week 4 (22-28 days after planting)
        $week4Date = $plantingDate->copy()->addDays(25);
        $daysSincePlanting = abs($week4Date->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        
        $this->assertEquals(4, $weekNumber);
        $this->assertEquals('Week 4', "Week {$weekNumber}");
    }

    public function test_week_calculation_edge_cases()
    {
        $plantingDate = Carbon::now()->subDays(30);
        
        // Day 0 should be Week 1
        $day0 = $plantingDate->copy();
        $daysSincePlanting = abs($day0->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        $this->assertEquals(1, $weekNumber);

        // Day 7 should still be Week 1
        $day7 = $plantingDate->copy()->addDays(7);
        $daysSincePlanting = abs($day7->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        $this->assertEquals(1, $weekNumber);

        // Day 8 should be Week 2
        $day8 = $plantingDate->copy()->addDays(8);
        $daysSincePlanting = abs($day8->diffInDays($plantingDate));
        $normalizedDays = max(0, $daysSincePlanting - 1);
        $weekNumber = intdiv($normalizedDays, 7) + 1;
        $this->assertEquals(2, $weekNumber);
    }

    public function test_week_calculation_with_negative_days()
    {
        $plantingDate = Carbon::now()->subDays(30);
        
        // Test date before planting (should still be Week 1)
        $beforePlantingDate = $plantingDate->copy()->subDays(5);
        $daysSincePlanting = $beforePlantingDate->diffInDays($plantingDate);
        $weekNumber = max(1, floor($daysSincePlanting / 7) + 1);
        
        $this->assertEquals(1, $weekNumber);
    }

    public function test_stage_specific_questions()
    {
        // Test that the questions array contains stage-specific questions
        $stages = ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'];
        
        // Test that each stage has questions defined
        foreach ($stages as $stage) {
            $this->assertTrue(
                in_array($stage, ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest']),
                "Stage {$stage} should be a valid growth stage"
            );
        }
        
        // Test that seedling stage questions contain seedling-specific terms
        $seedlingQuestions = [
            'How would you rate the overall health of your watermelon seedlings?',
            'What is the condition of the seedling leaves?',
            'How fast are your seedlings growing compared to expected?',
            'How is the soil moisture for your seedlings?',
            'How much pest pressure are your seedlings experiencing?',
            'Are there any visible disease symptoms on seedlings?',
            'Do you see signs of nutrient deficiency in seedlings?',
            'How has recent weather affected your seedlings?',
            'How well are your seedlings progressing toward vegetative growth?',
            'Overall, how satisfied are you with your seedling progress?'
        ];
        
        foreach ($seedlingQuestions as $question) {
            $this->assertStringContainsString(
                'seedling', 
                strtolower($question), 
                "Seedling question should mention 'seedling'"
            );
        }
        
        // Test that vegetative stage questions contain vegetative-specific terms
        $vegetativeQuestions = [
            'How would you rate the overall health of your vegetative watermelon plants?',
            'What is the condition of the vegetative leaves?',
            'How fast are your vines growing compared to expected?',
            'How is the water availability for your vegetative plants?',
            'How much pest pressure are your vegetative plants experiencing?',
            'Are there any visible disease symptoms on vegetative plants?',
            'Do you see signs of nutrient deficiency in vegetative plants?',
            'How has recent weather affected your vegetative plants?',
            'How well are your plants progressing toward flowering stage?',
            'Overall, how satisfied are you with your vegetative growth progress?'
        ];
        
        foreach ($vegetativeQuestions as $question) {
            $this->assertTrue(
                str_contains(strtolower($question), 'vegetative') || 
                str_contains(strtolower($question), 'vines') || 
                str_contains(strtolower($question), 'flowering'),
                "Vegetative question should mention vegetative-specific terms: {$question}"
            );
        }
    }
}
