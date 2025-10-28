<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\NutrientAnalysis;
use Carbon\Carbon;

class NutrientCalculatorController extends Controller
{
    /**
     * Display the nutrient calculator page with analysis history.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get recent analyses
        $analyses = NutrientAnalysis::where('user_id', $user->id)
            ->orderBy('analysis_date', 'desc')
            ->limit(10)
            ->get();
        
        // Calculate stats
        $totalAnalyses = NutrientAnalysis::where('user_id', $user->id)->count();
        $criticalIssues = NutrientAnalysis::where('user_id', $user->id)
            ->whereRaw("JSON_EXTRACT(detailed_analysis, '$.npk_balance') = 'critical'")
            ->count();
        $recentAnalyses = NutrientAnalysis::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $stats = [
            'total_analyses' => $totalAnalyses,
            'critical_issues' => $criticalIssues,
            'recent_analyses' => $recentAnalyses,
        ];
        
        return view('user.nutrient-calculator.index', compact('analyses', 'stats'));
    }

    /**
     * Analyze soil nutrients using Together AI (Mixtral).
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'nitrogen' => 'required|numeric|min:0|max:1000',
            'phosphorus' => 'required|numeric|min:0|max:1000',
            'potassium' => 'required|numeric|min:0|max:1000',
            'soil_ph' => 'required|numeric|min:0|max:14',
            'soil_moisture' => 'required|numeric|min:0|max:100',
            'growth_stage' => 'required|in:seedling,vegetative,flowering,fruiting,harvest',
        ]);

        $user = Auth::user();

        try {
            // Check if Together AI is available
            $aiAvailable = $this->checkTogetherAIAvailability();
            
            if ($aiAvailable) {
                // Prepare AI analysis prompt
                $prompt = $this->buildAIPrompt($validated);
                
                // Call Together AI (Mixtral)
                $aiResponse = $this->callTogetherAI($prompt);
                
                // Parse AI response
                $analysisResults = $this->parseAIResponse($aiResponse, $validated);
            } else {
                // Use fallback analysis engine (no AI required)
                $analysisResults = $this->generateFallbackAnalysis($validated);
            }
            
            // Save analysis to database
            $nutrientAnalysis = NutrientAnalysis::create([
                'user_id' => $user->id,
                'nitrogen' => $validated['nitrogen'],
                'phosphorus' => $validated['phosphorus'],
                'potassium' => $validated['potassium'],
                'soil_ph' => $validated['soil_ph'],
                'soil_moisture' => $validated['soil_moisture'],
                'growth_stage' => $validated['growth_stage'],
                'nutrient_status' => $analysisResults['nutrient_status'],
                'deficiency_detection' => $analysisResults['deficiency_detection'],
                'ai_recommendations' => $analysisResults['ai_recommendations'],
                'stage_advisory' => $analysisResults['stage_advisory'],
                'detailed_analysis' => $analysisResults['detailed_analysis'],
                'analysis_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'analysis' => $nutrientAnalysis,
                'results' => $analysisResults,
            ]);

        } catch (\Exception $e) {
            Log::error('Nutrient analysis error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $validated,
            ]);

            // Provide specific error message
            $errorMessage = 'Analysis failed: ' . $e->getMessage();
            
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'debug_info' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null,
            ], 500);
        }
    }

    /**
     * Build AI prompt for Together AI.
     */
    private function buildAIPrompt(array $data): string
    {
        $stageName = ucfirst(str_replace('_', ' ', $data['growth_stage']));
        
        return <<<PROMPT
You are an expert agricultural AI assistant specializing in watermelon cultivation and soil nutrient management.

Analyze the following soil nutrient data for a watermelon crop:

**Soil Nutrients:**
- Nitrogen (N): {$data['nitrogen']} ppm
- Phosphorus (P): {$data['phosphorus']} ppm
- Potassium (K): {$data['potassium']} ppm
- Soil pH: {$data['soil_ph']}
- Soil Moisture: {$data['soil_moisture']}%
- Growth Stage: {$stageName}

**Optimal Ranges for Watermelon:**
- Nitrogen: 50-150 ppm (varies by stage)
- Phosphorus: 30-80 ppm
- Potassium: 150-300 ppm
- pH: 6.0-6.8
- Moisture: 60-80%

Provide a comprehensive analysis in the following JSON format:

{
  "nutrient_status": "Brief overall NPK status summary (1-2 sentences)",
  "npk_balance": "balanced|moderate|critical",
  "deficiencies": ["List specific nutrient deficiencies"],
  "excesses": ["List specific nutrient excesses"],
  "recommendations": [
    "Specific fertilizer recommendation 1 with dosage",
    "Specific fertilizer recommendation 2 with dosage",
    "Soil amendment recommendation if needed"
  ],
  "stage_advisory": "Growth stage specific advice (2-3 sentences)",
  "ph_status": "pH assessment and correction if needed",
  "moisture_status": "Moisture assessment",
  "priority_actions": ["Most urgent action items"]
}

Respond ONLY with valid JSON. Be specific with fertilizer types and application rates (kg/ha or g/plant).
PROMPT;
    }

