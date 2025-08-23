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
        
        // Test Week 1 (0-6 days after planting)
        $week1Date = $plantingDate->copy()->addDays(3);
        $daysSincePlanting = abs($week1Date->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(1, $weekNumber);
        $this->assertEquals('Week 1', "Week {$weekNumber}");

        // Test Week 2 (7-13 days after planting)
        $week2Date = $plantingDate->copy()->addDays(10);
        $daysSincePlanting = abs($week2Date->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(2, $weekNumber);
        $this->assertEquals('Week 2', "Week {$weekNumber}");

        // Test Week 3 (14-20 days after planting)
        $week3Date = $plantingDate->copy()->addDays(17);
        $daysSincePlanting = abs($week3Date->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(3, $weekNumber);
        $this->assertEquals('Week 3', "Week {$weekNumber}");

        // Test Week 4 (21-27 days after planting)
        $week4Date = $plantingDate->copy()->addDays(25);
        $daysSincePlanting = abs($week4Date->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(4, $weekNumber);
        $this->assertEquals('Week 4', "Week {$weekNumber}");
    }

    public function test_week_calculation_edge_cases()
    {
        $plantingDate = Carbon::now()->subDays(30);
        
        // Test exactly 7 days (should be Week 2)
        $exactWeekDate = $plantingDate->copy()->addDays(7);
        $daysSincePlanting = abs($exactWeekDate->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(2, $weekNumber);

        // Test exactly 14 days (should be Week 3)
        $exactTwoWeeksDate = $plantingDate->copy()->addDays(14);
        $daysSincePlanting = abs($exactTwoWeeksDate->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(3, $weekNumber);

        // Test exactly 0 days (should be Week 1)
        $sameDayDate = $plantingDate->copy();
        $daysSincePlanting = abs($sameDayDate->diffInDays($plantingDate));
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        $this->assertEquals(1, $weekNumber);
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
}
