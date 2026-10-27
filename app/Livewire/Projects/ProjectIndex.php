<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Workspace;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectIndex extends Component
{
    use WithPagination;

    public Workspace $workspace;

    public function mount(Workspace $workspace): void
    {
        $this->authorize('view', $workspace);
        $this->workspace = $workspace;
    }

    public function archiveProject(int $projectId): void
    {
        $project = $this->workspace->projects()->findOrFail($projectId);
        $this->authorize('update', $project);

        $project->update(['archived_at' => now()]);

        session()->flash('success', 'Project archived successfully.');
    }

    public function unarchiveProject(int $projectId): void
    {
        $project = $this->workspace->projects()->withTrashed()->findOrFail($projectId);
        $this->authorize('update', $project);

        $project->update(['archived_at' => null]);

        session()->flash('success', 'Project restored successfully.');
    }

    public function render()
    {
        return view('livewire.projects.project-index', [
            'projects' => $this->workspace->projects()
                ->whereNull('archived_at')
                ->with('owner')
                ->withCount('tasks')
                ->paginate(12),
        ])->layout('components.layouts.app');
    }
}
