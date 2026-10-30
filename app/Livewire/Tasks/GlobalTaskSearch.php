<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Task;
use Livewire\Component;

class GlobalTaskSearch extends Component
{
    public bool $isOpen = false;

    public string $query = '';

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
    }

    public function render()
    {
        $term = trim($this->query);
        $tasks = collect();

        if ($this->isOpen && mb_strlen($term) >= 2) {
            $user = auth()->user();
            $pattern = '%'.addcslashes($term, '\\%_').'%';
            $tasks = Task::query()
                ->with(['project', 'status'])
                ->where(function ($query) use ($pattern) {
                    $query->where('title', 'like', $pattern)
                        ->orWhere('description', 'like', $pattern);
                })
                ->when(! $user->hasRole('Admin'), function ($query) use ($user) {
                    $query->whereHas('project.workspace', function ($workspaceQuery) use ($user) {
                        $workspaceQuery->where(function ($accessQuery) use ($user) {
                            $accessQuery
                                ->where('owner_id', $user->id)
                                ->orWhereHas('users', fn ($memberQuery) => $memberQuery->where('users.id', $user->id));
                        });
                    });
                })
                ->latest('tasks.updated_at')
                ->limit(8)
                ->get();
        }

        return view('livewire.tasks.global-task-search', [
            'tasks' => $tasks,
            'hasSearchTerm' => mb_strlen($term) >= 2,
        ]);
    }
}
