<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\Task;
use App\Services\TaskActivityService;
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
                'body' => $content,
            ]);

            app(TaskActivityService::class)->logCommentAdded($task, $comment);

            return $comment->load('user');
        });
    }
}