    /**
     * Check if Together AI is available.
     */
    private function checkTogetherAIAvailability(): bool
    {
        $apiKey = env('TOGETHER_API_KEY');
        return !empty($apiKey);
    }

    /**
     * Call Together AI API (Mixtral model).
     */
    private function callTogetherAI(string $prompt): string
    {
        $apiKey = env('TOGETHER_API_KEY');
        $model = env('TOGETHER_MODEL', 'mistralai/Mixtral-8x7B-Instruct-v0.1');

        if (empty($apiKey)) {
            throw new \Exception('Together AI API key not configured');
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.together.xyz/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert agricultural AI assistant specializing in watermelon cultivation and soil nutrient management. Respond only with valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Together AI API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid response from Together AI API');
        }

        return $data['choices'][0]['message']['content'];
    }

    /**
     * Parse AI response and format results.
     */
    private function parseAIResponse(string $aiResponse, array $inputData): array
    {
        try {
            $aiData = json_decode($aiResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse AI response as JSON');
            }

            // Fallback analysis if AI parsing fails
            if (!isset($aiData['nutrient_status'])) {
                $aiData = $this->generateFallbackAnalysis($inputData);
            }

            // Format recommendations as bullet points
            $recommendations = isset($aiData['recommendations']) && is_array($aiData['recommendations'])
                ? implode("\n", array_map(fn($r) => "• " . $r, $aiData['recommendations']))
                : "• Consult with agricultural expert for personalized recommendations";

            // Format deficiency detection
            $deficiencies = isset($aiData['deficiencies']) && is_array($aiData['deficiencies']) && count($aiData['deficiencies']) > 0
                ? implode("\n", array_map(fn($d) => "⚠️ " . $d, $aiData['deficiencies']))
                : "✅ No critical deficiencies detected";

            if (isset($aiData['excesses']) && is_array($aiData['excesses']) && count($aiData['excesses']) > 0) {
                $deficiencies .= "\n" . implode("\n", array_map(fn($e) => "⚠️ Excess: " . $e, $aiData['excesses']));
            }

            return [
                'nutrient_status' => $aiData['nutrient_status'] ?? 'Analysis completed',
                'deficiency_detection' => $deficiencies,
                'ai_recommendations' => $recommendations,
                'stage_advisory' => $aiData['stage_advisory'] ?? 'Monitor crop progress regularly',
                'detailed_analysis' => [
                    'npk_balance' => $aiData['npk_balance'] ?? 'moderate',
                    'deficiencies' => $aiData['deficiencies'] ?? [],
                    'excesses' => $aiData['excesses'] ?? [],
                    'ph_status' => $aiData['ph_status'] ?? 'Normal',
                    'moisture_status' => $aiData['moisture_status'] ?? 'Normal',
                    'priority_actions' => $aiData['priority_actions'] ?? [],
                ],
            ];

        } catch (\Exception $e) {
            Log::warning('AI response parsing failed, using fallback: ' . $e->getMessage());
            return $this->generateFallbackAnalysis($inputData);
        }
    }

