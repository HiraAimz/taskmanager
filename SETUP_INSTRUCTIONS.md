# Foundation Setup Instructions

## Overview
This setup implements Spec 1: Foundation - Authentication, Roles, and Workspaces for your internal task manager.

## Prerequisites
- Laragon must be running with MySQL service active
- PHP 8.3+ and Composer installed
- Node.js and npm installed

## Step-by-Step Setup

### 1. Database Setup
First, create the MySQL database in Laragon:
```powershell
# Open MySQL in Laragon, then create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS taskmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Or use Laragon's HeidiSQL to create a database named `taskmanager`.

### 2. Run the Setup Script
The core files have been created. Now run the automated setup script:

```powershell
# Make sure you're in the project root
cd c:\laragon\www\taskmanager

# Run the setup script
.\setup-foundation.ps1
```

This script will:
1. Install Laravel Breeze (Livewire stack with dark mode)
2. Install Spatie Permission package
3. Install Spatie Activity Log package  
4. Install Pest testing framework
5. Build frontend assets with npm
6. Run database migrations
7. Seed roles, permissions, and admin user
8. Format code with Laravel Pint
9. Run the test suite

### 3. After Breeze Installation
Once Breeze is installed, you need to:

#### A. Disable Public Registration
Edit `routes/auth.php` and comment out the register routes:

```php
// Public registration disabled - Admins create users
// Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
// Route::post('register', [RegisteredUserController::class, 'store']);
```

#### B. Create Livewire Components
Run these commands to create the Livewire components:

```powershell
php artisan make:livewire Admin/UserIndex
php artisan make:livewire Admin/UserCreate  
php artisan make:livewire Admin/UserEdit
php artisan make:livewire Workspaces/WorkspaceIndex
php artisan make:livewire Workspaces/WorkspaceCreate
php artisan make:livewire Workspaces/WorkspaceEdit
```

Then implement the components using the templates in the `COMPONENT_TEMPLATES.md` file.

#### C. Update Routes
Add the routes to `routes/web.php`. See `ROUTE_TEMPLATES.md` for the exact routes to add.

### 4. Verify the Installation

#### Test Login
```powershell
php artisan serve
```

Visit http://localhost:8000/login and log in with:
- **Email:** admin@example.com  
- **Password:** password

#### Run Tests
```powershell
php artisan test
```

All tests should pass:
- ✓ Public registration returns 404
- ✓ Admin can access user management
- ✓ Manager/Member cannot access user management  
- ✓ Seeder creates roles, permissions, and admin
- ✓ Workspace access control tests

### 5. Manual Verification

After logging in as admin, verify:
1. Navigate to `/admin/users` - should see user management
2. Navigate to `/workspaces` - should see workspace list
3. Try to access `/register` - should return 404
4. Create a test workspace
5. Create a test user with Member role
6. Log out and log in as the member - they should only see their workspaces

## What Was Implemented

### ✅ Models
- Updated User model with HasRoles, LogsActivity traits
- Created Workspace model with soft deletes
- Created Role enum (Admin, Manager, Member)

### ✅ Database
- Workspaces table migration
- workspace_user pivot table migration  
- Foreign keys with proper cascade/restrict rules
- Indexes on frequently queried columns

### ✅ Seeders
- RolePermissionSeeder creates 3 roles and 8 permissions
- Automatically creates admin user from .env credentials
- DatabaseSeeder calls RolePermissionSeeder

### ✅ Actions
- CreateUser action (used by admin to create users)
- CreateWorkspace action (creates workspace and attaches owner)

### ✅ Policies
- WorkspacePolicy enforcing admin-only creation/editing
- Member access restricted to workspaces they belong to

### ✅ Configuration
- .env configured for MySQL database
- Added ADMIN_EMAIL and ADMIN_PASSWORD variables
- Added APP_TIMEZONE and COMPANY_TIMEZONE
- config/app.php updated to use env variables
- AppServiceProvider prevents lazy loading in development

### ✅ Tests (Pest)
- Registration returns 404 tests
- Role-based access control tests
- Workspace membership tests
- Seeder verification tests

## Troubleshooting

### "Database does not exist" error
Create the database manually:
```powershell
mysql -u root -e "CREATE DATABASE taskmanager;"
```

### "Access denied for user 'root'" error  
Check your MySQL password in .env. Laragon default is empty password.

### npm install fails
Try clearing cache:
```powershell
npm cache clean --force
npm install
```

### Tests fail
Run migrations fresh:
```powershell
php artisan migrate:fresh --seed
php artisan test
```

## Next Steps

After the foundation is complete:
- Spec 2: Projects and Lists
- Spec 3: Tasks and Assignments  
- Spec 4: Comments and Activity Log
- Spec 5: Notifications and Reminders

## Files Created

```
app/
├── Actions/
│   ├── Users/
│   │   └── CreateUser.php
│   └── Workspaces/
│       └── CreateWorkspace.php
├── Enums/
│   └── Role.php
├── Models/
│   ├── User.php (updated)
│   └── Workspace.php
├── Policies/
│   └── WorkspacePolicy.php
└── Providers/
    └── AppServiceProvider.php (updated)

config/
└── app.php (updated)

database/
├── factories/
│   └── WorkspaceFactory.php
├── migrations/
│   ├── 2024_01_01_000003_create_workspaces_table.php
│   └── 2024_01_01_000004_create_workspace_user_table.php
└── seeders/
    ├── DatabaseSeeder.php (updated)
    └── RolePermissionSeeder.php

tests/
└── Feature/
    ├── Admin/
    │   └── UserManagementTest.php
    ├── Auth/
    │   └── RegistrationTest.php
    ├── SeederTest.php
    └── WorkspaceTest.php

.env (updated)
setup-foundation.ps1
```
