<?php

declare(strict_types=1);

namespace App\Actions\Attachments;

use App\Models\Attachment;
use App\Models\Task;
use App\Services\TaskActivityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UploadAttachment
{
    public function execute(Task $task, UploadedFile $file): Attachment
    {
        return DB::transaction(function () use ($task, $file) {
            $workspace = $task->project->workspace;

            // Store file in private disk
            $path = $file->store(
                "attachments/{$workspace->id}/{$task->id}",
                'local'
            );

            $attachment = Attachment::create([
                'attachable_type' => Task::class,
                'attachable_id' => $task->id,
                'user_id' => auth()->id(),
                'disk_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            // Log activity and notify
            app(TaskActivityService::class)->logAttachmentAdded($task, $attachment);

            return $attachment->load('user');
        });
    }
}
