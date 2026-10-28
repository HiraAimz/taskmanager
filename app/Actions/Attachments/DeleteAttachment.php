<?php

declare(strict_types=1);

namespace App\Actions\Attachments;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class DeleteAttachment
{
    public function execute(Attachment $attachment): bool
    {
        // Delete file from storage
        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        return $attachment->delete();
    }
}
