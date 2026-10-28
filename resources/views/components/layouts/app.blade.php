<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'TaskManager') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans antialiased bg-[#f7f8fa] dark:bg-[#141517] text-slate-900 dark:text-slate-100 selection:bg-purple-500 selection:text-white"
      x-data="{ 
          darkMode: document.documentElement.classList.contains('dark'),
          sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', 
          mobileMenuOpen: false,
          workspaceDropdown: false,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('darkMode', 'true');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('darkMode', 'false');
              }
          }
      }" 
      x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">

    @php
        $authUser = auth()->user();
        $userWorkspaces = $authUser 
            ? ($authUser->hasRole('Admin') 
                ? \App\Models\Workspace::with('projects')->get() 
                : $authUser->workspaces()->with('projects')->get())
            : collect();
        $currentWorkspace = isset($project) 
            ? ($project->relationLoaded('workspace') ? $project->workspace : $project->load('workspace.projects')->workspace) 
            : ($userWorkspaces->first() ?? null);
        if ($currentWorkspace && ! $currentWorkspace->relationLoaded('projects')) {
            $currentWorkspace->load('projects');
        }
        $allProjects = $currentWorkspace 
            ? $currentWorkspace->projects 
            : \App\Models\Project::take(8)->get();
        
        $myTasksCount = $authUser 
            ? \App\Models\Task::where(function($q) use ($authUser) {
                $q->where('assignee_id', $authUser->id)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $authUser->id));
            })->whereNull('completed_at')->count()
            : 0;

        $unreadMessagesCount = $authUser
            ? \App\Models\Message::whereHas('conversation', function ($q) use ($authUser) {
                $q->where('user_one_id', $authUser->id)->orWhere('user_two_id', $authUser->id);
            })->where('sender_id', '!=', $authUser->id)->whereNull('read_at')->count()
            : 0;
    @endphp

    <div class="min-h-screen flex">
        
        <!-- Sidebar Desktop (ClickUp Style - Theme Responsive) -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="hidden lg:flex bg-white dark:bg-[#1e1f21] text-slate-700 dark:text-slate-300 border-r border-slate-200/80 dark:border-[#2d2e32] transition-all duration-300 flex-col z-30 select-none">
            
            <!-- Workspace Switcher & Logo -->
            <div class="h-16 flex items-center justify-between px-3.5 border-b border-slate-200/80 dark:border-[#2d2e32]">
                <div class="flex items-center gap-2.5 min-w-0" x-show="sidebarOpen" x-cloak>
                    <!-- ClickUp Iconic Gradient Cube -->
                    <div class="w-8 h-8 rounded-lg bg-clickup-gradient flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 cursor-pointer hover:text-purple-600 dark:hover:text-white transition-colors" @click="workspaceDropdown = !workspaceDropdown">
                            <span class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $currentWorkspace->name ?? 'TaskManager' }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="workspaceDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <span class="text-[11px] text-slate-400 dark:text-slate-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                            Workspace
                        </span>
                    </div>
                </div>

                <!-- Collapse Toggle Icon -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#2b2c30] rounded-md transition-colors"
                        :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Workspace Dropdown Menu -->
            <div x-show="workspaceDropdown && sidebarOpen" 
                 @click.outside="workspaceDropdown = false" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mx-3 mt-2 bg-white dark:bg-[#28292d] border border-slate-200 dark:border-[#393a3f] rounded-xl p-2 shadow-2xl space-y-1">
                <div class="px-2 py-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Switch Workspace</div>
                @forelse($userWorkspaces as $ws)
                    <a href="{{ route('workspaces.edit', $ws) }}" 
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors {{ (isset($currentWorkspace) && $currentWorkspace->id === $ws->id) ? 'bg-purple-50 dark:bg-purple-600/30 text-purple-700 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#323338] hover:text-slate-900 dark:hover:text-white' }}">
                        <div class="w-5 h-5 rounded bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-[10px] text-white font-bold">
                            {{ strtoupper(substr($ws->name, 0, 1)) }}
                        </div>
                        <span class="truncate flex-1">{{ $ws->name }}</span>
                        @if(isset($currentWorkspace) && $currentWorkspace->id === $ws->id)
                            <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </a>
                @empty
                    <p class="text-xs text-slate-400 px-2 py-1">No workspaces</p>
                @endforelse
                <div class="pt-1.5 border-t border-slate-100 dark:border-[#393a3f] flex items-center justify-between px-1">
                    <a href="{{ route('workspaces.create') }}" class="text-[11px] text-purple-600 dark:text-purple-400 hover:text-purple-700 flex items-center gap-1 font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Workspace
                    </a>
                    <a href="{{ route('workspaces.index') }}" class="text-[11px] text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">View all</a>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-2.5 py-4 overflow-y-auto space-y-6">
                
                <!-- Main Core Items -->
                <div class="space-y-1">
                    <!-- My Tasks -->
                    <a href="{{ route('my-tasks') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('my-tasks') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="My Tasks">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('my-tasks') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">My Tasks</span>
                        @if($myTasksCount > 0)
                            <span x-show="sidebarOpen" x-cloak class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                {{ $myTasksCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Direct Messages -->
                    @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('message.access')))
                    <a href="{{ route('messages.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('messages.*') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="Direct Messages">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('messages.*') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">Messages</span>
                        @if($unreadMessagesCount > 0)
                            <span x-show="sidebarOpen" x-cloak class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                {{ $unreadMessagesCount }}
                            </span>
                        @endif
                    </a>
                    @endif

                    <!-- Workspaces -->
                    @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('workspace.view')))
                    <a href="{{ route('workspaces.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('workspaces.*') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="Workspaces">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('workspaces.*') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">Workspaces</span>
                    </a>
                    @endif

                    <!-- Projects Hub -->
                    @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('project.view')))
                    <a href="{{ route('projects.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('projects.index') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="All Projects">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('projects.index') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">Projects Hub</span>
                    </a>
                    @endif
                </div>

                <!-- ClickUp Spaces / Projects List -->
                <div x-show="sidebarOpen" x-cloak class="space-y-1">
                    <div class="flex items-center justify-between px-3 py-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Spaces & Projects</span>
                        @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('project.create') || $authUser->can('create-project')))
                        <a href="{{ route('projects.create') }}" class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#2b2c30] rounded" title="Create Project">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                    
                    <div class="space-y-0.5">
                        @forelse($allProjects as $p)
                            @php
                                $isCurrentProject = isset($project) && $project->id === $p->id;
                            @endphp
                            <a href="{{ route('projects.board', $p) }}" 
                               class="group flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs transition-colors {{ $isCurrentProject ? 'bg-slate-100 dark:bg-[#2b2c30] text-purple-700 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-[#25262a] hover:text-slate-900 dark:hover:text-white' }}">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $p->color ?? '#7b68ee' }}"></span>
                                <span class="truncate flex-1">{{ $p->icon ? $p->icon . ' ' : '' }}{{ $p->name }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">Board</span>
                            </a>
                        @empty
                            <div class="px-3 py-2 text-xs text-slate-400">
                                No projects yet. @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('project.create') || $authUser->can('create-project')))<a href="{{ route('projects.create') }}" class="text-purple-600 dark:text-purple-400 hover:underline">Create one</a>@endif
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('user.view') || $authUser->can('role.view') || $authUser->can('manage-users')))
                <!-- Admin Section -->
                <div class="space-y-1 pt-3 border-t border-slate-200/80 dark:border-[#2d2e32]">
                    <div class="px-3 py-1" x-show="sidebarOpen" x-cloak>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Administration</span>
                    </div>

                    @if($authUser->hasRole('Admin') || $authUser->can('user.view') || $authUser->can('manage-users'))
                    <a href="{{ route('admin.users') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('admin.users*') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="Manage Users">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.users*') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">Users</span>
                    </a>
                    @endif

                    @if($authUser->hasRole('Admin') || $authUser->can('role.view') || $authUser->can('manage-users'))
                    <a href="{{ route('admin.roles') }}" 
                       class="group flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition-all {{ request()->routeIs('admin.roles*') ? 'bg-purple-50 dark:bg-gradient-to-r dark:from-purple-600/30 dark:to-pink-600/20 text-purple-700 dark:text-white font-semibold border border-purple-200 dark:border-purple-500/30 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#2b2c30] hover:text-slate-900 dark:hover:text-white' }}"
                       :class="!sidebarOpen && 'justify-center px-0'"
                       title="Roles & Permissions">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.roles*') ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="flex-1 truncate">Roles & Permissions</span>
                    </a>
                    @endif
                </div>
                @endif
            </nav>

            <!-- User Profile & Sign Out Footer -->
            <div class="border-t border-slate-200/80 dark:border-[#2d2e32] p-3 bg-slate-50 dark:bg-[#18191c]">
                <div class="flex items-center gap-2.5" :class="!sidebarOpen && 'justify-center'">
                    <div class="w-8 h-8 rounded-full bg-clickup-gradient flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0">
                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0" x-show="sidebarOpen" x-cloak>
                        <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $authUser->name ?? 'User' }}</p>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 font-medium">
                                {{ $authUser->roles->first()->name ?? 'Member' }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-cloak>
                        @csrf
                        <button type="submit" 
                                class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-[#2b2c30] rounded-md transition-colors"
                                title="Sign out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Backdrop -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 z-40 lg:hidden bg-black/60 backdrop-blur-sm" x-cloak></div>
        
        <!-- Mobile Sidebar (Theme Responsive) -->
        <aside x-show="mobileMenuOpen" 
               x-transition:enter="transition ease-out duration-300" 
               x-transition:enter-start="-translate-x-full" 
               x-transition:enter-end="translate-x-0" 
               x-transition:leave="transition ease-in duration-200" 
               x-transition:leave-start="translate-x-0" 
               x-transition:leave-end="-translate-x-full" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#1e1f21] text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-[#2d2e32] lg:hidden flex flex-col shadow-2xl" x-cloak>
            
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200 dark:border-[#2d2e32]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-clickup-gradient flex items-center justify-center text-white font-bold">
                        ✓
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white text-base">TaskManager</span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">
                <a href="{{ route('my-tasks') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('my-tasks') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    My Tasks
                </a>
                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('message.access')))
                <a href="{{ route('messages.index') }}" class="flex items-center justify-between px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('messages.*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    <span>Messages</span>
                    @if($unreadMessagesCount > 0)
                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                            {{ $unreadMessagesCount }}
                        </span>
                    @endif
                </a>
                @endif
                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('project.view')))
                <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('projects.*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    Projects
                </a>
                @endif
                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('workspace.view')))
                <a href="{{ route('workspaces.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('workspaces.*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    Workspaces
                </a>
                @endif
                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('user.view') || $authUser->can('manage-users')))
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('admin.users*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    Users
                </a>
                @endif
                @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('role.view') || $authUser->can('manage-users')))
                <a href="{{ route('admin.roles') }}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg hover:bg-slate-100 dark:hover:bg-[#2b2c30] {{ request()->routeIs('admin.roles*') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-white font-bold' : '' }}">
                    Roles & Permissions
                </a>
                @endif
            </div>
        </aside>

        <!-- Main Content Surface -->
        <main class="flex-1 flex flex-col min-w-0 bg-[#f7f8fa] dark:bg-[#141517]">
            
            <!-- ClickUp Top Bar Header -->
            <header class="h-14 bg-white dark:bg-[#1a1b1e] border-b border-slate-200 dark:border-[#2a2b2f] flex items-center justify-between px-4 lg:px-6 flex-shrink-0 z-20">
                
                <!-- Left Title & Breadcrumb -->
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-1.5 -ml-1 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <div class="flex items-center gap-2 text-sm truncate">
                        <span class="text-slate-500 dark:text-slate-400 font-medium hidden sm:inline">{{ $currentWorkspace->name ?? 'Workspace' }}</span>
                        <span class="text-slate-300 dark:text-slate-600 hidden sm:inline">/</span>
                        <h1 class="text-sm lg:text-base font-bold text-slate-900 dark:text-white truncate">
                            {{ $pageTitle ?? $title ?? (isset($project) ? $project->name : 'Dashboard') }}
                        </h1>
                    </div>
                </div>

                <!-- Right Actions: Quick Search, New Task, Dark Mode Toggle -->
                <div class="flex items-center gap-2.5">
                    
                    <!-- Search Indicator Pill -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-[#242529] border border-slate-200 dark:border-[#323338] rounded-lg text-xs text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search tasks...</span>
                        <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-slate-700 rounded text-slate-400 shadow-xs">Ctrl K</kbd>
                    </div>

                    <!-- Quick Task Create Button if Project is present -->
                    @if(isset($project))
                        @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('task.create') || $authUser->can('create-task')))
                        <button wire:click="$dispatch('open-quick-create', { statusId: {{ $project->statuses->first()->id ?? 0 }} })" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-clickup-gradient hover:opacity-95 text-white text-xs font-semibold rounded-lg shadow-sm transition-all transform active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Task</span>
                        </button>
                        @endif
                    @else
                        @if($authUser && ($authUser->hasRole('Admin') || $authUser->can('project.create') || $authUser->can('create-project')))
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-clickup-gradient hover:opacity-95 text-white text-xs font-semibold rounded-lg shadow-sm transition-all transform active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Project</span>
                        </a>
                        @endif
                    @endif

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleTheme()" 
                            type="button"
                            class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#25262a] rounded-lg transition-colors cursor-pointer"
                            title="Toggle theme">
                        <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page Workspace Area -->
            <div class="flex-1 overflow-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
