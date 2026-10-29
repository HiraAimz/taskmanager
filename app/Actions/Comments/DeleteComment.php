<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\Task;
use App\Services\TaskActivityService;

class DeleteComment
{
    public function execute(Comment $comment): bool
    {
        $task = $comment->commentable instanceof Task ? $comment->commentable : null;

        $deleted = $comment->delete();

        if ($deleted && $task) {
            app(TaskActivityService::class)->logCommentDeleted($task);
        }

        return (bool) $deleted;
    }
}
