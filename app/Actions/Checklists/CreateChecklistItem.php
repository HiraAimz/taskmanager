<?php

declare(strict_types=1);

namespace App\Actions\Checklists;

use App\Models\ChecklistItem;
use App\Models\Task;

class CreateChecklistItem
{
    public function execute(Task $task, string $title): ChecklistItem
    {
        $maxPosition = $task->checklistItems()->max('position') ?? 0;

        return ChecklistItem::create([
            'task_id' => $task->id,
            'title' => $title,
            'position' => $maxPosition + 1,
            'is_completed' => false,
        ]);
    }
}
