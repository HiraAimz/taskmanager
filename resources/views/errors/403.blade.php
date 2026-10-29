<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Access Denied | {{ config('app.name', 'TaskManager') }}</title>
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
                <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-[11px] font-black shadow-md">
                403
            </span>
        </div>

        <div class="space-y-2">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                403 — Access Denied
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm mx-auto">
                You do not have the required permissions or membership level to access this protected resource. If you believe this is a mistake, request access from your workspace administrator.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-center gap-3 pt-2">
            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('dashboard') }}'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-semibold rounded-xl bg-white dark:bg-[#1e1f23] border border-slate-200 dark:border-[#35363b] text-slate-700 dark:text-slate-300 hover:border-purple-400 shadow-2xs transition-colors">
                ← Go Back
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
