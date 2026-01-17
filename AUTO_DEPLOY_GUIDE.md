# Руководство по автоматическому деплою с Git на сервер через PhpStorm

## Обзор

Этот гайд покажет, как настроить автоматический деплой Laravel-приложения из локального PhpStorm на удаленный сервер через Git.

**Что уже реализовано:**
- ✅ Автоматический commit и push в Git из PhpStorm

**Что нужно настроить:**
- ⚙️ Автоматическая выгрузка изменений с Git на сервер

---

## Часть 1: Настройка Git в PhpStorm (УЖЕ РЕАЛИЗОВАНО)

### 1.1. Включение Git интеграции

1. **VCS → Enable Version Control Integration**
2. Выберите **Git** и нажмите **OK**

### 1.2. Настройка автоматического commit

**Способ 1: Через GUI**
- `VCS → Commit` или `Ctrl+K`
- Галочка на измененных файлах
- Кнопка **Commit** → выбрать **Commit and Push**

**Способ 2: Автокоммит при сохранении (опционально)**
1. `Settings → Tools → Actions on Save`
2. Включить: **Upload to default server** (для SFTP)

---

## Часть 2: Автоматический деплой с Git на сервер

### Метод 1: Git Webhooks + Скрипт на сервере (РЕКОМЕНДУЕТСЯ)

#### 2.1. Настройка Git репозитория на сервере

**SSH в сервер:**
```bash
ssh lumastake@vm4303927.had.su
cd /var/www/lumastake/data/www/lumastake.com
```

**Инициализация Git (если еще не сделано):**
```bash
git init
git remote add origin https://github.com/mydream3000/lumastake
git branch -M main
```

**Проверка подключения:**
```bash
git remote -v
```

#### 2.2. Создание деплой-скрипта на сервере

Создайте файл `/var/www/lumastake/data/www/lumastake.com/deploy.sh`:

```bash
#!/bin/bash

# =========================
# AUTO-DEPLOY SCRIPT
# =========================

cd /var/www/lumastake/data/www/lumastake.com || exit

echo "🔄 Starting deployment..."

# 1. Backup текущей версии
echo "📦 Creating backup..."
tar -czf ../backups/backup-$(date +%Y%m%d_%H%M%S).tar.gz .

# 2. Pull последних изменений
echo "⬇️ Pulling from Git..."
git pull origin main

# 3. Установка/обновление зависимостей
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 Installing NPM dependencies..."
npm install

# 4. Сборка фронтенда (если нужно)
# echo "🏗️ Building assets..."
# npm run build

# 5. Очистка кеша Laravel
echo "🧹 Clearing Laravel cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Кеширование для продакшена
echo "⚡ Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Миграции (опционально, раскомментируйте если нужно)
# echo "🗄️ Running migrations..."
# php artisan migrate --force

# 8. Права доступа
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R lumastake:www-data storage bootstrap/cache

# 9. Перезапуск очереди (если используется supervisor)
echo "♻️ Restarting queue workers..."
php artisan queue:restart

echo "✅ Deployment completed successfully!"
```

**Сделайте скрипт исполняемым:**
```bash
chmod +x deploy.sh
```

#### 2.3. Настройка GitHub/GitLab Webhook

**Для GitHub:**
1. Откройте ваш репозиторий на GitHub
2. **Settings → Webhooks → Add webhook**
3. **Payload URL:** `https://lumastake.com/deploy-webhook.php`
4. **Content type:** `application/json`
5. **Secret:** Придумайте секретный ключ (например: `your-secret-token-123`)
6. **Events:** Выберите "Just the push event"
7. **Active:** Включите галочку
8. Нажмите **Add webhook**

**Для GitLab:**
1. **Settings → Webhooks**
2. **URL:** `https://lumastake.com/deploy-webhook.php`
3. **Secret Token:** Тот же секрет
4. **Trigger:** Push events
5. **Add webhook**

#### 2.4. Создание webhook-обработчика на сервере

Создайте файл `/var/www/lumastake/data/www/lumastake.com/public/deploy-webhook.php`:

