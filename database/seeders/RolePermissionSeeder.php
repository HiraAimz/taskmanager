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

        // Comprehensive granular permissions matrix
        $permissions = [
            // Workspaces
            'manage-workspaces',
            'workspace.view',
            'workspace.create',
            'workspace.edit',
            'workspace.delete',
            'workspace.manage_members',

            // Projects
            'manage-projects',
            'project.view',
            'project.create',
            'project.edit',
            'project.delete',
            'project.manage_members',

            // Tasks
            'create-task',
            'edit-task',
            'delete-task',
            'assign-task',
            'task.view',
            'task.create',
            'task.edit',
            'task.delete',
            'task.change_status',
            'task.assign',

            // Comments & Attachments
            'comment-task',
            'comment.create',
            'comment.delete',
            'attachment.upload',
            'attachment.delete',

            // Direct Messaging
            'message.access',
            'message.send',

            // Users
            'manage-users',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.manage_roles',

            // Roles & Permissions
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.assign_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create default system roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $member = Role::firstOrCreate(['name' => 'Member']);

        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // Manager gets full project & task management
        $manager->syncPermissions([
            'manage-workspaces',
            'workspace.view',
            'workspace.edit',
            'workspace.manage_members',
            'manage-projects',
            'project.view',
            'project.create',
            'project.edit',
            'project.delete',
            'project.manage_members',
            'create-task',
            'edit-task',
            'delete-task',
            'assign-task',
            'task.view',
            'task.create',
            'task.edit',
            'task.delete',
            'task.change_status',
            'task.assign',
            'comment-task',
            'comment.create',
            'comment.delete',
            'attachment.upload',
            'attachment.delete',
            'message.access',
            'message.send',
        ]);

        // Member gets collaborative task actions, status updates, messaging
        $member->syncPermissions([
            'workspace.view',
            'project.view',
            'create-task',
            'edit-task',
            'task.view',
            'task.create',
            'task.edit',
            'task.change_status',
            'comment-task',
            'comment.create',
            'attachment.upload',
            'message.access',
            'message.send',
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
