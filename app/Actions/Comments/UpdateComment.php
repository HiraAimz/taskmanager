<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\Task;
use App\Services\TaskActivityService;

class UpdateComment
{
    public function execute(Comment $comment, string $content): Comment
    {
        $comment->update(['body' => $content]);

        if ($comment->commentable instanceof Task) {
            app(TaskActivityService::class)->logCommentUpdated($comment->commentable, $comment);
        }

        return $comment->fresh('user');
    }
}
