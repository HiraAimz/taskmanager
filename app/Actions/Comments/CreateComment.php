<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CreateComment
{
    public function execute(Task $task, string $content): Comment
    {
        return DB::transaction(function () use ($task, $content) {
            $comment = Comment::create([
                'commentable_type' => Task::class,
                'commentable_id' => $task->id,
                'user_id' => auth()->id(),
                'content' => $content,
            ]);

            // Log activity
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->log('added comment');

            return $comment->load('user');
        });
    }
}
