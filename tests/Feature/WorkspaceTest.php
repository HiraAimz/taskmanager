<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('member cannot see workspace they dont belong to', function () {
    $member1 = User::factory()->create();
    $member1->assignRole('Member');

    $member2 = User::factory()->create();
    $member2->assignRole('Member');

    $workspace = Workspace::factory()->create(['owner_id' => $member1->id]);
    $workspace->users()->attach($member1->id);

    $this->actingAs($member2)
        ->get('/workspaces')
        ->assertOk()
        ->assertDontSee($workspace->name);
});

test('admin can see all workspaces', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $otherUser = User::factory()->create();
    $otherUser->assignRole('Member');

    $workspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);
    $workspace->users()->attach($otherUser->id);

    $this->actingAs($admin)
        ->get('/workspaces')
        ->assertOk()
        ->assertSee($workspace->name);
});

test('member can see their own workspace', function () {
    $member = User::factory()->create();
    $member->assignRole('Member');

    $workspace = Workspace::factory()->create(['owner_id' => $member->id]);
    $workspace->users()->attach($member->id);

    $this->actingAs($member)
        ->get('/workspaces')
        ->assertOk()
        ->assertSee($workspace->name);
});

test('workspace index renders properly with projects without lazy loading violation', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');

    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->users()->attach($user->id);

    \App\Models\Project::factory()->count(3)->create([
        'workspace_id' => $workspace->id,
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get('/workspaces')
        ->assertOk()
        ->assertSee($workspace->name)
        ->assertSee('3 projects');
});
