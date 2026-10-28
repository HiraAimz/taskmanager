<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Models\Workspace;
use Livewire\Component;
use Livewire\WithPagination;

class WorkspaceIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $query = auth()->user()->hasRole('Admin')
            ? Workspace::with(['owner', 'projects'])->withCount('projects')
            : auth()->user()->workspaces()->with(['owner', 'projects'])->withCount('projects');

        return view('livewire.workspaces.workspace-index', [
            'workspaces' => $query->paginate(15),
        ])->layout('components.layouts.app');
    }
}
