<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserEdit extends Component
{
    public User $user;

    public string $role = '';

    public function mount(User $user): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('user.edit') || 
            auth()->user()->can('manage-users'), 
            403
        );

        $this->user = $user->loadMissing('roles');
        $this->role = $this->user->roles->first()?->name ?? '';
    }

    public function rules(): array
    {
        return [
            'role' => 'required|exists:roles,name',
        ];
    }

    public function updateRole(): mixed
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('user.manage_roles') || 
            auth()->user()->can('manage-users'), 
            403
        );

        $this->validate();

        // Safety safeguard: Prevent accidental removal of the last administrator
        if ($this->user->hasRole('Admin') && $this->role !== 'Admin') {
            $adminCount = User::role('Admin')->count();
            if ($adminCount <= 1) {
                $this->addError('role', 'Cannot remove the Admin role from the last remaining administrator.');
                return null;
            }
        }

        $this->user->syncRoles([$this->role]);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        session()->flash('success', 'User role updated successfully.');

        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
            'roles' => Role::orderBy('name')->get(),
        ])->layout('components.layouts.app');
    }
}
