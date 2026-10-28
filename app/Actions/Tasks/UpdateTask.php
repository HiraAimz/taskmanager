<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class UpdateTask
{
    public function execute(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $changes = [];

            // Track what changed for activity log
            foreach (['title', 'description', 'priority', 'due_date', 'status_id', 'task_list_id', 'assignee_id'] as $field) {
                if (isset($data[$field]) && $task->{$field} != $data[$field]) {
                    $changes[$field] = [
                        'old' => $task->{$field},
                        'new' => $data[$field],
                    ];
                }
            }

            $task->update(array_filter([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'status_id' => $data['status_id'] ?? $task->status_id,
                'task_list_id' => $data['task_list_id'] ?? $task->task_list_id,
                'assignee_id' => array_key_exists('assignee_id', $data) ? $data['assignee_id'] : $task->assignee_id,
            ], fn ($value) => $value !== null || array_key_exists('assignee_id', $data)));

            // Sync tags if provided
            if (isset($data['tags'])) {
                $task->tags()->sync($data['tags']);
                $changes['tags'] = $data['tags'];
            }

            // Log activity for each significant change
            if (!empty($changes)) {
                foreach ($changes as $field => $change) {
                    $logMessage = match ($field) {
                        'status_id' => 'changed status',
                        'priority' => 'changed priority',
                        'assignee_id' => 'changed assignee',
                        'due_date' => 'changed due date',
                        'title' => 'changed title',
                        'description' => 'updated description',
                        'task_list_id' => 'moved to different list',
                        'tags' => 'updated tags',
                        default => "updated {$field}",
                    };

                    activity()
                        ->performedOn($task)
                        ->causedBy(auth()->user())
                        ->withProperties(['changes' => $change])
                        ->log($logMessage);
                }
            }

            return $task->fresh(['assignee', 'tags', 'status', 'taskList']);
        });
    }
}
