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

        // Get tasks assigned to current user
        $query = Task::with(['status', 'project', 'tags', 'checklistItems'])
            ->where('assignee_id', $userId);

        // Due Today
        $dueToday = (clone $query)->whereDate('due_date', today())->get();

        // Overdue
        $overdue = (clone $query)->where('due_date', '<', today())->get();

        // Upcoming (next 7 days)
        $upcoming = (clone $query)
            ->whereBetween('due_date', [today()->addDay(), today()->addDays(7)])
            ->get();

        // No Due Date
        $noDueDate = (clone $query)->whereNull('due_date')->get();

        // All My Tasks
        $allTasks = (clone $query)->latest()->get();

        return view('livewire.tasks.my-tasks', [
            'dueToday' => $dueToday,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'noDueDate' => $noDueDate,
            'allTasks' => $allTasks,
        ])->layout('components.layouts.app');
    }
}
