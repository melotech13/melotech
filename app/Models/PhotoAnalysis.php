<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PhotoAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'photo_path',
        'analysis_type',
        'identified_type',
        'identified_condition',
        'condition_key',
        'confidence_score',
        'recommendations',
        'analysis_date',
        'analysis_id',
        'processing_time',
        'image_metadata',
        'analysis_details',
        'condition_scores',
        'model_version'
    ];

    protected $casts = [
        'analysis_date' => 'datetime',
        'confidence_score' => 'decimal:2',
        'recommendations' => 'array',
        'image_metadata' => 'array',
        'analysis_details' => 'array',
        'processing_time' => 'decimal:2',
        'condition_scores' => 'array'
    ];

    /**
     * Get the user that owns the photo analysis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the analysis type label.
     */
    public function getAnalysisTypeLabelAttribute(): string
    {
        return $this->analysis_type ? ucfirst($this->analysis_type) : 'Unknown';
    }

    /**
     * Get the identified type label.
     */
    public function getIdentifiedTypeLabelAttribute(): string
    {
        return $this->identified_type ? ucfirst($this->identified_type) : 'Unknown Type';
    }

    /**
     * Get formatted analysis date.
     */
    public function getFormattedAnalysisDateAttribute(): string
    {
        return $this->analysis_date->format('M d, Y \a\t g:i A');
    }

    /**
     * Get the photo URL for display.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo_path) {
            return '';
        }
        $relative = ltrim($this->photo_path, '/');
        // If saved directly under public/ (e.g., 'uploads/...'), link without the storage prefix
        if (str_starts_with($relative, 'uploads/')) {
            return asset($relative);
        }
        return asset('storage/' . $relative);
    }

    /**
     * Check if the photo file exists.
     */
    public function getPhotoExistsAttribute(): bool
    {
        if (!$this->photo_path) {
            return false;
        }
        
        return Storage::disk('public')->exists($this->photo_path);
    }
}
