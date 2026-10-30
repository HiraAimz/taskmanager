<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use App\Services\AttachmentFileService;
use Illuminate\Support\Facades\Gate;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment, AttachmentFileService $files): mixed
    {
        $task = $attachment->attachable;
        if (! $task instanceof Task) {
            abort(404);
        }

        Gate::authorize('view', $task);

        return $files->download($attachment->disk_path ?? $attachment->file_path, $attachment->original_name ?? $attachment->file_name);
    }

    public function preview(Attachment $attachment, AttachmentFileService $files): mixed
    {
        $task = $attachment->attachable;
        if (! $task instanceof Task) {
            abort(404);
        }

        Gate::authorize('view', $task);
        $name = $attachment->original_name ?? $attachment->file_name ?? 'file';
        $response = $files->preview(
            $attachment->disk_path ?? $attachment->file_path,
            $name,
            request()->boolean('thumbnail')
        );

        if ($response) {
            return $response;
        }

        return view('attachments.preview-unavailable', [
            'filename' => $name,
            'downloadUrl' => route('attachments.download', $attachment),
        ]);
    }
}
