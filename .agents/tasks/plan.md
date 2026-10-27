# Implementation Plan: Foundation - Authentication, Roles, and Workspaces

This plan implements Spec 1 for a Laravel 13 ClickUp-style task manager with authentication, roles (Admin/Manager/Member), and workspaces. The app currently has no packages installed beyond core Laravel.

---

## Phase 1: Package Installation & Setup

- [ ] 1. Install Laravel Breeze with Livewire stack (dark mode)
      Run `composer require laravel/breeze --dev` then `php artisan breeze:install livewire --dark`.
      Accept defaults when prompted. Do NOT run npm install yet.
      **Gotcha:** Breeze will publish auth routes into routes/auth.php (included from routes/web.php) and create Livewire components in app/Livewire. We'll need to remove register routes in step 8.
      Files: composer.json, routes/auth.php, routes/web.php, app/Livewire/*, resources/views/livewire/*
      Verify: Check that routes/auth.php exists and contains register routes; app/Livewire directory created.

- [ ] 2. Install spatie/laravel-permission and publish migration
      Run `composer require spatie/laravel-permission:^6.0` then `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`.
      Files: composer.json, database/migrations/*_create_permission_tables.php, config/permission.php
      Verify: Check that database/migrations contains the spatie permission migration file.

- [ ] 3. Install spatie/laravel-activitylog and publish migration
      Run `composer require spatie/laravel-activitylog:^4.0` then `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"`.
      Files: composer.json, database/migrations/*_create_activity_log_table.php, config/activitylog.php
      Verify: Check that database/migrations contains the activitylog migration file.

- [ ] 4. Install Pest testing framework
      Run `composer require pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 --dev` then `php artisan pest:install`.
      Files: composer.json, tests/Pest.php
      Verify: Check that tests/Pest.php exists and composer.json includes pest packages.

- [ ] 5. Install and build frontend assets
      Run `npm install` then `npm run build`.
      Files: node_modules/, public/build/
      Verify: Check that public/build/manifest.json exists.

---

## Phase 2: Configuration

- [ ] 6. Configure database and environment variables
      Modify .env: Change DB_CONNECTION=mysql, uncomment and set DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=taskmanager, DB_USERNAME=root, DB_PASSWORD= (empty for Laragon). Add ADMIN_EMAIL=admin@example.com, ADMIN_PASSWORD=password, APP_TIMEZONE=UTC, COMPANY_TIMEZONE=UTC. QUEUE_CONNECTION=database is already set.
      Files: .env
      Verify: Check .env contains DB_CONNECTION=mysql, ADMIN_EMAIL, ADMIN_PASSWORD, APP_TIMEZONE, COMPANY_TIMEZONE.

- [ ] 7. Update config/app.php timezone settings
      Change 'timezone' => 'UTC' to 'timezone' => env('APP_TIMEZONE', 'UTC'). Add 'company_timezone' => env('COMPANY_TIMEZONE', 'UTC') in the array (after timezone).
      Files: config/app.php
      Verify: Run `php artisan config:cache` then `php artisan tinker --execute="echo config('app.timezone'); echo config('app.company_timezone');"` and confirm both output UTC.

---

## Phase 3: Database Migrations

- [ ] 8. Create workspaces table migration
      Run `php artisan make:migration create_workspaces_table`. Add columns: id, name (string, indexed), description (text nullable), owner_id (foreignId constrained to users with restrictOnDelete), timestamps, softDeletes. Add index on owner_id.
      Files: database/migrations/*_create_workspaces_table.php
      Verify: Read the migration file and confirm schema matches: name index, owner_id FK with restrict, softDeletes.

- [ ] 9. Create workspace_user pivot table migration
      Run `php artisan make:migration create_workspace_user_table`. Add columns: workspace_id (foreignId constrained to workspaces with cascadeOnDelete), user_id (foreignId constrained to users with cascadeOnDelete), role (string nullable for member-level role), timestamps. Primary key on (workspace_id, user_id).
      Files: database/migrations/*_create_workspace_user_table.php
      Verify: Read the migration file and confirm composite primary key, both FKs with cascade.

- [ ] 10. Run migrations
       Run `php artisan migrate` (NOT migrate:fresh).
       Files: database/database.sqlite (or MySQL tables)
       Verify: Run `php artisan db:show` and confirm tables exist: users, workspaces, workspace_user, permissions, roles, model_has_permissions, model_has_roles, role_has_permissions, activity_log, cache, cache_locks, jobs, job_batches, failed_jobs, password_reset_tokens, sessions.

---

## Phase 4: Models

- [ ] 11. Update User model with traits and $fillable array
       Modify app/Models/User.php: add `declare(strict_types=1);` at top after `<?php`. Remove #[Fillable] and #[Hidden] attributes. Add explicit properties: `protected $fillable = ['name', 'email', 'password'];` and `protected $hidden = ['password', 'remember_token'];`. Add `use Spatie\Permission\Traits\HasRoles;`, `use Spatie\Activitylog\Traits\LogsActivity;`, `use Spatie\Activitylog\LogOptions;` at the top. Add HasRoles trait to the class. Add LogsActivity trait to the class. Add method `public function getActivitylogOptions(): LogOptions { return LogOptions::defaults()->logFillable(); }`. Keep the casts() method.
       Files: app/Models/User.php
       Verify: Run `php artisan tinker --execute="$u = new App\Models\User; var_dump(\$u->getFillable());"` and confirm ['name', 'email', 'password'] output. Confirm no PHP errors.

- [ ] 12. Create Workspace model
       Run `php artisan make:model Workspace` (model only, migration already created). Add `declare(strict_types=1);` at top. Add `use Illuminate\Database\Eloquent\SoftDeletes;`, `use Spatie\Activitylog\Traits\LogsActivity;`, `use Spatie\Activitylog\LogOptions;`. Add traits: HasFactory, SoftDeletes, LogsActivity. Add `protected $fillable = ['name', 'description', 'owner_id'];`. Add relationships: `public function owner() { return $this->belongsTo(User::class, 'owner_id'); }` and `public function users() { return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps(); }`. Add `public function getActivitylogOptions(): LogOptions { return LogOptions::defaults()->logFillable(); }`.
       Files: app/Models/Workspace.php
       Verify: Run `php artisan tinker --execute="$w = new App\Models\Workspace; var_dump(\$w->getFillable());"` and confirm ['name', 'description', 'owner_id'] output.

---

## Phase 5: Factories & Seeders

- [ ] 13. Create WorkspaceFactory
       Run `php artisan make:factory WorkspaceFactory`. Add `declare(strict_types=1);`. Define definition() method returning: `['name' => fake()->company(), 'description' => fake()->sentence(), 'owner_id' => User::factory()]`. Import User model.
       Files: database/factories/WorkspaceFactory.php
       Verify: Run `php artisan tinker --execute="Workspace::factory()->count(1)->make();"` and confirm no errors (won't persist because owner doesn't exist yet).

- [ ] 14. Create RolePermissionSeeder
       Run `php artisan make:seeder RolePermissionSeeder`. Add `declare(strict_types=1);`. In run() method: create permissions array ['manage-users','manage-workspaces','manage-projects','create-task','edit-task','delete-task','assign-task','comment-task'] using `Permission::create(['name' => ...])`. Create roles Admin, Manager, Member using `Role::create(['name' => ...])`. Assign ALL permissions to Admin using `$admin->givePermissionTo(Permission::all())`. Assign [manage-workspaces, manage-projects, create-task, edit-task, delete-task, assign-task, comment-task] to Manager. Assign [create-task, edit-task, comment-task] to Member. Find or create default admin user: `User::firstOrCreate(['email' => env('ADMIN_EMAIL')], ['name' => 'Admin User', 'password' => Hash::make(env('ADMIN_PASSWORD'))])` then assign Admin role with `$user->assignRole('Admin')`. Import User, Role, Permission models and Hash facade.
       Files: database/seeders/RolePermissionSeeder.php
       Verify: Run `php artisan db:seed --class=RolePermissionSeeder` then `php artisan tinker --execute="echo Role::count(); echo Permission::count(); echo User::role('Admin')->count();"` and confirm 3 roles, 8 permissions, 1 admin user.

- [ ] 15. Update DatabaseSeeder to call RolePermissionSeeder
       Modify database/seeders/DatabaseSeeder.php: in run() method, call `$this->call(RolePermissionSeeder::class);`.
       Files: database/seeders/DatabaseSeeder.php
       Verify: Run `php artisan migrate:fresh --seed` then `php artisan tinker --execute="echo User::whereHas('roles', fn(\$q) => \$q->where('name', 'Admin'))->count();"` and confirm 1.

---

## Phase 6: Enums

- [ ] 16. Create Role enum
       Create directory app/Enums if it doesn't exist. Create app/Enums/Role.php with `declare(strict_types=1);`. Define `enum Role: string { case Admin = 'Admin'; case Manager = 'Manager'; case Member = 'Member'; }`.
       Files: app/Enums/Role.php
       Verify: Run `php artisan tinker --execute="echo App\Enums\Role::Admin->value;"` and confirm 'Admin' output.

---

## Phase 7: Actions

- [ ] 17. Create CreateUser action
       Create directory app/Actions/Users. Create app/Actions/Users/CreateUser.php with `declare(strict_types=1);`. Create class with `public function handle(array $data): User` method. Validate required fields (name, email, password, role). Hash password using Hash::make(). Create User model. Assign role using `$user->assignRole($data['role'])`. Log activity. Return user. Import User, Hash, and handle exceptions.
       Files: app/Actions/Users/CreateUser.php
       Verify: Run `php artisan tinker --execute="\$action = new App\Actions\Users\CreateUser; \$user = \$action->handle(['name' => 'Test', 'email' => 'test@example.com', 'password' => 'password', 'role' => 'Member']); echo \$user->id;"` and confirm user ID output.

- [ ] 18. Create CreateWorkspace action
       Create directory app/Actions/Workspaces. Create app/Actions/Workspaces/CreateWorkspace.php with `declare(strict_types=1);`. Create class with `public function handle(array $data, User $owner): Workspace` method. Create Workspace with owner_id. Attach owner to workspace users pivot with `$workspace->users()->attach($owner->id)`. Return workspace. Import User and Workspace models.
       Files: app/Actions/Workspaces/CreateWorkspace.php
       Verify: Run `php artisan tinker --execute="\$user = User::first(); \$action = new App\Actions\Workspaces\CreateWorkspace; \$ws = \$action->handle(['name' => 'Test WS', 'description' => 'Test'], \$user); echo \$ws->users->count();"` and confirm 1.

---

## Phase 8: Policies

- [ ] 19. Create WorkspacePolicy
       Run `php artisan make:policy WorkspacePolicy --model=Workspace`. Add `declare(strict_types=1);`. Implement: `viewAny(User $user)` returns `$user->hasRole('Admin')` (admins see all; non-admins rely on query scope so return false here to block direct access). `view(User $user, Workspace $workspace)` returns `$user->hasRole('Admin') || $workspace->users->contains($user)`. `create(User $user)` returns `$user->hasRole('Admin')`. `update(User $user, Workspace $workspace)` returns `$user->hasRole('Admin')`. `delete(User $user, Workspace $workspace)` returns `$user->hasRole('Admin')`. Add custom methods `addMember` and `removeMember` both returning `$user->hasRole('Admin')`.
       Files: app/Policies/WorkspacePolicy.php
       Verify: Run `php artisan tinker --execute="\$admin = User::role('Admin')->first(); \$policy = new App\Policies\WorkspacePolicy; var_dump(\$policy->create(\$admin));"` and confirm true output.

- [ ] 20. Register WorkspacePolicy in AppServiceProvider
       Modify app/Providers/AppServiceProvider.php: add `declare(strict_types=1);`. In boot() method, call `Model::preventLazyLoading(!app()->isProduction());`. Add `use Illuminate\Database\Eloquent\Model;` at top. Register WorkspacePolicy: add `use Illuminate\Support\Facades\Gate;` and in boot() add `Gate::policy(Workspace::class, WorkspacePolicy::class);`. Import Workspace model and WorkspacePolicy.
       Files: app/Providers/AppServiceProvider.php
       Verify: Run `php artisan tinker --execute="echo 'Policies registered';"` and confirm no errors after clearing cache with `php artisan optimize:clear`.

---

## Phase 9: Authentication Changes

- [ ] 21. Disable public registration routes
       Modify routes/auth.php: comment out or remove the two register routes: `Route::get('register', ...)` and `Route::post('register', ...)`. Add a comment `// Public registration disabled - Admins create users`.
       **Gotcha:** Breeze includes register routes in routes/auth.php which is included from routes/web.php via `require __DIR__.'/auth.php';`.
       Files: routes/auth.php
       Verify: Run `php artisan route:list --path=register` and confirm no register routes exist.

- [ ] 22. Remove register links from Breeze views
       Find and remove register links from navigation. Check resources/views/livewire/layout/navigation.blade.php and resources/views/livewire/welcome/navigation.blade.php (or wherever Breeze placed nav links). Remove or comment out any "Register" links.
       Files: resources/views/livewire/layout/navigation.blade.php, resources/views/livewire/welcome/navigation.blade.php (or similar)
       Verify: Search for "register" in resources/views with `grep -r "register" resources/views/livewire` and confirm no user-facing register links remain.

---

## Phase 10: Livewire Components

- [ ] 23. Create Admin\UserIndex Livewire component
       Run `php artisan make:livewire Admin/UserIndex`. Add `declare(strict_types=1);` to class. In render() method, return paginated users with roles: `User::with('roles')->paginate(15)`. Pass to view 'livewire.admin.user-index'. In the view, create table with columns: Name, Email, Roles, Actions (link to edit). Use @foreach loop with pagination links. Add "Create User" button linking to admin.user-create route.
       Files: app/Livewire/Admin/UserIndex.php, resources/views/livewire/admin/user-index.blade.php
       Verify: Create route and visit /admin/users after authentication (see step 27). Confirm user list displays.

- [ ] 24. Create Admin\UserCreate Livewire component
       Run `php artisan make:livewire Admin/UserCreate`. Add `declare(strict_types=1);` to class. Add public properties: $name, $email, $password, $role. Add validation rules in rules() method. Create save() method that calls CreateUser action, flashes success message, and redirects to admin.users route. In view, create form with name, email, password, role dropdown (use App\Enums\Role cases for options). Add submit button.
       Files: app/Livewire/Admin/UserCreate.php, resources/views/livewire/admin/user-create.blade.php
       Verify: Visit /admin/users/create, fill form, submit, confirm user created and redirects to user list.

- [ ] 25. Create Admin\UserEdit Livewire component
       Run `php artisan make:livewire Admin/UserEdit`. Add `declare(strict_types=1);` to class. Accept User $user as mount parameter. Add public property $role. In mount(), set $role to user's current role name. Create updateRole() method that syncs role: `$this->user->syncRoles([$this->role])`, flashes success, redirects to admin.users. In view, show user name/email (read-only) and role dropdown with submit button.
       Files: app/Livewire/Admin/UserEdit.php, resources/views/livewire/admin/user-edit.blade.php
       Verify: Visit /admin/users/{user}/edit, change role, submit, confirm role updated.

- [ ] 26. Create Workspaces\WorkspaceIndex Livewire component
       Run `php artisan make:livewire Workspaces/WorkspaceIndex`. Add `declare(strict_types=1);` to class. In render(), if user hasRole('Admin'), return all workspaces paginated: `Workspace::with('owner')->paginate(15)`. Otherwise return workspaces the user belongs to: `auth()->user()->workspaces()->with('owner')->paginate(15)`. Pass to view 'livewire.workspaces.workspace-index'. In view, create cards or table with workspace name, description, owner name. Add "Create Workspace" button (visible only @role('Admin')) linking to workspaces.create route.
       Files: app/Livewire/Workspaces/WorkspaceIndex.php, resources/views/livewire/workspaces/workspace-index.blade.php
       Verify: Visit /workspaces as admin and member, confirm admin sees all, member sees only theirs.

- [ ] 27. Create Workspaces\WorkspaceCreate Livewire component
       Run `php artisan make:livewire Workspaces/WorkspaceCreate`. Add `declare(strict_types=1);` to class. Add public properties: $name, $description. Add validation rules. Create save() method that calls CreateWorkspace action with auth()->user() as owner, flashes success, redirects to workspaces.index. In view, create form with name and description fields, submit button.
       Files: app/Livewire/Workspaces/WorkspaceCreate.php, resources/views/livewire/workspaces/workspace-create.blade.php
       Verify: Visit /workspaces/create as admin, create workspace, confirm created and redirects.

- [ ] 28. Create Workspaces\WorkspaceEdit Livewire component
       Run `php artisan make:livewire Workspaces/WorkspaceEdit`. Add `declare(strict_types=1);` to class. Accept Workspace $workspace as mount parameter. Add public properties: $name, $description, $selectedUserId. In mount(), set $name and $description from workspace. Create update() method to update workspace. Create addMember() method to attach user: `$this->workspace->users()->attach($this->selectedUserId)`. Create removeMember($userId) method to detach user. In view, show edit form for name/description, and a section to manage members: dropdown of all users not yet members, "Add" button, list of current members with "Remove" button next to each.
       Files: app/Livewire/Workspaces/WorkspaceEdit.php, resources/views/livewire/workspaces/workspace-edit.blade.php
       Verify: Visit /workspaces/{workspace}/edit as admin, update workspace, add/remove members, confirm changes persist.

---

## Phase 11: Routes

- [ ] 29. Define admin routes with role middleware
       Modify routes/web.php. Add admin routes group with middleware(['auth', 'role:Admin']): Route::get('/admin/users', UserIndex::class)->name('admin.users'), Route::get('/admin/users/create', UserCreate::class)->name('admin.users.create'), Route::get('/admin/users/{user}/edit', UserEdit::class)->name('admin.users.edit'). Import Livewire components at top.
       Files: routes/web.php
       Verify: Run `php artisan route:list --path=admin` and confirm all admin routes have auth and role:Admin middleware.

- [ ] 30. Define workspace routes with auth and policy middleware
       In routes/web.php, add workspace routes group with middleware('auth'): Route::get('/workspaces', WorkspaceIndex::class)->name('workspaces.index'), Route::get('/workspaces/create', WorkspaceCreate::class)->name('workspaces.create')->can('create', Workspace::class), Route::get('/workspaces/{workspace}/edit', WorkspaceEdit::class)->name('workspaces.edit')->can('update', 'workspace'). Import Workspace model and workspace Livewire components.
       Files: routes/web.php
       Verify: Run `php artisan route:list --path=workspaces` and confirm all workspace routes have auth middleware. Access control verified by policy.

- [ ] 31. Add dashboard route redirecting to workspaces
       In routes/web.php, add Route::get('/dashboard', function() { return redirect()->route('workspaces.index'); })->middleware('auth')->name('dashboard'); (Breeze expects a dashboard route).
       Files: routes/web.php
       Verify: Run `php artisan route:list --name=dashboard` and confirm route exists.

---

## Phase 12: Layout & UI

- [ ] 32. Create or modify main layout with sidebar
       Check if Breeze created resources/views/components/layout.blade.php or resources/views/layouts/app.blade.php. Modify to include a sidebar with navigation: Workspaces link (visible to all auth users), Admin section with Users link (visible only @role('Admin')). Top bar should have user name and logout button (Breeze likely provides this already in its navigation component). Use Tailwind utility classes.
       Files: resources/views/layouts/app.blade.php or resources/views/components/layout.blade.php or resources/views/livewire/layout/navigation.blade.php
       Verify: Visit /workspaces after login, confirm sidebar shows Workspaces link for all users, Admin > Users link only for admins.

---

## Phase 13: Tests (Pest)

- [ ] 33. Create Auth/RegistrationTest for public registration
       Create tests/Feature/Auth/RegistrationTest.php. Add `declare(strict_types=1);`. Write test `test_public_registration_returns_404`: attempt to GET /register and assert status 404. Attempt to POST /register and assert status 404.
       Files: tests/Feature/Auth/RegistrationTest.php
       Verify: Run `php artisan test --filter=RegistrationTest` and confirm test passes.

- [ ] 34. Create Admin/UserManagementTest for role-based access
       Create tests/Feature/Admin/UserManagementTest.php. Add `declare(strict_types=1);`. Write tests: `test_admin_can_access_user_management` (create admin user with role, actingAs, GET /admin/users, assert 200), `test_manager_cannot_access_user_management` (create manager user with role, actingAs, GET /admin/users, assert 403), `test_member_cannot_access_user_management` (create member user with role, actingAs, GET /admin/users, assert 403), `test_guest_redirected_to_login` (GET /admin/users without auth, assert redirect to login).
       Files: tests/Feature/Admin/UserManagementTest.php
       Verify: Run `php artisan test --filter=UserManagementTest` and confirm all tests pass.

- [ ] 35. Create WorkspaceTest for workspace access control
       Create tests/Feature/WorkspaceTest.php. Add `declare(strict_types=1);`. Write tests: `test_member_cannot_see_workspace_they_dont_belong_to` (create two members, create workspace for member1, actingAs member2, visit /workspaces, assert workspace name not in response), `test_admin_can_see_all_workspaces` (create admin, create workspace for another user, actingAs admin, visit /workspaces, assert workspace visible), `test_member_can_see_their_own_workspace` (create member, create workspace with member attached, actingAs member, visit /workspaces, assert workspace visible).
       Files: tests/Feature/WorkspaceTest.php
       Verify: Run `php artisan test --filter=WorkspaceTest` and confirm all tests pass.

- [ ] 36. Create SeederTest for database seeding
       Create tests/Feature/SeederTest.php. Add `declare(strict_types=1);`. Write test `test_seeder_creates_roles_permissions_and_admin`: run `$this->artisan('db:seed', ['--class' => 'RolePermissionSeeder'])`, assert Role::count() is 3, assert Permission::count() is 8, assert User::role('Admin')->count() is 1.
       Files: tests/Feature/SeederTest.php
       Verify: Run `php artisan test --filter=SeederTest` and confirm test passes.

---

## Phase 14: Finalization

- [ ] 37. Run Laravel Pint to format all PHP files
       Run `./vendor/bin/pint` from project root to format all PHP files according to PSR-12.
       Files: All PHP files
       Verify: Run `./vendor/bin/pint --test` and confirm no issues.

- [ ] 38. Run full test suite
       Run `php artisan test` to execute all Pest tests.
       Files: N/A
       Verify: All tests pass. If any fail, fix the issues and re-run until green.

- [ ] 39. Verify migrations and database state
       Run `php artisan migrate:fresh --seed` to reset database and seed. Verify no errors. Log in with ADMIN_EMAIL/ADMIN_PASSWORD from .env. Navigate through: /admin/users (admin only), /workspaces (all users), create a workspace, create a user, assign roles.
       Files: N/A
       Verify: Manual verification that all features work: login, admin can create users, admin can create/manage workspaces, members only see their workspaces, public registration disabled.

---

## Key Gotchas & Notes

1. **User model attributes:** Laravel 13 uses PHP 8 #[Fillable] and #[Hidden] attributes by default. The steering rules require explicit $fillable arrays, so step 11 removes attributes and adds properties.

2. **Register routes location:** Breeze publishes auth routes into routes/auth.php which is included from routes/web.php. Register routes must be removed from routes/auth.php (step 21), not web.php.

3. **WorkspacePolicy viewAny:** Returns false for non-admins because we rely on query scoping in the Livewire component (WorkspaceIndex filters by user's workspaces). Returning true would bypass the per-workspace membership check.

4. **MySQL configuration:** .env must switch from sqlite to mysql. Laragon default is root user with empty password. Ensure MySQL service is running in Laragon before running migrations.

5. **Breeze dark mode:** The `breeze:install livewire --dark` command sets up dark mode by default. All custom Blade views should follow the Tailwind dark: variants pattern Breeze uses.

6. **Pagination:** All lists (users, workspaces) paginate at 15 items per page per steering rules.

7. **Activity logging:** User and Workspace models use LogsActivity trait. Only fillable attributes are logged via `logFillable()`.

8. **Prevent lazy loading:** AppServiceProvider boot() calls `Model::preventLazyLoading(!app()->isProduction())` to catch N+1 queries during development.

9. **Test database:** phpunit.xml already configures sqlite in-memory database for tests, so tests won't affect the MySQL development database.

10. **Role enum vs spatie roles:** App\Enums\Role is a PHP enum for type safety in code. Spatie roles are database records. The enum values match spatie role names for consistency.

---

## Verification Commands Summary

After completing all steps, run these commands in order to verify the implementation:

```bash
# 1. Run Pint to check code style
./vendor/bin/pint --test

# 2. Run full test suite
php artisan test

# 3. Clear and rebuild caches
php artisan optimize:clear

# 4. Check routes
php artisan route:list

# 5. Verify seeder
php artisan migrate:fresh --seed

# 6. Manual login test
# Visit http://localhost:8000/login
# Login with admin@example.com / password (or values from .env)
# Navigate to /admin/users, /workspaces, create test data
```

Expected outcome: All tests pass, all routes registered with correct middleware, admin can access admin panel, members cannot, workspaces properly scoped by membership, public registration returns 404.
