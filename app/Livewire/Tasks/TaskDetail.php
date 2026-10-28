<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\DeleteTask;
use App\Actions\Tasks\UpdateTask;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class TaskDetail extends Component
{
    public Task $task;

    public string $title;

    public string $description;

    public string $priority;

    public ?int $statusId;

    public ?int $assigneeId;

    public array $selectedAssignees = [];

    public ?string $dueDate;

    public function mount(int $taskId): void
    {
        $this->task = Task::with(['assignee', 'status', 'tags', 'project.statuses', 'project.workspace.members', 'checklistItems', 'comments.user', 'attachments.user'])
            ->findOrFail($taskId);

        $this->authorize('view', $this->task);

        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->priority = $this->task->priority->value;
        $this->statusId = $this->task->status_id;
        $this->assigneeId = $this->task->assignee_id;
        $this->selectedAssignees = $this->task->assignees->pluck('id')->toArray();
        $this->dueDate = $this->task->due_date?->format('Y-m-d');
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
            'dueDate' => ['nullable', 'date'],
        ]);

        $action = new UpdateTask();
        $task = $action->execute($this->task, [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status_id' => $this->statusId,
            'assignee_id' => $this->assigneeId,
            'due_date' => $this->dueDate,
        ]);

        // Sync multiple assignees
        $this->task->assignees()->sync($this->selectedAssignees);

        $this->dispatch('task-updated');
        $this->task = $this->task->fresh();

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

    public function close(): void
    {
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $workspaceMembers = User::whereHas('workspaces', function ($q) {
            $q->where('workspaces.id', $this->task->project->workspace_id);
        })->get();

        return view('livewire.tasks.task-detail', [
            'workspaceMembers' => $workspaceMembers,
            'projectStatuses' => $this->task->project->statuses,
        ]);
    }
}
