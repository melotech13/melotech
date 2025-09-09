<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AdminLayoutComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $currentUser = Auth::user();
            
            // Get unread notification count for the current admin
            $unreadNotificationCount = Notification::where('user_id', $currentUser->id)
                ->where('read', false)
                ->count();
            
            $view->with('unreadNotificationCount', $unreadNotificationCount);
        } else {
            $view->with('unreadNotificationCount', 0);
        }
    }
}

