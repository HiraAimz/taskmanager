<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 shadow-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">TaskManager</h1>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('workspaces.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('workspaces.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <span class="ml-2">Workspaces</span>
                </a>

                <a href="{{ route('projects.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('projects.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <span class="ml-2">Projects</span>
                </a>
                
                @can('manage-users')
                <div class="mt-4">
                    <p class="px-6 text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Admin</p>
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <span class="ml-2">Users</span>
                    </a>
                </div>
                @endcan
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="flex justify-between items-center px-6 py-4">
                    <div class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $title ?? 'Dashboard' }}
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
