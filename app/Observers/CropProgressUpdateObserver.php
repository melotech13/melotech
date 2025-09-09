<?php

namespace App\Observers;

use App\Models\CropProgressUpdate;
use App\Services\NotificationService;

class CropProgressUpdateObserver
{
    /**
     * Handle the CropProgressUpdate "created" event.
     */
    public function created(CropProgressUpdate $cropProgressUpdate): void
    {
        // Notify admins about crop progress update
        $farmName = $cropProgressUpdate->farm->farm_name ?? 'Unknown Farm';
        $stage = $cropProgressUpdate->growth_stage ?? 'Unknown Stage';
        NotificationService::notifyCropProgressUpdate($cropProgressUpdate->user, $farmName, $stage);
    }
}
