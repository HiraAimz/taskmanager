<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-xl mx-auto w-full space-y-6">
        <div>
            <a href="{{ route('admin.users') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors">
                ← Back to Users
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Edit User Role</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Update system authorization level for this user.</p>
        </div>

        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs space-y-4">
            <!-- User summary card -->
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-[#16171a] border border-slate-200/60 dark:border-[#2f3036]">
                <div class="w-10 h-10 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-sm font-bold shadow-xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                </div>
            </div>

            <form wire:submit="updateRole" class="space-y-4">
                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                        Assigned Role *
                    </label>
                    <select id="role" wire:model="role" 
                            class="form-select w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Select a role</option>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name ?? $roleOption->value }}">{{ $roleOption->name ?? $roleOption->value }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#25262c]">
                    <a href="{{ route('admin.users') }}" 
                       class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
