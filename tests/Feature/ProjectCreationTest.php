<?php

declare(strict_types=1);

use App\Actions\Projects\CreateProject;
use App\Enums\StatusType;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('creating a project auto creates default statuses', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

    $action = new CreateProject;
    $project = $action->handle([
        'workspace_id' => $workspace->id,
        'name' => 'Test Project',
        'description' => 'Test Description',
    ], $user);

    expect($project->statuses()->count())->toBe(4);
    expect($project->statuses()->where('name', 'To Do')->first())->not->toBeNull();
    expect($project->statuses()->where('name', 'In Progress')->first())->not->toBeNull();
    expect($project->statuses()->where('name', 'In Review')->first())->not->toBeNull();
    expect($project->statuses()->where('name', 'Done')->first())->not->toBeNull();

    $doneStatus = $project->statuses()->where('name', 'Done')->first();
    expect($doneStatus->type)->toBe(StatusType::Done);
});
