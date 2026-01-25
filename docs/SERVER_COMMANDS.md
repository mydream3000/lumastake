# Lumastake Server Commands (AlmaLinux)

## 🚀 Quick Fix — одна команда

```bash
cd /var/www/arbitex/data/www/lumastake.com && bash scripts/fix-all.sh
```

---

## Supervisor (Queue Workers)

```bash
# Статус
sudo supervisorctl status

# Перезапуск lumastake
sudo supervisorctl restart lumastake-queue

# Стоп / Старт
sudo supervisorctl stop lumastake-queue
sudo supervisorctl start lumastake-queue

# Логи (если настроены)
tail -f /var/log/supervisor/lumastake-queue.log

# Перечитать конфиги
sudo supervisorctl reread && sudo supervisorctl update
```

**Конфиг:** `/etc/supervisord.d/lumastake-queue.ini`

---

## Laravel Queue

```bash
# Graceful перезапуск workers
php artisan queue:restart

# Посмотреть failed jobs
php artisan queue:failed

# Повторить все failed
php artisan queue:retry all

# Очистить failed
php artisan queue:flush

# Ручной запуск (для отладки)
php artisan queue:work --verbose
```

---

## Очистка кеша

```bash
# Всё сразу
php artisan optimize:clear

# Удалить временные email шаблоны
rm -f resources/views/emails/dynamic/*.blade.php

# По отдельности
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

---

## Сервисы

```bash
# MySQL/MariaDB
sudo systemctl status mysqld
sudo systemctl restart mysqld

# PHP-FPM
sudo systemctl status php-fpm
sudo systemctl restart php-fpm

# Apache (httpd)
sudo systemctl status httpd
sudo systemctl restart httpd
```

---

## Логи

```bash
# Laravel
tail -f storage/logs/laravel.log
tail -100 storage/logs/laravel.log

# Supervisor
tail -f /var/log/supervisor/supervisord.log

# Apache
tail -f /var/log/httpd/error_log
```

---

## База данных

```bash
# Проверить подключение
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"

# Активные стейкинги
php artisan tinker --execute="echo \App\Models\StakingDeposit::where('status','active')->count();"

# Миграции
php artisan migrate

# Обновить email шаблоны
php artisan db:seed --class=EmailTemplatesSeeder --force
```

---

## Права доступа

```bash
sudo chown -R apache:apache storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Быстрая диагностика

```bash
echo "=== SERVICES ===" && \
systemctl is-active mysqld php-fpm httpd && \
echo "=== SUPERVISOR ===" && \
sudo supervisorctl status && \
echo "=== FAILED JOBS ===" && \
php artisan queue:failed --limit=5
```
