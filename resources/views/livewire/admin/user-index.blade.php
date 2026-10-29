<div class="h-full flex flex-col p-6 bg-[#f7f8fa] dark:bg-[#141517]">
    <div class="max-w-7xl mx-auto w-full space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span><svg class="w-6 h-6 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> User &amp; Role Management</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Manage team member access and assign system roles. Public registration is disabled.
                </p>
            </div>

            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('user.create') || auth()->user()->can('manage-users'))
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-sm hover:opacity-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Create User
            </a>
            @endif
        </div>

        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span>{{ session('success') }}</span></div>
            </div>
        @endif

        <!-- Users Table Card -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl shadow-xs border border-slate-200/80 dark:border-[#2a2b2f] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-[#202126] border-b border-slate-200 dark:border-[#2a2b2f] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-3.5">User</th>
                            <th class="px-6 py-3.5">Email</th>
                            <th class="px-6 py-3.5">Role</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#25262c] text-xs">
                        @foreach($users as $user)
                            <tr class="hover:bg-purple-50/30 dark:hover:bg-[#222329] transition-colors">
                                <!-- User Name & Avatar -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900 dark:text-white">
                                            {{ $user->name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-300">
                                    {{ $user->email }}
                                </td>

                                <!-- Role Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleName = $user->roles->first()->name ?? 'No Role';
                                    @endphp
                                    @if($roleName === 'Admin')
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <svg class="w-3 h-3 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> Admin
                                        </span>
                                    @elseif($roleName === 'Manager')
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            <span class="inline-flex items-center gap-1"><svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>Manager</span>
                                        </span>
                                    @elseif($roleName === 'Member')
                                        <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            <span class="inline-flex items-center gap-1"><svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Member</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                            <span class="inline-flex items-center gap-1"><svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>{{ $roleName }}</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('user.edit') || auth()->user()->can('manage-users'))
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 hover:underline">
                                        Edit
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-3 border-t border-slate-200 dark:border-[#2a2b2f] bg-slate-50/50 dark:bg-[#1a1b1e]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
