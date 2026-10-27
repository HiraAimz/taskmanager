<?php

declare(strict_types=1);

use App\Actions\DeleteStatus;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;

test('status deletion blocks when tasks exist', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'Admin']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);
    $status = Status::factory()->create(['project_id' => $project->id]);
    
    // Create a task assigned to this status
    Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => $status->id,
    ]);

    $action = new DeleteStatus();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot delete status with existing tasks');

    $action->execute($status, null);
});

test('status deletion moves tasks to another status when specified', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'Admin']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);
    $status1 = Status::factory()->create(['project_id' => $project->id]);
    $status2 = Status::factory()->create(['project_id' => $project->id]);
    
    // Create tasks assigned to status1
    $task1 = Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => $status1->id,
    ]);
    $task2 = Task::factory()->create([
        'project_id' => $project->id,
        'status_id' => $status1->id,
    ]);

    $action = new DeleteStatus();
    $action->execute($status1, $status2->id);

    // Tasks should now be moved to status2
    expect($task1->fresh()->status_id)->toBe($status2->id);
    expect($task2->fresh()->status_id)->toBe($status2->id);

    // Status1 should be deleted
    expect(Status::find($status1->id))->toBeNull();
});

test('status deletion succeeds when no tasks exist', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'Admin']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);
    $status = Status::factory()->create(['project_id' => $project->id]);

    $action = new DeleteStatus();
    $action->execute($status, null);

    expect(Status::find($status->id))->toBeNull();
});
