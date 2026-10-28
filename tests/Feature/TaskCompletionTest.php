<?php

declare(strict_types=1);

use App\Enums\StatusType;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('completing a task sets completed_at', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $user->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);

    $doneStatus = $project->statuses()->where('type', StatusType::Done)->first();

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $project->statuses()->where('type', StatusType::Todo)->first()->id,
        'creator_id' => $user->id,
        'completed_at' => null,
    ]);

    expect($task->completed_at)->toBeNull();

    $task->update([
        'status_id' => $doneStatus->id,
        'completed_at' => now(),
    ]);

    $task->refresh();

    expect($task->completed_at)->not->toBeNull();
    expect($task->status->type)->toBe(StatusType::Done);
});

test('reopening a task clears completed_at', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $user->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);

    $doneStatus = $project->statuses()->where('type', StatusType::Done)->first();
    $todoStatus = $project->statuses()->where('type', StatusType::Todo)->first();

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $doneStatus->id,
        'creator_id' => $user->id,
        'completed_at' => now(),
    ]);

    expect($task->completed_at)->not->toBeNull();

    $task->update([
        'status_id' => $todoStatus->id,
        'completed_at' => null,
    ]);

    $task->refresh();

    expect($task->completed_at)->toBeNull();
    expect($task->status->type)->toBe(StatusType::Todo);
});

test('user can add a comment to a task without field body error', function () {
    $user = User::factory()->create();
    $user->assignRole('Member');

    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'creator_id' => $user->id,
    ]);

    $this->actingAs($user);

    \Livewire\Livewire::test(\App\Livewire\Comments\CommentList::class, ['task' => $task])
        ->set('newComment', 'This is a test comment')
        ->call('addComment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'commentable_type' => Task::class,
        'commentable_id' => $task->id,
        'user_id' => $user->id,
        'body' => 'This is a test comment',
    ]);
});
