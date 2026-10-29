<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Task;
use Livewire\Attributes\On;
use Livewire\Component;

class MyTasks extends Component
{
    public ?int $selectedTaskId = null;

    public string $activeTab = 'today';

    #[On('task-created')]
    #[On('task-updated')]
    #[On('task-deleted')]
    public function refreshTasks(): void
    {
        // Trigger re-render
    }

    public function selectTask(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
    }

    public function closeTaskDetail(): void
    {
        $this->selectedTaskId = null;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $userId = auth()->id();

        // Get tasks assigned to current user (as primary assignee or collaborator)
        $query = Task::with(['status', 'project.workspace', 'tags', 'checklistItems', 'assignee', 'assignees', 'comments'])
            ->where(function ($q) use ($userId) {
                $q->where('assignee_id', $userId)
                    ->orWhereHas('assignees', fn ($sq) => $sq->where('users.id', $userId));
            });

        // Due Today (incomplete)
        $dueToday = (clone $query)
            ->whereDate('due_date', today())
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', \App\Enums\StatusType::Done))
            ->get();

        // Overdue (incomplete tasks before today)
        $overdue = (clone $query)
            ->where('due_date', '<', today())
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', \App\Enums\StatusType::Done))
            ->orderBy('due_date')
            ->get();

        // Upcoming (next 7 days, incomplete)
        $upcoming = (clone $query)
            ->whereBetween('due_date', [today()->addDay(), today()->addDays(7)])
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', \App\Enums\StatusType::Done))
            ->orderBy('due_date')
            ->get();

        // No Due Date (incomplete)
        $noDueDate = (clone $query)
            ->whereNull('due_date')
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', \App\Enums\StatusType::Done))
            ->get();

        // Completed
        $completed = (clone $query)
            ->where(function ($q) {
                $q->whereNotNull('completed_at')
                    ->orWhereHas('status', fn ($sq) => $sq->where('type', \App\Enums\StatusType::Done));
            })
            ->latest('updated_at')
            ->get();

        // All My Tasks
        $allTasks = (clone $query)->latest()->get();

        return view('livewire.tasks.my-tasks', [
            'dueToday' => $dueToday,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'noDueDate' => $noDueDate,
            'completed' => $completed,
            'allTasks' => $allTasks,
        ])->layout('components.layouts.app');
    }
}
