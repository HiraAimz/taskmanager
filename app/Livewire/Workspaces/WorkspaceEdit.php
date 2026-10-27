<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Models\User;
use App\Models\Workspace;
use Livewire\Component;

class WorkspaceEdit extends Component
{
    public Workspace $workspace;

    public string $name = '';

    public string $description = '';

    public ?int $selectedUserId = null;

    public function mount(Workspace $workspace): void
    {
        $this->authorize('update', $workspace);

        $this->workspace = $workspace;
        $this->name = $workspace->name;
        $this->description = $workspace->description ?? '';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function update(): void
    {
        $this->authorize('update', $this->workspace);

        $this->validate();

        $this->workspace->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Workspace updated successfully.');
    }

    public function addMember(): void
    {
        $this->authorize('addMember', $this->workspace);

        if ($this->selectedUserId) {
            $this->workspace->users()->syncWithoutDetaching([$this->selectedUserId]);
            $this->selectedUserId = null;
            session()->flash('success', 'Member added successfully.');
        }
    }

    public function removeMember(int $userId): void
    {
        $this->authorize('removeMember', $this->workspace);

        $this->workspace->users()->detach($userId);
        session()->flash('success', 'Member removed successfully.');
    }

    public function render()
    {
        $currentMemberIds = $this->workspace->users->pluck('id')->toArray();
        $availableUsers = User::whereNotIn('id', $currentMemberIds)->get();

        return view('livewire.workspaces.workspace-edit', [
            'availableUsers' => $availableUsers,
        ])->layout('components.layouts.app');
    }
}
