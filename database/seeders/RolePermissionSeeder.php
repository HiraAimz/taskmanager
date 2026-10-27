<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-workspaces',
            'manage-projects',
            'create-task',
            'edit-task',
            'delete-task',
            'assign-task',
            'comment-task',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $member = Role::firstOrCreate(['name' => 'Member']);

        // Assign permissions to roles
        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // Manager gets workspace and task management permissions
        $manager->syncPermissions([
            'manage-workspaces',
            'manage-projects',
            'create-task',
            'edit-task',
            'delete-task',
            'assign-task',
            'comment-task',
        ]);

        // Member gets basic task permissions
        $member->syncPermissions([
            'create-task',
            'edit-task',
            'comment-task',
        ]);

        // Create default admin user
        $adminUser = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Admin User',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ]
        );

        if (! $adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }
    }
}
