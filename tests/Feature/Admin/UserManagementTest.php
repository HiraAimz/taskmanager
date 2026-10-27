<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin can access user management', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk();
});

test('manager cannot access user management', function () {
    $manager = User::factory()->create();
    $manager->assignRole('Manager');

    $this->actingAs($manager)
        ->get('/admin/users')
        ->assertForbidden();
});

test('member cannot access user management', function () {
    $member = User::factory()->create();
    $member->assignRole('Member');

    $this->actingAs($member)
        ->get('/admin/users')
        ->assertForbidden();
});

test('guest is redirected to login', function () {
    $this->get('/admin/users')
        ->assertRedirect('/login');
});
