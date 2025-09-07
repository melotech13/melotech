<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CropGrowth
 * 
 * @property int $id
 * @property int $farm_id
 * @property string $current_stage
 * @property int $stage_progress
 * @property int $overall_progress
 * @property \Carbon\Carbon $last_updated
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Farm $farm
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CropGrowth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CropGrowth whereFarmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CropGrowth whereCurrentStage($value)
 */
class CropGrowth extends Model
{
    use HasFactory;

    protected $table = 'crop_growth';

    protected $fillable = [
        'farm_id',
        'current_stage',
        'stage_progress',
        'overall_progress',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'date',
        'stage_progress' => 'integer',
        'overall_progress' => 'integer',
    ];

    protected $appends = [
        'days_elapsed',
        'days_remaining',
        'harvest_date',
    ];

    // Growth stage constants
    const STAGES = [
        'seedling' => [
            'name' => 'Seedling',
            'duration_days' => 20,
            'description' => 'Young plants emerging from soil',
            'icon' => '🌱',
            'color' => '#10b981'
        ],
        'vegetative' => [
            'name' => 'Vegetative Growth',
            'duration_days' => 25,
            'description' => 'Rapid leaf and stem development',
            'icon' => '🌿',
            'color' => '#059669'
        ],
        'flowering' => [
            'name' => 'Flowering',
            'duration_days' => 15,
            'description' => 'Flowers appear and pollination begins',
            'icon' => '🌸',
            'color' => '#8b5cf6'
        ],
        'fruiting' => [
            'name' => 'Fruit Development',
            'duration_days' => 20,
            'description' => 'Fruits grow and mature',
            'icon' => '🍉',
            'color' => '#f59e0b'
        ],
        'harvest' => [
            'name' => 'Ready for Harvest',
            'duration_days' => 0,
            'description' => 'Fruits are ready to be harvested',
            'icon' => '✂️',
            'color' => '#dc2626'
        ]
    ];

    /**
     * Get the farm that owns the crop growth record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function getStageInfo($stage = null)
    {
        $stage = $stage ?? $this->current_stage;
        return self::STAGES[$stage] ?? null;
    }

    public function getNextStage($stage = null)
    {
        $stages = array_keys(self::STAGES);
        $targetStage = $stage ?? $this->current_stage;
        $currentIndex = array_search($targetStage, $stages);
        
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            return $stages[$currentIndex + 1];
        }
        
        return null;
    }

    public function getPreviousStage()
    {
        $stages = array_keys(self::STAGES);
        $currentIndex = array_search($this->current_stage, $stages);
        
        if ($currentIndex > 0) {
            return $stages[$currentIndex - 1];
        }
        
        return null;
    }

    public function canAdvanceStage()
    {
        return $this->stage_progress >= 100;
    }

    public function advanceStage()
    {
        if ($this->canAdvanceStage()) {
            $nextStage = $this->getNextStage();
            if ($nextStage) {
                $this->current_stage = $nextStage;
                $this->stage_progress = 0;
                $this->last_updated = now();
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function updateProgress($stageProgress)
    {
        $this->stage_progress = max(0, min(100, $stageProgress));
        
        // Calculate overall progress based on current stage and progress
        $stages = array_keys(self::STAGES);
        $currentIndex = array_search($this->current_stage, $stages);
        $totalStages = count($stages);
        
        if ($currentIndex !== false) {
            $stageWeight = 100 / $totalStages;
            $this->overall_progress = ($currentIndex * $stageWeight) + ($this->stage_progress * $stageWeight / 100);
        }
        
        $this->last_updated = now();
        $this->save();
        
        return $this;
    }
    
    /**
     * Automatically update progress based on time elapsed since planting
     */
    public function updateProgressFromTime()
    {
        $farm = $this->farm;
        if (!$farm) {
            return $this;
        }
        
        $plantingDate = $farm->planting_date;
        $currentDate = now();
        
        // Skip if planting date is in the future
        if ($plantingDate->isAfter($currentDate)) {
            return $this;
        }
        
        $daysElapsed = $plantingDate->diffInDays($currentDate);
        
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
        
        // If we've passed all stages, we're ready for harvest
        $totalStageDays = array_sum($stageDurations);
        if ($daysElapsed >= $totalStageDays) {
            $currentStage = 'harvest';
            $stageProgress = 100;
        }
        
        // Only update if the calculated progress is different from current
        if ($this->current_stage !== $currentStage || abs($this->stage_progress - $stageProgress) > 1) {
            $this->current_stage = $currentStage;
            $this->stage_progress = $stageProgress;
            $this->last_updated = now();
            
            // Recalculate overall progress
            $stages = array_keys(self::STAGES);
            $currentIndex = array_search($this->current_stage, $stages);
            $totalStages = count($stages);
            
            if ($currentIndex !== false) {
                $stageWeight = 100 / $totalStages;
                $this->overall_progress = ($currentIndex * $stageWeight) + ($this->stage_progress * $stageWeight / 100);
            }
            
            $this->save();
        }
        
        return $this;
    }

    /**
     * Get days elapsed since planting
     */
    public function getDaysElapsedAttribute()
    {
        // Ensure farm relationship is loaded
        if (!$this->relationLoaded('farm')) {
            $this->load('farm');
        }
        
        if (!$this->farm || !$this->farm->planting_date) {
            return 0;
        }
        
        $plantingDate = $this->farm->planting_date;
        $currentDate = now();
        
        // Skip if planting date is in the future
        if ($plantingDate->isAfter($currentDate)) {
            return 0;
        }
        
        return round($plantingDate->diffInDays($currentDate));
    }

    /**
     * Get days remaining until harvest
     */
    public function getDaysRemainingAttribute()
    {
        // Ensure farm relationship is loaded
        if (!$this->relationLoaded('farm')) {
            $this->load('farm');
        }
        
        if (!$this->farm || !$this->farm->planting_date) {
            return 80; // Full growth period if not planted yet
        }
        
        $plantingDate = $this->farm->planting_date;
        $currentDate = now();
        
        // Skip if planting date is in the future
        if ($plantingDate->isAfter($currentDate)) {
            return 80; // Full growth period if not planted yet
        }
        
        // Calculate total growth period (80 days for watermelon)
        $totalGrowthDays = 80;
        $daysElapsed = $plantingDate->diffInDays($currentDate);
        
        $daysRemaining = $totalGrowthDays - $daysElapsed;
        return max(0, round($daysRemaining));
    }

    /**
     * Get estimated harvest date
     */
    public function getHarvestDateAttribute()
    {
        // Ensure farm relationship is loaded
        if (!$this->relationLoaded('farm')) {
            $this->load('farm');
        }
        
        if (!$this->farm || !$this->farm->planting_date) {
            return null;
        }
        
        // Watermelon typically takes 80 days from planting to harvest
        return $this->farm->planting_date->addDays(80);
    }

    /**
     * Ensure notes is always an array
     */
    public function setNotesAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['notes'] = json_encode([$value]);
        } elseif (is_array($value)) {
            $this->attributes['notes'] = json_encode($value);
        } else {
            $this->attributes['notes'] = json_encode([]);
        }
    }

    /**
     * Get notes as array
     */
    public function getNotesAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }
}
