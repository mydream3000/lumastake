@echo off
echo Clearing all Laravel caches...

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo Done! Now reload your browser with Ctrl+F5
pause
