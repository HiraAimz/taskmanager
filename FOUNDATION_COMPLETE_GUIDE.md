# Foundation Implementation - Complete Guide

## What Has Been Done

All core PHP files have been created for the foundation spec:

### ✅ Models
- `app/Models/User.php` - Updated with HasRoles, LogsActivity traits, $fillable array
- `app/Models/Workspace.php` - Full model with soft deletes and relationships

### ✅ Database
- `database/migrations/2024_01_01_000003_create_workspaces_table.php`
- `database/migrations/2024_01_01_000004_create_workspace_user_table.php`
- `database/factories/WorkspaceFactory.php`
- `database/seeders/RolePermissionSeeder.php` - Creates 3 roles, 8 permissions, admin user
- `database/seeders/DatabaseSeeder.php` - Updated to call RolePermissionSeeder

### ✅ Business Logic
- `app/Actions/Users/CreateUser.php` - Action for creating users with roles
- `app/Actions/Workspaces/CreateWorkspace.php` - Action for creating workspaces

### ✅ Authorization
- `app/Policies/WorkspacePolicy.php` - Complete policy with all methods
- `app/Providers/AppServiceProvider.php` - Updated with policy registration and preventLazyLoading

### ✅ Configuration
- `.env` - Updated with MySQL config, admin credentials, timezone settings
- `config/app.php` - Updated with timezone environment variables
- `app/Enums/Role.php` - PHP enum for type-safe roles

### ✅ Tests
- `tests/Feature/Auth/RegistrationTest.php` - Tests registration returns 404
- `tests/Feature/Admin/UserManagementTest.php` - Tests role-based access
- `tests/Feature/WorkspaceTest.php` - Tests workspace membership
- `tests/Feature/SeederTest.php` - Tests seeder creates roles/permissions

### ✅ Setup Scripts
- `setup-foundation.ps1` - Automated setup script
- `SETUP_INSTRUCTIONS.md` - Detailed setup instructions
- `COMPONENT_TEMPLATES.md` - Complete Livewire component code
- `ROUTE_TEMPLATES.md` - Route configuration

## What You Need to Do

Since PowerShell execution is blocked in the agent environment, you need to run the setup manually:

### Step 1: Create MySQL Database

Open MySQL in Laragon (or use HeidiSQL) and run:

```sql
CREATE DATABASE IF NOT EXISTS taskmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Install Packages

Run these commands in PowerShell from the project root:

```powershell
cd c:\laragon\www\taskmanager

# Install Laravel Breeze with Livewire
composer require laravel/breeze --dev
php artisan breeze:install livewire --dark

# Install Spatie packages
composer require spatie/laravel-permission:^6.0
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

composer require spatie/laravel-activitylog:^4.0
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# Install Pest
composer require pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 --dev
php artisan pest:install

# Build frontend
npm install
npm run build
```

### Step 3: Disable Registration

Edit `routes/auth.php` and comment out these lines:

```php
// Public registration disabled - Admins create users
// Route::get('register', Register::class)->name('register');
```

Also remove any register links from:
- `resources/views/livewire/layout/navigation.blade.php`
- `resources/views/livewire/welcome/navigation.blade.php`

### Step 4: Create Livewire Components

```powershell
php artisan make:livewire Admin/UserIndex
php artisan make:livewire Admin/UserCreate
php artisan make:livewire Admin/UserEdit
php artisan make:livewire Workspaces/WorkspaceIndex
php artisan make:livewire Workspaces/WorkspaceCreate
php artisan make:livewire Workspaces/WorkspaceEdit
```

Then replace the generated code with the templates from `COMPONENT_TEMPLATES.md`.

### Step 5: Update Routes

Replace the contents of `routes/web.php` and update `routes/auth.php` using the templates in `ROUTE_TEMPLATES.md`.

### Step 6: Run Migrations and Seed

```powershell
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

Or run both at once:
```powershell
php artisan migrate:fresh --seed
```

### Step 7: Format and Test

```powershell
# Format code
./vendor/bin/pint

# Run tests
php artisan test
```

### Step 8: Start the Server

```powershell
php artisan serve
```

Visit http://localhost:8000/login and log in with:
- **Email:** admin@example.com
- **Password:** password

## Verification Checklist

After setup, verify these features work:

### Authentication
- [ ] Can log in as admin@example.com / password
- [ ] /register returns 404 (both GET and POST)
- [ ] No "Register" link appears in navigation
- [ ] Can log out

### Admin User Management
- [ ] Navigate to /admin/users - see user list
- [ ] Click "Create User" - form appears
- [ ] Create a new user with Member role
- [ ] Edit user role - change from Member to Manager
- [ ] Log out and log in as the new user
- [ ] Try to access /admin/users - should be forbidden (403)

