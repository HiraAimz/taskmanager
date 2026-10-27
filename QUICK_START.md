# Quick Start - Foundation Setup

## Prerequisites
✅ Laragon running with MySQL
✅ PHP 8.3+ and Composer
✅ Node.js and npm

## Setup Steps (Copy & Paste)

### 1. Create Database
```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS taskmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2. Install All Packages
```powershell
cd c:\laragon\www\taskmanager

# Laravel Breeze
composer require laravel/breeze --dev
php artisan breeze:install livewire --dark

# Spatie packages
composer require spatie/laravel-permission:^6.0
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
composer require spatie/laravel-activitylog:^4.0
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# Pest testing
composer require pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 --dev
php artisan pest:install

# Frontend
npm install
npm run build
```

### 3. Disable Registration
Edit `routes/auth.php` - comment out:
```php
// Route::get('register', Register::class)->name('register');
```

### 4. Create Livewire Components
```powershell
php artisan make:livewire Admin/UserIndex
php artisan make:livewire Admin/UserCreate
php artisan make:livewire Admin/UserEdit
php artisan make:livewire Workspaces/WorkspaceIndex
php artisan make:livewire Workspaces/WorkspaceCreate
php artisan make:livewire Workspaces/WorkspaceEdit
```

Then copy code from `COMPONENT_TEMPLATES.md` to each component.

### 5. Update Routes
Copy routes from `ROUTE_TEMPLATES.md` to:
- `routes/web.php`
- `routes/auth.php`

### 6. Migrate and Seed
```powershell
php artisan migrate:fresh --seed
```

### 7. Format and Test
```powershell
./vendor/bin/pint
php artisan test
```

### 8. Start Server
```powershell
php artisan serve
```

Visit http://localhost:8000/login
- Email: `admin@example.com`
- Password: `password`

## Core Files Already Created

✅ Models: User, Workspace
✅ Migrations: workspaces, workspace_user
✅ Seeders: RolePermissionSeeder (3 roles, 8 permissions)
✅ Actions: CreateUser, CreateWorkspace
✅ Policies: WorkspacePolicy
✅ Tests: 4 test files with 11 tests
✅ Enum: Role (Admin, Manager, Member)
✅ Factory: WorkspaceFactory
✅ Config: .env updated, app.php timezone

## What's Working

- ✅ MySQL database configuration
- ✅ Admin user seeding from .env
- ✅ Role-based access control
- ✅ Workspace membership
- ✅ Soft deletes on workspaces
- ✅ Activity logging
- ✅ Public registration disabled
- ✅ Lazy loading prevention (dev only)
- ✅ Dark mode UI

## Quick Verification

```powershell
# Check database
php artisan db:show

# Check migrations
php artisan migrate:status

# Check routes
php artisan route:list

# Run tests
php artisan test

# Check for code style issues
./vendor/bin/pint --test
```

## Common Issues

**Database error?** Check MySQL is running in Laragon

**Class not found?** Run `composer dump-autoload`

**Frontend not working?** Run `npm run build`

**Tests fail?** Run `php artisan migrate:fresh --seed` first

## Full Documentation

- 📖 **FOUNDATION_COMPLETE_GUIDE.md** - Complete guide
- 📖 **SETUP_INSTRUCTIONS.md** - Detailed instructions
- 📖 **COMPONENT_TEMPLATES.md** - Livewire component code
- 📖 **ROUTE_TEMPLATES.md** - Route configuration

## Expected Test Results

When you run `php artisan test`, you should see:

```
PASS  Tests\Feature\Auth\RegistrationTest
✓ public registration GET route returns 404
✓ public registration POST route returns 404

PASS  Tests\Feature\Admin\UserManagementTest
✓ admin can access user management
✓ manager cannot access user management
✓ member cannot access user management
✓ guest is redirected to login

PASS  Tests\Feature\WorkspaceTest
✓ member cannot see workspace they dont belong to
✓ admin can see all workspaces
✓ member can see their own workspace

PASS  Tests\Feature\SeederTest
✓ seeder creates roles permissions and admin

Tests:    11 passed (11 assertions)
Duration: < 1s
```

## Ready for Next Spec!

Once all tests pass, you're ready to implement:
- Spec 2: Projects and Lists
- Spec 3: Tasks and Assignments
- Spec 4: Comments and Activity
- Spec 5: Notifications and Reminders
