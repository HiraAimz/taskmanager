<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MessageAttachment;
use App\Services\AttachmentFileService;

class MessageAttachmentController extends Controller
{
    public function download(MessageAttachment $attachment, AttachmentFileService $files): mixed
    {
        $message = $attachment->message;

        if (! $message) {
            abort(404);
        }

        $conversation = $message->conversation;

        // Only conversation participants or admins can download
        if (! auth()->user()->hasRole('Admin')) {
            if (! $conversation->hasParticipant(auth()->id())) {
                abort(403, 'Unauthorized access to attachment');
            }
        }

        return $files->download($attachment->disk_path, $attachment->original_name);
    }

    public function preview(MessageAttachment $attachment, AttachmentFileService $files): mixed
    {
        $message = $attachment->message;

        if (! $message) {
            abort(404);
        }

        $conversation = $message->conversation;

        // Only conversation participants or admins can preview
        if (! auth()->user()->hasRole('Admin')) {
            if (! $conversation->hasParticipant(auth()->id())) {
                abort(403, 'Unauthorized access to attachment');
            }
        }

        $response = $files->preview($attachment->disk_path, $attachment->original_name, request()->boolean('thumbnail'));
        if ($response) {
            return $response;
        }

        return view('attachments.preview-unavailable', [
            'filename' => $attachment->original_name,
            'downloadUrl' => route('message-attachments.download', $attachment),
        ]);
    }
}
