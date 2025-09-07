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
     * Determine the growth stage based on analysis using the new analysis service
     */
    private function determineGrowthStage($analysis)
    {
        try {
            $analysisService = app(\App\Services\Analysis\GrowthAnalysisService::class);
            return $analysisService->determineGrowthStage($analysis);
        } catch (\Exception $e) {
            \Log::error('Growth stage determination failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to simple time-based logic
            $daysOld = $analysis->created_at->diffInDays(now());
            if ($daysOld < 7) return 'Seedling Stage';
            if ($daysOld < 21) return 'Vegetative Stage';
            if ($daysOld < 35) return 'Flowering Stage';
            if ($daysOld < 50) return 'Fruiting Stage';
            return 'Mature Stage';
        }
    }

    /**
     * Calculate growth progress percentage using the new analysis service
     */
    private function calculateGrowthProgress($analysis)
    {
        try {
            $analysisService = app(\App\Services\Analysis\GrowthAnalysisService::class);
            return $analysisService->calculateGrowthProgress($analysis);
        } catch (\Exception $e) {
            \Log::error('Growth progress calculation failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to simple time-based calculation
            $daysOld = $analysis->created_at->diffInDays(now());
            return min(100, max(5, ($daysOld * 2)));
        }
    }

    /**
     * Get recommended next steps using the new analysis service
     */
    private function getNextSteps($analysis)
    {
        try {
            $analysisService = app(\App\Services\Analysis\GrowthAnalysisService::class);
            return $analysisService->getNextSteps($analysis);
        } catch (\Exception $e) {
            \Log::error('Next steps generation failed', [
                'analysis_id' => $analysis->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback recommendations
            return [
                'Monitor soil moisture daily',
                'Check for pests and diseases',
                'Apply recommended fertilizer',
                'Ensure proper sunlight exposure'
            ];
        }
    }
}
