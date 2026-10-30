<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | {{ config('app.name', 'TaskManager') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full font-sans antialiased bg-[#f7f8fa] dark:bg-[#141517] text-slate-900 dark:text-slate-100 flex flex-col justify-center items-center p-6 selection:bg-purple-500 selection:text-white">
    
    <div class="max-w-md w-full text-center space-y-6">
        <!-- Badge & Icon -->
        <div class="relative mx-auto w-24 h-24">
            <div class="w-24 h-24 rounded-3xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200/80 dark:border-purple-800/80 flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full bg-purple-600 text-white text-[11px] font-black shadow-md">
                404
            </span>
        </div>

        <div class="space-y-2">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                404 — Page Not Found
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm mx-auto">
                The page, workspace, or project view you are looking for does not exist or may have been archived or moved.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-center gap-3 pt-2">
            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('dashboard') }}'" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold rounded-xl bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#35363b] text-slate-700 dark:text-slate-300 hover:border-purple-400 shadow-2xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Go Back</span>
            </button>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-clickup-gradient text-white text-xs font-semibold rounded-xl shadow-md hover:opacity-95 transition-all">
                <span>Return to Dashboard</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="pt-6 border-t border-slate-200/60 dark:border-[#25262a]">
            <p class="text-[11px] text-slate-400">
                TaskManager Workspace Suite
            </p>
        </div>
    </div>

</body>
</html>
