<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

test('admin can create projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    expect($user->can('create', Project::class))->toBeTrue();
});

test('manager can create projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Manager');

    expect($user->can('create', Project::class))->toBeTrue();
});

test('member cannot create projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Member');

    expect($user->can('create', Project::class))->toBeFalse();
});

test('admin can update any project in their workspace', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'Admin']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('update', $project))->toBeTrue();
});

test('manager can update projects in their workspace', function () {
    $user = User::factory()->create();
    $user->assignRole('Manager');
    
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($user->id, ['role' => 'Manager']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('update', $project))->toBeTrue();
});

test('member cannot update projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Member');
    
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($user->id, ['role' => 'Member']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('update', $project))->toBeFalse();
});

test('user cannot update project in workspace they are not member of', function () {
    $user = User::factory()->create();
    $user->assignRole('Manager');
    
    $workspace = Workspace::factory()->create();
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('update', $project))->toBeFalse();
});

test('admin can delete projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'Admin']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('delete', $project))->toBeTrue();
});

test('manager can delete projects in their workspace', function () {
    $user = User::factory()->create();
    $user->assignRole('Manager');
    
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($user->id, ['role' => 'Manager']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('delete', $project))->toBeTrue();
});

test('member cannot delete projects', function () {
    $user = User::factory()->create();
    $user->assignRole('Member');
    
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($user->id, ['role' => 'Member']);
    
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    expect($user->can('delete', $project))->toBeFalse();
});
