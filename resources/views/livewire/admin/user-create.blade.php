<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-xl mx-auto w-full space-y-6">
        <div>
            <a href="{{ route('admin.users') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors">
                ← Back to Users
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Create User</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Add a new internal user and assign their access role.</p>
        </div>

        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Full Name *</label>
                    <input type="text" id="name" wire:model="name" 
                           class="w-full text-xs font-medium bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                           placeholder="e.g. Jane Doe" required>
                    @error('name') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Email Address *</label>
                    <input type="email" id="email" wire:model="email" 
                           class="w-full text-xs font-medium bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                           placeholder="jane@example.com" required>
                    @error('email') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Password *</label>
                    <input type="password" id="password" wire:model="password" 
                           class="w-full text-xs font-medium bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                           placeholder="Minimum 8 characters" required>
                    @error('password') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">System Role *</label>
                    <select id="role" wire:model="role" 
                            class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
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
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
