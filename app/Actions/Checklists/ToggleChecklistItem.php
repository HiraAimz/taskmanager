<?php

declare(strict_types=1);

namespace App\Actions\Checklists;

use App\Models\ChecklistItem;

class ToggleChecklistItem
{
    public function execute(ChecklistItem $item): ChecklistItem
    {
        $item->update(['is_completed' => !$item->is_completed]);

        return $item->fresh();
    }
}
