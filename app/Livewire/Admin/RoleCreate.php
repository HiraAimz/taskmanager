<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleCreate extends Component
{
    public string $name = '';

    public array $selectedPermissions = [];

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('role.create'), 
            403
        );
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:roles,name',
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'string|exists:permissions,name',
        ];
    }

    public function selectAllGroup(array $permissions): void
    {
        foreach ($permissions as $p) {
            if (!in_array($p, $this->selectedPermissions)) {
                $this->selectedPermissions[] = $p;
            }
        }
    }

    public function deselectAllGroup(array $permissions): void
    {
        $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $permissions));
    }

    public function toggleAll(): void
    {
        $allPermissions = Permission::pluck('name')->toArray();
        if (count($this->selectedPermissions) === count($allPermissions)) {
            $this->selectedPermissions = [];
        } else {
            $this->selectedPermissions = $allPermissions;
        }
    }

    public function save(): mixed
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('role.create'), 
            403
        );

        $this->validate();

        $role = Role::create(['name' => trim($this->name)]);
        $role->syncPermissions($this->selectedPermissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        session()->flash('success', "Role '{$role->name}' created successfully with " . count($this->selectedPermissions) . " permission(s).");

        return redirect()->route('admin.roles');
    }

    public function render()
    {
        $groupedPermissions = [
            'Workspaces' => [
                'workspace.view' => 'View Workspaces',
                'workspace.create' => 'Create Workspaces',
                'workspace.edit' => 'Edit Workspaces',
                'workspace.delete' => 'Delete Workspaces',
                'workspace.manage_members' => 'Manage Workspace Members',
                'manage-workspaces' => 'Full Workspace Administration (Legacy)',
            ],
            'Projects' => [
                'project.view' => 'View Projects',
                'project.create' => 'Create Projects',
                'project.edit' => 'Edit Projects',
                'project.delete' => 'Delete Projects',
                'project.manage_members' => 'Manage Project Members',
                'manage-projects' => 'Full Project Administration (Legacy)',
            ],
            'Tasks & Workflows' => [
                'task.view' => 'View Tasks',
                'task.create' => 'Create Tasks',
                'task.edit' => 'Edit Tasks',
                'task.delete' => 'Delete Tasks',
                'task.change_status' => 'Change Task Status / Move Columns',
                'task.assign' => 'Assign Users to Tasks',
            ],
            'Comments & Attachments' => [
                'comment.create' => 'Post Comments',
                'comment.delete' => 'Delete Comments',
                'attachment.upload' => 'Upload Attachments',
                'attachment.delete' => 'Delete Attachments',
            ],
            'Direct Messaging' => [
                'message.access' => 'Access Direct Messages',
                'message.send' => 'Send Direct Messages',
            ],
            'User Administration' => [
                'user.view' => 'View Users',
                'user.create' => 'Create Users',
                'user.edit' => 'Edit Users',
                'user.delete' => 'Delete Users',
                'user.manage_roles' => 'Assign & Manage User Roles',
            ],
            'Role Management (RBAC)' => [
                'role.view' => 'View Roles',
                'role.create' => 'Create Custom Roles',
                'role.edit' => 'Edit Roles & Permissions',
                'role.delete' => 'Delete Roles',
                'role.assign_permissions' => 'Manage Role Permissions',
            ],
        ];

        return view('livewire.admin.role-create', [
            'groupedPermissions' => $groupedPermissions,
        ])->layout('components.layouts.app');
    }
}
