<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\DeleteTask;
use App\Actions\Tasks\UpdateTask;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskActivityService;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class TaskDetail extends Component
{
    public Task $task;

    public string $title;

    public string $description;

    public string $priority;

    public ?int $statusId = null;

    public ?int $assigneeId = null;

    public array $selectedAssignees = [];

    public ?string $startDate = null;

    public ?string $dueDate = null;

    public int $activityLimit = 10;

    public string $activeTab = 'comments'; // 'comments' or 'activity'

    public function mount(int $taskId): void
    {
        $this->task = Task::with(['assignee', 'assignees', 'status', 'tags', 'project.statuses', 'project.workspace.members', 'checklistItems', 'comments.user', 'attachments.user'])
            ->findOrFail($taskId);

        $this->authorize('view', $this->task);

        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->priority = $this->task->priority->value;
        $this->statusId = $this->task->status_id;
        $this->assigneeId = $this->task->assignee_id;
        $this->selectedAssignees = $this->task->assignees->pluck('id')->toArray();
        $this->startDate = $this->task->start_date?->format('Y-m-d');
        $this->dueDate = $this->task->due_date?->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function loadMoreActivity(): void
    {
        $this->activityLimit += 10;
    }

    #[On('comment-added')]
    #[On('comment-updated')]
    #[On('comment-deleted')]
    #[On('attachment-uploaded')]
    #[On('attachment-deleted')]
    public function refreshActivities(): void
    {
        $this->task->loadMissing('comments.user', 'attachments.user');
    }

    public function save(): void
    {
        $this->authorize('update', $this->task);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:urgent,high,normal,low'],
            'statusId' => ['required', 'exists:statuses,id'],
            'assigneeId' => ['nullable', 'exists:users,id'],
            'selectedAssignees' => ['array'],
            'selectedAssignees.*' => ['exists:users,id'],
            'startDate' => ['nullable', 'date'],
            'dueDate' => ['nullable', 'date', 'after_or_equal:startDate'],
        ]);

        $action = new UpdateTask();
        $task = $action->execute($this->task, [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status_id' => $this->statusId,
            'assignee_id' => $this->assigneeId,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
        ]);

        // Sync multiple assignees
        $this->task->assignees()->sync($this->selectedAssignees);

        $this->dispatch('task-updated');
        $this->task = $this->task->fresh(['assignee', 'assignees', 'status', 'tags', 'project.statuses', 'project.workspace.members', 'checklistItems', 'comments.user', 'attachments.user']);

        session()->flash('message', 'Task updated successfully!');
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->task);

        $action = new DeleteTask();
        $action->execute($this->task);

        $this->dispatch('task-deleted');
        $this->dispatch('close-modal');
    }

    public function removeCollaborator(int $userId): void
    {
        $this->authorize('update', $this->task);

        $this->selectedAssignees = array_values(array_filter($this->selectedAssignees, fn ($id) => (int) $id !== $userId));
        $this->task->assignees()->sync($this->selectedAssignees);

        $collab = User::find($userId);
        if ($collab) {
            app(TaskActivityService::class)->logCollaboratorRemoved($this->task, $collab);
        }

        $this->dispatch('task-updated');
    }

    public function toggleCollaborator(int $userId): void
    {
        $this->authorize('update', $this->task);

        if (in_array($userId, $this->selectedAssignees)) {
            $this->removeCollaborator($userId);
        } else {
            $this->selectedAssignees[] = $userId;
            $this->task->assignees()->sync($this->selectedAssignees);

            $collab = User::find($userId);
            if ($collab) {
                app(TaskActivityService::class)->logCollaboratorAdded($this->task, $collab);
            }

            $this->dispatch('task-updated');
        }
    }

    public function close(): void
    {
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $this->task->loadMissing(['assignee', 'assignees', 'status', 'tags', 'project.statuses', 'project.workspace.members', 'checklistItems', 'comments.user', 'attachments.user']);

        $workspaceMembers = User::whereHas('workspaces', function ($q) {
            $q->where('workspaces.id', $this->task->project->workspace_id);
        })->get();

        $activitiesQuery = Activity::where('subject_type', Task::class)
            ->where('subject_id', $this->task->id)
            ->with('causer')
            ->latest();

        $totalActivitiesCount = (clone $activitiesQuery)->count();
        $activities = $activitiesQuery->take($this->activityLimit)->get();

        return view('livewire.tasks.task-detail', [
            'workspaceMembers' => $workspaceMembers,
            'projectStatuses' => $this->task->project->statuses,
            'activities' => $activities,
            'totalActivitiesCount' => $totalActivitiesCount,
        ]);
    }
}
