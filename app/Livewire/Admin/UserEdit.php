<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\Role;
use App\Models\User;
use Livewire\Component;

class UserEdit extends Component
{
    public User $user;

    public string $role = '';

    public function mount(User $user): void
    {
        abort_unless(auth()->user()->can('manage-users'), 403);

        $this->user = $user;
        $this->role = $user->roles->first()?->name ?? '';
    }

    public function rules(): array
    {
        return [
            'role' => 'required|in:Admin,Manager,Member',
        ];
    }

    public function updateRole(): mixed
    {
        abort_unless(auth()->user()->can('manage-users'), 403);

        $this->validate();

        $this->user->syncRoles([$this->role]);

        session()->flash('success', 'User role updated successfully.');

        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
            'roles' => Role::cases(),
        ])->layout('components.layouts.app');
    }
}
