<?php

namespace App\Observers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Only notify for new user registrations (not admin-created users)
        // Check if this is a user registration (not admin creation)
        if ($user->role === 'user' && !Auth::check()) {
            NotificationService::notifyNewUserRegistration($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Only notify for user self-updates, not admin updates
        // Admin updates are handled directly in AdminController
        if (Auth::check() && Auth::user() && Auth::user()->id === $user->id && Auth::user()->role !== 'admin') {
            // This is a user updating their own profile
            // Could add notification here if needed
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Only notify for user self-deletions, not admin deletions
        // Admin deletions are handled directly in AdminController
        if (!Auth::check() || (Auth::user() && Auth::user()->id === $user->id)) {
            // This is a user deleting their own account
            NotificationService::notifyUserDeleted($user->name, $user->email);
        }
    }
}
