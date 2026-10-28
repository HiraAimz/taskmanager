<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('role.view') || 
            auth()->user()->can('manage-users'), 
            403
        );
    }

    public function deleteRole(int $roleId): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('role.delete'), 
            403
        );

        $role = Role::findOrFail($roleId);

        // System safety check: never delete standard Admin role
        if ($role->name === 'Admin') {
            session()->flash('error', 'The core Admin role cannot be deleted.');
            return;
        }

        // Dependency safeguard: check if any users are currently assigned to this role
        $assignedCount = $role->users()->count();
        if ($assignedCount > 0) {
            session()->flash('error', "Cannot delete role '{$role->name}' because it is assigned to {$assignedCount} user(s). Reassign them first.");
            return;
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        session()->flash('success', "Role '{$roleName}' deleted successfully.");
    }

    public function render()
    {
        $query = Role::withCount(['users', 'permissions'])->with('permissions');

        if (trim($this->search)) {
            $query->where('name', 'like', '%' . trim($this->search) . '%');
        }

        return view('livewire.admin.role-index', [
            'roles' => $query->orderBy('name')->paginate(12),
        ])->layout('components.layouts.app');
    }
}
