#!/bin/bash
# Quick fix script for Lumastake
# Usage: bash scripts/fix-all.sh

set -e

echo "🔧 Lumastake Quick Fix"
echo "======================"

cd /var/www/lumastake

echo ""
echo "1. Clearing caches..."
php artisan optimize:clear
rm -f resources/views/emails/dynamic/*.blade.php

echo ""
echo "2. Restarting queue workers..."
php artisan queue:restart
sudo supervisorctl restart lumastake-worker:* 2>/dev/null || echo "   (supervisor not configured)"

echo ""
echo "3. Checking services..."
echo -n "   MySQL: "
sudo systemctl is-active mysql 2>/dev/null || sudo systemctl is-active mariadb 2>/dev/null || echo "not running!"

echo -n "   PHP-FPM: "
sudo systemctl is-active php*-fpm 2>/dev/null || echo "not running!"

echo -n "   Nginx: "
sudo systemctl is-active nginx 2>/dev/null || echo "not running!"

echo ""
echo "4. Checking queue status..."
sudo supervisorctl status 2>/dev/null || echo "   (supervisor not available)"

echo ""
echo "5. Checking failed jobs..."
php artisan queue:failed --limit=5 2>/dev/null || echo "   No failed jobs table"

echo ""
echo "✅ Done! If issues persist, check logs:"
echo "   tail -f storage/logs/laravel.log"
echo "   tail -f /var/log/lumastake-worker.log"