```php
<?php

// Секретный ключ (должен совпадать с GitHub/GitLab)
define('SECRET_KEY', 'your-secret-token-123');

// Путь к деплой-скрипту
define('DEPLOY_SCRIPT', '/var/www/lumastake/data/www/lumastake.com/deploy.sh');

// Лог файл
define('LOG_FILE', '/var/www/lumastake/data/www/lumastake.com/storage/logs/deploy.log');

// Проверка секретного ключа
$headers = getallheaders();
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? $_SERVER['HTTP_X_GITLAB_TOKEN'] ?? '';

// GitHub использует HMAC SHA256
if (isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
    $payload = file_get_contents('php://input');
    $hash = 'sha256=' . hash_hmac('sha256', $payload, SECRET_KEY);

    if (!hash_equals($hash, $signature)) {
        http_response_code(403);
        die('Invalid signature');
    }
}
// GitLab использует простой токен
elseif (isset($_SERVER['HTTP_X_GITLAB_TOKEN'])) {
    if ($signature !== SECRET_KEY) {
        http_response_code(403);
        die('Invalid token');
    }
} else {
    http_response_code(401);
    die('No signature provided');
}

// Логирование
$logMessage = date('Y-m-d H:i:s') . " - Deployment triggered from IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);

// Запуск деплоя в фоне
exec('nohup ' . DEPLOY_SCRIPT . ' > /dev/null 2>&1 &');

// Ответ
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deployment started',
    'timestamp' => date('c')
]);
```

**Права доступа:**
```bash
chmod 755 /var/www/lumastake/data/www/lumastake.com/public/deploy-webhook.php
```

#### 2.5. Настройка прав для скрипта

```bash
# Добавьте пользователя www-data в группу lumastake
sudo usermod -a -G lumastake www-data

# Права на папку проекта
sudo chown -R lumastake:www-data /var/www/lumastake/data/www/lumastake.com
sudo chmod -R 775 /var/www/lumastake/data/www/lumastake.com
```

#### 2.6. Тестирование

**Тест webhook вручную:**
```bash
curl -X POST https://lumastake.com/deploy-webhook.php \
  -H "X-Gitlab-Token: your-secret-token-123"
```

**Проверка логов:**
```bash
tail -f /var/www/lumastake/data/www/lumastake.com/storage/logs/deploy.log
```

---

### Метод 2: SFTP Auto-Upload из PhpStorm (АЛЬТЕРНАТИВА)

#### 2.1. Настройка SFTP сервера в PhpStorm

1. **Tools → Deployment → Configuration**
2. Нажмите **+** → **SFTP**
3. **Name:** `Lumastake Production`
4. **Вкладка Connection:**
   - **Type:** SFTP
   - **Host:** `vm4303927.had.su`
   - **Port:** `22`
   - **Username:** `lumastake`
   - **Auth type:** Key pair или Password
   - **Root path:** `/var/www/lumastake/data/www/lumastake.com`
5. **Вкладка Mappings:**
   - **Local path:** `C:\Users\user\LUMASTAKE`
   - **Deployment path:** `/`
6. **Вкладка Excluded Paths:**
   Добавьте:
   - `.git`
   - `node_modules`
   - `vendor`
   - `.idea`
   - `storage/logs`
7. Нажмите **Test Connection** → **OK**

#### 2.2. Автоматическая загрузка при сохранении

1. **Settings → Tools → Actions on Save**
2. Включите:
   - ✅ **Upload to default server**
   - ✅ **Always** (или **On explicit save action**)
3. **OK**

#### 2.3. Установка по умолчанию

1. **Tools → Deployment → Configuration**
2. Выберите ваш сервер
3. Нажмите на галочку ✓ (Use as default)

---

## Часть 3: Рекомендуемый Workflow

### Workflow с Git Webhook (ЛУЧШИЙ)

```
1. Редактируете код в PhpStorm
2. Сохраняете (Ctrl+S)
3. Commit: Ctrl+K → вводите сообщение → Commit and Push
4. Git Push отправляет в GitHub/GitLab
5. Webhook автоматически триггерит deploy.sh на сервере
6. Сервер обновляется автоматически
```

**Преимущества:**
- ✅ Версионирование через Git
- ✅ Автоматический деплой
- ✅ Можно откатить изменения
- ✅ История всех изменений
- ✅ Работает с командой

### Workflow с SFTP (АЛЬТЕРНАТИВА)

```
1. Редактируете код в PhpStorm
2. Сохраняете (Ctrl+S)
3. PhpStorm автоматически загружает на сервер через SFTP
```

**Преимущества:**
- ✅ Мгновенная загрузка
- ✅ Не нужен Git

**Недостатки:**
- ❌ Нет версионирования
- ❌ Риск потери кода
- ❌ Сложно работать в команде

---

## Часть 4: Настройка .gitignore

Создайте/обновите файл `.gitignore` в корне проекта:

