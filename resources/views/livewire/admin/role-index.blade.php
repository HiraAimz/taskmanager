<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.users') }}" class="hover:text-purple-600">Administration</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Roles & Permissions</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span>🛡️ Roles & Permissions (RBAC)</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Configure dynamic custom access roles and manage granular feature permissions across modules.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-[#2a2b2f] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl hover:border-purple-400 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Manage Users
                </a>

                @can('role.create')
                <a href="{{ route('admin.roles.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Create Custom Role
                </a>
                @endcan
            </div>
        </div>

        <!-- Alert messages -->
        @if (session()->has('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300">
                ⚠ {{ session('error') }}
            </div>
        @endif

        <!-- Search bar -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative w-full max-w-xs">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search roles..." 
                       class="w-full pl-9 pr-4 py-2 text-xs bg-white dark:bg-[#1a1b1e] border border-slate-200/80 dark:border-[#2a2b2f] rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 font-medium">
            </div>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($roles as $role)
                <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-5 shadow-xs hover:border-purple-400/80 dark:hover:border-purple-500/80 card-hover flex flex-col justify-between">
                    <div>
                        <!-- Role Header -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60 flex items-center justify-center font-bold text-sm shadow-2xs">
                                    {{ strtoupper(substr($role->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>{{ $role->name }}</span>
                                        @if($role->name === 'Admin')
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                Superadmin
                                            </span>
                                        @endif
                                    </h3>
                                    <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        👥 {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }} assigned
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions summary badge list -->
                        <div class="mb-4 space-y-2">
                            <div class="flex items-center justify-between text-2xs font-bold text-slate-400 uppercase tracking-wider">
                                <span>Assigned Permissions</span>
                                <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $role->permissions_count }}</span>
                            </div>
                            
                            <div class="flex flex-wrap gap-1 max-h-24 overflow-hidden">
                                @forelse($role->permissions->take(6) as $perm)
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 dark:bg-[#25262a] text-slate-600 dark:text-slate-300">
                                        {{ $perm->name }}
                                    </span>
                                @empty
                                    <span class="text-2xs text-slate-400 italic">No permissions assigned yet</span>
                                @endforelse
                                @if($role->permissions_count > 6)
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                                        +{{ $role->permissions_count - 6 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="pt-3 border-t border-slate-100 dark:border-[#25262c] flex items-center justify-between">
                        @can('role.edit')
                        <a href="{{ route('admin.roles.edit', $role) }}" 
                           class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Permissions
                        </a>
                        @endcan

                        @if($role->name !== 'Admin')
                            @can('role.delete')
                            <button wire:click="deleteRole({{ $role->id }})"
                                    wire:confirm="Are you sure you want to delete role '{{ $role->name }}'? Users must be reassigned first."
                                    class="text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                Delete
                            </button>
                            @endcan
                        @else
                            <span class="text-2xs text-slate-400 italic">Protected</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-12 text-center">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No roles found matching criteria</p>
                </div>
            @endforelse
        </div>

        @if($roles->hasPages())
            <div class="pt-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
