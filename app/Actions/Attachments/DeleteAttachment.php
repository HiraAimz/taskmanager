<?php

declare(strict_types=1);

namespace App\Actions\Attachments;

use App\Models\Attachment;
use App\Models\Task;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\Storage;

class DeleteAttachment
{
    public function execute(Attachment $attachment): bool
    {
        $task = $attachment->attachable instanceof Task ? $attachment->attachable : null;
        $fileName = $attachment->original_name ?? $attachment->file_name ?? 'file';

        $path = $attachment->disk_path ?? $attachment->file_path;

        // Delete file from storage
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $deleted = $attachment->delete();

        if ($deleted && $task) {
            app(TaskActivityService::class)->logAttachmentRemoved($task, $fileName);
        }

        return (bool) $deleted;
    }
}
