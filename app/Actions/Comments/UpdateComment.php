<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;

class UpdateComment
{
    public function execute(Comment $comment, string $content): Comment
    {
        $comment->update(['body' => $content]);

        return $comment->fresh('user');
    }
}
