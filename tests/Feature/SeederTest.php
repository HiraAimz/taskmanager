<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('seeder creates roles permissions and admin', function () {
    $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

    expect(Role::count())->toBe(3);
    expect(Permission::count())->toBe(8);
    expect(User::role('Admin')->count())->toBeGreaterThanOrEqual(1);
});
