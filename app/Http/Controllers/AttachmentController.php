<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment): Response
    {
        // Check if user has access to this attachment's task's workspace
        $task = $attachment->attachable;

        if (!$task || !$task->project) {
            abort(404);
        }

        $workspace = $task->project->workspace;

        if (!auth()->user()->workspaces->contains($workspace->id)) {
            abort(403, 'Unauthorized access to attachment');
        }

        // Check if file exists
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return response()->download(
            Storage::disk('local')->path($attachment->file_path),
            $attachment->file_name
        );
    }
}
