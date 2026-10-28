<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'project_id',
        'task_list_id',
        'parent_id',
        'status_id',
        'title',
        'description',
        'priority',
        'creator_id',
        'assignee_id',
        'start_date',
        'due_date',
        'estimated_minutes',
        'position',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Task $task) {
            if ($task->isDirty('status_id') && ! $task->isDirty('completed_at')) {
                $status = Status::find($task->status_id);
                if ($status) {
                    $isDone = ($status->type instanceof \App\Enums\StatusType)
                        ? $status->type === \App\Enums\StatusType::Done
                        : $status->type === 'done';

                    $task->completed_at = $isDone ? now() : null;
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('position');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_task')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('position');
    }
}
