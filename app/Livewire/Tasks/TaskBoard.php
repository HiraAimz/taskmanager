<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\UpdateTaskStatus;
use App\Models\Project;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskBoard extends Component
{
    public Project $project;

    public ?int $filterAssignee = null;

    public ?string $filterPriority = null;

    public array $filterTags = [];

    public ?int $selectedTaskId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project->loadMissing(['workspace.projects']);
    }

    #[On('task-created')]
    #[On('task-updated')]
    #[On('task-deleted')]
    public function refreshBoard(): void
    {
        // This will trigger a re-render
    }

    public function updateTaskStatus(int $taskId, int $statusId): void
    {
        $task = $this->project->tasks()->findOrFail($taskId);

        $this->authorize('changeStatus', $task);

        $action = new UpdateTaskStatus();
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
        $query = $this->project->statuses()
            ->with([
                'tasks' => function ($query) {
                    $query->with(['assignee', 'assignees', 'tags', 'checklistItems', 'comments'])
                        ->orderBy('position');

                    // Apply filters
                    if ($this->filterAssignee) {
                        $query->where('assignee_id', $this->filterAssignee);
                    }

                    if ($this->filterPriority) {
                        $query->where('priority', $this->filterPriority);
                    }

                    if (!empty($this->filterTags)) {
                        $query->whereHas('tags', function ($q) {
                            $q->whereIn('tags.id', $this->filterTags);
                        });
                    }
                },
            ])
            ->orderBy('position');

        $statuses = $query->get();

        // Get workspace members for filter dropdown
        $workspaceMembers = User::whereHas('workspaces', function ($q) {
            $q->where('workspaces.id', $this->project->workspace_id);
        })->get();

        // Get all tags used in this project
        $availableTags = $this->project->tasks()
            ->with('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique('id');

        $this->project->loadMissing(['workspace.projects']);

        return view('livewire.tasks.task-board', [
            'statuses' => $statuses,
            'workspaceMembers' => $workspaceMembers,
            'availableTags' => $availableTags,
        ])->layout('components.layouts.app', ['project' => $this->project]);
    }
}
