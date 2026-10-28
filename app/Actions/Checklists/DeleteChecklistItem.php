<?php

declare(strict_types=1);

namespace App\Actions\Checklists;

use App\Models\ChecklistItem;

class DeleteChecklistItem
{
    public function execute(ChecklistItem $item): bool
    {
        return $item->delete();
    }
}
