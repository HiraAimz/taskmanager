<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Enums\StatusType;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class WorkspaceProgress extends Component
{
    use WithPagination;

    public Workspace $workspace;

    // Filters
    public ?int $selectedProjectId = null;

    public ?int $selectedMemberId = null;

    public string $selectedStatusType = '';

    public string $selectedPriority = '';

    public bool $filterOverdueOnly = false;

    public bool $filterUpcomingOnly = false;

    public string $search = '';

    public string $dateRangeType = 'all';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public string $dateField = 'due_date';

    // Modals
    public ?int $inspectingMemberId = null;

    public ?int $selectedTaskId = null;

    // Sorting
    public string $sortField = 'due_date';

    public string $sortDirection = 'asc';

    public function mount(Workspace $workspace): void
    {
        $this->authorize('viewProgress', $workspace);

        $this->workspace = $workspace->loadMissing(['projects', 'owner']);
    }

    public function switchWorkspace(int $workspaceId): void
    {
        $targetWorkspace = Workspace::findOrFail($workspaceId);
        $this->authorize('viewProgress', $targetWorkspace);

        $this->workspace = $targetWorkspace->loadMissing(['projects', 'owner']);
        $this->clearFilters();
    }

    public function clearFilters(): void
    {
        $this->selectedProjectId = null;
        $this->selectedMemberId = null;
        $this->selectedStatusType = '';
        $this->selectedPriority = '';
        $this->filterOverdueOnly = false;
        $this->filterUpcomingOnly = false;
        $this->search = '';
        $this->dateRangeType = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->inspectingMemberId = null;
        $this->resetPage();
    }

    public function updatedDateRangeType(string $value): void
    {
        $today = Carbon::today();

        if ($value === 'today') {
            $this->dateFrom = $today->toDateString();
            $this->dateTo = $today->toDateString();
        } elseif ($value === 'this_week') {
            $this->dateFrom = $today->copy()->startOfWeek()->toDateString();
            $this->dateTo = $today->copy()->endOfWeek()->toDateString();
        } elseif ($value === 'this_month') {
            $this->dateFrom = $today->copy()->startOfMonth()->toDateString();
            $this->dateTo = $today->copy()->endOfMonth()->toDateString();
        } elseif ($value === 'all') {
            $this->dateFrom = null;
            $this->dateTo = null;
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedProjectId(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedMemberId(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedStatusType(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedPriority(): void
    {
        $this->resetPage();
    }

    public function toggleOverdueOnly(): void
    {
        $this->filterOverdueOnly = !$this->filterOverdueOnly;
        if ($this->filterOverdueOnly) {
            $this->filterUpcomingOnly = false;
        }
        $this->resetPage();
    }

    public function toggleUpcomingOnly(): void
    {
        $this->filterUpcomingOnly = !$this->filterUpcomingOnly;
        if ($this->filterUpcomingOnly) {
            $this->filterOverdueOnly = false;
        }
        $this->resetPage();
    }

    public function inspectMember(?int $memberId): void
    {
        $this->inspectingMemberId = $memberId;
    }

    public function closeMemberInspection(): void
    {
        $this->inspectingMemberId = null;
    }

    public function filterByMember(int $memberId): void
    {
        $this->selectedMemberId = $memberId;
        $this->inspectingMemberId = null;
        $this->resetPage();
    }

    public function selectTask(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
    }

    public function closeTaskDetail(): void
    {
        $this->selectedTaskId = null;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[On('task-updated')]
    #[On('task-deleted')]
    #[On('task-created')]
    public function refreshData(): void
    {
        // Re-renders view reactively
    }

    public function render()
    {
        $today = Carbon::today();
        $authUser = auth()->user();

        // 1. Available Workspaces for Switcher (Admins see all; owners see their owned workspaces)
        $switchableWorkspaces = $authUser->hasRole('Admin')
            ? Workspace::orderBy('name')->get()
            : Workspace::where('owner_id', $authUser->id)
                ->orWhereHas('users', fn ($q) => $q->where('users.id', $authUser->id))
                ->orderBy('name')
                ->get();

        // 2. Members of this Workspace (including owner)
        $members = User::where(function ($q) {
            $q->where('id', $this->workspace->owner_id)
              ->orWhereHas('workspaces', fn ($sq) => $sq->where('workspaces.id', $this->workspace->id));
        })->orderBy('name')->get();

        // 3. Projects in this Workspace
        $projects = $this->workspace->projects()->orderBy('name')->get();
        $projectIds = $projects->pluck('id')->all();

        // 4. Fetch All Tasks in this Workspace for Aggregate Progress Calculations
        // We use an optimized single query with status & assignees
        $allTasksQuery = Task::whereIn('project_id', $projectIds)
            ->with(['status', 'assignees:id'])
            ->select(['id', 'project_id', 'status_id', 'assignee_id', 'title', 'priority', 'due_date', 'completed_at', 'created_at']);

        // Apply date range filter to calculations if active
        if ($this->dateFrom && $this->dateTo) {
            $field = in_array($this->dateField, ['due_date', 'completed_at', 'created_at']) ? $this->dateField : 'due_date';
            $allTasksQuery->whereBetween($field, [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ]);
        }

        $allWorkspaceTasks = $allTasksQuery->get();

        // 5. Compute Workspace-Wide Totals (each task counted once)
        $workspaceStats = [
            'total' => $allWorkspaceTasks->count(),
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'overdue' => 0,
            'approaching' => 0,
            'completion_rate' => 0,
        ];

        foreach ($allWorkspaceTasks as $task) {
            $isDone = $task->completed_at !== null || $task->status?->type === StatusType::Done || $task->status?->type === 'done';
            $isOverdue = $task->due_date && $task->due_date < $today && !$isDone;
            $isApproaching = $task->due_date && $task->due_date >= $today && $task->due_date <= $today->copy()->addDays(3) && !$isDone;

            if ($isDone) {
                $workspaceStats['completed']++;
            } elseif ($isOverdue) {
                $workspaceStats['overdue']++;
            } elseif ($task->status?->type === StatusType::InProgress || $task->status?->type === 'in_progress') {
                $workspaceStats['in_progress']++;
            } else {
                $workspaceStats['pending']++;
            }

            if ($isApproaching) {
                $workspaceStats['approaching']++;
            }
        }

        if ($workspaceStats['total'] > 0) {
            $workspaceStats['completion_rate'] = (int) round(($workspaceStats['completed'] / $workspaceStats['total']) * 100);
        }

        // 6. Compute Member Progress Metrics (each task counted once per member)
        $memberMetrics = [];
        foreach ($members as $member) {
            $memberMetrics[$member->id] = [
                'member' => $member,
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'pending' => 0,
                'overdue' => 0,
                'completion_rate' => 0,
            ];
        }

        foreach ($allWorkspaceTasks as $task) {
            $isDone = $task->completed_at !== null || $task->status?->type === StatusType::Done || $task->status?->type === 'done';
            $isOverdue = $task->due_date && $task->due_date < $today && !$isDone;

            // Unique assignees for this task (primary + collaborators)
            $assigneeIds = array_filter(array_unique(array_merge(
                [$task->assignee_id],
                $task->assignees->pluck('id')->all()
            )));

            foreach ($assigneeIds as $uid) {
                if (!isset($memberMetrics[$uid])) {
                    continue;
                }

                $memberMetrics[$uid]['total']++;

                if ($isDone) {
                    $memberMetrics[$uid]['completed']++;
                } elseif ($isOverdue) {
                    $memberMetrics[$uid]['overdue']++;
                } elseif ($task->status?->type === StatusType::InProgress || $task->status?->type === 'in_progress') {
                    $memberMetrics[$uid]['in_progress']++;
                } else {
                    $memberMetrics[$uid]['pending']++;
                }
            }
        }

        // Calculate member completion rates (avoiding division-by-zero)
        foreach ($memberMetrics as $uid => &$data) {
            if ($data['total'] > 0) {
                $data['completion_rate'] = (int) round(($data['completed'] / $data['total']) * 100);
            } else {
                $data['completion_rate'] = 0;
            }
        }
        unset($data);

        // Sort member metrics by total tasks descending, then completion rate descending
        uasort($memberMetrics, function ($a, $b) {
            if ($a['total'] === $b['total']) {
                return $b['completion_rate'] <=> $a['completion_rate'];
            }
            return $b['total'] <=> $a['total'];
        });

        // 7. Member Being Inspected Details
        $inspectingMemberData = null;
        $inspectingMemberTasks = collect();
        if ($this->inspectingMemberId && isset($memberMetrics[$this->inspectingMemberId])) {
            $inspectingMemberData = $memberMetrics[$this->inspectingMemberId];
            $inspectingMemberTasks = Task::whereIn('project_id', $projectIds)
                ->where(function ($q) {
                    $q->where('assignee_id', $this->inspectingMemberId)
                      ->orWhereHas('assignees', fn ($sq) => $sq->where('users.id', $this->inspectingMemberId));
                })
                ->with(['project.workspace', 'status'])
                ->orderBy('due_date', 'asc')
                ->take(30)
                ->get();
        }

        // 8. Server-Side Filtered & Paginated Workspace Tasks Table
        $tasksTableQuery = Task::whereIn('project_id', $projectIds)
            ->with(['project.workspace', 'status', 'assignee', 'assignees']);

        if ($this->selectedProjectId) {
            $tasksTableQuery->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedMemberId) {
            $tasksTableQuery->where(function ($q) {
                $q->where('assignee_id', $this->selectedMemberId)
                  ->orWhereHas('assignees', fn ($sq) => $sq->where('users.id', $this->selectedMemberId));
            });
        }

        if ($this->selectedStatusType) {
            $tasksTableQuery->whereHas('status', fn ($q) => $q->where('type', $this->selectedStatusType));
        }

        if ($this->selectedPriority) {
            $tasksTableQuery->where('priority', $this->selectedPriority);
        }

        if ($this->filterOverdueOnly) {
            $tasksTableQuery->where('due_date', '<', $today)
                ->whereNull('completed_at')
                ->whereHas('status', fn ($q) => $q->where('type', '!=', 'done'));
        }

        if ($this->filterUpcomingOnly) {
            $tasksTableQuery->whereBetween('due_date', [$today, $today->copy()->addDays(3)->endOfDay()])
                ->whereNull('completed_at')
                ->whereHas('status', fn ($q) => $q->where('type', '!=', 'done'));
        }

        if ($this->dateFrom && $this->dateTo) {
            $field = in_array($this->dateField, ['due_date', 'completed_at', 'created_at']) ? $this->dateField : 'due_date';
            $tasksTableQuery->whereBetween($field, [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ]);
        }

        if (trim($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $tasksTableQuery->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        // Sorting
        if ($this->sortField === 'due_date') {
            $tasksTableQuery->orderByRaw('due_date IS NULL, due_date ' . $this->sortDirection);
        } elseif (in_array($this->sortField, ['title', 'priority', 'created_at'])) {
            $tasksTableQuery->orderBy($this->sortField, $this->sortDirection);
        } else {
            $tasksTableQuery->orderBy('id', 'desc');
        }

        $workspaceTasks = $tasksTableQuery->paginate(12);

        return view('livewire.workspaces.workspace-progress', [
            'switchableWorkspaces' => $switchableWorkspaces,
            'members' => $members,
            'projects' => $projects,
            'workspaceStats' => $workspaceStats,
            'memberMetrics' => $memberMetrics,
            'workspaceTasks' => $workspaceTasks,
            'inspectingMemberData' => $inspectingMemberData,
            'inspectingMemberTasks' => $inspectingMemberTasks,
            'today' => $today,
        ])->layout('components.layouts.app', ['title' => 'Workspace Progress - ' . $this->workspace->name]);
    }
}
