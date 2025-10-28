<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class NutrientAnalysis
 * 
 * @property int $id
 * @property int $user_id
 * @property float $nitrogen
 * @property float $phosphorus
 * @property float $potassium
 * @property float $soil_ph
 * @property float $soil_moisture
 * @property string $growth_stage
 * @property string|null $nutrient_status
 * @property string|null $deficiency_detection
 * @property string|null $ai_recommendations
 * @property string|null $stage_advisory
 * @property array|null $detailed_analysis
 * @property \Carbon\Carbon $analysis_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\User $user
 */
class NutrientAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nitrogen',
        'phosphorus',
        'potassium',
        'soil_ph',
        'soil_moisture',
        'growth_stage',
        'nutrient_status',
        'deficiency_detection',
        'ai_recommendations',
        'stage_advisory',
        'detailed_analysis',
        'analysis_date',
    ];

    protected $casts = [
        'nitrogen' => 'decimal:2',
        'phosphorus' => 'decimal:2',
        'potassium' => 'decimal:2',
        'soil_ph' => 'decimal:2',
        'soil_moisture' => 'decimal:2',
        'detailed_analysis' => 'array',
        'analysis_date' => 'datetime',
    ];

    /**
     * Get the user that owns the nutrient analysis.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted growth stage name.
     */
    public function getGrowthStageName(): string
    {
        return match($this->growth_stage) {
            'seedling' => 'Seedling',
            'vegetative' => 'Vegetative',
            'flowering' => 'Flowering',
            'fruiting' => 'Fruiting',
            'harvest' => 'Harvest',
            default => ucfirst($this->growth_stage),
        };
    }

    /**
     * Get NPK balance status color.
     */
    public function getNPKStatusColor(): string
    {
        $detailed = $this->detailed_analysis ?? [];
        
        if (!isset($detailed['npk_balance'])) {
            return 'secondary';
        }

        return match($detailed['npk_balance']) {
            'balanced' => 'success',
            'moderate' => 'warning',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Check if analysis has critical issues.
     */
    public function hasCriticalIssues(): bool
    {
        $detailed = $this->detailed_analysis ?? [];
        return isset($detailed['npk_balance']) && $detailed['npk_balance'] === 'critical';
    }
}
