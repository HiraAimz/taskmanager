<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\Projects\CreateProject;
use App\Models\Project;
use App\Models\Workspace;
use Livewire\Component;

class ProjectCreate extends Component
{
    public int $workspaceId;

    public string $name = '';

    public string $description = '';

    public string $color = '#3B82F6';

    public string $icon = '📁';

    public function mount(): void
    {
        $this->authorize('create', Project::class);
        
        // Get user's workspaces
        $workspaces = auth()->user()->workspaces;
        
        // Set first workspace as default if available
        $this->workspaceId = $workspaces->first()?->id ?? 0;
    }

    public function rules(): array
    {
        return [
            'workspaceId' => 'required|exists:workspaces,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|size:7',
            'icon' => 'nullable|string|max:10',
        ];
    }

    public function save(): mixed
    {
        $this->authorize('create', Project::class);

        $this->validate();

        $action = new CreateProject();
        $project = $action->handle([
            'workspace_id' => $this->workspaceId,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ], auth()->user());

        session()->flash('success', 'Project created successfully.');

        return redirect()->route('projects.index');
    }

    public function render()
    {
        $workspaces = auth()->user()->workspaces;

        return view('livewire.projects.project-create', [
            'workspaces' => $workspaces,
        ])->layout('components.layouts.app');
    }
}
