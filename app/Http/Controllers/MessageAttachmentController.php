<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MessageAttachment;
use Illuminate\Support\Facades\Storage;

class MessageAttachmentController extends Controller
{
    public function download(MessageAttachment $attachment): mixed
    {
        $message = $attachment->message;

        if (!$message) {
            abort(404);
        }

        $conversation = $message->conversation;

        // Only conversation participants or admins can download
        if (!auth()->user()->hasRole('Admin')) {
            if (!$conversation->hasParticipant(auth()->id())) {
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

    public function preview(MessageAttachment $attachment): mixed
    {
        $message = $attachment->message;

        if (!$message) {
            abort(404);
        }

        $conversation = $message->conversation;

        // Only conversation participants or admins can preview
        if (!auth()->user()->hasRole('Admin')) {
            if (!$conversation->hasParticipant(auth()->id())) {
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
