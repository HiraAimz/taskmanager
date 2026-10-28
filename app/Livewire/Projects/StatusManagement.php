<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\Statuses\DeleteStatus;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class StatusManagement extends Component
{
    public Project $project;

    public string $newStatusName = '';

    public string $newStatusColor = '#6B7280';

    public ?int $deletingStatusId = null;

    public ?int $moveToStatusId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project->loadMissing(['workspace.projects']);
    }

    public function addStatus(): void
    {
        $this->authorize('create', Status::class);

        $this->validate([
            'newStatusName' => 'required|string|max:255',
            'newStatusColor' => 'required|string|size:7',
        ]);

        $maxPosition = $this->project->statuses()->max('position') ?? 0;

        Status::create([
            'project_id' => $this->project->id,
            'name' => $this->newStatusName,
            'color' => $this->newStatusColor,
            'type' => 'todo',
            'position' => $maxPosition + 1,
        ]);

        $this->newStatusName = '';
        $this->newStatusColor = '#6B7280';

        session()->flash('success', 'Status added successfully.');
    }

    public function updateStatus(int $statusId, string $field, string $value): void
    {
        $status = $this->project->statuses()->findOrFail($statusId);
        $this->authorize('update', $status);

        $status->update([$field => $value]);
    }

    public function confirmDelete(int $statusId): void
    {
        $status = $this->project->statuses()->findOrFail($statusId);
        $this->authorize('delete', $status);

        $this->deletingStatusId = $statusId;
        $this->moveToStatusId = null;
    }

    public function deleteStatus(): void
    {
        if (! $this->deletingStatusId) {
            return;
        }

        $status = $this->project->statuses()->findOrFail($this->deletingStatusId);
        $this->authorize('delete', $status);

        try {
            $action = new DeleteStatus();
            $action->handle($status, $this->moveToStatusId);

            $this->deletingStatusId = null;
            $this->moveToStatusId = null;

            session()->flash('success', 'Status deleted successfully.');
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancelDelete(): void
    {
        $this->deletingStatusId = null;
        $this->moveToStatusId = null;
    }

    public function reorderStatus(int $statusId, string $direction): void
    {
        $status = $this->project->statuses()->findOrFail($statusId);
        $this->authorize('update', $status);

        $statuses = $this->project->statuses()->orderBy('position')->get();
        $currentIndex = $statuses->search(fn ($s) => $s->id === $statusId);

        if ($direction === 'up' && $currentIndex > 0) {
            $swapWith = $statuses[$currentIndex - 1];
            $tempPosition = $status->position;
            $status->update(['position' => $swapWith->position]);
            $swapWith->update(['position' => $tempPosition]);
        } elseif ($direction === 'down' && $currentIndex < $statuses->count() - 1) {
            $swapWith = $statuses[$currentIndex + 1];
            $tempPosition = $status->position;
            $status->update(['position' => $swapWith->position]);
            $swapWith->update(['position' => $tempPosition]);
        }
    }

    public function render()
    {
        $this->project->loadMissing(['workspace.projects']);

        return view('livewire.projects.status-management', [
            'statuses' => $this->project->statuses()->orderBy('position')->withCount('tasks')->get(),
            'project' => $this->project,
        ])->layout('components.layouts.app', ['project' => $this->project]);
    }
}
