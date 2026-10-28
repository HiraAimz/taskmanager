<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Actions\Projects\UpdateProject;
use App\Models\Project;
use App\Models\User;
use Livewire\Component;

class ProjectEdit extends Component
{
    public Project $project;

    public string $name = '';

    public string $description = '';

    public string $color = '#3B82F6';

    public string $icon = '';

    public ?int $selectedUserId = null;

    public string $selectedRole = 'member';

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);

        $this->project = $project->loadMissing(['workspace.projects', 'members']);
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

    public function addMember(): void
    {
        $this->authorize('manageMembers', $this->project);

        if (!$this->selectedUserId) {
            return;
        }

        // Verify the user belongs to the workspace
        $isWorkspaceMember = $this->project->workspace->users()
            ->where('users.id', $this->selectedUserId)
            ->exists();

        if (!$isWorkspaceMember) {
            session()->flash('error', 'User must be a member of the workspace before being added to this project.');
            return;
        }

        $this->project->members()->syncWithoutDetaching([
            $this->selectedUserId => ['role' => $this->selectedRole],
        ]);

        $this->selectedUserId = null;
        $this->selectedRole = 'member';

        session()->flash('success', 'Member added to project successfully.');
    }

    public function removeMember(int $userId): void
    {
        $this->authorize('manageMembers', $this->project);

        // Ownership safeguard: cannot remove project owner
        if ($userId === $this->project->owner_id) {
            session()->flash('error', 'Cannot remove the project owner from the project.');
            return;
        }

        $this->project->members()->detach($userId);

        session()->flash('success', 'Member removed from project.');
    }

    public function render()
    {
        $this->project->loadMissing(['workspace.projects', 'workspace.users', 'members']);

        // Eligible users: members of the workspace not yet in the project
        $currentMemberIds = $this->project->members->pluck('id')->toArray();
        $availableUsers = $this->project->workspace->users
            ->whereNotIn('id', $currentMemberIds);

        return view('livewire.projects.project-edit', [
            'project' => $this->project,
            'availableUsers' => $availableUsers,
            'projectMembers' => $this->project->members()->withPivot('role')->get(),
        ])->layout('components.layouts.app', ['project' => $this->project]);
    }
}
