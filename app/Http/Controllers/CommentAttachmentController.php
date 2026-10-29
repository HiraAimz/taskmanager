<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CommentAttachment;
use Illuminate\Support\Facades\Storage;

class CommentAttachmentController extends Controller
{
    public function download(CommentAttachment $attachment): mixed
    {
        $comment = $attachment->comment()->withTrashed()->first();

        if (!$comment) {
            abort(404);
        }

        // Only the comment author, admins, or workspace members can download
        $task = $comment->commentable;
        $workspace = $task?->project?->workspace;

        if (!auth()->user()->hasRole('Admin')) {
            if ($workspace && !auth()->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
                abort(403, 'Unauthorized access to attachment');
            }
        }

        if (!$attachment->disk_path || !Storage::disk('local')->exists($attachment->disk_path)) {
            abort(404, 'File not found');
        }

        return response()->download(
            Storage::disk('local')->path($attachment->disk_path),
            $attachment->original_name
        );
    }

    public function preview(CommentAttachment $attachment): mixed
    {
        $comment = $attachment->comment()->withTrashed()->first();

        if (!$comment) {
            abort(404);
        }

        $task = $comment->commentable;
        $workspace = $task?->project?->workspace;

        if (!auth()->user()->hasRole('Admin')) {
            if ($workspace && !auth()->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
                abort(403, 'Unauthorized access to attachment');
            }
        }

        if (!$attachment->disk_path || !Storage::disk('local')->exists($attachment->disk_path)) {
            abort(404, 'File not found');
        }

        $mime = $attachment->file_type ?: 'application/octet-stream';

        return response()->file(
            Storage::disk('local')->path($attachment->disk_path),
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($attachment->original_name) . '"',
                'Cache-Control' => 'private, max-age=86400',
            ]
        );
    }
}
