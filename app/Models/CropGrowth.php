<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropGrowth extends Model
{
    use HasFactory;

    protected $table = 'crop_growth';

    protected $fillable = [
        'farm_id',
        'current_stage',
        'stage_progress',
        'overall_progress',
        'stage_data',
        'last_updated',
        'notes',
    ];

    protected $casts = [
        'stage_data' => 'array',
        'last_updated' => 'date',
        'stage_progress' => 'integer',
        'overall_progress' => 'integer',
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

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function getStageInfo($stage = null)
    {
        $stage = $stage ?? $this->current_stage;
        return self::STAGES[$stage] ?? null;
    }

    public function getNextStage()
    {
        $stages = array_keys(self::STAGES);
        $currentIndex = array_search($this->current_stage, $stages);
        
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

    public function updateProgress($stageProgress, $notes = null)
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
        if ($notes) {
            $this->notes = $notes;
        }
        
        $this->save();
        
        return $this;
    }
}