```
# Laravel
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log

# IDE
/.idea
/.vscode
/.fleet
/.phpstorm.meta.php
/_ide_helper.php
/_ide_helper_models.php
.phpstorm.meta.php

# OS
.DS_Store
Thumbs.db

# Backups
*.sql
*.backup
backup_*.sql

# Logs
storage/logs/*.log
```

---

## Часть 5: Безопасность

### 5.1. Защита webhook

Убедитесь, что:
- ✅ Используется HTTPS
- ✅ Секретный ключ достаточно длинный (минимум 32 символа)
- ✅ IP адреса GitHub/GitLab в whitelist (опционально)

### 5.2. Права доступа

```bash
# Файлы проекта
find /var/www/lumastake/data/www/lumastake.com -type f -exec chmod 644 {} \;
find /var/www/lumastake/data/www/lumastake.com -type d -exec chmod 755 {} \;

# Исполняемые файлы
chmod +x /var/www/lumastake/data/www/lumastake.com/deploy.sh

# Storage и cache
chmod -R 775 storage bootstrap/cache
```

### 5.3. Переменные окружения

**НЕ коммитьте .env в Git!**

На сервере создайте `.env` вручную с реальными credentials.

---

## Часть 6: Проверка работы

### 6.1. Проверка Git

```bash
cd /var/www/lumastake/data/www/lumastake.com
git status
git log --oneline -5
```

### 6.2. Проверка webhook

После push в Git проверьте:
- Логи webhook: `tail -f storage/logs/deploy.log`
- Логи Laravel: `tail -f storage/logs/laravel.log`
- Логи веб-сервера: `/var/log/nginx/error.log`

### 6.3. Тестовый деплой

1. Измените файл (например, добавьте комментарий)
2. Commit and Push
3. Проверьте на сервере:
```bash
cat /var/www/lumastake/data/www/lumastake.com/storage/logs/deploy.log
```

---

## Часть 7: Troubleshooting

### Проблема: Webhook не срабатывает

**Решение:**
```bash
# Проверьте логи nginx
tail -f /var/log/nginx/error.log

# Проверьте права на deploy.sh
ls -l deploy.sh

# Проверьте содержимое deploy-webhook.php
cat public/deploy-webhook.php
```

### Проблема: Git pull не работает

**Решение:**
```bash
# Проверьте SSH ключи
ssh-add -l

# Настройте Git credentials
git config --global user.name "Your Name"
git config --global user.email "your@email.com"

# Используйте HTTPS вместо SSH (опционально)
git remote set-url origin https://github.com/username/repo.git
```

### Проблема: Permissions denied

**Решение:**
```bash
# Добавьте www-data в группу lumastake
sudo usermod -a -G lumastake www-data

# Перезапустите nginx
sudo systemctl restart nginx

# Установите правильные права
sudo chown -R lumastake:www-data /var/www/lumastake/data/www/lumastake.com
sudo chmod -R 775 /var/www/lumastake/data/www/lumastake.com
```

---

## Часть 8: Дополнительные возможности

### 8.1. Уведомления в Telegram при деплое

Добавьте в `deploy.sh`:

```bash
# Telegram notification
TELEGRAM_BOT_TOKEN="your-bot-token"
TELEGRAM_CHAT_ID="your-chat-id"

send_telegram() {
    curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
        -d chat_id="${TELEGRAM_CHAT_ID}" \
        -d text="$1" \
        -d parse_mode="HTML"
}

send_telegram "🚀 <b>Deployment started</b> on lumastake.com"
# ... ваш код деплоя ...
send_telegram "✅ <b>Deployment completed</b> successfully!"
```

### 8.2. Rollback функция

Добавьте в `deploy.sh`:

```bash
rollback() {
    echo "⏪ Rolling back to previous version..."
    LATEST_BACKUP=$(ls -t ../backups/*.tar.gz | head -1)
    tar -xzf "$LATEST_BACKUP" -C .
    echo "✅ Rollback completed"
}

# Использование: ./deploy.sh rollback
if [ "$1" == "rollback" ]; then
    rollback
    exit 0
fi
```

---

## Резюме

**Рекомендуемая настройка:**

1. ✅ Git в PhpStorm (уже настроено)
2. ✅ GitHub/GitLab Webhook → `deploy-webhook.php`
3. ✅ Автоматический `deploy.sh` на сервере
4. ✅ Telegram уведомления (опционально)

**Итоговый workflow:**
```
PhpStorm → Git Push → GitHub/GitLab → Webhook → Сервер → Автодеплой
```

Готово! Теперь каждый push в Git будет автоматически деплоиться на сервер! 🚀
