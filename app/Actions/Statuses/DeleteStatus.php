<?php

declare(strict_types=1);

namespace App\Actions\Statuses;

use App\Models\Status;
use Illuminate\Validation\ValidationException;

class DeleteStatus
{
    public function handle(Status $status, ?int $moveToStatusId = null): void
    {
        $taskCount = $status->tasks()->count();

        if ($taskCount > 0 && $moveToStatusId === null) {
            throw ValidationException::withMessages([
                'status' => "Cannot delete status with {$taskCount} tasks. Please move them to another status first.",
            ]);
        }

        if ($taskCount > 0 && $moveToStatusId) {
            $moveToStatus = Status::findOrFail($moveToStatusId);

            if ($moveToStatus->project_id !== $status->project_id) {
                throw ValidationException::withMessages([
                    'status' => 'Target status must belong to the same project.',
                ]);
            }

            $status->tasks()->update(['status_id' => $moveToStatusId]);
        }

        $status->delete();
    }
}