    /**
     * Generate fallback analysis when AI is unavailable.
     */
    private function generateFallbackAnalysis(array $data): array
    {
        $deficiencies = [];
        $recommendations = [];
        $balance = 'balanced';

        // Analyze Nitrogen
        if ($data['nitrogen'] < 50) {
            $deficiencies[] = "Low Nitrogen - May cause yellowing of leaves";
            $recommendations[] = "Apply urea-based fertilizer at 50-70 kg/ha";
            $balance = 'critical';
        } elseif ($data['nitrogen'] > 150) {
            $deficiencies[] = "Excess Nitrogen - May delay fruit ripening";
            $recommendations[] = "Reduce nitrogen fertilizer application";
            $balance = $balance === 'balanced' ? 'moderate' : $balance;
        }

        // Analyze Phosphorus
        if ($data['phosphorus'] < 30) {
            $deficiencies[] = "Low Phosphorus - May affect root development";
            $recommendations[] = "Apply phosphate fertilizer or bone meal at 40-60 kg/ha";
            $balance = 'critical';
        }

        // Analyze Potassium
        if ($data['potassium'] < 150) {
            $deficiencies[] = "Low Potassium - May reduce fruit sweetness";
            $recommendations[] = "Apply potassium sulfate at 60-80 kg/ha";
            $balance = 'critical';
        }

        // Analyze pH
        $phStatus = "Normal pH";
        if ($data['soil_ph'] < 6.0) {
            $deficiencies[] = "Acidic soil pH - May reduce nutrient availability";
            $recommendations[] = "Apply agricultural lime to raise pH to 6.0-6.8";
            $phStatus = "Acidic - needs correction";
            $balance = $balance === 'balanced' ? 'moderate' : $balance;
        } elseif ($data['soil_ph'] > 6.8) {
            $deficiencies[] = "Alkaline soil pH - May cause micronutrient deficiency";
            $recommendations[] = "Apply elemental sulfur to lower pH";
            $phStatus = "Alkaline - needs correction";
            $balance = $balance === 'balanced' ? 'moderate' : $balance;
        }

        // Analyze Moisture
        $moistureStatus = "Optimal moisture";
        if ($data['soil_moisture'] < 60) {
            $deficiencies[] = "Low soil moisture - May stress the crop";
            $recommendations[] = "Increase irrigation frequency";
            $moistureStatus = "Low - increase irrigation";
            $balance = $balance === 'balanced' ? 'moderate' : $balance;
        } elseif ($data['soil_moisture'] > 80) {
            $deficiencies[] = "Excessive soil moisture - May cause root rot";
            $recommendations[] = "Improve drainage or reduce irrigation";
            $moistureStatus = "High - risk of waterlogging";
            $balance = $balance === 'balanced' ? 'moderate' : $balance;
        }

        // Stage-specific advice
        $stageAdvisory = match($data['growth_stage']) {
            'seedling' => "Focus on balanced NPK with emphasis on phosphorus for root development. Maintain consistent moisture.",
            'vegetative' => "Increase nitrogen for leaf growth. Ensure adequate moisture and balanced nutrients.",
            'flowering' => "Increase phosphorus to support flower development. Maintain moderate nitrogen.",
            'fruiting' => "Boost potassium for fruit sweetness and quality. Reduce nitrogen to prevent excessive vegetative growth.",
            'harvest' => "Minimize nitrogen. Maintain potassium for fruit quality. Prepare soil for next planting cycle.",
            default => "Monitor crop progress and adjust nutrients accordingly.",
        };

        if (empty($recommendations)) {
            $recommendations[] = "Maintain current fertilization program";
            $recommendations[] = "Monitor crop health regularly";
        }

        $deficiencyText = empty($deficiencies)
            ? "✅ No critical deficiencies detected"
            : implode("\n", array_map(fn($d) => "⚠️ " . $d, $deficiencies));

        $recommendationText = implode("\n", array_map(fn($r) => "• " . $r, $recommendations));

        $nutrientStatus = $balance === 'balanced'
            ? "Nutrient levels are within acceptable ranges for the {$data['growth_stage']} stage."
            : "Some nutrient imbalances detected. Follow recommendations below.";

        return [
            'nutrient_status' => $nutrientStatus,
            'deficiency_detection' => $deficiencyText,
            'ai_recommendations' => $recommendationText,
            'stage_advisory' => $stageAdvisory,
            'detailed_analysis' => [
                'npk_balance' => $balance,
                'deficiencies' => $deficiencies,
                'excesses' => [],
                'ph_status' => $phStatus,
                'moisture_status' => $moistureStatus,
                'priority_actions' => array_slice($recommendations, 0, 3),
            ],
        ];
    }

    /**
     * Get analysis details.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $analysis = NutrientAnalysis::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis,
        ]);
    }

    /**
     * Display all nutrient analysis history.
     */
    public function history()
    {
        $user = Auth::user();
        
        // Get all analyses with pagination
        $analyses = NutrientAnalysis::where('user_id', $user->id)
            ->orderBy('analysis_date', 'desc')
            ->paginate(15);
        
        // Calculate stats
        $totalAnalyses = NutrientAnalysis::where('user_id', $user->id)->count();
        $criticalIssues = NutrientAnalysis::where('user_id', $user->id)
            ->whereRaw("JSON_EXTRACT(detailed_analysis, '$.npk_balance') = 'critical'")
            ->count();
        $balancedCount = NutrientAnalysis::where('user_id', $user->id)
            ->whereRaw("JSON_EXTRACT(detailed_analysis, '$.npk_balance') = 'balanced'")
            ->count();
        $recentAnalyses = NutrientAnalysis::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $stats = [
            'total_analyses' => $totalAnalyses,
            'critical_issues' => $criticalIssues,
            'balanced_count' => $balancedCount,
            'recent_analyses' => $recentAnalyses,
        ];
        
        return view('user.nutrient-calculator.history', compact('analyses', 'stats'));
    }
    
    /**
     * Delete an analysis.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $analysis = NutrientAnalysis::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $analysis->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Analysis deleted successfully',
        ]);
    }
}
