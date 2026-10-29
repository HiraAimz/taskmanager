<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\DB;

class UpdateTask
{
    public function execute(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $activityService = app(TaskActivityService::class);
            $task->loadMissing('status', 'assignee');

            $oldStatus = $task->status;
            $oldAssignee = $task->assignee;
            $oldPriority = $task->priority?->value ?? (string) $task->priority;
            $oldDueDate = $task->due_date?->format('Y-m-d');
            $oldTitle = $task->title;
            $oldDescription = $task->description;

            // Track what changed
            $hasStatusChange = isset($data['status_id']) && (int) $task->status_id !== (int) $data['status_id'];
            $hasAssigneeChange = array_key_exists('assignee_id', $data) && (int) ($task->assignee_id ?? 0) !== (int) ($data['assignee_id'] ?? 0);
            $hasPriorityChange = isset($data['priority']) && $oldPriority !== (string) $data['priority'];
            $newDue = ! empty($data['due_date']) ? \Illuminate\Support\Carbon::parse($data['due_date'])->format('Y-m-d') : null;
            $hasDueDateChange = array_key_exists('due_date', $data) && $oldDueDate !== $newDue;
            $hasTitleChange = isset($data['title']) && $task->title !== $data['title'];
            $hasDescChange = array_key_exists('description', $data) && $task->description !== $data['description'];

            $task->update(array_filter([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'status_id' => $data['status_id'] ?? $task->status_id,
                'task_list_id' => $data['task_list_id'] ?? $task->task_list_id,
                'assignee_id' => array_key_exists('assignee_id', $data) ? $data['assignee_id'] : $task->assignee_id,
            ], fn ($value) => $value !== null || array_key_exists('assignee_id', $data) || array_key_exists('due_date', $data) || array_key_exists('description', $data)));

            $task->load('status', 'assignee');

            // Sync tags if provided
            if (isset($data['tags'])) {
                $task->tags()->sync($data['tags']);
            }

            // Log status change
            if ($hasStatusChange) {
                $newStatus = Status::find($data['status_id']);
                if ($oldStatus && $newStatus) {
                    $activityService->logStatusChange($task, $oldStatus, $newStatus);
                }
            }

            // Log assignee change
            if ($hasAssigneeChange) {
                $newAssignee = ! empty($data['assignee_id']) ? User::find($data['assignee_id']) : null;
                $activityService->logAssigneeChange($task, $oldAssignee, $newAssignee);
            }

            // Log priority change
            if ($hasPriorityChange) {
                $activityService->logPriorityChange($task, $oldPriority, (string) $data['priority']);
            }

            // Log due date change
            if ($hasDueDateChange) {
                $activityService->logDueDateChange($task, $oldDueDate, $newDue);
            }

            // Log title change
            if ($hasTitleChange) {
                $activityService->logAndNotify(
                    task: $task,
                    event: 'task_updated',
                    description: "changed title from '{$oldTitle}' to '{$task->title}'",
                    changes: [
                        'field_name' => 'Title',
                        'old' => $oldTitle,
                        'new' => $task->title,
                    ]
                );
            }

            // Log description change
            if ($hasDescChange) {
                $activityService->logAndNotify(
                    task: $task,
                    event: 'task_updated',
                    description: 'updated the description',
                    changes: [
                        'field_name' => 'Description',
                    ]
                );
            }

            return $task->fresh(['assignee', 'tags', 'status', 'taskList']);
        });
    }
}