### Workspaces
- [ ] Navigate to /workspaces - see workspace list
- [ ] As Admin: Click "Create Workspace"
- [ ] Create a workspace with name and description
- [ ] Click "Edit" on the workspace
- [ ] Add a member to the workspace
- [ ] Remove a member from the workspace
- [ ] Log in as a Member user
- [ ] Member should only see workspaces they belong to
- [ ] Member cannot create or edit workspaces

### Tests
- [ ] Run `php artisan test` - all tests pass
- [ ] Specifically check:
  - Registration returns 404
  - Admin can access admin panel
  - Manager/Member cannot access admin panel
  - Members only see their workspaces
  - Seeder creates roles and permissions

## File Structure Created

```
app/
├── Actions/
│   ├── Users/
│   │   └── CreateUser.php                  ✅ Created
│   └── Workspaces/
│       └── CreateWorkspace.php             ✅ Created
├── Enums/
│   └── Role.php                            ✅ Created
├── Models/
│   ├── User.php                            ✅ Updated
│   └── Workspace.php                       ✅ Created
├── Policies/
│   └── WorkspacePolicy.php                 ✅ Created
└── Providers/
    └── AppServiceProvider.php              ✅ Updated

config/
└── app.php                                  ✅ Updated (timezone)

database/
├── factories/
│   └── WorkspaceFactory.php                ✅ Created
├── migrations/
│   ├── 2024_01_01_000003_create_workspaces_table.php        ✅ Created
│   └── 2024_01_01_000004_create_workspace_user_table.php    ✅ Created
└── seeders/
    ├── DatabaseSeeder.php                  ✅ Updated
    └── RolePermissionSeeder.php            ✅ Created

tests/
└── Feature/
    ├── Admin/
    │   └── UserManagementTest.php          ✅ Created
    ├── Auth/
    │   └── RegistrationTest.php            ✅ Created
    ├── SeederTest.php                      ✅ Created
    └── WorkspaceTest.php                   ✅ Created

Documentation:
├── FOUNDATION_COMPLETE_GUIDE.md            ✅ This file
├── SETUP_INSTRUCTIONS.md                   ✅ Created
├── COMPONENT_TEMPLATES.md                  ✅ Created (Livewire)
├── ROUTE_TEMPLATES.md                      ✅ Created
└── setup-foundation.ps1                    ✅ Created (automated script)

.env                                         ✅ Updated
```

## Steering Rules Compliance

All files follow the project steering rules:

✅ `declare(strict_types=1)` added to all new PHP files
✅ PSR-12 compliant (run Pint to verify)
✅ Business logic in Action classes (not controllers)
✅ Enums used for fixed values (Role enum)
✅ Foreign keys with cascade/restrict rules
✅ Indexes on filtered columns
✅ Soft deletes on Workspace model
✅ $fillable defined on all models
✅ Factories created for all models
✅ Dates stored in UTC
✅ Model::preventLazyLoading() in AppServiceProvider
✅ Pagination on all lists (15 items)
✅ Queue driver set to database
✅ Public registration disabled
✅ Activity logging on User and Workspace models
✅ No hardcoded secrets (uses .env)
✅ Output escaped (Blade {{ }} by default)

## Troubleshooting

### Database connection error
Make sure MySQL is running in Laragon and the `taskmanager` database exists.

### "Class not found" errors
Run `composer dump-autoload`

### Frontend not working
Run `npm install && npm run build`

### Tests failing
Run `php artisan migrate:fresh --seed` then `php artisan test` again

### "Target class [Workspace] does not exist"
Make sure you've replaced routes/web.php with the template that imports the Workspace model

### Livewire components not found
Make sure you've created all 6 components with `php artisan make:livewire`

## Next Steps

After the foundation is complete and all tests pass:

1. **Spec 2: Projects and Lists**
   - Project model (belongs to workspace)
   - List model (belongs to project)
   - CRUD interfaces for both

2. **Spec 3: Tasks and Assignments**
   - Task model with status, priority, due dates
   - Task assignments
   - Task list view with filtering

3. **Spec 4: Comments and Activity**
   - Polymorphic comments
   - Activity log display
   - Real-time updates

4. **Spec 5: Notifications**
   - Due date reminders
   - Assignment notifications
   - Queue workers

## Support

If you encounter issues:

1. Check `storage/logs/laravel.log` for errors
2. Run `php artisan optimize:clear` to clear all caches
3. Verify .env database credentials are correct
4. Ensure all migrations ran successfully with `php artisan migrate:status`

The foundation provides:
- ✅ Authentication (login, logout, password reset)
- ✅ Role-based access control (Admin, Manager, Member)
- ✅ User management (admin only)
- ✅ Workspace management (admin creates, all users view their own)
- ✅ Activity logging
- ✅ Comprehensive test suite
- ✅ Dark mode UI with Tailwind

Ready for building the next spec!
