<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Farm
 * 
 * @property int $id
 * @property int $user_id
 * @property string $farm_name
 * @property string $province_name
 * @property string $city_municipality_name
 * @property string $barangay_name
 * @property string $watermelon_variety
 * @property \Carbon\Carbon $planting_date
 * @property float $field_size
 * @property string $field_size_unit
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\User $user
 * @property-read \App\Models\CropGrowth|null $cropGrowth
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Farm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Farm whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Farm whereFarmName($value)
 */
class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'province_name',
        'city_municipality_name',
        'barangay_name',
        'watermelon_variety',
        'planting_date',
        'field_size',
        'field_size_unit',
    ];

    protected $casts = [
        'planting_date' => 'date',
        'field_size' => 'decimal:2',
    ];

    /**
     * Get the user that owns the farm.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crop growth record for the farm.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cropGrowth()
    {
        return $this->hasOne(CropGrowth::class);
    }

    /**
     * Get the progress updates for the farm.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cropProgressUpdates()
    {
        return $this->hasMany(CropProgressUpdate::class);
    }

    public function getOrCreateCropGrowth()
    {
        if (!$this->cropGrowth) {
            // Calculate initial progress based on time elapsed since planting
            $plantingDate = Carbon::parse($this->planting_date);
            $currentDate = now();
            
            $initialProgress = 0;
            $currentStage = 'seedling';
            
            if (!$plantingDate->isAfter($currentDate)) {
                // Farm has been planted, calculate progress
                $daysElapsed = $plantingDate->diffInDays($currentDate);
                
                // Seedling stage duration is 20 days
                $seedlingDuration = 20;
                
                if ($daysElapsed >= $seedlingDuration) {
                    // Past seedling stage, move to next stage
                    $currentStage = 'vegetative';
                    $initialProgress = 0; // Start new stage
                } else {
                    // Still in seedling stage, calculate progress
                    $initialProgress = min(100, ($daysElapsed / $seedlingDuration) * 100);
                }
            }
            
            $cropGrowth = $this->cropGrowth()->create([
                'current_stage' => $currentStage,
                'stage_progress' => $initialProgress,
                'overall_progress' => $this->calculateOverallProgress($currentStage, $initialProgress),
                'last_updated' => now(),
            ]);
            
            // Set the farm relationship to avoid N+1 queries
            $cropGrowth->setRelation('farm', $this);
            
            $this->refresh();
        }
        
        // If crop growth exists but progress is 0 and farm has been planted, update progress
        if ($this->cropGrowth && $this->cropGrowth->stage_progress == 0 && !Carbon::parse($this->planting_date)->isAfter(now())) {
            $this->updateCropProgressFromTime();
        }
        
        return $this->cropGrowth;
    }
    
    /**
     * Calculate overall progress based on current stage and progress
     */
    private function calculateOverallProgress($stage, $stageProgress)
    {
        $stages = ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'];
        $currentIndex = array_search($stage, $stages);
        
        if ($currentIndex === false) {
            return 0;
        }
        
        $totalStages = count($stages);
        $stageWeight = 100 / $totalStages;
        
        return ($currentIndex * $stageWeight) + ($stageProgress * $stageWeight / 100);
    }
    
    /**
     * Update crop progress based on time elapsed since planting
     */
    private function updateCropProgressFromTime()
    {
        $plantingDate = Carbon::parse($this->planting_date);
        $currentDate = now();
        
        if ($plantingDate->isAfter($currentDate)) {
            return; // Not planted yet
        }
        
        $daysElapsed = Carbon::parse($plantingDate)->diffInDays($currentDate);
        $cropGrowth = $this->cropGrowth;
        
        // Define stage durations
        $stageDurations = [
            'seedling' => 20,
            'vegetative' => 25,
            'flowering' => 15,
            'fruiting' => 20
        ];
        
        $currentStage = 'seedling';
        $stageProgress = 0;
        $daysRemaining = $daysElapsed;
        
        // Calculate which stage we should be in and progress within that stage
        foreach ($stageDurations as $stage => $duration) {
            if ($daysRemaining >= $duration) {
                $daysRemaining -= $duration;
                $currentStage = $this->getNextStage($stage);
            } else {
                $stageProgress = ($daysRemaining / $duration) * 100;
                break;
            }
        }
        
        // Only set to harvest if we've actually completed all stages (80+ days)
        $totalStageDays = array_sum($stageDurations);
        if ($daysElapsed >= $totalStageDays) {
            $currentStage = 'harvest';
            $stageProgress = 100;
        }
        
        // Update the crop growth record
        $cropGrowth->update([
            'current_stage' => $currentStage,
            'stage_progress' => $stageProgress,
            'overall_progress' => $this->calculateOverallProgress($currentStage, $stageProgress),
            'last_updated' => now(),
        ]);
    }
    
    /**
     * Get the next stage
     */
    private function getNextStage($currentStage)
    {
        $stages = ['seedling', 'vegetative', 'flowering', 'fruiting', 'harvest'];
        $currentIndex = array_search($currentStage, $stages);
        
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            return $stages[$currentIndex + 1];
        }
        
        return $currentStage;
    }
}
