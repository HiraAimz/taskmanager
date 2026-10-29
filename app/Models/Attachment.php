<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'user_id',
        'disk_path',
        'original_name',
        'mime_type',
        'size',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function getFileNameAttribute(): string
    {
        return $this->attributes['original_name'] ?? $this->attributes['file_name'] ?? '';
    }

    public function setFileNameAttribute(mixed $value): void
    {
        $this->attributes['original_name'] = $value;
    }

    public function getFilePathAttribute(): string
    {
        return $this->attributes['disk_path'] ?? $this->attributes['file_path'] ?? '';
    }

    public function setFilePathAttribute(mixed $value): void
    {
        $this->attributes['disk_path'] = $value;
    }

    public function getFileTypeAttribute(): string
    {
        return $this->attributes['mime_type'] ?? $this->attributes['file_type'] ?? '';
    }

    public function setFileTypeAttribute(mixed $value): void
    {
        $this->attributes['mime_type'] = $value;
    }

    public function getFileSizeAttribute(): int
    {
        return (int) ($this->attributes['size'] ?? $this->attributes['file_size'] ?? 0);
    }

    public function setFileSizeAttribute(mixed $value): void
    {
        $this->attributes['size'] = (int) $value;
    }

    public function attachable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
