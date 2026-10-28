<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;

class DeleteComment
{
    public function execute(Comment $comment): bool
    {
        return $comment->delete();
    }
}
