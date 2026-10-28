<?php

declare(strict_types=1);

namespace App\Livewire\Comments;

use App\Actions\Comments\CreateComment;
use App\Actions\Comments\DeleteComment;
use App\Actions\Comments\UpdateComment;
use App\Models\Comment;
use App\Models\Task;
use Livewire\Component;

class CommentList extends Component
{
    public Task $task;

    public string $newComment = '';

    public ?int $editingCommentId = null;

    public string $editingContent = '';

    public function addComment(): void
    {
        $this->validate([
            'newComment' => ['required', 'string', 'max:5000'],
        ]);

        $action = new CreateComment();
        $action->execute($this->task, $this->newComment);

        $this->newComment = '';
        $this->task = $this->task->fresh('comments.user');
    }

    public function startEdit(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $this->editingCommentId = $commentId;
        $this->editingContent = $comment->content;
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
        $this->editingContent = '';
        $this->task = $this->task->fresh('comments.user');
    }

    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $action = new DeleteComment();
        $action->execute($comment);

        $this->task = $this->task->fresh('comments.user');
    }

    public function render()
    {
        return view('livewire.comments.comment-list', [
            'comments' => $this->task->comments()->with('user')->latest()->get(),
        ]);
    }
}
