<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Farm;
use App\Models\CropGrowth;

class CropGrowthController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $farms = $user->farms()->with('cropGrowth')->get();
        
        return view('crop-growth.index', compact('farms'));
    }

    public function show(Farm $farm)
    {
        $cropGrowth = $farm->getOrCreateCropGrowth();
        $stages = CropGrowth::STAGES;
        $questions = $this->getStageQuestions($cropGrowth->current_stage);
        
        return view('crop-growth.show', compact('farm', 'cropGrowth', 'stages', 'questions'));
    }

    public function updateProgress(Request $request, Farm $farm)
    {
        $request->validate([
            'stage_progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $cropGrowth = $farm->getOrCreateCropGrowth();
        $cropGrowth->updateProgress($request->stage_progress, $request->notes);

        // Check if stage can be advanced
        if ($cropGrowth->canAdvanceStage()) {
            $cropGrowth->advanceStage();
            $message = 'Progress updated and stage advanced to ' . $cropGrowth->getStageInfo()['name'] . '!';
        } else {
            $message = 'Progress updated successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'crop_growth' => $cropGrowth->fresh(),
            'can_advance' => $cropGrowth->canAdvanceStage(),
        ]);
    }

    public function advanceStage(Request $request, Farm $farm)
    {
        $cropGrowth = $farm->getOrCreateCropGrowth();
        
        if ($cropGrowth->advanceStage()) {
            return response()->json([
                'success' => true,
                'message' => 'Stage advanced to ' . $cropGrowth->getStageInfo()['name'] . '!',
                'crop_growth' => $cropGrowth->fresh(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cannot advance stage yet. Complete current stage first.',
        ], 400);
    }

    public function quickUpdate(Request $request, Farm $farm)
    {
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required|boolean',
        ]);

        $cropGrowth = $farm->getOrCreateCropGrowth();
        $currentStage = $cropGrowth->current_stage;
        
        // Define questions and their impact on progress for each stage
        $questions = $this->getStageQuestions($currentStage);
        $question = $questions[$request->question_id] ?? null;
        
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid question.',
            ], 400);
        }

        // Calculate progress change based on answer
        $progressChange = $request->answer ? $question['positive_impact'] : $question['negative_impact'];
        $newProgress = max(0, min(100, $cropGrowth->stage_progress + $progressChange));
        
        $cropGrowth->updateProgress($newProgress, $request->answer ? $question['positive_note'] : $question['negative_note']);

        // Check if stage can be advanced
        $stageAdvanced = false;
        if ($cropGrowth->canAdvanceStage()) {
            $cropGrowth->advanceStage();
            $stageAdvanced = true;
        }

        return response()->json([
            'success' => true,
            'message' => $request->answer ? $question['positive_message'] : $question['negative_message'],
            'crop_growth' => $cropGrowth->fresh(),
            'stage_advanced' => $stageAdvanced,
            'new_stage' => $stageAdvanced ? $cropGrowth->getStageInfo()['name'] : null,
        ]);
    }

    public function getStageQuestions($stage)
    {
        $questions = [
            'seedling' => [
                'q1' => [
                    'text' => 'Are the seedlings growing well with healthy green leaves?',
                    'positive_impact' => 25,
                    'negative_impact' => -15,
                    'positive_message' => 'Great! Your seedlings are thriving! 🌱',
                    'negative_message' => 'Seedlings need attention. Check soil moisture and sunlight.',
                    'positive_note' => 'Seedlings showing healthy growth',
                    'negative_note' => 'Seedlings need care - check conditions'
                ],
                'q2' => [
                    'text' => 'Are there any signs of pests or diseases?',
                    'positive_impact' => 20,
                    'negative_impact' => -20,
                    'positive_message' => 'Excellent! No pest issues detected! ✅',
                    'negative_message' => 'Pest issues found. Consider treatment options.',
                    'positive_note' => 'No pest or disease issues',
                    'negative_note' => 'Pest or disease detected - needs treatment'
                ],
                'q3' => [
                    'text' => 'Is the soil moist but not waterlogged?',
                    'positive_impact' => 20,
                    'negative_impact' => -15,
                    'positive_message' => 'Perfect soil conditions! 💧',
                    'negative_message' => 'Soil moisture needs adjustment.',
                    'positive_note' => 'Soil moisture is optimal',
                    'negative_note' => 'Soil moisture needs adjustment'
                ],
                'q4' => [
                    'text' => 'Are the seedlings getting enough sunlight?',
                    'positive_impact' => 15,
                    'negative_impact' => -10,
                    'positive_message' => 'Sunlight conditions are perfect! ☀️',
                    'negative_message' => 'Consider adjusting sunlight exposure.',
                    'positive_note' => 'Adequate sunlight exposure',
                    'negative_note' => 'Sunlight exposure needs improvement'
                ]
            ],
            'vegetative' => [
                'q1' => [
                    'text' => 'Are the vines growing vigorously with many leaves?',
                    'positive_impact' => 20,
                    'negative_impact' => -15,
                    'positive_message' => 'Vines are growing strong! 🌿',
                    'negative_message' => 'Vine growth needs attention.',
                    'positive_note' => 'Vines showing vigorous growth',
                    'negative_note' => 'Vine growth needs improvement'
                ],
                'q2' => [
                    'text' => 'Are the leaves dark green and healthy?',
                    'positive_impact' => 20,
                    'negative_impact' => -20,
                    'positive_message' => 'Leaves look perfect! 🍃',
                    'negative_message' => 'Leaf health needs attention.',
                    'positive_note' => 'Leaves are healthy and green',
                    'negative_note' => 'Leaf health issues detected'
                ],
                'q3' => [
                    'text' => 'Are you providing adequate fertilizer?',
                    'positive_impact' => 20,
                    'negative_impact' => -15,
                    'positive_message' => 'Fertilization is on track! 🌱',
                    'negative_message' => 'Consider fertilizer application.',
                    'positive_note' => 'Fertilization is adequate',
                    'negative_note' => 'Fertilization needs attention'
                ],
                'q4' => [
                    'text' => 'Are the plants properly spaced and supported?',
                    'positive_impact' => 20,
                    'negative_impact' => -10,
                    'positive_message' => 'Plant spacing is perfect! 📏',
                    'negative_message' => 'Consider adjusting plant spacing.',
                    'positive_note' => 'Plants are properly spaced',
                    'negative_note' => 'Plant spacing needs adjustment'
                ]
            ],
            'flowering' => [
                'q1' => [
                    'text' => 'Are flowers appearing on the vines?',
                    'positive_impact' => 25,
                    'negative_impact' => -20,
                    'positive_message' => 'Flowers are blooming beautifully! 🌸',
                    'negative_message' => 'Flower development needs time.',
                    'positive_note' => 'Flowers are developing well',
                    'negative_note' => 'Flower development delayed'
                ],
                'q2' => [
                    'text' => 'Are bees and pollinators visiting the flowers?',
                    'positive_impact' => 25,
                    'negative_impact' => -15,
                    'positive_message' => 'Great pollination activity! 🐝',
                    'negative_message' => 'Pollination may need assistance.',
                    'positive_note' => 'Good pollinator activity',
                    'negative_note' => 'Pollination needs attention'
                ],
                'q3' => [
                    'text' => 'Are the flower buds healthy and not dropping?',
                    'positive_impact' => 20,
                    'negative_impact' => -20,
                    'positive_message' => 'Flower buds are strong! 💪',
                    'negative_message' => 'Flower buds need care.',
                    'positive_note' => 'Flower buds are healthy',
                    'negative_note' => 'Flower buds showing issues'
                ],
                'q4' => [
                    'text' => 'Is the weather suitable for flowering?',
                    'positive_impact' => 15,
                    'negative_impact' => -15,
                    'positive_message' => 'Perfect flowering weather! 🌤️',
                    'negative_message' => 'Weather may affect flowering.',
                    'positive_note' => 'Weather is suitable for flowering',
                    'negative_note' => 'Weather may impact flowering'
                ]
            ],
            'fruiting' => [
                'q1' => [
                    'text' => 'Are small fruits forming after pollination?',
                    'positive_impact' => 25,
                    'negative_impact' => -20,
                    'positive_message' => 'Fruits are developing! 🍉',
                    'negative_message' => 'Fruit formation needs time.',
                    'positive_note' => 'Fruits are forming well',
                    'negative_note' => 'Fruit formation delayed'
                ],
                'q2' => [
                    'text' => 'Are the fruits growing in size daily?',
                    'positive_impact' => 25,
                    'negative_impact' => -15,
                    'positive_message' => 'Fruits are growing fast! 📈',
                    'negative_message' => 'Fruit growth may be slow.',
                    'positive_note' => 'Fruits showing good growth',
                    'negative_note' => 'Fruit growth is slow'
                ],
                'q3' => [
                    'text' => 'Are the fruits free from pests and diseases?',
                    'positive_impact' => 20,
                    'negative_impact' => -25,
                    'positive_message' => 'Fruits are healthy! ✅',
                    'negative_message' => 'Fruits need protection.',
                    'positive_note' => 'Fruits are healthy',
                    'negative_note' => 'Fruits have pest/disease issues'
                ],
                'q4' => [
                    'text' => 'Are you providing adequate water for fruit development?',
                    'positive_impact' => 20,
                    'negative_impact' => -20,
                    'positive_message' => 'Watering is perfect! 💧',
                    'negative_message' => 'Watering needs adjustment.',
                    'positive_note' => 'Adequate water for fruits',
                    'negative_note' => 'Watering needs improvement'
                ]
            ],
            'harvest' => [
                'q1' => [
                    'text' => 'Are the fruits the right size for your variety?',
                    'positive_impact' => 25,
                    'negative_impact' => -15,
                    'positive_message' => 'Perfect fruit size! 🎯',
                    'negative_message' => 'Fruits may need more time.',
                    'positive_note' => 'Fruits reached optimal size',
                    'negative_note' => 'Fruits need more time to grow'
                ],
                'q2' => [
                    'text' => 'Do the fruits have the right color and sound when tapped?',
                    'positive_impact' => 25,
                    'negative_impact' => -20,
                    'positive_message' => 'Fruits are ready! 🎉',
                    'negative_message' => 'Fruits need more ripening.',
                    'positive_note' => 'Fruits show harvest readiness',
                    'negative_note' => 'Fruits need more ripening time'
                ],
                'q3' => [
                    'text' => 'Are the stems near the fruits starting to dry?',
                    'positive_impact' => 25,
                    'negative_impact' => -15,
                    'positive_message' => 'Perfect harvest timing! ✂️',
                    'negative_message' => 'Wait for stems to dry more.',
                    'positive_note' => 'Stems showing harvest readiness',
                    'negative_note' => 'Stems need more drying time'
                ],
                'q4' => [
                    'text' => 'Are you ready to harvest within the next few days?',
                    'positive_impact' => 25,
                    'negative_impact' => -10,
                    'positive_message' => 'Harvest time is here! 🚀',
                    'negative_message' => 'Plan your harvest timing.',
                    'positive_note' => 'Ready for harvest',
                    'negative_note' => 'Harvest planning needed'
                ]
            ]
        ];

        return $questions[$stage] ?? [];
    }
}
