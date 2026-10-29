<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskManager — ClickUp-style Professional Task Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#0e0f12] text-slate-100 flex flex-col selection:bg-purple-500 selection:text-white">
    
    <!-- Top Navigation -->
    <header class="h-16 border-b border-slate-800/80 px-6 lg:px-12 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-clickup-gradient flex items-center justify-center text-white shadow-lg"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
            <span class="text-lg font-bold tracking-tight text-white">TaskManager</span>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider">
                ClickUp Edition
            </span>
        </div>

        <nav class="flex items-center gap-3">
            @auth
                <a href="{{ route('my-tasks') }}" 
                   class="px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-md hover:opacity-95 transition-all">
                    Open Workspace →
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="px-4 py-2 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-md hover:opacity-95 transition-all">
                    Sign In →
                </a>
            @endauth
        </nav>
    </header>

    <!-- Hero Content -->
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center max-w-4xl mx-auto space-y-8">
        
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/80 text-xs font-medium text-slate-300">
            <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
            <span>Next-Generation Collaborative Workspace</span>
        </div>

        <!-- Main Headline -->
        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight text-white">
            One workspace for <br />
            <span class="text-clickup-gradient">every task, team & project.</span>
        </h1>

        <!-- Subtitle -->
        <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Organize complex workflows with Kanban boards, interactive lists, calendar timelines, subtasks, checklists, and real-time team collaboration.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            @auth
                <a href="{{ route('my-tasks') }}" 
                   class="w-full sm:w-auto px-7 py-3.5 bg-clickup-gradient text-white text-sm font-bold rounded-xl shadow-xl hover:opacity-95 transition-all transform hover:-translate-y-0.5">
                    Go to My Tasks
                </a>
                <a href="{{ route('projects.index') }}" 
                   class="w-full sm:w-auto px-7 py-3.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded-xl border border-slate-700 transition-all">
                    Browse Projects
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="w-full sm:w-auto px-7 py-3.5 bg-clickup-gradient text-white text-sm font-bold rounded-xl shadow-xl hover:opacity-95 transition-all transform hover:-translate-y-0.5">
                    Sign In to Workspace
                </a>
            @endauth
        </div>

        <!-- Demo Accounts Box -->
        <div class="mt-8 p-5 rounded-2xl bg-slate-900/90 border border-slate-800 max-w-xl mx-auto text-left space-y-2">
            <div class="text-xs font-bold uppercase tracking-wider text-purple-400 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Demo Access Credentials (Development Mode)
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <div class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60">
                    <span class="font-bold text-white block"><svg class="w-3 h-3 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> Admin</span>
                    <span class="text-slate-400">admin@example.com</span>
                    <span class="text-slate-500 block">password</span>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60">
                    <span class="font-bold text-white block"><span class="inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>Manager</span></span>
                    <span class="text-slate-400">manager@example.com</span>
                    <span class="text-slate-500 block">password</span>
                </div>
                <div class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60">
                    <span class="font-bold text-white block"><span class="inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Member</span></span>
                    <span class="text-slate-400">member1@example.com</span>
                    <span class="text-slate-500 block">password</span>
                </div>
            </div>
        </div>

        <!-- 3 Feature Highlights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-8 text-left">
            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-2">
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <h3 class="text-sm font-bold text-white">Board & List Views</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Drag and drop cards across custom status columns, switch instantly to table view, or view due dates on the calendar.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-2">
                <div class="w-9 h-9 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                <h3 class="text-sm font-bold text-white">Multi-Assignees & Teams</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Assign primary leads and collaborators, collaborate with comments, track checklist progress, and attach files.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-2">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                <h3 class="text-sm font-bold text-white">Workspace Permissions</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Granular role-based authorization for Admins, Managers, and Members with audit activity logging on all changes.
                </p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-4 px-6 text-center text-xs text-slate-500">
        TaskManager • Inspired by ClickUp • Built with Laravel 13, Livewire 3 & Tailwind CSS
    </footer>
</body>
</html>
