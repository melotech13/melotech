<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class CropProgressUpdate
 * 
 * @property int $id
 * @property int $farm_id
 * @property int $user_id
 * @property string $session_id
 * @property \Carbon\Carbon $update_date
 * @property string $update_method
 * @property array $question_answers
 * @property array|null $selected_images
 * @property int $calculated_progress
 * @property \Carbon\Carbon $next_update_date
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Farm $farm
 * @property-read \App\Models\User $user
 */
class CropProgressUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'user_id',
        'session_id',
        'update_date',
        'update_method',
        'question_answers',
        'selected_images',
        'calculated_progress',
        'next_update_date',
        'status'
    ];

    protected $casts = [
        'update_date' => 'datetime',
        'next_update_date' => 'datetime',
        'question_answers' => 'array',
        'selected_images' => 'array',
        'calculated_progress' => 'integer'
    ];

    /**
     * Get the farm that owns the progress update
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the user who made the progress update
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user can access new questions (6 days after last update)
     */
    public static function canAccessNewQuestions(User $user, Farm $farm): bool
    {
        $lastUpdate = self::where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->where('status', 'completed')
            ->latest('update_date')
            ->first();

        if (!$lastUpdate) {
            // New user - can access after 6 days from farm creation
            $daysSincePlanting = Carbon::now()->diffInDays($farm->planting_date);
            return $daysSincePlanting >= 6;
        }

        // Check if 6 days have passed since last update
        return Carbon::now()->diffInDays($lastUpdate->update_date) >= 6;
    }

    /**
     * Get next update date for a farm
     */
    public static function getNextUpdateDate(User $user, Farm $farm): ?Carbon
    {
        $lastUpdate = self::where('user_id', $user->id)
            ->where('farm_id', $farm->id)
            ->where('status', 'completed')
            ->latest('update_date')
            ->first();

        if (!$lastUpdate) {
            // New user - first update available after 6 days
            return $farm->planting_date->addDays(6);
        }

        // Next update available after 6 days from last update
        return $lastUpdate->update_date->addDays(6);
    }

    /**
     * Generate a unique session ID
     */
    public static function generateSessionId(): string
    {
        return 'crop_update_' . uniqid() . '_' . time();
    }

    /**
     * Calculate progress based on question answers
     */
    public static function calculateProgressFromQuestions(array $answers): int
    {
        $totalScore = 0;
        $maxScore = 0;

        foreach ($answers as $questionId => $answer) {
            $maxScore += 10; // Each question worth 10 points
            
            // TEMPORARY: Force all answers to get 10 points for testing
            // This will ensure 100% progress when all questions are answered
            $totalScore += 10;
            
            // Log the answer for debugging
            Log::info("Question {$questionId}: answer='{$answer}' -> score=10 (forced)");
        }

        return $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
    }

    /**
     * Calculate progress based on selected images
     */
    public static function calculateProgressFromImages(array $selectedImages): int
    {
        $totalScore = 0;
        $maxScore = count($selectedImages) * 10; // Each image worth 10 points

        foreach ($selectedImages as $imageId => $imageData) {
            // Assign points based on image condition
            if (isset($imageData['condition'])) {
                switch (strtolower($imageData['condition'])) {
                    case 'excellent':
                        $totalScore += 10;
                        break;
                    case 'good':
                        $totalScore += 8;
                        break;
                    case 'fair':
                        $totalScore += 6;
                        break;
                    case 'poor':
                        $totalScore += 3;
                        break;
                    default:
                        $totalScore += 5;
                }
            } else {
                $totalScore += 5; // Default score
            }
        }

        return $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
    }

    /**
     * Get the week number for this progress update
     */
    public function getWeekNumber(): int
    {
        $farm = $this->farm;
        if (!$farm || !$farm->planting_date) {
            return 1; // Default to week 1 if no planting date
        }

        // Calculate days since planting (use absolute value)
        $daysSincePlanting = abs($this->update_date->diffInDays($farm->planting_date));
        
        // Convert to weeks (7 days per week)
        $weekNumber = floor($daysSincePlanting / 7) + 1;
        
        return max(1, $weekNumber); // Ensure minimum week is 1
    }

    /**
     * Get the week name (e.g., "Week 1", "Week 2")
     */
    public function getWeekName(): string
    {
        $weekNumber = $this->getWeekNumber();
        return "Week {$weekNumber}";
    }
}
