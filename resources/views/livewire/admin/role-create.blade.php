<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-4xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.roles') }}" class="text-xs text-slate-500 hover:text-purple-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Roles
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Create Custom Role</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Define a custom role title and select specific module permissions.</p>
            </div>

            <button type="button" 
                    wire:click="toggleAll" 
                    class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-[#2a2b2f] text-slate-700 dark:text-slate-300 hover:border-purple-400 transition-colors">
                Toggle All Permissions
            </button>
        </div>

        <form wire:submit="save" class="space-y-6">
            <!-- Role Details Card -->
            <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-6 shadow-xs space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Role Name *
                    </label>
                    <input type="text" 
                           id="name"
                           wire:model="name" 
                           placeholder="e.g. Quality Assurance, Project Lead, External Auditor"
                           class="w-full text-xs font-semibold bg-slate-50 dark:bg-[#16171a] border border-slate-200 dark:border-[#2f3036] rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-[#16171a] focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                           required autofocus>
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permission Groups Grid -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Module Permission Matrix
                    </h3>
                    <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                        {{ count($selectedPermissions) }} permission(s) selected
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($groupedPermissions as $groupName => $perms)
                        @php
                            $groupKeys = array_keys($perms);
                            $allGroupSelected = count(array_intersect($groupKeys, $selectedPermissions)) === count($groupKeys);
                        @endphp
                        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-5 shadow-xs space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-[#25262c]">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $groupName }}</span>
                                <div class="flex items-center gap-2">
                                    @if($allGroupSelected)
                                        <button type="button" 
                                                wire:click="deselectAllGroup({{ Js::from($groupKeys) }})" 
                                                class="text-[11px] font-semibold text-rose-500 hover:text-rose-700">
                                            Deselect All
                                        </button>
                                    @else
                                        <button type="button" 
                                                wire:click="selectAllGroup({{ Js::from($groupKeys) }})" 
                                                class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                                            Select All
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2">
                                @foreach($perms as $key => $label)
                                    <label class="flex items-start gap-2.5 cursor-pointer group select-none">
                                        <input type="checkbox" 
                                               wire:model.live="selectedPermissions" 
                                               value="{{ $key }}"
                                               class="mt-0.5 h-4 w-4 rounded border-slate-300 dark:border-[#35363b] text-purple-600 focus:ring-purple-500/20 accent-purple-600 dark:bg-[#16171a]">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-white transition-colors">
                                                {{ $label }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $key }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#2a2b2f]">
                <a href="{{ route('admin.roles') }}" 
                   class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#28292e] rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-md hover:opacity-95 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>
