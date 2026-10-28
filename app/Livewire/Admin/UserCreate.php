<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Users\CreateUser;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserCreate extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'Member';

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('user.create') || 
            auth()->user()->can('manage-users'), 
            403
        );
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ];
    }

    public function save(): mixed
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('user.create') || 
            auth()->user()->can('manage-users'), 
            403
        );

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
            'roles' => Role::orderBy('name')->get(),
        ])->layout('components.layouts.app');
    }
}
