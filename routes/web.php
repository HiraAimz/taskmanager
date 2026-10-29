<?php

declare(strict_types=1);

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentAttachmentController;
use App\Http\Controllers\MessageAttachmentController;
use App\Livewire\Admin\GlobalProgress;
use App\Livewire\Admin\RoleCreate;
use App\Livewire\Admin\RoleEdit;
use App\Livewire\Admin\RoleIndex;
use App\Livewire\Admin\UserCreate;
use App\Livewire\Admin\UserEdit;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Messages\DirectMessages;
use App\Livewire\Projects\ProjectCreate;
use App\Livewire\Projects\ProjectEdit;
use App\Livewire\Projects\ProjectIndex;
use App\Livewire\Projects\StatusManagement;
use App\Livewire\Tasks\MyTasks;
use App\Livewire\Tasks\TaskBoard;
use App\Livewire\Tasks\TaskCalendar;
use App\Livewire\Tasks\TaskList;
use App\Livewire\Workspaces\WorkspaceCreate;
use App\Livewire\Workspaces\WorkspaceEdit;
use App\Livewire\Workspaces\WorkspaceIndex;
use App\Livewire\Workspaces\WorkspaceProgress;
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

// Direct messaging binding & route
Route::model('role', \Spatie\Permission\Models\Role::class);

// Admin routes - Admin role or granular permissions
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', UserIndex::class)
        ->middleware('role_or_permission:Admin|user.view|manage-users')
        ->name('users');
    Route::get('/users/create', UserCreate::class)
        ->middleware('role_or_permission:Admin|user.create|manage-users')
        ->name('users.create');
    Route::get('/users/{user}/edit', UserEdit::class)
        ->middleware('role_or_permission:Admin|user.edit|user.manage_roles|manage-users')
        ->name('users.edit');

    Route::get('/roles', RoleIndex::class)
        ->middleware('role_or_permission:Admin|role.view|manage-users')
        ->name('roles');
    Route::get('/roles/create', RoleCreate::class)
        ->middleware('role_or_permission:Admin|role.create|manage-users')
        ->name('roles.create');
    Route::get('/roles/{role}/edit', RoleEdit::class)
        ->middleware('role_or_permission:Admin|role.edit|manage-users')
        ->name('roles.edit');
    Route::get('/progress', GlobalProgress::class)
        ->middleware('role_or_permission:Admin|manage-workspaces')
        ->name('progress');
});

// Direct Messages
Route::middleware(['auth', 'role_or_permission:Admin|message.access'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/{conversation?}', DirectMessages::class)->name('index');
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
    Route::get('/{workspace}/progress', WorkspaceProgress::class)
        ->name('progress');
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
    Route::get('/{project}/board', TaskBoard::class)
        ->name('board')
        ->can('view', 'project');
    Route::get('/{project}/list', TaskList::class)
        ->name('list')
        ->can('view', 'project');
    Route::get('/{project}/calendar', TaskCalendar::class)
        ->name('calendar')
        ->can('view', 'project');
});

// My Tasks Dashboard
Route::get('/my-tasks', MyTasks::class)
    ->name('my-tasks')
    ->middleware('auth');

// Notification Preferences
Route::get('/settings/notifications', \App\Livewire\Profile\NotificationPreferences::class)
    ->name('profile.notifications')
    ->middleware('auth');

// Attachment download and preview routes
Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
    ->name('attachments.download')
    ->middleware('auth');
Route::get('/attachments/{attachment}/preview', [AttachmentController::class, 'preview'])
    ->name('attachments.preview')
    ->middleware('auth');

// Comment attachment download and preview
Route::get('/comment-attachments/{attachment}/download', [CommentAttachmentController::class, 'download'])
    ->name('comment-attachments.download')
    ->middleware('auth');
Route::get('/comment-attachments/{attachment}/preview', [CommentAttachmentController::class, 'preview'])
    ->name('comment-attachments.preview')
    ->middleware('auth');

// Message attachment download and preview
Route::get('/message-attachments/{attachment}/download', [MessageAttachmentController::class, 'download'])
    ->name('message-attachments.download')
    ->middleware('auth');
Route::get('/message-attachments/{attachment}/preview', [MessageAttachmentController::class, 'preview'])
    ->name('message-attachments.preview')
    ->middleware('auth');

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
