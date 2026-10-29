<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Status;
use App\Models\Task;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\DB;

class UpdateTaskStatus
{
    public function execute(Task $task, int $statusId): Task
    {
        return DB::transaction(function () use ($task, $statusId) {
            $task->loadMissing('status');
            $oldStatus = $task->status ?? Status::find($task->status_id);

            $task->update(['status_id' => $statusId]);
            $newStatus = Status::find($statusId);

            if ($oldStatus && $newStatus && $oldStatus->id !== $newStatus->id) {
                app(TaskActivityService::class)->logStatusChange($task, $oldStatus, $newStatus);
            }

            return $task->fresh('status');
        });
    }
}
