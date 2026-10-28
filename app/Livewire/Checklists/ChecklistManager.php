<?php

declare(strict_types=1);

namespace App\Livewire\Checklists;

use App\Actions\Checklists\CreateChecklistItem;
use App\Actions\Checklists\DeleteChecklistItem;
use App\Actions\Checklists\ToggleChecklistItem;
use App\Models\ChecklistItem;
use App\Models\Task;
use Livewire\Component;

class ChecklistManager extends Component
{
    public Task $task;

    public string $newItemTitle = '';

    public ?int $editingItemId = null;

    public string $editingItemTitle = '';

    public function addItem(): void
    {
        $this->validate([
            'newItemTitle' => ['required', 'string', 'max:255'],
        ]);

        $action = new CreateChecklistItem();
        $action->execute($this->task, $this->newItemTitle);

        $this->newItemTitle = '';
        $this->task = $this->task->fresh('checklistItems');
    }

    public function toggleItem(int $itemId): void
    {
        $item = ChecklistItem::findOrFail($itemId);

        if ($item->task_id !== $this->task->id) {
            abort(403);
        }

        $action = new ToggleChecklistItem();
        $action->execute($item);

        $this->task = $this->task->fresh('checklistItems');
        $this->dispatch('task-updated');
    }

    public function startEdit(int $itemId): void
    {
        $item = ChecklistItem::findOrFail($itemId);

        if ($item->task_id !== $this->task->id) {
            abort(403);
        }

        $this->editingItemId = $itemId;
        $this->editingItemTitle = $item->title;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingItemTitle' => ['required', 'string', 'max:255'],
        ]);

        $item = ChecklistItem::findOrFail($this->editingItemId);

        if ($item->task_id !== $this->task->id) {
            abort(403);
        }

        $item->update(['title' => $this->editingItemTitle]);

        $this->editingItemId = null;
        $this->editingItemTitle = '';
        $this->task = $this->task->fresh('checklistItems');
    }

    public function cancelEdit(): void
    {
        $this->editingItemId = null;
        $this->editingItemTitle = '';
    }

    public function deleteItem(int $itemId): void
    {
        $item = ChecklistItem::findOrFail($itemId);

        if ($item->task_id !== $this->task->id) {
            abort(403);
        }

        $action = new DeleteChecklistItem();
        $action->execute($item);

        $this->task = $this->task->fresh('checklistItems');
        $this->dispatch('task-updated');
    }

    public function render()
    {
        $items = $this->task->checklistItems()->orderBy('position')->get();
        $completedCount = $items->where('is_completed', true)->count();
        $totalCount = $items->count();
        $progress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('livewire.checklists.checklist-manager', [
            'items' => $items,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'progress' => $progress,
        ]);
    }
}
