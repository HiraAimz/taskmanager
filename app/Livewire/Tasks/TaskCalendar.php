<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Project;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskCalendar extends Component
{
    public Project $project;

    public int $year;

    public int $month;

    public ?int $selectedTaskId = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
        $this->year = now()->year;
        $this->month = now()->month;
    }

    #[On('task-created')]
    #[On('task-updated')]
    #[On('task-deleted')]
    public function refreshCalendar(): void
    {
        // Trigger re-render
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function today(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function selectTask(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
    }

    public function closeTaskDetail(): void
    {
        $this->selectedTaskId = null;
    }

    public function render()
    {
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all tasks with due dates in this month
        $tasks = $this->project->tasks()
            ->with(['assignee', 'status', 'tags'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($task) => $task->due_date->format('Y-m-d'));

        // Build calendar grid
        $calendarStart = $startDate->copy()->startOfWeek();
        $calendarEnd = $endDate->copy()->endOfWeek();
        $weeks = [];
        $currentDate = $calendarStart->copy();

        while ($currentDate <= $calendarEnd) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $week[] = [
                    'date' => $currentDate->copy(),
                    'tasks' => $tasks->get($dateKey, collect()),
                    'isCurrentMonth' => $currentDate->month === $this->month,
                    'isToday' => $currentDate->isToday(),
                ];
                $currentDate->addDay();
            }
            $weeks[] = $week;
        }

        return view('livewire.tasks.task-calendar', [
            'weeks' => $weeks,
            'monthName' => $startDate->format('F Y'),
        ])->layout('components.layouts.app');
    }
}
