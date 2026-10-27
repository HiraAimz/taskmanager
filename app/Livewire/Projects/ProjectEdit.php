<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\Projects\UpdateProject;
use App\Models\Project;
use Livewire\Component;

class ProjectEdit extends Component
{
    public Project $project;

    public string $name = '';

    public string $description = '';

    public string $color = '#3B82F6';

    public string $icon = '';

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);

        $this->project = $project;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->color = $project->color;
        $this->icon = $project->icon ?? '';
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
        $this->authorize('update', $this->project);

        $this->validate();

        $action = new UpdateProject();
        $action->handle($this->project, [
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ]);

        session()->flash('success', 'Project updated successfully.');

        return redirect()->route('projects.index', $this->project->workspace);
    }

    public function render()
    {
        return view('livewire.projects.project-edit')->layout('components.layouts.app');
    }
}
