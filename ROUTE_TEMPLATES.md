# Route Templates

After Laravel Breeze is installed, add these routes to `routes/web.php`.

## Complete routes/web.php

Replace the contents of `routes/web.php` with this:

```php
<?php

declare(strict_types=1);

use App\Livewire\Admin\UserCreate;
use App\Livewire\Admin\UserEdit;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Workspaces\WorkspaceCreate;
use App\Livewire\Workspaces\WorkspaceEdit;
use App\Livewire\Workspaces\WorkspaceIndex;
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

// Auth routes are included from routes/auth.php
// Remember to disable register routes in auth.php
require __DIR__.'/auth.php';
```

## Updates to routes/auth.php

Find and comment out the register routes:

```php
<?php

use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Public registration disabled - Admins create users
    // Route::get('register', Register::class)->name('register');

    Route::get('login', Login::class)->name('login');

    Route::get('forgot-password', ForgotPassword::class)->name('password.request');

    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');

    Route::get('confirm-password', ConfirmPassword::class)->name('password.confirm');
});
```

## Remove Register Links from Navigation

After Breeze is installed, check these files and remove any "Register" links:

1. `resources/views/livewire/layout/navigation.blade.php`
2. `resources/views/livewire/welcome/navigation.blade.php` (if exists)
3. Any other navigation components

Look for and remove sections like:

```blade
<a href="{{ route('register') }}">Register</a>
```

or

```blade
@if (Route::has('register'))
    <a href="{{ route('register') }}">Register</a>
@endif
```
