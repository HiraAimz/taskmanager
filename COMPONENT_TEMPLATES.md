# Livewire Component Templates

After running `php artisan make:livewire` for each component, replace the generated code with these templates:

## Admin/UserIndex.php

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.user-index', [
            'users' => User::with('roles')->paginate(15),
        ]);
    }
}
```

### admin/user-index.blade.php

```blade
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Management</h2>
        <a href="{{ route('admin.users.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Create User
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $user->roles->pluck('name')->join(', ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
```

## Admin/UserCreate.php

```php
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

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Manager,Member',
        ];
    }

    public function save()
    {
        $this->validate();

        $action = new CreateUser();
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
        ]);
    }
}
```

### admin/user-create.blade.php

```blade
<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create User</h2>

    <form wire:submit="save" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" id="name" wire:model="name" 
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" id="email" wire:model="email" 
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <input type="password" id="password" wire:model="password" 
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
            <select id="role" wire:model="role" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select a role</option>
                @foreach($roles as $roleOption)
                    <option value="{{ $roleOption->value }}">{{ $roleOption->value }}</option>
                @endforeach
            </select>
            @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Create User
            </button>
            <a href="{{ route('admin.users') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Cancel
            </a>
        </div>
    </form>
</div>
```

## Admin/UserEdit.php

```php
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

    public function mount(User $user)
    {
        $this->user = $user;
        $this->role = $user->roles->first()?->name ?? '';
    }

    public function rules()
    {
        return [
            'role' => 'required|in:Admin,Manager,Member',
        ];
    }

    public function updateRole()
    {
        $this->validate();

        $this->user->syncRoles([$this->role]);

        session()->flash('success', 'User role updated successfully.');

        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user-edit', [
            'roles' => Role::cases(),
        ]);
    }
}
```

### admin/user-edit.blade.php

```blade
<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit User Role</h2>

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-md">
        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Name:</strong> {{ $user->name }}</p>
        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Email:</strong> {{ $user->email }}</p>
    </div>

    <form wire:submit="updateRole" class="space-y-6">
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
            <select id="role" wire:model="role" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select a role</option>
                @foreach($roles as $roleOption)
                    <option value="{{ $roleOption->value }}">{{ $roleOption->value }}</option>
                @endforeach
            </select>
            @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Update Role
            </button>
            <a href="{{ route('admin.users') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Cancel
            </a>
        </div>
    </form>
</div>
```

## Workspaces/WorkspaceIndex.php

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Models\Workspace;
use Livewire\Component;
use Livewire\WithPagination;

class WorkspaceIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $query = auth()->user()->hasRole('Admin')
            ? Workspace::with('owner')
            : auth()->user()->workspaces()->with('owner');

        return view('livewire.workspaces.workspace-index', [
            'workspaces' => $query->paginate(15),
        ]);
    }
}
```

### workspaces/workspace-index.blade.php

```blade
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Workspaces</h2>
        @can('create', App\Models\Workspace::class)
        <a href="{{ route('workspaces.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Create Workspace
        </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($workspaces as $workspace)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $workspace->name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $workspace->description }}</p>
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                Owner: {{ $workspace->owner->name }}
            </div>
            <div class="flex gap-2">
                @can('update', $workspace)
                <a href="{{ route('workspaces.edit', $workspace) }}" 
                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 text-sm">
                    Edit
                </a>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
            No workspaces found.
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $workspaces->links() }}
    </div>
</div>
```

## Workspaces/WorkspaceCreate.php

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Actions\Workspaces\CreateWorkspace;
use Livewire\Component;

class WorkspaceCreate extends Component
{
    public string $name = '';
    public string $description = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function save()
    {
        $this->validate();

        $action = new CreateWorkspace();
        $action->handle([
            'name' => $this->name,
            'description' => $this->description,
        ], auth()->user());

        session()->flash('success', 'Workspace created successfully.');

        return redirect()->route('workspaces.index');
    }

    public function render()
    {
        return view('livewire.workspaces.workspace-create');
    }
}
```

### workspaces/workspace-create.blade.php

```blade
<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create Workspace</h2>

    <form wire:submit="save" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" id="name" wire:model="name" 
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="description" wire:model="description" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Create Workspace
            </button>
            <a href="{{ route('workspaces.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Cancel
            </a>
        </div>
    </form>
</div>
```

## Workspaces/WorkspaceEdit.php

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Workspaces;

use App\Models\User;
use App\Models\Workspace;
use Livewire\Component;

class WorkspaceEdit extends Component
{
    public Workspace $workspace;
    public string $name = '';
    public string $description = '';
    public ?int $selectedUserId = null;

    public function mount(Workspace $workspace)
    {
        $this->workspace = $workspace;
        $this->name = $workspace->name;
        $this->description = $workspace->description ?? '';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function update()
    {
        $this->validate();

        $this->workspace->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Workspace updated successfully.');
    }

    public function addMember()
    {
        if ($this->selectedUserId) {
            $this->workspace->users()->syncWithoutDetaching([$this->selectedUserId]);
            $this->selectedUserId = null;
            session()->flash('success', 'Member added successfully.');
        }
    }

    public function removeMember(int $userId)
    {
        $this->workspace->users()->detach($userId);
        session()->flash('success', 'Member removed successfully.');
    }

    public function render()
    {
        $currentMemberIds = $this->workspace->users->pluck('id')->toArray();
        $availableUsers = User::whereNotIn('id', $currentMemberIds)->get();

        return view('livewire.workspaces.workspace-edit', [
            'availableUsers' => $availableUsers,
        ]);
    }
}
```

### workspaces/workspace-edit.blade.php

```blade
<div class="p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit Workspace</h2>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="update" class="space-y-6 mb-8">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" id="name" wire:model="name" 
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="description" wire:model="description" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Update Workspace
        </button>
    </form>

    <hr class="my-8 border-gray-300 dark:border-gray-700">

    <div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Manage Members</h3>

        <div class="mb-6 flex gap-4">
            <select wire:model="selectedUserId" 
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select a user</option>
                @foreach($availableUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            <button wire:click="addMember" type="button"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                Add Member
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($workspace->users as $member)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $member->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($member->id !== $workspace->owner_id)
                            <button wire:click="removeMember({{ $member->id }})" 
                                    wire:confirm="Are you sure you want to remove this member?"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400">
                                Remove
                            </button>
                            @else
                            <span class="text-gray-500 dark:text-gray-400">Owner</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('workspaces.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
            Back to Workspaces
        </a>
    </div>
</div>
```
