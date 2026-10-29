<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\StatusType;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalProgress extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'tasks_count';

    public string $sortDirection = 'desc';

    public string $filterWorkload = 'all'; // all, has_overdue, high_completion

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('manage-workspaces'), 
            403
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterWorkload(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    public function render()
    {
        $today = Carbon::today();

        // 1. System-Wide Key Aggregate Metrics
        $totalWorkspaces = Workspace::count();
        $totalProjects = Project::count();
        $totalUsers = User::count();
        $totalTasks = Task::count();

        $completedTasks = Task::where(function ($q) {
            $q->whereNotNull('completed_at')
              ->orWhereHas('status', fn ($sq) => $sq->where('type', StatusType::Done)->orWhere('type', 'done'));
        })->count();

        $overdueTasks = Task::where('due_date', '<', $today)
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', 'done')->where('type', '!=', StatusType::Done))
            ->count();

        $globalCompletionRate = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        // 2. Query Workspaces with Counts and Metrics
        $workspacesQuery = Workspace::with(['owner', 'projects'])
            ->withCount(['projects', 'users']);

        if (trim($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $workspacesQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhereHas('owner', fn ($sq) => $sq->where('name', 'like', $term));
            });
        }

        $workspaces = $workspacesQuery->get();

        // Compute per-workspace metrics efficiently
        $workspaceData = [];
        foreach ($workspaces as $ws) {
            $projectIds = $ws->projects->pluck('id')->all();

            $wsTasks = Task::whereIn('project_id', $projectIds)
                ->with('status:id,type')
                ->select(['id', 'status_id', 'due_date', 'completed_at'])
                ->get();

            $wsTotal = $wsTasks->count();
            $wsCompleted = 0;
            $wsOverdue = 0;

            foreach ($wsTasks as $t) {
                $isDone = $t->completed_at !== null || $t->status?->type === StatusType::Done || $t->status?->type === 'done';
                if ($isDone) {
                    $wsCompleted++;
                } elseif ($t->due_date && $t->due_date < $today) {
                    $wsOverdue++;
                }
            }

            $rate = $wsTotal > 0 ? (int) round(($wsCompleted / $wsTotal) * 100) : 0;

            // Apply filterWorkload if active
            if ($this->filterWorkload === 'has_overdue' && $wsOverdue === 0) {
                continue;
            }
            if ($this->filterWorkload === 'high_completion' && $rate < 80) {
                continue;
            }

            $workspaceData[] = [
                'workspace' => $ws,
                'projects_count' => $ws->projects_count,
                'users_count' => max($ws->users_count, 1),
                'tasks_count' => $wsTotal,
                'completed_count' => $wsCompleted,
                'overdue_count' => $wsOverdue,
                'completion_rate' => $rate,
            ];
        }

        // Sort workspace comparison data
        usort($workspaceData, function ($a, $b) {
            $valA = $a[$this->sortField] ?? 0;
            $valB = $b[$this->sortField] ?? 0;

            if ($this->sortDirection === 'asc') {
                return $valA <=> $valB;
            }
            return $valB <=> $valA;
        });

        // Top 5 Overdue Members across the entire system
        $topOverdueMembers = Task::where('due_date', '<', $today)
            ->whereNull('completed_at')
            ->whereHas('status', fn ($sq) => $sq->where('type', '!=', 'done')->where('type', '!=', StatusType::Done))
            ->whereNotNull('assignee_id')
            ->with('assignee')
            ->selectRaw('assignee_id, count(*) as overdue_count')
            ->groupBy('assignee_id')
            ->orderByDesc('overdue_count')
            ->take(5)
            ->get();

        return view('livewire.admin.global-progress', [
            'totalWorkspaces' => $totalWorkspaces,
            'totalProjects' => $totalProjects,
            'totalUsers' => $totalUsers,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'overdueTasks' => $overdueTasks,
            'globalCompletionRate' => $globalCompletionRate,
            'workspaceData' => $workspaceData,
            'topOverdueMembers' => $topOverdueMembers,
        ])->layout('components.layouts.app', ['title' => 'Global Workspace Progress - Admin']);
    }
}
