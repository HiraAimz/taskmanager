@echo off
echo Installing packages compatible with Laravel 13...
echo.

echo Installing Spatie Permission (Laravel 13 compatible)...
composer require spatie/laravel-permission
if %ERRORLEVEL% NEQ 0 (
    echo Failed to install Spatie Permission
    pause
    exit /b 1
)

echo.
echo Publishing Permission migrations...
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

echo.
echo Installing Spatie Activity Log (Laravel 13 compatible)...
composer require spatie/laravel-activitylog
if %ERRORLEVEL% NEQ 0 (
    echo Failed to install Activity Log
    pause
    exit /b 1
)

echo.
echo Publishing Activity Log migrations...
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

echo.
echo Installing Pest (Laravel 13 compatible)...
composer require pestphp/pest --dev --with-all-dependencies
composer require pestphp/pest-plugin-laravel --dev --with-all-dependencies
if %ERRORLEVEL% NEQ 0 (
    echo Failed to install Pest
    pause
    exit /b 1
)

echo.
echo Running migrations...
php artisan migrate

echo.
echo Clearing config cache...
php artisan config:clear

echo.
echo ============================================
echo Packages installed successfully!
echo ============================================
echo.
echo Now run: php artisan db:seed --class=RolePermissionSeeder
echo.
pause
