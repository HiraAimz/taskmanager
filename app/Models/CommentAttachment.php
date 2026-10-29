<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentAttachment extends Model
{
    protected $fillable = [
        'comment_id',
        'user_id',
        'original_name',
        'disk_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Human-readable file name accessor */
    public function getFileNameAttribute(): string
    {
        return $this->original_name;
    }

    /** File size in KB, rounded to 1 decimal */
    public function getFileSizeKbAttribute(): string
    {
        return number_format($this->file_size / 1024, 1);
    }
}
