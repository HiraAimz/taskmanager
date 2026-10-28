<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('Admin') || 
            auth()->user()->can('user.view') || 
            auth()->user()->can('manage-users'), 
            403
        );
    }

    public function render()
    {
        return view('livewire.admin.user-index', [
            'users' => User::with('roles')->paginate(15),
        ])->layout('components.layouts.app');
    }
}
