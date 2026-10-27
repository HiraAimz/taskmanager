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
