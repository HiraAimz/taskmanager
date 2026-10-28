<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'TaskManager') }}</title>
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
<body class="h-full font-sans antialiased bg-[#f7f8fa] dark:bg-[#141517] text-slate-900 dark:text-slate-100 flex flex-col justify-center items-center p-4 selection:bg-purple-500 selection:text-white"
      x-data="{ 
          darkMode: document.documentElement.classList.contains('dark'),
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
      }">

    <div class="w-full max-w-md space-y-6">
        <!-- Logo Header -->
        <div class="flex items-center justify-center gap-3">
            <div class="w-12 h-12 bg-clickup-gradient rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <span class="text-2xl font-black bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">TaskManager</span>
                <span class="block text-[10px] uppercase font-bold tracking-widest text-slate-400">Workspace Suite</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1a1b1e] rounded-2xl border border-slate-200/80 dark:border-[#2a2b2f] p-8 shadow-xl shadow-slate-200/50 dark:shadow-none">
            {{ $slot }}
        </div>

        <!-- Theme Switcher & Back Home -->
        <div class="flex items-center justify-between text-xs text-slate-400 px-2">
            <a href="{{ url('/') }}" class="hover:text-purple-600 transition-colors flex items-center gap-1 font-semibold">
                ← Back to Home
            </a>

            <button type="button" 
                    @click="toggleTheme()" 
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-[#1a1b1e] border border-slate-200 dark:border-[#2a2b2f] text-slate-600 dark:text-slate-300 hover:border-purple-400 shadow-2xs transition-all">
                <span x-show="!darkMode" class="flex items-center gap-1.5">
                    🌙 <span>Dark Mode</span>
                </span>
                <span x-show="darkMode" class="flex items-center gap-1.5" x-cloak>
                    ☀️ <span>Light Mode</span>
                </span>
            </button>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
