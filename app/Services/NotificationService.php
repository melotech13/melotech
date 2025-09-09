<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Farm;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for admin users.
     */
    public static function notifyAdmins(string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        try {
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'action_url' => $actionUrl,
                    'read' => false,
                ]);
            }
            
            Log::info('Admin notifications created', [
                'type' => $type,
                'title' => $title,
                'admin_count' => $adminUsers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create admin notifications', [
                'error' => $e->getMessage(),
                'type' => $type,
                'title' => $title
            ]);
        }
    }

    /**
     * Create a notification for a specific user.
     */
    public static function notifyUser(User $user, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        try {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'read' => false,
            ]);
            
            Log::info('User notification created', [
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create user notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title
            ]);
        }
    }

    /**
     * Notify when a new user registers.
     */
    public static function notifyNewUserRegistration(User $user): void
    {
        $title = 'New User Registration';
        $message = "{$user->name} ({$user->email}) has registered a new account.";
        $actionUrl = route('admin.users.show', $user);
        
        self::notifyAdmins('user', $title, $message, $actionUrl);
    }

    /**
     * Notify when a new farm is created.
     */
    public static function notifyNewFarmCreated(User $user, string $farmName): void
    {
        $title = 'New Farm Created';
        $message = "{$user->name} has created a new farm: {$farmName}";
        $actionUrl = route('admin.farms.index');
        
        self::notifyAdmins('farm', $title, $message, $actionUrl);
    }

    /**
     * Notify when a photo analysis is performed.
     */
    public static function notifyPhotoAnalysis(User $user, string $farmName): void
    {
        $title = 'Photo Analysis Completed';
        $message = "{$user->name} has completed a photo analysis for {$farmName}";
        $actionUrl = route('admin.farms.index');
        
        self::notifyAdmins('analysis', $title, $message, $actionUrl);
    }

    /**
     * Notify when crop progress is updated.
     */
    public static function notifyCropProgressUpdate(User $user, string $farmName, string $stage): void
    {
        $title = 'Crop Progress Update';
        $message = "{$user->name} has updated crop progress for {$farmName} to {$stage} stage";
        $actionUrl = route('admin.farms.index');
        
        self::notifyAdmins('progress', $title, $message, $actionUrl);
    }

    /**
     * Notify when a user is updated by admin.
     */
    public static function notifyUserUpdated(User $user, User $admin): void
    {
        $title = 'Account Updated';
        $message = "Your account has been updated by {$admin->name}";
        $actionUrl = route('profile');
        
        self::notifyUser($user, 'system', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin creates a user.
     */
    public static function notifyAdminCreatedUser(User $admin, User $newUser): void
    {
        $title = 'User Created by Admin';
        $message = "{$admin->name} has created a new user account for {$newUser->name} ({$newUser->email})";
        $actionUrl = route('admin.users.show', $newUser);
        
        self::notifyAdmins('user', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin deletes a user.
     */
    public static function notifyAdminDeletedUser(User $admin, string $userName, string $userEmail): void
    {
        $title = 'User Deleted by Admin';
        $message = "{$admin->name} has deleted user account: {$userName} ({$userEmail})";
        $actionUrl = route('admin.users.index');
        
        self::notifyAdmins('user', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin updates a user.
     */
    public static function notifyAdminUpdatedUser(User $admin, User $updatedUser): void
    {
        $title = 'User Updated by Admin';
        $message = "{$admin->name} has updated user account: {$updatedUser->name} ({$updatedUser->email})";
        $actionUrl = route('admin.users.show', $updatedUser);
        
        self::notifyAdmins('user', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin creates a farm.
     */
    public static function notifyAdminCreatedFarm(User $admin, Farm $farm): void
    {
        $title = 'Farm Created by Admin';
        $message = "{$admin->name} has created a new farm: {$farm->farm_name} for {$farm->user->name}";
        $actionUrl = route('admin.farms.show', $farm);
        
        self::notifyAdmins('farm', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin updates a farm.
     */
    public static function notifyAdminUpdatedFarm(User $admin, Farm $farm): void
    {
        $title = 'Farm Updated by Admin';
        $message = "{$admin->name} has updated farm: {$farm->farm_name}";
        $actionUrl = route('admin.farms.show', $farm);
        
        self::notifyAdmins('farm', $title, $message, $actionUrl);
    }

    /**
     * Notify when admin deletes a farm.
     */
    public static function notifyAdminDeletedFarm(User $admin, string $farmName, string $ownerName): void
    {
        $title = 'Farm Deleted by Admin';
        $message = "{$admin->name} has deleted farm: {$farmName} (owned by {$ownerName})";
        $actionUrl = route('admin.farms.index');
        
        self::notifyAdmins('farm', $title, $message, $actionUrl);
    }

    /**
     * Notify when a farm is updated.
     */
    public static function notifyFarmUpdated(User $user, string $farmName): void
    {
        $title = 'Farm Updated';
        $message = "Your farm '{$farmName}' has been updated";
        $actionUrl = route('crop-growth.index');
        
        self::notifyUser($user, 'farm', $title, $message, $actionUrl);
    }

    /**
     * Notify when a user is deleted.
     */
    public static function notifyUserDeleted(string $userName, string $userEmail): void
    {
        $title = 'User Account Deleted';
        $message = "User account for {$userName} ({$userEmail}) has been deleted";
        
        self::notifyAdmins('user', $title, $message, route('admin.users.index'));
    }

    /**
     * Notify when a farm is deleted.
     */
    public static function notifyFarmDeleted(User $user, string $farmName): void
    {
        $title = 'Farm Deleted';
        $message = "Your farm '{$farmName}' has been deleted";
        
        self::notifyUser($user, 'farm', $title, $message, route('crop-growth.index'));
    }

    /**
     * Notify when system maintenance is scheduled.
     */
    public static function notifySystemMaintenance(string $message, ?string $scheduledTime = null): void
    {
        $title = 'System Maintenance';
        $fullMessage = $scheduledTime ? "Scheduled for {$scheduledTime}. {$message}" : $message;
        
        // Notify all users
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            self::notifyUser($user, 'system', $title, $fullMessage, null);
        }
    }

    /**
     * Get unread notifications count for a user.
     */
    public static function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);
    }

    /**
     * Clean up old notifications (older than 30 days).
     */
    public static function cleanupOldNotifications(): void
    {
        try {
            $deletedCount = Notification::where('created_at', '<', now()->subDays(30))->delete();
            
            Log::info('Old notifications cleaned up', [
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old notifications', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
