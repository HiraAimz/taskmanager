<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-xl mx-auto w-full space-y-6">
        <div>
            <a href="{{ route('workspaces.index') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors">
                ← Back to Workspaces
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Create Workspace</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Set up a space for your team to organize projects, lists, and tasks.</p>
        </div>

        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                        Workspace Name *
                    </label>
                    <input type="text" id="name" wire:model="name" 
                           class="w-full text-xs font-medium bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                           placeholder="e.g. Design & Creative Agency" required autofocus>
                    @error('name') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                        Description
                    </label>
                    <textarea id="description" wire:model="description" rows="3"
                              class="w-full text-xs bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl p-3 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                              placeholder="Brief description of the workspace purpose..."></textarea>
                    @error('description') <span class="text-rose-600 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-[#25262c]">
                    <a href="{{ route('workspaces.index') }}" 
                       class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-5 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Create Workspace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
