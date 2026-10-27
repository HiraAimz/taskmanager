# Foundation Setup Script for Task Manager
# Run this script from the project root directory

Write-Host "Starting Foundation Setup..." -ForegroundColor Green

# Phase 1: Install packages
Write-Host "`n[1/10] Installing Laravel Breeze with Livewire stack..." -ForegroundColor Cyan
composer require laravel/breeze --dev
php artisan breeze:install livewire --dark

Write-Host "`n[2/10] Installing Spatie Permission package..." -ForegroundColor Cyan
composer require spatie/laravel-permission:^6.0
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

Write-Host "`n[3/10] Installing Spatie Activity Log package..." -ForegroundColor Cyan
composer require spatie/laravel-activitylog:^4.0
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

Write-Host "`n[4/10] Installing Pest testing framework..." -ForegroundColor Cyan
composer require pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 --dev
php artisan pest:install

Write-Host "`n[5/10] Installing and building frontend assets..." -ForegroundColor Cyan
npm install
npm run build

# Phase 2: Run migrations
Write-Host "`n[6/10] Running database migrations..." -ForegroundColor Cyan
php artisan migrate

# Phase 3: Seed database
Write-Host "`n[7/10] Seeding roles, permissions, and admin user..." -ForegroundColor Cyan
php artisan db:seed --class=RolePermissionSeeder

# Phase 4: Clear caches
Write-Host "`n[8/10] Clearing application caches..." -ForegroundColor Cyan
php artisan optimize:clear

# Phase 5: Run code formatter
Write-Host "`n[9/10] Running Laravel Pint (code formatter)..." -ForegroundColor Cyan
./vendor/bin/pint

# Phase 6: Run tests
Write-Host "`n[10/10] Running test suite..." -ForegroundColor Cyan
php artisan test

Write-Host "`nFoundation setup complete!" -ForegroundColor Green
Write-Host "`nYou can now:" -ForegroundColor Yellow
Write-Host "  - Start the dev server: php artisan serve" -ForegroundColor White
Write-Host "  - Login with: $env:ADMIN_EMAIL / $env:ADMIN_PASSWORD" -ForegroundColor White
Write-Host "  - Visit: http://localhost:8000/login" -ForegroundColor White
