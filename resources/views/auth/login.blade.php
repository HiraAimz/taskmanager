<x-layouts.guest>
    <x-slot name="title">Login - TaskManager</x-slot>

    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Welcome back</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Sign in to your account to continue</p>
        </div>

        @if ($errors->any())
            <x-alert variant="danger" dismissible>
                <div class="font-medium">Whoops! Something went wrong.</div>
                <ul class="mt-2 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        @if (session('status'))
            <x-alert variant="success">
                {{ session('status') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ url('/login') }}" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <x-input
                name="email"
                type="email"
                label="Email Address"
                :value="old('email')"
                required
                :error="$errors->first('email')"
            >
                <x-slot name="icon">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </x-slot>
            </x-input>

            <x-input
                name="password"
                type="password"
                label="Password"
                required
                :error="$errors->first('password')"
            >
                <x-slot name="icon">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </x-slot>
            </x-input>

            <x-checkbox
                name="remember"
                label="Remember me"
                helper="Stay signed in for 30 days"
            />

            <x-button type="submit" variant="primary" class="w-full justify-center" x-bind:disabled="loading">
                <span x-show="!loading">Sign In</span>
                <span x-show="loading" class="flex items-center gap-2" x-cloak>
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Signing in...
                </span>
            </x-button>
        </form>

        <div class="text-center text-sm text-slate-600 dark:text-slate-400">
            <p>This is an internal tool. Contact your administrator for access.</p>
        </div>
    </div>
</x-layouts.guest>
