<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin can update any task', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $workspace = Workspace::factory()->create(['owner_id' => $admin->id]);
    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $admin->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $admin->id,
    ]);

    $this->actingAs($admin);

    expect($admin->can('update', $task))->toBeTrue();
});

test('member cannot edit tasks that are not theirs', function () {
    $member1 = User::factory()->create();
    $member1->assignRole('Member');

    $member2 = User::factory()->create();
    $member2->assignRole('Member');

    $workspace = Workspace::factory()->create(['owner_id' => $member1->id]);
    $workspace->users()->attach([$member1->id, $member2->id]);

    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $member1->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $member1->id,
    ]);

    $this->actingAs($member2);

    expect($member2->can('update', $task))->toBeFalse();
});

test('member can edit tasks assigned to them', function () {
    $member = User::factory()->create();
    $member->assignRole('Member');

    $creator = User::factory()->create();
    $creator->assignRole('Manager');

    $workspace = Workspace::factory()->create(['owner_id' => $creator->id]);
    $workspace->users()->attach([$creator->id, $member->id]);

    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $creator->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $creator->id,
    ]);
    $task->assignees()->attach($member->id);

    $this->actingAs($member);

    expect($member->can('update', $task))->toBeTrue();
});

test('manager can manage tasks in their workspace', function () {
    $manager = User::factory()->create();
    $manager->assignRole('Manager');

    $workspace = Workspace::factory()->create(['owner_id' => $manager->id]);
    $workspace->users()->attach($manager->id);

    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $manager->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $manager->id,
    ]);

    $this->actingAs($manager);

    expect($manager->can('update', $task))->toBeTrue();
    expect($manager->can('delete', $task))->toBeTrue();
});

test('member can change status of task assigned to them via assignee_id', function () {
    $member = User::factory()->create();
    $member->assignRole('Member');

    $creator = User::factory()->create();
    $creator->assignRole('Manager');

    $workspace = Workspace::factory()->create(['owner_id' => $creator->id]);
    $workspace->users()->attach([$creator->id, $member->id]);

    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $creator->id]);
    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $creator->id,
        'assignee_id' => $member->id,
    ]);

    $this->actingAs($member);

    expect($member->can('changeStatus', $task))->toBeTrue();
});

test('project member with task.change_status permission can change status of project task', function () {
    $member = User::factory()->create();
    $member->assignRole('Member');
    $member->givePermissionTo('task.change_status');

    $creator = User::factory()->create();
    $creator->assignRole('Manager');

    $workspace = Workspace::factory()->create(['owner_id' => $creator->id]);
    $workspace->users()->attach([$creator->id, $member->id]);

    $project = Project::factory()->create(['workspace_id' => $workspace->id, 'owner_id' => $creator->id]);
    $project->members()->attach($member->id, ['role' => 'member']);

    $list = TaskList::factory()->create(['project_id' => $project->id]);
    $status = $project->statuses()->first();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'task_list_id' => $list->id,
        'status_id' => $status->id,
        'creator_id' => $creator->id,
    ]);

    $this->actingAs($member);

    expect($member->can('changeStatus', $task))->toBeTrue();
});
