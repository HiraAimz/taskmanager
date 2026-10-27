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
