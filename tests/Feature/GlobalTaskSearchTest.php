<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Tasks\GlobalTaskSearch;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalTaskSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_search_opens_and_returns_accessible_task_matches(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('Member');
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->users()->attach($owner->id);
        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);
        $status = Status::factory()->create(['project_id' => $project->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $owner->id,
            'title' => 'Prepare launch checklist',
        ]);

        $this->actingAs($owner);

        Livewire::test(GlobalTaskSearch::class)
            ->assertDontSee('Prepare launch checklist')
            ->call('open')
            ->set('query', 'launch')
            ->assertSee('Prepare launch checklist')
            ->assertSee(route('projects.list', $project).'?task='.$task->id);

        $this->get(route('projects.list', $project).'?task='.$task->id)
            ->assertOk()
            ->assertSee('Prepare launch checklist')
            ->assertSee('window.history.replaceState', false)
            ->assertSee('url.searchParams.delete', false);
    }

    public function test_search_is_limited_to_workspaces_the_user_belongs_to_even_for_their_own_tasks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);
        $status = Status::factory()->create(['project_id' => $project->id]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $user->id,
            'title' => 'Confidential rollout task',
        ]);

        $this->actingAs($user);

        Livewire::test(GlobalTaskSearch::class)
            ->call('open')
            ->set('query', 'Confidential rollout')
            ->assertDontSee('Confidential rollout task')
            ->assertSee('No matching tasks found.');
    }

    public function test_non_admin_search_finds_tasks_across_their_workspaces(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user->id);
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);
        $status = Status::factory()->create(['project_id' => $project->id]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'title' => 'Workspace-wide planning task',
        ]);

        $this->actingAs($user);

        Livewire::test(GlobalTaskSearch::class)
            ->call('open')
            ->set('query', 'Workspace-wide planning')
            ->assertSee('Workspace-wide planning task');
    }

    public function test_admin_search_can_find_tasks_across_all_workspaces(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $owner->id,
        ]);
        $status = Status::factory()->create(['project_id' => $project->id]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $owner->id,
            'title' => 'Admin cross-workspace search task',
        ]);

        $this->actingAs($admin);

        Livewire::test(GlobalTaskSearch::class)
            ->call('open')
            ->set('query', 'Admin cross-workspace')
            ->assertSee('Admin cross-workspace search task');
    }
}
