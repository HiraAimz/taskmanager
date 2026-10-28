<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class DeleteTask
{
    public function execute(Task $task): bool
    {
        return DB::transaction(function () use ($task) {
            // Log activity before deletion
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $task->title])
                ->log('deleted task');

            // Soft delete the task
            return $task->delete();
        });
    }
}
