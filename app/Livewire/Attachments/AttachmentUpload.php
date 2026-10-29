<?php

declare(strict_types=1);

namespace App\Livewire\Attachments;

use App\Actions\Attachments\DeleteAttachment;
use App\Actions\Attachments\UploadAttachment;
use App\Models\Attachment;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttachmentUpload extends Component
{
    use WithFileUploads;

    public Task $task;

    public $files = [];

    public function updatedFiles(): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('Admin') ||
            $user->can('attachment.upload') ||
            $user->can('view', $this->task),
            403
        );

        $this->validate([
            'files.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,zip'],
        ]);

        $action = new UploadAttachment();

        foreach ($this->files as $file) {
            $action->execute($this->task, $file);
        }

        $this->files = [];
        $this->task = $this->task->fresh('attachments.user');
        $this->dispatch('task-updated');
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = Attachment::findOrFail($attachmentId);

        // Check authorization
        if ($attachment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin') && !auth()->user()->can('attachment.delete')) {
            abort(403);
        }

        $action = new DeleteAttachment();
        $action->execute($attachment);

        $this->task = $this->task->fresh('attachments.user');
        $this->dispatch('task-updated');
    }

    public function render()
    {
        return view('livewire.attachments.attachment-list', [
            'attachments' => $this->task->attachments()->with('user')->latest()->get(),
        ]);
    }
}
