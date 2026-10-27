<?php

declare(strict_types=1);

use App\Livewire\Admin\UserCreate;
use App\Livewire\Admin\UserEdit;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Projects\ProjectCreate;
use App\Livewire\Projects\ProjectEdit;
use App\Livewire\Projects\ProjectIndex;
use App\Livewire\Projects\StatusManagement;
use App\Livewire\Workspaces\WorkspaceCreate;
use App\Livewire\Workspaces\WorkspaceEdit;
use App\Livewire\Workspaces\WorkspaceIndex;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirects to workspaces
Route::get('/dashboard', function () {
    return redirect()->route('workspaces.index');
})->middleware('auth')->name('dashboard');

// Admin routes - Admin role only
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', UserIndex::class)->name('users');
    Route::get('/users/create', UserCreate::class)->name('users.create');
    Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
});

// Workspace routes - authenticated users with policy checks
Route::middleware('auth')->prefix('workspaces')->name('workspaces.')->group(function () {
    Route::get('/', WorkspaceIndex::class)->name('index');
    Route::get('/create', WorkspaceCreate::class)
        ->name('create')
        ->can('create', Workspace::class);
    Route::get('/{workspace}/edit', WorkspaceEdit::class)
        ->name('edit')
        ->can('update', 'workspace');
});

// Project routes - authenticated users with policy checks
Route::middleware('auth')->prefix('projects')->name('projects.')->group(function () {
    Route::get('/', ProjectIndex::class)->name('index');
    Route::get('/create', ProjectCreate::class)
        ->name('create')
        ->can('create', Project::class);
    Route::get('/{project}/edit', ProjectEdit::class)
        ->name('edit')
        ->can('update', 'project');
    Route::get('/{project}/statuses', StatusManagement::class)
        ->name('statuses')
        ->can('update', 'project');
});

// Login and logout routes - no public registration
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials)) {
            request()->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

// Public registration disabled - returns 404
Route::get('/register', fn () => abort(404));
Route::post('/register', fn () => abort(404));
