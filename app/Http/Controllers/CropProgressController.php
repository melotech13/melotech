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
use App\Services\ResearchPaperService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
            return view('user.crop-progress.index', [
                'farms' => collect(),
                'selectedFarm' => null,
                'canUpdate' => false,
                'nextUpdateDate' => null,
                'lastUpdate' => null
            ]);
        }

        // Check if user can access new questions (6 days after last update)
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

        return view('user.crop-progress.index', [
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




        
        // Check if user can access new questions (6 days after last update)
        $canAccess = CropProgressUpdate::canAccessNewQuestions($user, $farm);
        $nextUpdateDate = CropProgressUpdate::getNextUpdateDate($user, $farm);
        
        if (!$canAccess) {
            $nextWeekNumber = CropProgressUpdate::getNextWeekNumber($user, $farm);
            return view('user.crop-progress.waiting', [
                'farm' => $farm,
                'nextUpdateDate' => $nextUpdateDate,
                'nextWeekNumber' => $nextWeekNumber
            ]);
        }

        $questions = $this->getGuidedQuestions($farm);
        
        return view('user.crop-progress.questions', [
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

        // Check if user can access new questions (6 days after last update)
        if (!CropProgressUpdate::canAccessNewQuestions($user, $farm)) {
            return response()->json(['success' => false, 'message' => 'Cannot update progress yet. Please wait 6 days between updates.'], 400);
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
            'calculated_progress' => $calculatedProgress,
            'next_update_date' => Carbon::now()->addDays(6),
            'status' => 'completed'
        ]);

        // Update crop growth progress
        $this->updateCropGrowthProgress($farm, $calculatedProgress);

        // Fetch relevant research papers FIRST
        $researchService = new ResearchPaperService();
        $cropStage = $farm->cropGrowth->current_stage ?? 'seedling';
        // Generate initial concerns from answers for research search
        $initialConcerns = $this->extractConcernsFromAnswers($answers);
        $researchPapers = $researchService->fetchRelevantResearch(
            $farm->watermelon_variety ?? 'watermelon',
            $cropStage,
            $initialConcerns
        );

        // Generate AI recommendations based on answers AND research papers
        $recommendationService = new CropRecommendationService();
        $recommendations = $recommendationService->generateRecommendations(
            $answers, 
            $farm->watermelon_variety,
            $researchPapers
        );
        $recommendationSummary = $recommendationService->getRecommendationSummary($recommendations);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully!',
            'calculated_progress' => $calculatedProgress,
            'next_update_date' => $progressUpdate->next_update_date->format('M d, Y'),
            'recommendations' => $recommendations,
            'recommendation_summary' => $recommendationSummary,
            'research_papers' => $researchPapers
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
                'land_size' => $farm->land_size . ' ' . $farm->land_size_unit,
                'planting_date' => $farm->planting_date->format('M d, Y')
            ],
            'progress_updates' => $progressUpdates->map(function ($update) {
                return [
                    'update_date' => $update->update_date->format('M d, Y'),
                    'update_method' => $update->update_method,
                    'calculated_progress' => $update->calculated_progress . '%',
                    'question_answers' => $update->question_answers,
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
        return view('user.crop-progress.single-update-report', $data);
    }

    /**
     * Get guided questions for the farm
     */
    private function getGuidedQuestions(Farm $farm): array
    {
        $currentStage = $farm->cropGrowth->current_stage ?? 'seedling';
        
        // Stage-specific questions aligned with crop growth stages
        // 10 questions per stage: 5 focused on leaves, 5 on general crop progress
        $questions = [
            'seedling' => [
                // LEAF-FOCUSED QUESTIONS (5)
                'leaf_color' => [
                    'id' => 'leaf_color',
                    'question' => 'What is the predominant color of your seedling leaves?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Dark green (healthy)',
                        '4' => 'Medium green',
                        '3' => 'Light green',
                        '2' => 'Yellowish green',
                        '1' => 'Yellow or pale'
                    ]
                ],
                'leaf_size' => [
                    'id' => 'leaf_size',
                    'question' => 'How would you describe the size of the first true leaves?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Large and well-developed (5-8 cm)',
                        '4' => 'Good size (4-5 cm)',
                        '3' => 'Average size (3-4 cm)',
                        '2' => 'Small (2-3 cm)',
                        '1' => 'Very small or stunted (<2 cm)'
                    ]
                ],
                'leaf_spots' => [
                    'id' => 'leaf_spots',
                    'question' => 'Are there any spots, holes, or discoloration on the leaves?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Leaves are clean and uniform',
                        'yes' => 'Yes - I see spots or damage'
                    ]
                ],
                'leaf_texture' => [
                    'id' => 'leaf_texture',
                    'question' => 'How do the leaves feel when you gently touch them?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Firm and turgid (well-hydrated)',
                        '4' => 'Slightly firm',
                        '3' => 'Normal texture',
                        '2' => 'Slightly wilted or soft',
                        '1' => 'Wilted or drooping'
                    ]
                ],
                'leaf_count' => [
                    'id' => 'leaf_count',
                    'question' => 'How many true leaves have developed per seedling?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 10,
                    'unit' => 'leaves',
                    'optimal' => '4-6'
                ],
                
                // GENERAL CROP PROGRESS QUESTIONS (5)
                'stem_strength' => [
                    'id' => 'stem_strength',
                    'question' => 'How strong and sturdy are the seedling stems?',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Very strong - thick and upright',
                        '4' => 'Strong - standing well',
                        '3' => 'Moderate - some bending',
                        '2' => 'Weak - thin and bending',
                        '1' => 'Very weak - falling over'
                    ]
                ],
                'root_development' => [
                    'id' => 'root_development',
                    'question' => 'Can you see roots emerging from drainage holes or soil surface?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Good root development visible',
                        'no' => 'No - Roots not yet visible'
                    ]
                ],
                'growth_speed' => [
                    'id' => 'growth_speed',
                    'question' => 'Rate the daily growth rate you observe (10-15 days after planting)',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Very fast - noticeable daily changes',
                        '4' => 'Fast - clear growth every 2 days',
                        '3' => 'Moderate - growth visible weekly',
                        '2' => 'Slow - minimal changes',
                        '1' => 'Very slow or no growth'
                    ]
                ],
                'pest_damage' => [
                    'id' => 'pest_damage',
                    'question' => 'What percentage of seedlings show pest damage?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '0-5%'
                ],
                'seedling_uniformity' => [
                    'id' => 'seedling_uniformity',
                    'question' => 'How uniform is the growth across all seedlings?',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Very uniform - all similar size',
                        '4' => 'Mostly uniform - minor variations',
                        '3' => 'Moderate - some size differences',
                        '2' => 'Uneven - significant variations',
                        '1' => 'Very uneven - large disparities'
                    ]
                ]
            ],
            'vegetative' => [
                // LEAF-FOCUSED QUESTIONS (5)
                'leaf_color' => [
                    'id' => 'leaf_color',
                    'question' => 'What is the predominant color of your vine leaves?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Deep dark green (optimal)',
                        '4' => 'Dark green',
                        '3' => 'Medium green',
                        '2' => 'Light green or yellowing',
                        '1' => 'Yellow or brown edges'
                    ]
                ],
                'leaf_size' => [
                    'id' => 'leaf_size',
                    'question' => 'What is the average size of mature leaves on the vines?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Very large (15-20 cm wide)',
                        '4' => 'Large (12-15 cm)',
                        '3' => 'Medium (10-12 cm)',
                        '2' => 'Small (7-10 cm)',
                        '1' => 'Very small (<7 cm)'
                    ]
                ],
                'leaf_disease' => [
                    'id' => 'leaf_disease',
                    'question' => 'Do you see any powdery mildew, downy mildew, or fungal spots on leaves?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Leaves are disease-free',
                        'yes' => 'Yes - I see disease symptoms'
                    ]
                ],
                'leaf_density' => [
                    'id' => 'leaf_density',
                    'question' => 'How dense is the leaf canopy coverage?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '% coverage',
                    'optimal' => '60-80%'
                ],
                'leaf_damage' => [
                    'id' => 'leaf_damage',
                    'question' => 'What percentage of leaves show insect damage or chewing marks?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '0-10%'
                ],
                
                // GENERAL CROP PROGRESS QUESTIONS (5)
                'vine_length' => [
                    'id' => 'vine_length',
                    'question' => 'What is the approximate length of the longest vines?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 300,
                    'unit' => 'cm',
                    'optimal' => '100-200 cm'
                ],
                'lateral_branches' => [
                    'id' => 'lateral_branches',
                    'question' => 'How many lateral branches have developed per plant?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 15,
                    'unit' => 'branches',
                    'optimal' => '4-8'
                ],
                'flower_buds' => [
                    'id' => 'flower_buds',
                    'question' => 'Can you see flower buds starting to form?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Flower buds are visible',
                        'no' => 'No - No flower buds yet'
                    ]
                ],
                'water_stress' => [
                    'id' => 'water_stress',
                    'question' => 'Rate the water stress level during hottest part of day',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'No stress - leaves stay turgid',
                        '4' => 'Minimal - slight wilting',
                        '3' => 'Moderate - visible wilting',
                        '2' => 'High - significant wilting',
                        '1' => 'Severe - extreme wilting'
                    ]
                ],
                'overall_vigor' => [
                    'id' => 'overall_vigor',
                    'question' => 'Rate the overall plant vigor and growth rate',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Excellent - rapid vigorous growth',
                        '4' => 'Good - steady healthy growth',
                        '3' => 'Fair - moderate growth',
                        '2' => 'Poor - slow growth',
                        '1' => 'Very poor - stunted'
                    ]
                ]
            ],
            'flowering' => [
                // LEAF-FOCUSED QUESTIONS (5)
                'leaf_health' => [
                    'id' => 'leaf_health',
                    'question' => 'What is the overall health status of leaves during flowering?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Excellent - vibrant and healthy',
                        '4' => 'Good - mostly healthy',
                        '3' => 'Fair - some yellowing',
                        '2' => 'Poor - significant yellowing',
                        '1' => 'Very poor - dying leaves'
                    ]
                ],
                'older_leaf_condition' => [
                    'id' => 'older_leaf_condition',
                    'question' => 'Are the older (lower) leaves starting to yellow or die back?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - All leaves remain green',
                        'yes' => 'Yes - Natural senescence occurring'
                    ]
                ],
                'leaf_curl' => [
                    'id' => 'leaf_curl',
                    'question' => 'Do you observe any leaf curling or distortion?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Leaves are flat and normal',
                        'yes' => 'Yes - Leaves show curling'
                    ]
                ],
                'leaf_nutrient_signs' => [
                    'id' => 'leaf_nutrient_signs',
                    'question' => 'What percentage of leaves show nutrient deficiency symptoms (yellowing between veins, purple tints)?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '0-15%'
                ],
                'leaf_photosynthesis' => [
                    'id' => 'leaf_photosynthesis',
                    'question' => 'Rate the photosynthetic capacity based on leaf appearance',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Excellent - dark green, full canopy',
                        '4' => 'Good - healthy green color',
                        '3' => 'Fair - adequate but pale',
                        '2' => 'Poor - yellowing, sparse',
                        '1' => 'Very poor - severe yellowing'
                    ]
                ],
                
                // GENERAL CROP PROGRESS QUESTIONS (5)
                'male_flowers' => [
                    'id' => 'male_flowers',
                    'question' => 'How many male flowers are open per plant?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 50,
                    'unit' => 'flowers',
                    'optimal' => '10-30'
                ],
                'female_flowers' => [
                    'id' => 'female_flowers',
                    'question' => 'How many female flowers (with small fruit behind) are visible per plant?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 20,
                    'unit' => 'flowers',
                    'optimal' => '5-15'
                ],
                'pollinator_activity' => [
                    'id' => 'pollinator_activity',
                    'question' => 'Do you observe bees or other pollinators visiting the flowers?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Good pollinator activity',
                        'no' => 'No - Few or no pollinators'
                    ]
                ],
                'fruit_set' => [
                    'id' => 'fruit_set',
                    'question' => 'What percentage of female flowers are successfully setting fruit?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '40-70%'
                ],
                'flowering_duration' => [
                    'id' => 'flowering_duration',
                    'question' => 'How many days has the plant been flowering?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 30,
                    'unit' => 'days',
                    'optimal' => '10-20'
                ]
            ],
            'fruiting' => [
                // LEAF-FOCUSED QUESTIONS (5)
                'leaf_color' => [
                    'id' => 'leaf_color',
                    'question' => 'What is the color of leaves near the developing fruits?',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Dark green and healthy',
                        '4' => 'Green with good color',
                        '3' => 'Light green',
                        '2' => 'Yellowing',
                        '1' => 'Yellow or brown'
                    ]
                ],
                'leaf_retention' => [
                    'id' => 'leaf_retention',
                    'question' => 'What percentage of the original leaf canopy is still intact?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '70-90%'
                ],
                'leaf_disease_spread' => [
                    'id' => 'leaf_disease_spread',
                    'question' => 'Is disease spreading on the leaves?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Disease is controlled',
                        'yes' => 'Yes - Disease is spreading'
                    ]
                ],
                'leaf_shading' => [
                    'id' => 'leaf_shading',
                    'question' => 'Are the leaves providing adequate shade for developing fruits?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Fruits are well-shaded',
                        'no' => 'No - Fruits exposed to sun'
                    ]
                ],
                'leaf_senescence' => [
                    'id' => 'leaf_senescence',
                    'question' => 'Rate the natural aging/yellowing of older leaves',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Normal - only oldest leaves yellowing',
                        '4' => 'Slight - some lower leaves yellow',
                        '3' => 'Moderate - many lower leaves yellow',
                        '2' => 'High - yellowing spreading upward',
                        '1' => 'Severe - most leaves yellowing'
                    ]
                ],
                
                // GENERAL CROP PROGRESS QUESTIONS (5)
                'fruit_count' => [
                    'id' => 'fruit_count',
                    'question' => 'How many fruits are developing per plant?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 10,
                    'unit' => 'fruits',
                    'optimal' => '2-4'
                ],
                'fruit_size' => [
                    'id' => 'fruit_size',
                    'question' => 'What is the diameter of the largest fruit?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 40,
                    'unit' => 'cm',
                    'optimal' => '15-30 cm'
                ],
                'fruit_color' => [
                    'id' => 'fruit_color',
                    'question' => 'Rate the fruit skin color development',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Excellent - vibrant pattern developing',
                        '4' => 'Good - clear color pattern',
                        '3' => 'Fair - color starting to show',
                        '2' => 'Poor - minimal color change',
                        '1' => 'Very poor - still very pale'
                    ]
                ],
                'fruit_growth_rate' => [
                    'id' => 'fruit_growth_rate',
                    'question' => 'How much are fruits growing per week?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 10,
                    'unit' => 'cm/week',
                    'optimal' => '3-7 cm/week'
                ],
                'fruit_health' => [
                    'id' => 'fruit_health',
                    'question' => 'Rate the overall health and appearance of developing fruits',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Excellent - uniform, no blemishes',
                        '4' => 'Good - healthy with minor marks',
                        '3' => 'Fair - some imperfections',
                        '2' => 'Poor - visible damage or rot',
                        '1' => 'Very poor - severe problems'
                    ]
                ]
            ],
            'harvest' => [
                // LEAF-FOCUSED QUESTIONS (5)
                'leaf_condition' => [
                    'id' => 'leaf_condition',
                    'question' => 'What percentage of leaves are still green and functional?',
                    'category' => 'leaf',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '40-70%'
                ],
                'leaf_drying' => [
                    'id' => 'leaf_drying',
                    'question' => 'Are the leaves naturally drying and dying back?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Natural senescence (normal)',
                        'no' => 'No - Leaves still mostly green'
                    ]
                ],
                'leaf_disease_final' => [
                    'id' => 'leaf_disease_final',
                    'question' => 'Is leaf disease affecting fruit quality at harvest?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Fruits remain healthy',
                        'yes' => 'Yes - Disease impacting fruits'
                    ]
                ],
                'leaf_coverage_harvest' => [
                    'id' => 'leaf_coverage_harvest',
                    'question' => 'Rate the remaining leaf canopy coverage',
                    'category' => 'leaf',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'High - 70-100% coverage',
                        '4' => 'Good - 50-70% coverage',
                        '3' => 'Moderate - 30-50% coverage',
                        '2' => 'Low - 10-30% coverage',
                        '1' => 'Very low - <10% coverage'
                    ]
                ],
                'leaf_pest_final' => [
                    'id' => 'leaf_pest_final',
                    'question' => 'Are pests still actively damaging leaves?',
                    'category' => 'leaf',
                    'type' => 'yesno',
                    'options' => [
                        'no' => 'No - Pest pressure has decreased',
                        'yes' => 'Yes - Pests still active'
                    ]
                ],
                
                // GENERAL CROP PROGRESS QUESTIONS (5)
                'fruit_maturity' => [
                    'id' => 'fruit_maturity',
                    'question' => 'What percentage of fruits show harvest maturity signs?',
                    'category' => 'progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'unit' => '%',
                    'optimal' => '80-100%'
                ],
                'ground_spot' => [
                    'id' => 'ground_spot',
                    'question' => 'Has the ground spot (where fruit touches soil) turned creamy yellow?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Ground spot is yellow (ready)',
                        'no' => 'No - Still white or pale'
                    ]
                ],
                'tendril_drying' => [
                    'id' => 'tendril_drying',
                    'question' => 'Are the tendrils near the fruit stem dried and brown?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Tendrils are dry (harvest ready)',
                        'no' => 'No - Tendrils still green'
                    ]
                ],
                'hollow_sound' => [
                    'id' => 'hollow_sound',
                    'question' => 'When you tap the fruit, does it sound hollow/deep?',
                    'category' => 'progress',
                    'type' => 'yesno',
                    'options' => [
                        'yes' => 'Yes - Hollow sound (ripe)',
                        'no' => 'No - Dull/flat sound (not ready)'
                    ]
                ],
                'harvest_readiness' => [
                    'id' => 'harvest_readiness',
                    'question' => 'Based on all indicators, rate harvest readiness',
                    'category' => 'progress',
                    'type' => 'rating',
                    'options' => [
                        '5' => 'Fully ready - harvest now',
                        '4' => 'Nearly ready - harvest in 2-3 days',
                        '3' => 'Getting close - wait 5-7 days',
                        '2' => 'Not ready - wait 10-14 days',
                        '1' => 'Far from ready - wait 2+ weeks'
                    ]
                ]
            ]
        ];

        // Return stage-specific questions
        return $questions[$currentStage] ?? $questions['seedling'];
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
     * Export progress history as PDF
     */
    public function exportPDF()
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            abort(404, 'No farm found');
        }

        $progressUpdates = CropProgressUpdate::where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->orderBy('update_date', 'desc')
            ->get();

        // Generate AI recommendations based on the latest progress update
        $aiRecommendations = [];
        $recommendationService = new CropRecommendationService();
        
        if ($progressUpdates->count() > 0) {
            $latestUpdate = $progressUpdates->first();
            if ($latestUpdate->question_answers) {
                $aiRecommendations = $recommendationService->generateRecommendations(
                    $latestUpdate->question_answers, 
                    $farm->watermelon_variety ?? 'watermelon'
                );
            }
        }

        // Generate fallback recommendations if no data available
        if (empty($aiRecommendations)) {
            $aiRecommendations = $this->generateFallbackRecommendations($progressUpdates);
        }

        $data = [
            'farm' => $farm,
            'progressUpdates' => $progressUpdates,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'aiRecommendations' => $aiRecommendations,
            'aiConfidence' => $this->calculateAIConfidence($progressUpdates)
        ];

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('user.crop-progress.pdf-report', $data);
        
        $fileName = 'farm_progress_report_' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }

    public function printReport()
    {
        /** @var User $user */
        $user = Auth::user();
        $farm = $user->farms()->first();
        
        if (!$farm) {
            abort(404, 'No farm found');
        }

        $progressUpdates = CropProgressUpdate::where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->orderBy('update_date', 'desc')
            ->get();

        // Generate AI recommendations based on the latest progress update
        $aiRecommendations = [];
        $recommendationService = new CropRecommendationService();
        
        if ($progressUpdates->count() > 0) {
            $latestUpdate = $progressUpdates->first();
            if ($latestUpdate->question_answers) {
                $aiRecommendations = $recommendationService->generateRecommendations(
                    $latestUpdate->question_answers, 
                    $farm->watermelon_variety ?? 'watermelon'
                );
            }
        }

        // Generate fallback recommendations if no data available
        if (empty($aiRecommendations)) {
            $aiRecommendations = $this->generateFallbackRecommendations($progressUpdates);
        }

        $data = [
            'farm' => $farm,
            'progressUpdates' => $progressUpdates,
            'exportDate' => Carbon::now()->format('M d, Y H:i:s'),
            'aiRecommendations' => $aiRecommendations,
            'aiConfidence' => $this->calculateAIConfidence($progressUpdates)
        ];

        // Add cache control headers to prevent caching issues
        return response()->view('user.crop-progress.pdf-report', $data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Generate fallback recommendations when no progress data is available
     */
    private function generateFallbackRecommendations($progressUpdates)
    {
        return [
            'priority_alerts' => [
                '⚠️ Start regular progress monitoring to track crop development',
                '⚠️ Establish baseline measurements for future comparisons',
                '⚠️ Set up systematic observation schedule'
            ],
            'immediate_actions' => [
                '📊 Begin documenting crop progress with photos and notes',
                '🌱 Check current growth stage and plant health',
                '💧 Verify irrigation system is functioning properly',
                '🔍 Inspect for any visible pest or disease issues'
            ],
            'weekly_plan' => [
                '📅 Schedule weekly progress updates',
                '📸 Take progress photos for comparison',
                '📝 Record observations in farming journal',
                '🌡️ Monitor weather conditions and their impact'
            ],
            'long_term_tips' => [
                '✅ Consistent monitoring leads to better crop management',
                '📈 Track trends over time for improved decision making',
                '🔄 Regular updates help identify issues early',
                '📊 Data collection supports future planning'
            ]
        ];
    }

    /**
     * Calculate AI confidence based on available data
     */
    private function calculateAIConfidence($progressUpdates)
    {
        $confidence = 50; // Base confidence
        
        if ($progressUpdates->count() > 0) {
            $confidence += 20; // Data available
        }
        
        if ($progressUpdates->count() >= 3) {
            $confidence += 15; // Multiple data points
        }
        
        $latestUpdate = $progressUpdates->first();
        if ($latestUpdate && $latestUpdate->question_answers) {
            $confidence += 15; // Detailed answers available
        }
        
        return min($confidence, 95); // Cap at 95%
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

    /**
     * Get progress summary for a specific progress update
     */
    public function getSummary(int $updateId): JsonResponse
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

        // Get the question answers and map them to readable format
        $questionAnswers = $progressUpdate->question_answers ?? [];
        $questions = $this->mapQuestionAnswersToSummary($questionAnswers);

        $summary = [
            'update_date' => $progressUpdate->update_date->format('M d, Y'),
            'progress' => $progressUpdate->calculated_progress,
            'method' => ucfirst($progressUpdate->update_method),
            'questions' => $questions,
            'week_number' => $progressUpdate->getWeekNumber(),
            'week_name' => $progressUpdate->getWeekName(),
            'total_questions' => count($questions),
            'session_id' => $progressUpdate->session_id
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    /**
     * Map question answers to a readable summary format
     */
    private function mapQuestionAnswersToSummary(array $questionAnswers): array
    {
        $questionMappings = [
            'plant_health' => 'Plant Health',
            'leaf_condition' => 'Leaf Condition', 
            'growth_rate' => 'Growth Rate',
            'water_availability' => 'Water Availability',
            'pest_pressure' => 'Pest Pressure',
            'disease_issues' => 'Disease Issues',
            'nutrient_deficiency' => 'Nutrient Deficiency',
            'weather_impact' => 'Weather Impact',
            'stage_progression' => 'Stage Progression',
            'overall_satisfaction' => 'Overall Satisfaction'
        ];

        $questions = [];
        
        foreach ($questionAnswers as $questionId => $answer) {
            $questionText = $questionMappings[$questionId] ?? ucfirst(str_replace('_', ' ', $questionId));
            
            // Format the answer for display
            $formattedAnswer = $this->formatAnswerForDisplay($answer);
            
            // Generate explanation based on answer
            $explanation = $this->generateAnswerExplanation($questionId, $answer);
            
            $questions[] = [
                'question' => $questionText,
                'answer' => $formattedAnswer,
                'explanation' => $explanation
            ];
        }

        return $questions;
    }

    /**
     * Format answer for display
     */
    private function formatAnswerForDisplay(string $answer): string
    {
        // Convert snake_case to Title Case and handle special cases
        $formatted = str_replace('_', ' ', $answer);
        $formatted = ucwords($formatted);
        
        // Handle specific cases
        $replacements = [
            'On Track' => 'On Track',
            'Slower' => 'Slower',
            'Faster' => 'Faster',
            'Good' => 'Good',
            'Excellent' => 'Excellent',
            'Poor' => 'Poor',
            'Low' => 'Low',
            'High' => 'High',
            'Moderate' => 'Moderate',
            'Minor' => 'Minor',
            'Major' => 'Major',
            'Positive' => 'Positive',
            'Negative' => 'Negative',
            'Satisfied' => 'Satisfied',
            'Unsatisfied' => 'Unsatisfied'
        ];

        return $replacements[$formatted] ?? $formatted;
    }

    /**
     * Generate explanation for answer
     */
    private function generateAnswerExplanation(string $questionId, string $answer): string
    {
        $explanations = [
            'plant_health' => [
                'excellent' => 'Plants are showing exceptional health and vigor',
                'good' => 'Plants are showing healthy growth patterns',
                'fair' => 'Plants show some signs of stress but are generally healthy',
                'poor' => 'Plants are showing significant health issues'
            ],
            'leaf_condition' => [
                'excellent' => 'Leaves are vibrant, well-formed, and free from damage',
                'good' => 'Leaves are green and well-formed',
                'fair' => 'Some leaves show minor discoloration or damage',
                'poor' => 'Leaves show significant damage or discoloration'
            ],
            'growth_rate' => [
                'faster' => 'Growth is exceeding expectations for this stage',
                'on_track' => 'Growth is progressing as expected',
                'slower' => 'Growth has slowed due to environmental factors'
            ],
            'water_availability' => [
                'excellent' => 'Optimal water supply maintained consistently',
                'good' => 'Adequate water supply maintained',
                'fair' => 'Water supply is sufficient but could be improved',
                'poor' => 'Insufficient water supply affecting plant health'
            ],
            'pest_pressure' => [
                'low' => 'Minimal pest activity observed',
                'moderate' => 'Some pest activity but under control',
                'high' => 'Significant pest pressure requiring attention'
            ],
            'disease_issues' => [
                'none' => 'No disease issues detected',
                'minor' => 'Some minor disease symptoms observed',
                'moderate' => 'Noticeable disease issues present',
                'major' => 'Significant disease problems affecting crops'
            ],
            'nutrient_deficiency' => [
                'none' => 'No signs of nutrient deficiency',
                'minor' => 'Slight signs of nutrient deficiency',
                'moderate' => 'Moderate nutrient deficiency affecting growth',
                'severe' => 'Severe nutrient deficiency requiring immediate attention'
            ],
            'weather_impact' => [
                'positive' => 'Favorable weather conditions supporting growth',
                'neutral' => 'Weather conditions are normal for this season',
                'negative' => 'Adverse weather conditions affecting crop development'
            ],
            'stage_progression' => [
                'ahead' => 'Development is ahead of schedule',
                'on_track' => 'Development progressing as expected',
                'behind' => 'Development is behind schedule'
            ],
            'overall_satisfaction' => [
                'very_satisfied' => 'Crop performance exceeds expectations',
                'satisfied' => 'Crop performance meets expectations',
                'neutral' => 'Crop performance is acceptable',
                'dissatisfied' => 'Crop performance is below expectations'
            ]
        ];

        $questionExplanations = $explanations[$questionId] ?? [];
        return $questionExplanations[$answer] ?? 'Assessment completed for this category';
    }

    /**
     * Extract concerns from user answers for research paper search
     */
    private function extractConcernsFromAnswers(array $answers): array
    {
        $concerns = [];
        
        foreach ($answers as $key => $value) {
            // Check for leaf issues
            if (str_contains($key, 'leaf_color')) {
                if (is_numeric($value) && $value <= 3) {
                    $concerns[] = 'nutrient deficiency';
                    $concerns[] = 'leaf health';
                }
            }
            
            // Check for water/texture issues
            if (str_contains($key, 'water') || str_contains($key, 'texture')) {
                if (is_numeric($value) && $value <= 2) {
                    $concerns[] = 'water management';
                    $concerns[] = 'irrigation';
                }
            }
            
            // Check for disease
            if (str_contains($key, 'disease') || str_contains($key, 'spots')) {
                if ($value === 'yes') {
                    $concerns[] = 'disease management';
                    $concerns[] = 'fungal control';
                }
            }
            
            // Check for pollination
            if (str_contains($key, 'pollinator') || str_contains($key, 'flower')) {
                if ($value === 'no' || (is_numeric($value) && $value < 5)) {
                    $concerns[] = 'pollination';
                    $concerns[] = 'fruit set';
                }
            }
            
            // Check for pest issues
            if (str_contains($key, 'pest') || str_contains($key, 'damage')) {
                if (is_numeric($value) && $value > 10) {
                    $concerns[] = 'pest management';
                }
            }
        }
        
        // If no specific concerns, use general keywords
        if (empty($concerns)) {
            $concerns = ['crop management', 'best practices'];
        }
        
        return array_unique($concerns);
    }
}
