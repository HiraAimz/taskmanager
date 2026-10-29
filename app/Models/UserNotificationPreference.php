<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'task_assigned',
        'task_status_changed',
        'task_comment_added',
        'task_due_date_changed',
        'task_priority_changed',
        'task_updated',
    ];

    protected $casts = [
        'task_assigned' => 'boolean',
        'task_status_changed' => 'boolean',
        'task_comment_added' => 'boolean',
        'task_due_date_changed' => 'boolean',
        'task_priority_changed' => 'boolean',
        'task_updated' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create default notification preferences for a user.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'task_assigned' => true,
                'task_status_changed' => true,
                'task_comment_added' => true,
                'task_due_date_changed' => true,
                'task_priority_changed' => true,
                'task_updated' => true,
            ]
        );
    }
}
