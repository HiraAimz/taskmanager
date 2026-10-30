<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CommentAttachment;
use App\Models\Task;
use App\Services\AttachmentFileService;
use Illuminate\Support\Facades\Gate;

class CommentAttachmentController extends Controller
{
    public function download(CommentAttachment $attachment, AttachmentFileService $files): mixed
    {
        $comment = $attachment->comment()->withTrashed()->first();
        $task = $comment?->commentable;
        if (! $task instanceof Task) {
            abort(404);
        }

        Gate::authorize('view', $task);

        return $files->download($attachment->disk_path, $attachment->original_name);
    }

    public function preview(CommentAttachment $attachment, AttachmentFileService $files): mixed
    {
        $comment = $attachment->comment()->withTrashed()->first();
        $task = $comment?->commentable;
        if (! $task instanceof Task) {
            abort(404);
        }

        Gate::authorize('view', $task);
        $response = $files->preview($attachment->disk_path, $attachment->original_name, request()->boolean('thumbnail'));
        if ($response) {
            return $response;
        }

        return view('attachments.preview-unavailable', [
            'filename' => $attachment->original_name,
            'downloadUrl' => route('comment-attachments.download', $attachment),
        ]);
    }
}
