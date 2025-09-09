<?php

namespace App\Observers;

use App\Models\PhotoAnalysis;
use App\Services\NotificationService;

class PhotoAnalysisObserver
{
    /**
     * Handle the PhotoAnalysis "created" event.
     */
    public function created(PhotoAnalysis $photoAnalysis): void
    {
        // Notify admins about new photo analysis
        $farmName = $photoAnalysis->farm->farm_name ?? 'Unknown Farm';
        NotificationService::notifyPhotoAnalysis($photoAnalysis->user, $farmName);
    }
}
