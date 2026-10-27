<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Users\CreateUser;
use App\Enums\Role;
use Livewire\Component;

class UserCreate extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('manage-users'), 403);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Manager,Member',
        ];
    }

    public function save(): mixed
    {
        abort_unless(auth()->user()->can('manage-users'), 403);

        $this->validate();

        $action = new CreateUser;
        $action->handle([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        session()->flash('success', 'User created successfully.');

        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user-create', [
            'roles' => Role::cases(),
        ])->layout('components.layouts.app');
    }
}
