<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Admin\GlobalProgress;
use App\Livewire\Workspaces\WorkspaceProgress;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkspaceProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_workspace_owner_can_access_progress_dashboard(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Member');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $this->actingAs($owner)
            ->get(route('workspaces.progress', $workspace))
            ->assertOk();
    }

    public function test_workspace_owner_cannot_access_unrelated_workspace_progress(): void
    {
        $owner1 = User::factory()->create();
        $owner1->assignRole('Member');
        $workspace1 = Workspace::factory()->create(['owner_id' => $owner1->id]);

        $owner2 = User::factory()->create();
        $owner2->assignRole('Member');
        $workspace2 = Workspace::factory()->create(['owner_id' => $owner2->id]);

        $this->actingAs($owner1)
            ->get(route('workspaces.progress', $workspace2))
            ->assertForbidden();
    }

    public function test_regular_member_without_permission_cannot_access_workspace_progress(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);

        $member = User::factory()->create();
        $member->assignRole('Member');
        $workspace->users()->attach($member->id);

        $this->actingAs($member)
            ->get(route('workspaces.progress', $workspace))
            ->assertForbidden();
    }

    public function test_system_admin_can_access_global_progress_and_any_workspace(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($admin);

        // Global progress overview
        $this->get(route('admin.progress'))
            ->assertOk();

        // Any specific workspace progress
        $this->get(route('workspaces.progress', $workspace))
            ->assertOk();
    }

    public function test_workspace_owner_sees_tasks_across_all_projects_in_workspace(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Member');

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $otherUser = User::factory()->create();
        $workspace->users()->attach($otherUser->id);

        // Project created and owned by otherUser, owner is NOT a member in project_user
        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $otherUser->id,
        ]);

        $status = Status::factory()->create(['project_id' => $project->id, 'type' => 'todo']);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $otherUser->id,
            'assignee_id' => $otherUser->id,
            'title' => 'Secret Sub-project Task',
        ]);

        $this->actingAs($owner);

        // Owner can see the task on progress dashboard
        Livewire::test(WorkspaceProgress::class, ['workspace' => $workspace])
            ->assertSee('Secret Sub-project Task')
            ->assertSee($otherUser->name);

        // Owner can view the task details
        $this->assertTrue($owner->can('view', $task));

        // But owner cannot update or delete the task without edit permissions
        $this->assertFalse($owner->can('update', $task));
        $this->assertFalse($owner->can('delete', $task));
    }

    public function test_member_progress_calculation_and_percentages(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $member = User::factory()->create(['name' => 'Alice Worker']);
        $workspace->users()->attach($member->id);

        $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $owner->id]);

        $todoStatus = Status::factory()->create(['project_id' => $project->id, 'type' => 'todo']);
        $inProgressStatus = Status::factory()->create(['project_id' => $project->id, 'type' => 'in_progress']);
        $doneStatus = Status::factory()->create(['project_id' => $project->id, 'type' => 'done']);

        // Task 1: Completed
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $doneStatus->id,
            'assignee_id' => $member->id,
            'completed_at' => now(),
        ]);

        // Task 2: In Progress
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $inProgressStatus->id,
            'assignee_id' => $member->id,
            'due_date' => now()->addDays(5),
            'completed_at' => null,
        ]);

        // Task 3: Overdue
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $todoStatus->id,
            'assignee_id' => $member->id,
            'due_date' => now()->subDays(2),
            'completed_at' => null,
        ]);

        // Task 4: Pending / To-Do
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $todoStatus->id,
            'assignee_id' => $member->id,
            'due_date' => now()->addDays(3),
            'completed_at' => null,
        ]);

        $this->actingAs($owner);

        // 1 completed out of 4 assigned = 25%
        Livewire::test(WorkspaceProgress::class, ['workspace' => $workspace])
            ->assertSee('Alice Worker')
            ->assertSee('25%');
    }

    public function test_tasks_with_multiple_assignees_do_not_duplicate_counts(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $member1 = User::factory()->create(['name' => 'Collab 1']);
        $member2 = User::factory()->create(['name' => 'Collab 2']);
        $workspace->users()->attach([$member1->id, $member2->id]);

        $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $owner->id]);
        $doneStatus = Status::factory()->create(['project_id' => $project->id, 'type' => 'done']);

        // Single task assigned to member 1 as primary AND member 1 + member 2 in pivot
        $sharedTask = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $doneStatus->id,
            'assignee_id' => $member1->id,
            'completed_at' => now(),
            'title' => 'Multi-assigned Task',
        ]);
        $sharedTask->assignees()->attach([$member1->id, $member2->id]);

        $this->actingAs($owner);

        // Workspace total must be 1, not 2 or 3
        Livewire::test(WorkspaceProgress::class, ['workspace' => $workspace])
            ->assertViewHas('workspaceStats', function ($stats) {
                return $stats['total'] === 1 && $stats['completed'] === 1;
            })
            ->assertViewHas('memberMetrics', function ($metrics) use ($member1, $member2) {
                // Member 1 has total 1 (not duplicated)
                // Member 2 has total 1
                return $metrics[$member1->id]['total'] === 1
                    && $metrics[$member2->id]['total'] === 1;
            });
    }

    public function test_filters_return_scoped_results(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);

        $projectA = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $owner->id, 'name' => 'Alpha Project']);
        $projectB = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $owner->id, 'name' => 'Beta Project']);

        $statusA = Status::factory()->create(['project_id' => $projectA->id, 'type' => 'todo']);
        $statusB = Status::factory()->create(['project_id' => $projectB->id, 'type' => 'done']);

        Task::factory()->create([
            'project_id' => $projectA->id,
            'status_id' => $statusA->id,
            'title' => 'Alpha Unique Task',
            'priority' => 'urgent',
        ]);

        Task::factory()->create([
            'project_id' => $projectB->id,
            'status_id' => $statusB->id,
            'title' => 'Beta Unique Task',
            'priority' => 'low',
        ]);

        $this->actingAs($owner);

        // Filter by project
        Livewire::test(WorkspaceProgress::class, ['workspace' => $workspace])
            ->set('selectedProjectId', $projectA->id)
            ->assertSee('Alpha Unique Task')
            ->assertDontSee('Beta Unique Task');

        // Filter by priority
        Livewire::test(WorkspaceProgress::class, ['workspace' => $workspace])
            ->set('selectedPriority', 'urgent')
            ->assertSee('Alpha Unique Task')
            ->assertDontSee('Beta Unique Task');
    }

    public function test_admin_global_progress_component(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $ws1 = Workspace::factory()->create(['name' => 'Space One']);
        $ws2 = Workspace::factory()->create(['name' => 'Space Two']);

        $proj1 = Project::factory()->create(['workspace_id' => $ws1->id, 'owner_id' => $admin->id]);
        $status1 = Status::factory()->create(['project_id' => $proj1->id, 'type' => 'todo']);

        Task::factory()->create([
            'project_id' => $proj1->id,
            'status_id' => $status1->id,
            'due_date' => now()->subDays(3),
            'completed_at' => null,
            'title' => 'Overdue Item',
        ]);

        $this->actingAs($admin);

        Livewire::test(GlobalProgress::class)
            ->assertSee('Space One')
            ->assertSee('Space Two')
            ->set('filterWorkload', 'has_overdue')
            ->assertSee('Space One')
            ->assertDontSee('Space Two');
    }

    public function test_non_admin_cannot_access_global_progress_route(): void
    {
        $member = User::factory()->create();
        $member->assignRole('Member');

        $this->actingAs($member)
            ->get(route('admin.progress'))
            ->assertForbidden();
    }
}
