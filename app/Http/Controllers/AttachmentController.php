<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment): mixed
    {
        // Check if user has access to this attachment's task's workspace
        $task = $attachment->attachable;

        if (!$task || !$task->project) {
            abort(404);
        }

        $workspace = $task->project->workspace;

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            abort(403, 'Unauthorized access to attachment');
        }

        $path = $attachment->disk_path ?? $attachment->file_path;
        $name = $attachment->original_name ?? $attachment->file_name;

        // Check if file exists
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download(
            Storage::disk('local')->path($path),
            $name
        );
    }

    public function preview(Attachment $attachment): mixed
    {
        $task = $attachment->attachable;

        if (!$task || !$task->project) {
            abort(404);
        }

        $workspace = $task->project->workspace;

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            abort(403, 'Unauthorized access to attachment');
        }

        $path = $attachment->disk_path ?? $attachment->file_path;
        $name = $attachment->original_name ?? $attachment->file_name ?? 'file';
        $mime = $attachment->file_type ?: 'application/octet-stream';

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file(
            Storage::disk('local')->path($path),
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
                'Cache-Control' => 'private, max-age=86400',
            ]
        );
    }
}
