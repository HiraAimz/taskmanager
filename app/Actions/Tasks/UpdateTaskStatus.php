<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class UpdateTaskStatus
{
    public function execute(Task $task, int $statusId): Task
    {
        return DB::transaction(function () use ($task, $statusId) {
            $oldStatusId = $task->status_id;

            $task->update(['status_id' => $statusId]);

            // Log activity
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status_id' => $oldStatusId,
                    'new_status_id' => $statusId,
                ])
                ->log('changed task status');

            return $task->fresh('status');
        });
    }
}
