<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Actions\Workspaces\CreateWorkspace;
use App\Models\Workspace;
use Livewire\Component;

class WorkspaceCreate extends Component
{
    public string $name = '';

    public string $description = '';

    public function mount(): void
    {
        $this->authorize('create', Workspace::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function save(): mixed
    {
        $this->authorize('create', Workspace::class);

        $this->validate();

        $action = new CreateWorkspace;
        $action->handle([
            'name' => $this->name,
            'description' => $this->description,
        ], auth()->user());

        session()->flash('success', 'Workspace created successfully.');

        return redirect()->route('workspaces.index');
    }

    public function render()
    {
        return view('livewire.workspaces.workspace-create')->layout('components.layouts.app');
    }
}
