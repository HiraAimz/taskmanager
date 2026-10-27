<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\Projects\CreateProject;
use App\Models\Project;
use App\Models\Workspace;
use Livewire\Component;

class ProjectCreate extends Component
{
    public Workspace $workspace;

    public string $name = '';

    public string $description = '';

    public string $color = '#3B82F6';

    public string $icon = '📁';

    public function mount(Workspace $workspace): void
    {
        $this->authorize('create', Project::class);
        $this->workspace = $workspace;
    }

    public function rules(): array
    {
        return [
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
            'workspace_id' => $this->workspace->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ], auth()->user());

        session()->flash('success', 'Project created successfully.');

        return redirect()->route('projects.index', $this->workspace);
    }

    public function render()
    {
        return view('livewire.projects.project-create')->layout('components.layouts.app');
    }
}
