<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CreateTask
{
    public function execute(array $data, int $projectId, int $statusId): Task
    {
        return DB::transaction(function () use ($data, $projectId, $statusId) {
            // Get or create a default task list for the project
            $taskList = \App\Models\TaskList::firstOrCreate(
                ['project_id' => $projectId, 'name' => 'Default'],
                ['position' => 0]
            );

            $task = Task::create([
                'project_id' => $projectId,
                'status_id' => $statusId,
                'task_list_id' => $data['task_list_id'] ?? $taskList->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
                'due_date' => $data['due_date'] ?? null,
                'creator_id' => auth()->id(),
                'assignee_id' => $data['assignee_id'] ?? auth()->id(),
            ]);

            // Sync tags if provided
            if (isset($data['tags'])) {
                $task->tags()->sync($data['tags']);
            }

            // Log activity
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $task->title])
                ->log('created task');

            return $task->load('assignee', 'tags', 'status');
        });
    }
}
