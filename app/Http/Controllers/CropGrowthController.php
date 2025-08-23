<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Farm;
use App\Models\CropGrowth;
use App\Models\User;
use Carbon\Carbon;

class CropGrowthController extends Controller
{
    public function index(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                Log::error('User not authenticated in crop-growth index');
                return redirect()->route('login');
            }
            
            $farms = $user->farms()->with('cropGrowth')->get();
            $isAuthenticated = Auth::check();
            
            Log::info('Crop growth index loaded', ['user_id' => $user->id, 'farms_count' => $farms->count()]);
            
            // Get selected farm for detailed view
            $selectedFarm = null;
            $selectedCropGrowth = null;
            $stages = CropGrowth::STAGES;
            
            if ($request->has('selected_farm') && $farms->where('id', $request->selected_farm)->count() > 0) {
                $selectedFarm = $farms->where('id', $request->selected_farm)->first();
                $selectedCropGrowth = $selectedFarm->getOrCreateCropGrowth();
            } elseif ($farms->count() > 0) {
                // Default to first farm if no selection
                $selectedFarm = $farms->first();
                $selectedCropGrowth = $selectedFarm->getOrCreateCropGrowth();
            }
            
            // Get questions for all farms with their stages
            $farmQuestions = [];
            foreach ($farms as $farm) {
                try {
                    $cropGrowth = $farm->getOrCreateCropGrowth();
                    $farmQuestions[$farm->id] = [
                        'questions' => $this->getStageQuestions($cropGrowth->current_stage),
                        'stage' => $cropGrowth->current_stage,
                        'crop_growth' => $cropGrowth
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing farm in crop-growth index', ['farm_id' => $farm->id, 'error' => $e->getMessage()]);
                    // Continue with empty questions for this farm
                    $farmQuestions[$farm->id] = [
                        'questions' => [],
                        'stage' => 'seedling',
                        'crop_growth' => null
                    ];
                }
            }
            
            Log::info('Farm questions prepared', ['farm_questions_count' => count($farmQuestions)]);
            
            return view('crop-growth.index', compact('farms', 'isAuthenticated', 'farmQuestions', 'selectedFarm', 'selectedCropGrowth', 'stages'));
        } catch (\Exception $e) {
            Log::error('Error in crop-growth index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load crop growth data. Please try again.');
        }
    }

    public function show(Farm $farm)
    {
        // Redirect to index with farm parameter for single page view
        return redirect()->route('crop-growth.index', ['selected_farm' => $farm->id]);
    }

    /**
     * Store a newly created farm
     */
    public function store(Request $request)
    {
        $request->validate([
            'farm_name' => 'required|string|max:255',
            'watermelon_variety' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'field_size' => 'required|numeric|min:0.1',
            'field_size_unit' => 'required|in:acres,hectares',
            'province_name' => 'required|string|max:255',
            'city_municipality_name' => 'required|string|max:255',
            'barangay_name' => 'nullable|string|max:255',
        ]);

        try {
            $user = Auth::user();
            
            $farm = new Farm();
            $farm->user_id = $user->id;
            $farm->farm_name = $request->farm_name;
            $farm->watermelon_variety = $request->watermelon_variety;
            $farm->planting_date = $request->planting_date;
            $farm->field_size = $request->field_size;
            $farm->field_size_unit = $request->field_size_unit;
            $farm->city_municipality_name = $request->city_municipality_name ?? 'Unknown';
            $farm->province_name = $request->province_name ?? 'Unknown';
            $farm->barangay_name = $request->barangay_name;
            $farm->save();

            // Create initial crop growth record
            $cropGrowth = new CropGrowth();
            $cropGrowth->farm_id = $farm->id;
            $cropGrowth->current_stage = 'seedling';
            $cropGrowth->stage_progress = 0;
            $cropGrowth->overall_progress = 0;
            $cropGrowth->save();

            return redirect()->route('crop-growth.index', ['selected_farm' => $farm->id])
                ->with('success', 'Farm created successfully! Your crop growth tracking has begun.');
                
        } catch (\Exception $e) {
            Log::error('Error creating farm', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return back()->with('error', 'Failed to create farm. Please try again.');
        }
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

    public function getDashboardData()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                Log::error('User not authenticated in getDashboardData');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error' => 'User not authenticated'
                ], 401);
            }
            
            Log::info('getDashboardData called for user: ' . $user->id);
            
            $farms = $user->farms()->with('cropGrowth')->get();
            
            Log::info('Found farms for user ' . $user->id . ': ' . $farms->count());
            
            if ($farms->isEmpty()) {
                Log::info('User has no farms, returning empty data');
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No farms found for user',
                    'timestamp' => now()->toISOString(),
                ]);
            }
            
            $dashboardData = [];
            
            foreach ($farms as $farm) {
                try {
                    $cropGrowth = $farm->getOrCreateCropGrowth();
                    
                    // Automatically update progress based on time elapsed
                    $cropGrowth->updateProgressFromTime();
                    
                    $currentStageInfo = $cropGrowth->getStageInfo();
                    $nextStage = $cropGrowth->getNextStage();
                    $nextStageInfo = $nextStage ? $cropGrowth->getStageInfo($nextStage) : null;
                    
                    // Calculate harvest date based on current progress and remaining stages
                    $plantingDate = Carbon::parse($farm->planting_date);
                    $currentDate = Carbon::now();
                    $harvestDate = $this->calculateHarvestDate($farm, $cropGrowth);
                    
                    // Calculate days remaining and elapsed
                    $daysElapsed = $plantingDate->isAfter($currentDate) ? 0 : $plantingDate->diffInDays($currentDate);
                    $daysRemaining = $this->calculateDaysRemaining($farm, $cropGrowth);
                    
                    // Get nutrient predictions and harvest countdown with error handling
                    $nutrientPredictions = null;
                    $harvestCountdown = null;
                    
                    try {
                        $nutrientPredictions = $this->getNutrientPredictions($cropGrowth, $farm);
                    } catch (\Exception $e) {
                        Log::error('Error getting nutrient predictions for farm ' . $farm->id . ': ' . $e->getMessage());
                        $nutrientPredictions = [
                            'nitrogen' => 'Data unavailable',
                            'phosphorus' => 'Data unavailable',
                            'potassium' => 'Data unavailable',
                            'recommendations' => ['Data temporarily unavailable']
                        ];
                    }
                    
                    try {
                        $harvestCountdown = $this->getHarvestCountdown($harvestDate);
                    } catch (\Exception $e) {
                        Log::error('Error getting harvest countdown for farm ' . $farm->id . ': ' . $e->getMessage());
                        $harvestCountdown = [
                            'status' => 'error',
                            'message' => 'Data unavailable',
                            'days' => 0,
                            'color' => 'secondary',
                            'icon' => 'fas fa-question-circle'
                        ];
                    }
                    
                    $dashboardData[] = [
                        'farm_id' => $farm->id,
                        'farm_name' => $farm->farm_name,
                        'planting_date' => $plantingDate->format('Y-m-d'),
                        'planting_date_formatted' => $plantingDate->format('M d, Y'),
                        'current_stage' => $cropGrowth->current_stage,
                        'stage_name' => $currentStageInfo['name'],
                        'stage_icon' => $currentStageInfo['icon'],
                        'stage_color' => $currentStageInfo['color'],
                        'stage_progress' => $cropGrowth->stage_progress,
                        'overall_progress' => round($cropGrowth->overall_progress, 1),
                        'next_stage' => $nextStage,
                        'next_stage_name' => $nextStageInfo ? $nextStageInfo['name'] : null,
                        'harvest_date' => $harvestDate->format('Y-m-d'),
                        'harvest_date_formatted' => $harvestDate->format('M d, Y'),
                        'days_elapsed' => $daysElapsed,
                        'days_remaining' => $daysRemaining,
                        'total_growth_period' => 80, // Standard watermelon growth period
                        'last_updated' => Carbon::parse($cropGrowth->last_updated)->format('Y-m-d H:i:s'),
                        'notes' => $cropGrowth->notes,
                        'can_advance' => $cropGrowth->canAdvanceStage(),
                        'growth_status' => $this->getGrowthStatus($cropGrowth, $farm),
                        'nutrient_predictions' => $nutrientPredictions,
                        'harvest_countdown' => $harvestCountdown,
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing farm ' . $farm->id . ' in getDashboardData: ' . $e->getMessage());
                    // Skip this farm if there's an error
                    continue;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $dashboardData,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'debug_trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Force update crop progress based on time elapsed since planting
     */
    public function forceUpdateProgress(Request $request, Farm $farm)
    {
        $cropGrowth = $farm->getOrCreateCropGrowth();
        
        // Force update progress based on time
        $cropGrowth->updateProgressFromTime();
        
        return response()->json([
            'success' => true,
            'message' => 'Crop progress updated based on time elapsed',
            'crop_growth' => $cropGrowth->fresh(),
        ]);
    }

    /**
     * Calculate estimated harvest date based on current crop growth progress
     */
    private function calculateHarvestDate($farm, $cropGrowth)
    {
        $plantingDate = Carbon::parse($farm->planting_date);
        $currentDate = Carbon::now();
        
        // If planting date is in the future, return estimated date
        if ($plantingDate->isAfter($currentDate)) {
            return $plantingDate->copy()->addDays(80);
        }
        
        // Calculate based on current progress and remaining stages
        $stages = array_keys(CropGrowth::STAGES);
        $currentIndex = array_search($cropGrowth->current_stage, $stages);
        $totalStages = count($stages);
        
        if ($currentIndex === false) {
            return $plantingDate->copy()->addDays(80);
        }
        
        // Calculate remaining days based on current stage progress and remaining stages
        $remainingStages = array_slice($stages, $currentIndex);
        $remainingDays = 0;
        
        foreach ($remainingStages as $stage) {
            if ($stage === $cropGrowth->current_stage) {
                // For current stage, calculate remaining days based on progress
                $stageProgress = $cropGrowth->stage_progress;
                $stageDuration = CropGrowth::STAGES[$stage]['duration_days'];
                $remainingDays += ($stageDuration * (100 - $stageProgress)) / 100;
            } else {
                // For future stages, add full duration
                $remainingDays += CropGrowth::STAGES[$stage]['duration_days'];
            }
        }
        
        return $currentDate->copy()->addDays(ceil($remainingDays));
    }

    /**
     * Calculate days remaining until harvest
     */
    private function calculateDaysRemaining($farm, $cropGrowth)
    {
        $harvestDate = $this->calculateHarvestDate($farm, $cropGrowth);
        $currentDate = Carbon::now();
        
        if ($harvestDate->isBefore($currentDate)) {
            return 0; // Already past harvest date
        }
        
        return $currentDate->diffInDays($harvestDate);
    }

    /**
     * Get growth status for dashboard display
     */
    private function getGrowthStatus($cropGrowth, $farm)
    {
        $plantingDate = Carbon::parse($farm->planting_date);
        $currentDate = Carbon::now();
        
        if ($plantingDate->isAfter($currentDate)) {
            return [
                'status' => 'not_planted',
                'message' => 'Farm not yet planted',
                'color' => 'secondary',
                'icon' => 'fas fa-calendar-plus'
            ];
        }
        
        if ($cropGrowth->current_stage === 'harvest') {
            return [
                'status' => 'ready_harvest',
                'message' => 'Ready for harvest!',
                'color' => 'success',
                'icon' => 'fas fa-cut'
            ];
        }
        
        if ($cropGrowth->stage_progress >= 90) {
            return [
                'status' => 'nearing_completion',
                'message' => 'Stage nearly complete',
                'color' => 'warning',
                'icon' => 'fas fa-clock'
            ];
        }
        
        if ($cropGrowth->stage_progress >= 50) {
            return [
                'status' => 'good_progress',
                'message' => 'Good progress',
                'color' => 'info',
                'icon' => 'fas fa-chart-line'
            ];
        }
        
        return [
            'status' => 'early_stage',
            'message' => 'Early stage development',
            'color' => 'primary',
            'icon' => 'fas fa-seedling'
        ];
    }

    /**
     * Get nutrient predictions based on current growth stage
     */
    private function getNutrientPredictions($cropGrowth, $farm)
    {
        $stage = $cropGrowth->current_stage;
        $plantingDate = Carbon::parse($farm->planting_date);
        $currentDate = Carbon::now();
        
        if ($plantingDate->isAfter($currentDate)) {
            return [
                'nitrogen' => 'Not applicable yet',
                'phosphorus' => 'Not applicable yet',
                'potassium' => 'Not applicable yet',
                'recommendations' => ['Wait until planting date']
            ];
        }
        
        $predictions = [
            'seedling' => [
                'nitrogen' => 'High (100-120 kg/ha)',
                'phosphorus' => 'Medium (60-80 kg/ha)',
                'potassium' => 'Low (40-60 kg/ha)',
                'recommendations' => [
                    'Focus on root development',
                    'Maintain soil moisture',
                    'Protect from pests'
                ]
            ],
            'vegetative' => [
                'nitrogen' => 'Very High (120-150 kg/ha)',
                'phosphorus' => 'High (80-100 kg/ha)',
                'potassium' => 'Medium (60-80 kg/ha)',
                'recommendations' => [
                    'Maximize leaf growth',
                    'Ensure adequate spacing',
                    'Monitor for diseases'
                ]
            ],
            'flowering' => [
                'nitrogen' => 'Medium (80-100 kg/ha)',
                'phosphorus' => 'Very High (100-120 kg/ha)',
                'potassium' => 'High (80-100 kg/ha)',
                'recommendations' => [
                    'Reduce nitrogen application',
                    'Increase phosphorus for flowers',
                    'Maintain potassium levels'
                ]
            ],
            'fruiting' => [
                'nitrogen' => 'Low (40-60 kg/ha)',
                'phosphorus' => 'Medium (60-80 kg/ha)',
                'potassium' => 'Very High (100-120 kg/ha)',
                'recommendations' => [
                    'Minimize nitrogen',
                    'Focus on fruit quality',
                    'Ensure adequate potassium'
                ]
            ],
            'harvest' => [
                'nitrogen' => 'Minimal (20-30 kg/ha)',
                'phosphorus' => 'Low (40-60 kg/ha)',
                'potassium' => 'Medium (60-80 kg/ha)',
                'recommendations' => [
                    'Stop fertilization',
                    'Prepare for harvest',
                    'Maintain soil health'
                ]
            ]
        ];
        
        return $predictions[$stage] ?? $predictions['seedling'];
    }

    /**
     * Get harvest countdown information
     */
    private function getHarvestCountdown($harvestDate)
    {
        $currentDate = Carbon::now();
        
        if ($harvestDate->isBefore($currentDate)) {
            return [
                'status' => 'overdue',
                'message' => 'Harvest is overdue',
                'days' => 0,
                'color' => 'danger',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        }
        
        $daysRemaining = $currentDate->diffInDays($harvestDate);
        
        if ($daysRemaining === 0) {
            return [
                'status' => 'today',
                'message' => 'Harvest today!',
                'days' => 0,
                'color' => 'success',
                'icon' => 'fas fa-cut'
            ];
        }
        
        if ($daysRemaining <= 7) {
            return [
                'status' => 'imminent',
                'message' => "Harvest in {$daysRemaining} days",
                'days' => $daysRemaining,
                'color' => 'warning',
                'icon' => 'fas fa-clock'
            ];
        }
        
        if ($daysRemaining <= 14) {
            return [
                'status' => 'soon',
                'message' => "Harvest in {$daysRemaining} days",
                'days' => $daysRemaining,
                'color' => 'info',
                'icon' => 'fas fa-calendar-alt'
            ];
        }
        
        return [
            'status' => 'planned',
            'message' => "Harvest in {$daysRemaining} days",
            'days' => $daysRemaining,
            'color' => 'primary',
            'icon' => 'fas fa-calendar'
        ];
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
