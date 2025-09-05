<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'read',
        'action_url',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read' => true]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read' => false]);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }
}
