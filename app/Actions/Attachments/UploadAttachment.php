<?php

declare(strict_types=1);

namespace App\Actions\Attachments;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            // Log activity
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties(['file_name' => $attachment->file_name])
                ->log('added attachment');

            return $attachment->load('user');
        });
    }
}
