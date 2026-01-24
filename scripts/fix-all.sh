#!/bin/bash
# Quick fix script for Lumastake (AlmaLinux)
# Usage: bash scripts/fix-all.sh

set -e

echo "🔧 Lumastake Quick Fix"
echo "======================"

cd /var/www/arbitex/data/www/lumastake.com

echo ""
echo "1. Clearing caches..."
php artisan optimize:clear
rm -f resources/views/emails/dynamic/*.blade.php

echo ""
echo "2. Restarting queue worker..."
php artisan queue:restart
sudo supervisorctl restart lumastake-queue

echo ""
echo "3. Checking services..."
echo -n "   MySQL: "
systemctl is-active mysqld 2>/dev/null || systemctl is-active mariadb 2>/dev/null || echo "checking..."

echo -n "   PHP-FPM: "
systemctl is-active php-fpm 2>/dev/null || echo "not running!"

echo -n "   Apache/Nginx: "
systemctl is-active httpd 2>/dev/null || systemctl is-active nginx 2>/dev/null || echo "not running!"

echo ""
echo "4. Queue worker status..."
sudo supervisorctl status lumastake-queue

echo ""
echo "5. Checking failed jobs..."
php artisan queue:failed --limit=5 2>/dev/null || echo "   No failed jobs"

echo ""
echo "✅ Done!"
echo ""
echo "📋 Useful commands:"
echo "   tail -f storage/logs/laravel.log     # Laravel logs"
echo "   sudo supervisorctl status            # All workers"
echo "   php artisan queue:retry all          # Retry failed jobs"
