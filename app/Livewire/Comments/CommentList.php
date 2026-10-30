<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Actions\Comments\CreateComment;
use App\Actions\Comments\DeleteComment;
use App\Actions\Comments\UpdateComment;
use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CommentList extends Component
{
    use WithFileUploads;

    public Task $task;

    public string $newComment = '';

    public ?int $editingCommentId = null;

    public string $editingContent = '';

    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $commentFiles = [];

    public function addComment(): void
    {
        $this->validate([
            'newComment'    => ['required_without:commentFiles', 'nullable', 'string', 'max:5000'],
            'commentFiles'  => ['nullable', 'array', 'max:5'],
            'commentFiles.*'=> ['file', 'max:10240', 'mimes:avif,bmp,csv,doc,docx,gif,jpg,jpeg,md,pdf,png,ppt,pptx,svg,txt,webp,xls,xlsx,zip'],
        ]);

        // Create the comment (body can be empty if only files)
        $action = new CreateComment();
        $comment = $action->execute($this->task, $this->newComment ?: ' ');

        // Upload attachments if any
        foreach ($this->commentFiles as $file) {
            $path = $file->store('comment-attachments', 'local');

            CommentAttachment::create([
                'comment_id'    => $comment->id,
                'user_id'       => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'disk_path'     => $path,
                'file_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        $this->newComment  = '';
        $this->commentFiles = [];
        $this->task = $this->task->fresh('comments.user', 'comments.attachments');
    }

    public function startEdit(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $this->editingCommentId = $commentId;
        $this->editingContent   = $comment->content;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingContent' => ['required', 'string', 'max:5000'],
        ]);

        $comment = Comment::findOrFail($this->editingCommentId);

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $action = new UpdateComment();
        $action->execute($comment, $this->editingContent);

        $this->editingCommentId = null;
        $this->editingContent   = '';
        $this->task = $this->task->fresh('comments.user', 'comments.attachments');
    }

    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingContent   = '';
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        // Delete files from storage
        foreach ($comment->attachments as $att) {
            if ($att->disk_path && Storage::disk('local')->exists($att->disk_path)) {
                Storage::disk('local')->delete($att->disk_path);
            }
            $att->delete();
        }

        $action = new DeleteComment();
        $action->execute($comment);

        $this->task = $this->task->fresh('comments.user', 'comments.attachments');
    }

    public function deleteCommentAttachment(int $attachmentId): void
    {
        $attachment = CommentAttachment::findOrFail($attachmentId);
        $comment    = $attachment->comment;

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        if ($attachment->disk_path && Storage::disk('local')->exists($attachment->disk_path)) {
            Storage::disk('local')->delete($attachment->disk_path);
        }

        $attachment->delete();
        $this->task = $this->task->fresh('comments.user', 'comments.attachments');
    }

    public function render()
    {
        return view('livewire.comments.comment-list', [
            'comments' => $this->task->comments()->with('user', 'attachments')->latest()->get(),
        ]);
    }
}
