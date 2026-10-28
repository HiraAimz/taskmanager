<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\CreateTask;
use App\Models\Project;
use App\Models\Task;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskQuickCreate extends Component
{
    public Project $project;

    public bool $isOpen = false;

    public ?int $statusId = null;

    public string $title = '';

    public string $description = '';

    #[On('open-quick-create')]
    public function open(int $statusId): void
    {
        $this->isOpen = true;
        $this->statusId = $statusId;
        $this->title = '';
        $this->description = '';
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->statusId = null;
        $this->title = '';
        $this->description = '';
    }

    public function save(): void
    {
        $this->authorize('create', [Task::class, $this->project]);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $action = new CreateTask();
        $action->execute(
            [
                'title' => $this->title,
                'description' => $this->description,
                'assignee_id' => auth()->id(),
            ],
            $this->project->id,
            $this->statusId
        );

        $this->dispatch('task-created');
        $this->close();
    }

    public function render()
    {
        return view('livewire.tasks.task-quick-create');
    }
}
