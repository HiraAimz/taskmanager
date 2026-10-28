<x-layouts.guest>
    <x-slot name="title">Sign In - TaskManager</x-slot>

    <div class="space-y-6" x-data="{
        loading: false,
        email: '{{ old('email', '') }}',
        password: '',
        fill(e, p) {
            this.email = e;
            this.password = p;
            let emailInput = document.getElementById('email');
            let passInput = document.getElementById('password');
            if (emailInput) emailInput.value = e;
            if (passInput) passInput.value = p;
        }
    }">
        <div class="text-center">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Welcome back</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sign in to your ClickUp-style workspace</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300">
                <div class="font-bold flex items-center gap-1.5 mb-1">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Unable to sign in
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-600 dark:text-rose-300 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                ✓ {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" class="space-y-4" @submit="loading = true">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Email Address *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           x-model="email"
                           value="{{ old('email') }}"
                           placeholder="name@example.com" 
                           required 
                           autofocus
                           class="w-full text-xs font-semibold bg-white dark:bg-[#16171a] border border-slate-200 dark:border-[#323338] rounded-xl pl-10 pr-3.5 py-2.5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Password *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           x-model="password"
                           placeholder="••••••••" 
                           required
                           class="w-full text-xs font-semibold bg-white dark:bg-[#16171a] border border-slate-200 dark:border-[#323338] rounded-xl pl-10 pr-3.5 py-2.5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 dark:border-[#323338] text-purple-600 focus:ring-purple-500/20 accent-purple-600 dark:bg-[#16171a]">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-clickup-gradient text-white text-xs font-bold rounded-xl shadow-md hover:opacity-95 transition-all transform active:scale-98 disabled:opacity-50">
                <span x-show="!loading" class="flex items-center gap-1.5">
                    Sign In to Workspace
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
                <span x-show="loading" class="flex items-center gap-2" x-cloak>
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Signing in...
                </span>
            </button>
        </form>

        <!-- Quick Demo Credentials Box -->
        <div class="pt-4 border-t border-slate-100 dark:border-[#28292e]">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 text-center">Quick Demo Login</p>
            <div class="grid grid-cols-3 gap-1.5 text-center">
                <button type="button" @click="fill('admin@example.com', 'password')" class="px-2 py-1.5 rounded-lg bg-slate-50 dark:bg-[#202126] border border-slate-200 dark:border-[#323338] text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:border-purple-400 transition-colors">
                    👑 Admin
                </button>
                <button type="button" @click="fill('manager@example.com', 'password')" class="px-2 py-1.5 rounded-lg bg-slate-50 dark:bg-[#202126] border border-slate-200 dark:border-[#323338] text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:border-purple-400 transition-colors">
                    💼 Manager
                </button>
                <button type="button" @click="fill('member1@example.com', 'password')" class="px-2 py-1.5 rounded-lg bg-slate-50 dark:bg-[#202126] border border-slate-200 dark:border-[#323338] text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:border-purple-400 transition-colors">
                    👤 Member
                </button>
            </div>
        </div>
    </div>
</x-layouts.guest>
