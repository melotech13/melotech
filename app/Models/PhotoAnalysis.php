<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'photo_path',
        'analysis_type',
        'identified_type',
        'confidence_score',
        'recommendations',
        'analysis_date',
        'analysis_id',
        'processing_time',
        'image_metadata',
        'analysis_details'
    ];

    protected $casts = [
        'analysis_date' => 'datetime',
        'confidence_score' => 'decimal:2',
        'recommendations' => 'array',
        'image_metadata' => 'array',
        'analysis_details' => 'array',
        'processing_time' => 'decimal:2'
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
}
