<?php

namespace App\Observers;

use App\Models\Farm;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class FarmObserver
{
    /**
     * Handle the Farm "created" event.
     */
    public function created(Farm $farm): void
    {
        // Only notify for user-created farms, not admin-created farms
        // Admin-created farms are handled directly in AdminController
        if (!Auth::check() || (Auth::user() && Auth::user()->role !== 'admin')) {
            NotificationService::notifyNewFarmCreated($farm->user, $farm->farm_name);
        }
    }

    /**
     * Handle the Farm "updated" event.
     */
    public function updated(Farm $farm): void
    {
        // Only notify for user-updated farms, not admin-updated farms
        // Admin-updated farms are handled directly in AdminController
        if (!Auth::check() || (Auth::user() && Auth::user()->role !== 'admin')) {
            NotificationService::notifyFarmUpdated($farm->user, $farm->farm_name);
        }
    }

    /**
     * Handle the Farm "deleted" event.
     */
    public function deleted(Farm $farm): void
    {
        // Only notify for user-deleted farms, not admin-deleted farms
        // Admin-deleted farms are handled directly in AdminController
        if (!Auth::check() || (Auth::user() && Auth::user()->role !== 'admin')) {
            NotificationService::notifyFarmDeleted($farm->user, $farm->farm_name);
        }
    }
}
