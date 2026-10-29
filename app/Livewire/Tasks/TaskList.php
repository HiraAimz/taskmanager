<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public Project $project;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public ?int $filterAssignee = null;

    public ?string $filterPriority = null;

    public ?int $filterStatus = null;

    public ?int $selectedTaskId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project->loadMissing(['workspace.projects', 'statuses']);
    }

    #[On('task-created')]
    #[On('task-updated')]
    #[On('task-deleted')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public ?string $search = '';

    public function updateTaskStatus(int $taskId, int $statusId): void
    {
        $task = $this->project->tasks()->with(['project.workspace', 'status'])->findOrFail($taskId);
        $this->authorize('changeStatus', $task);

        $action = new \App\Actions\Tasks\UpdateTaskStatus();
        $action->execute($task, $statusId);

        $this->dispatch('task-updated');
    }

    public function selectTask(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
    }

    public function closeTaskDetail(): void
    {
        $this->selectedTaskId = null;
    }

    public function render()
    {
        $query = $this->project->tasks()
            ->with(['project.workspace', 'assignee', 'assignees', 'status', 'tags', 'checklistItems', 'comments']);

        if (! empty($this->search)) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        // Apply filters
        if ($this->filterAssignee) {
            $query->where(function ($q) {
                $q->where('assignee_id', $this->filterAssignee)
                    ->orWhereHas('assignees', fn ($sq) => $sq->where('users.id', $this->filterAssignee));
            });
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterStatus) {
            $query->where('status_id', $this->filterStatus);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $tasks = $query->paginate(20);

        // Get workspace members for filter
        $workspaceMembers = User::whereHas('workspaces', function ($q) {
            $q->where('workspaces.id', $this->project->workspace_id);
        })->get();

        $this->project->loadMissing(['workspace.projects', 'statuses']);

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
            'workspaceMembers' => $workspaceMembers,
            'statuses' => $this->project->statuses,
            'project' => $this->project,
        ])->layout('components.layouts.app', ['project' => $this->project]);
    }
}
