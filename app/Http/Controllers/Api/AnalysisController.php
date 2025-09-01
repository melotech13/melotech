<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalysisController extends Controller
{
    /**
     * Get growth progress for a specific analysis
     */
    public function getGrowthProgress($id)
    {
        try {
            $analysis = PhotoAnalysis::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Get related growth data (this is a simplified example)
            $growthData = [
                'stage' => $this->determineGrowthStage($analysis),
                'progress' => $this->calculateGrowthProgress($analysis),
                'next_steps' => $this->getNextSteps($analysis),
                'last_updated' => now()->format('M d, Y H:i')
            ];

            return response()->json([
                'success' => true,
                'html' => view('components.analysis.growth-progress', [
                    'analysis' => $analysis,
                    'growthData' => $growthData
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load growth progress.'
            ], 500);
        }
    }

    /**
     * Determine the growth stage based on analysis
     */
    private function determineGrowthStage($analysis)
    {
        // This is a simplified example - you would implement your own logic here
        $stages = [
            'seedling' => 'Seedling Stage',
            'vegetative' => 'Vegetative Stage',
            'flowering' => 'Flowering Stage',
            'fruiting' => 'Fruiting Stage',
            'mature' => 'Mature Stage'
        ];

        // Simple logic to determine stage (replace with your actual logic)
        $daysOld = $analysis->created_at->diffInDays(now());
        
        if ($daysOld < 7) return $stages['seedling'];
        if ($daysOld < 21) return $stages['vegetative'];
        if ($daysOld < 35) return $stages['flowering'];
        if ($daysOld < 50) return $stages['fruiting'];
        return $stages['mature'];
    }

    /**
     * Calculate growth progress percentage
     */
    private function calculateGrowthProgress($analysis)
    {
        // Simple progress calculation (replace with your actual logic)
        $daysOld = $analysis->created_at->diffInDays(now());
        return min(100, max(5, ($daysOld * 2))); // 2% per day, max 100%
    }

    /**
     * Get recommended next steps
     */
    private function getNextSteps($analysis)
    {
        // Simple next steps (customize based on your needs)
        return [
            'Monitor soil moisture daily',
            'Check for pests and diseases',
            'Apply recommended fertilizer',
            'Ensure proper sunlight exposure'
        ];
    }
}
