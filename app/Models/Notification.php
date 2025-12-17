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
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for notifications of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): bool
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Get notification types.
     */
    public static function getTypes(): array
    {
        return [
            'order_status' => 'Order Status Update',
            'promotion' => 'Promotional Offer',
            'system' => 'System Notification',
            'review' => 'Review Response',
            'loyalty' => 'Loyalty Points',
            'wishlist' => 'Wishlist Update',
            'payment' => 'Payment Notification',
        ];
    }
}
