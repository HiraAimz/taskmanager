<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Livewire\Tasks\TaskBoard;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskBoardViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        // Explicitly enforce strict lazy loading prevention in tests
        Model::preventLazyLoading(true);
    }

    public function test_project_board_renders_without_lazy_loading_violation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->users()->attach($user->id, ['role' => 'owner']);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $user->id,
        ]);

        $status = $project->statuses()->first();

        // Create tasks in this project
        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
            'creator_id' => $user->id,
            'assignee_id' => $user->id,
        ]);

        $this->actingAs($user);

        // HTTP GET request to the board
        $response = $this->get(route('projects.board', $project));
        $response->assertOk();

        // Livewire component test
        Livewire::test(TaskBoard::class, ['project' => $project])
            ->assertOk()
            ->assertSee($project->name);
    }

    public function test_updating_task_status_on_board_does_not_violate_lazy_loading(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->users()->attach($user->id, ['role' => 'owner']);

        $project = Project::factory()->create([
            'workspace_id' => $workspace->id,
            'owner_id' => $user->id,
        ]);

        $statuses = $project->statuses;
        $status1 = $statuses[0];
        $status2 = $statuses[1];

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status1->id,
            'creator_id' => $user->id,
            'assignee_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(TaskBoard::class, ['project' => $project])
            ->call('updateTaskStatus', $task->id, $status2->id)
            ->assertHasNoErrors();

        $this->assertEquals($status2->id, $task->fresh()->status_id);
    }
}
