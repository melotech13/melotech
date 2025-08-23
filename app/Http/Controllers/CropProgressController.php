<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Farm;
use App\Models\CropProgressUpdate;
use App\Models\CropGrowth;
use App\Models\User;
use App\Services\CropRecommendationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CropProgressController extends Controller
{
    /**
     * Show the crop progress update page
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $farms = $user->farms()->with('cropGrowth')->get();
        
        // Get the first farm (since users can only have one farm)
        $selectedFarm = $farms->first();
        
        if (!$selectedFarm) {
            return view('crop-progress.index', [
                'farms' => collect(),
                'selectedFarm' => null,
                'canUpdate' => false,
                'nextUpdateDate' => null,
                'lastUpdate' => null
            ]);
        }

        // TEMPORARY: Allow immediate access for testing (remove this in production)
        $canUpdate = CropProgressUpdate::canAccessNewQuestions($user, $selectedFarm);
        $nextUpdateDate = CropProgressUpdate::getNextUpdateDate($user, $selectedFarm);
        $lastUpdate = CropProgressUpdate::where('user_id', $user->id)
            ->where('farm_id', $selectedFarm->id)
            ->latest('update_date')
            ->first();

        $progressUpdates = CropProgressUpdate::where('user_id', $user->id)
            ->where('farm_id', $selectedFarm->id)
            ->orderBy('update_date', 'desc')
            ->get();

        return view('crop-progress.index', [
            'farms' => $farms,
            'selectedFarm' => $selectedFarm,
            'canUpdate' => $canUpdate,
            'nextUpdateDate' => $nextUpdateDate,
            'lastUpdate' => $lastUpdate,
            'progressUpdates' => $progressUpdates
        ]);
    }

    /**
     * Show the guided questions form
     */
    public function showQuestions(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            abort(404, 'No farm found');
        }

        if (!CropProgressUpdate::canAccessNewQuestions($user, $farm)) {
            $nextUpdateDate = CropProgressUpdate::getNextUpdateDate($user, $farm);
            return view('crop-progress.waiting', [
                'farm' => $farm,
                'nextUpdateDate' => $nextUpdateDate
            ]);
        }

        $questions = $this->getGuidedQuestions($farm);
        
        return view('crop-progress.questions', [
            'farm' => $farm,
            'questions' => $questions
        ]);
    }

    /**
     * Store the guided questions answers
     */
    public function storeQuestions(Request $request): JsonResponse
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            return response()->json(['success' => false, 'message' => 'No farm found'], 404);
        }

        if (!CropProgressUpdate::canAccessNewQuestions($user, $farm)) {
            return response()->json(['success' => false, 'message' => 'Cannot update progress yet'], 400);
        }

        $answers = $request->input('answers');
        
        // Calculate progress from answers
        $calculatedProgress = CropProgressUpdate::calculateProgressFromQuestions($answers);
        
        // Create progress update record
        $progressUpdate = CropProgressUpdate::create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'session_id' => CropProgressUpdate::generateSessionId(),
            'update_date' => Carbon::now(),
            'update_method' => 'questions',
            'question_answers' => $answers,
            'selected_images' => null,
            'calculated_progress' => $calculatedProgress,
            'next_update_date' => Carbon::now()->addDays(6),
            'status' => 'completed'
        ]);

        // Update crop growth progress
        $this->updateCropGrowthProgress($farm, $calculatedProgress);

        // Generate AI recommendations based on answers
        $recommendationService = new CropRecommendationService();
        $recommendations = $recommendationService->generateRecommendations($answers, $farm->watermelon_variety);
        $recommendationSummary = $recommendationService->getRecommendationSummary($recommendations);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully!',
            'calculated_progress' => $calculatedProgress,
            'next_update_date' => $progressUpdate->next_update_date->format('M d, Y'),
            'recommendations' => $recommendations,
            'recommendation_summary' => $recommendationSummary
        ]);
    }

    /**
     * Export progress data as JSON
     */
    public function exportProgress(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            return response()->json(['success' => false, 'message' => 'No farm found'], 404);
        }

        $progressUpdates = CropProgressUpdate::where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->orderBy('update_date', 'desc')
            ->get();

        $exportData = [
            'farm_info' => [
                'farm_name' => $farm->farm_name,
                'watermelon_variety' => $farm->watermelon_variety,
                'field_size' => $farm->field_size . ' ' . $farm->field_size_unit,
                'planting_date' => $farm->planting_date->format('M d, Y')
            ],
            'progress_updates' => $progressUpdates->map(function ($update) {
                return [
                    'update_date' => $update->update_date->format('M d, Y'),
                    'update_method' => $update->update_method,
                    'calculated_progress' => $update->calculated_progress . '%',
                    'question_answers' => $update->question_answers,
                    'selected_images' => $update->selected_images
                ];
            }),
            'export_date' => Carbon::now()->format('M d, Y H:i:s')
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }

    /**
     * Export single progress update as printable HTML
     */
    public function exportSingleUpdate(int $updateId): View
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            abort(404, 'No farm found');
        }

        $progressUpdate = CropProgressUpdate::where('id', $updateId)
            ->where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->first();

        if (!$progressUpdate) {
            abort(404, 'Progress update not found');
        }

        $data = [
            'farm' => $farm,
            'progressUpdate' => $progressUpdate,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s')
        ];

        // Return a printable HTML view for single update
        return view('crop-progress.single-update-report', $data);
    }

    /**
     * Get guided questions for the farm
     */
    private function getGuidedQuestions(Farm $farm): array
    {
        $currentStage = $farm->cropGrowth->current_stage ?? 'seedling';
        
        $questions = [
            'plant_health' => [
                'id' => 'plant_health',
                'question' => 'How would you rate the overall health of your watermelon plants?',
                'type' => 'select',
                'options' => [
                    'excellent' => 'Excellent - Plants look very healthy and vigorous',
                    'good' => 'Good - Plants look healthy with minor issues',
                    'fair' => 'Fair - Plants have some problems but are growing',
                    'poor' => 'Poor - Plants have significant health issues'
                ]
            ],
            'leaf_condition' => [
                'id' => 'leaf_condition',
                'question' => 'What is the condition of the leaves?',
                'type' => 'select',
                'options' => [
                    'excellent' => 'Excellent - Dark green, no spots or damage',
                    'good' => 'Good - Green with minor spots or slight yellowing',
                    'fair' => 'Fair - Some yellowing or spots, but mostly healthy',
                    'poor' => 'Poor - Significant yellowing, spots, or damage'
                ]
            ],
            'growth_rate' => [
                'id' => 'growth_rate',
                'question' => 'How fast are your plants growing compared to expected?',
                'type' => 'select',
                'options' => [
                    'faster' => 'Faster than expected',
                    'normal' => 'Normal growth rate',
                    'slower' => 'Slower than expected',
                    'stunted' => 'Growth seems stunted'
                ]
            ],
            'water_availability' => [
                'id' => 'water_availability',
                'question' => 'How is the water availability for your plants?',
                'type' => 'select',
                'options' => [
                    'excellent' => 'Excellent - Consistent moisture, no drought stress',
                    'good' => 'Good - Adequate moisture with minor fluctuations',
                    'fair' => 'Fair - Some drought stress or overwatering',
                    'poor' => 'Poor - Significant water issues'
                ]
            ],
            'pest_pressure' => [
                'id' => 'pest_pressure',
                'question' => 'How much pest pressure are you experiencing?',
                'type' => 'select',
                'options' => [
                    'none' => 'None - No visible pests',
                    'low' => 'Low - Occasional pests, easily controlled',
                    'moderate' => 'Moderate - Regular pest activity, requires attention',
                    'high' => 'High - Significant pest problems'
                ]
            ],
            'disease_issues' => [
                'id' => 'disease_issues',
                'question' => 'Are there any visible disease symptoms?',
                'type' => 'select',
                'options' => [
                    'none' => 'None - No visible disease symptoms',
                    'minor' => 'Minor - Some symptoms but not affecting growth',
                    'moderate' => 'Moderate - Symptoms affecting some plants',
                    'severe' => 'Severe - Widespread disease issues'
                ]
            ],
            'nutrient_deficiency' => [
                'id' => 'nutrient_deficiency',
                'question' => 'Do you see signs of nutrient deficiency?',
                'type' => 'select',
                'options' => [
                    'none' => 'None - Plants show good nutrient status',
                    'slight' => 'Slight - Minor yellowing or color changes',
                    'moderate' => 'Moderate - Clear signs of deficiency',
                    'severe' => 'Severe - Significant nutrient problems'
                ]
            ],
            'weather_impact' => [
                'id' => 'weather_impact',
                'question' => 'How has recent weather affected your plants?',
                'type' => 'select',
                'options' => [
                    'positive' => 'Positive - Weather has been beneficial',
                    'neutral' => 'Neutral - Weather has had minimal impact',
                    'negative' => 'Negative - Weather has caused some stress',
                    'damaging' => 'Damaging - Weather has caused significant damage'
                ]
            ],
            'stage_progression' => [
                'id' => 'stage_progression',
                'question' => 'How well are your plants progressing through growth stages?',
                'type' => 'select',
                'options' => [
                    'ahead' => 'Ahead of schedule',
                    'on_track' => 'On track with expected timeline',
                    'slightly_behind' => 'Slightly behind schedule',
                    'significantly_behind' => 'Significantly behind schedule'
                ]
            ],
            'overall_satisfaction' => [
                'id' => 'overall_satisfaction',
                'question' => 'Overall, how satisfied are you with your crop progress?',
                'type' => 'select',
                'options' => [
                    'very_satisfied' => 'Very satisfied - Exceeding expectations',
                    'satisfied' => 'Satisfied - Meeting expectations',
                    'somewhat_satisfied' => 'Somewhat satisfied - Below expectations',
                    'dissatisfied' => 'Dissatisfied - Significantly below expectations'
                ]
            ]
        ];

        // Remove conditional questions to ensure exactly 10 questions
        // Stage-specific information can be gathered from other questions

        return $questions;
    }

    /**
     * Update crop growth progress based on calculated progress
     */
    private function updateCropGrowthProgress(Farm $farm, int $calculatedProgress): void
    {
        $cropGrowth = $farm->cropGrowth;
        
        if (!$cropGrowth) {
            $cropGrowth = new CropGrowth();
            $cropGrowth->farm_id = $farm->id;
            $cropGrowth->current_stage = 'seedling';
            $cropGrowth->stage_progress = 0;
            $cropGrowth->overall_progress = 0;
            $cropGrowth->last_updated = Carbon::now();
        }

        // Update overall progress (average of existing and new)
        $existingProgress = $cropGrowth->overall_progress ?? 0;
        $newOverallProgress = round(($existingProgress + $calculatedProgress) / 2);
        
        $cropGrowth->overall_progress = min(100, $newOverallProgress);
        $cropGrowth->last_updated = Carbon::now();
        
        // Update stage progress based on overall progress
        if ($cropGrowth->overall_progress >= 80) {
            $cropGrowth->current_stage = 'harvest';
            $cropGrowth->stage_progress = 100;
        } elseif ($cropGrowth->overall_progress >= 60) {
            $cropGrowth->current_stage = 'fruiting';
            $cropGrowth->stage_progress = min(100, ($cropGrowth->overall_progress - 60) * 5);
        } elseif ($cropGrowth->overall_progress >= 35) {
            $cropGrowth->current_stage = 'flowering';
            $cropGrowth->stage_progress = min(100, ($cropGrowth->overall_progress - 35) * 4);
        } elseif ($cropGrowth->overall_progress >= 15) {
            $cropGrowth->current_stage = 'vegetative';
            $cropGrowth->stage_progress = min(100, ($cropGrowth->overall_progress - 15) * 5);
        } else {
            $cropGrowth->current_stage = 'seedling';
            $cropGrowth->stage_progress = min(100, $cropGrowth->overall_progress * 6.67);
        }

        $cropGrowth->save();
    }

    /**
     * Get AI recommendations for a specific progress update
     */
    public function getRecommendations(int $updateId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            return response()->json(['success' => false, 'message' => 'No farm found'], 404);
        }

        $progressUpdate = CropProgressUpdate::where('id', $updateId)
            ->where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->first();

        if (!$progressUpdate) {
            return response()->json(['success' => false, 'message' => 'Progress update not found'], 404);
        }

        // Generate AI recommendations based on the stored answers
        $recommendationService = new CropRecommendationService();
        $recommendations = $recommendationService->generateRecommendations(
            $progressUpdate->question_answers, 
            $farm->watermelon_variety
        );
        $recommendationSummary = $recommendationService->getRecommendationSummary($recommendations);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'recommendation_summary' => $recommendationSummary,
            'update_date' => $progressUpdate->update_date->format('M d, Y'),
            'progress' => $progressUpdate->calculated_progress
        ]);
    }
}
