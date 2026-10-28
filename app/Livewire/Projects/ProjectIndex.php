<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectIndex extends Component
{
    use WithPagination;

    public function archiveProject(int $projectId): void
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('update', $project);

        $project->update(['archived_at' => now()]);

        session()->flash('success', 'Project archived successfully.');
    }

    public function unarchiveProject(int $projectId): void
    {
        $project = Project::withTrashed()->findOrFail($projectId);
        $this->authorize('update', $project);

        $project->update(['archived_at' => null]);

        session()->flash('success', 'Project restored successfully.');
    }

    public function render()
    {
        // Get all projects from workspaces the user is a member of (or all if admin)
        $workspaceIds = auth()->user()->hasRole('Admin')
            ? \App\Models\Workspace::pluck('id')
            : auth()->user()->workspaces()->pluck('workspaces.id');

        return view('livewire.projects.project-index', [
            'projects' => Project::whereIn('workspace_id', $workspaceIds)
                ->whereNull('archived_at')
                ->with(['owner', 'workspace'])
                ->withCount('tasks')
                ->latest()
                ->paginate(12),
        ])->layout('components.layouts.app');
    }
}
