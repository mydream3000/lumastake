# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Lumastake** is a Laravel-based cryptocurrency staking platform for USDT. Users can deposit USDT, create staking deposits with interest, withdraw funds, and earn referral rewards through a multi-tier system.

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.4+)
- **Frontend**: Blade templates + Alpine.js + Vue 3 (for data tables)
- **Styling**: Tailwind CSS 4
- **Build Tool**: Vite 7
- **Testing**: Pest (PHP)
- **Package Manager**: Composer + npm
- **Queue System**: Laravel Queue (for processing deposits, withdrawals, staking)
- **Crypto API**: CryptocurrencyAPI.net for USDT deposits/withdrawals

## Essential Commands

### Development

```bash
# Start all services (server, queue, logs, vite) - single command
composer dev

# Alternatively, services can be started individually:
php artisan serve        # Start Laravel server
php artisan queue:work  # Start queue worker
npm run dev              # Start Vite (already running in dev environment)
```

**Important**: The user typically has Vite running continuously, so avoid running `npm run build`.

### Testing

```bash
# Run all tests (clears config first)
composer test

# Or directly:
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/SomeTest.php
```

### Database

```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Fresh migration + seeders
php artisan db:seed              # Run seeders only
```

### Code Quality

```bash
php artisan pint  # Laravel Pint for code formatting
```

### Scheduled Tasks (Cron)

```bash
php artisan schedule:work  # Run scheduler for development
```

For production, add to crontab:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Architecture

### Route Structure

Routes are split across multiple files:
- `routes/web.php` - Public routes (home, blog, FAQ, contact, auth)
- `routes/cabinet.php` - User dashboard routes (auth + verified middleware)
- `routes/admin.php` - Admin panel routes (auth + admin middleware)
- `routes/api.php` - API v1 routes (prefix `/api`, includes crypto webhooks)

### Middleware

- `admin` - Custom middleware checking `is_admin` flag on authenticated user
- `verified` - Email verification required for cabinet routes
- `throttle:X,Y` - Rate limiting (e.g., `throttle:5,1` = 5 attempts per minute)

### Frontend Architecture

**Two separate entry points:**
- `resources/css/public.css` + `resources/js/public.js` - Public pages
- `resources/css/cabinet.css` + `resources/js/cabinet.js` - User dashboard

**Component Loading:**
- Vue components are lazy-loaded on specific pages using `mountComponent()` function
- Alpine.js is used for simple interactivity (forms, modals, dropdowns)
- Vue 3 tables are mounted automatically via `data-url` attributes on elements with `.js-*` classes

**Important**: When working with Alpine.js and encountering `@click` issues, always check that `x-show` is properly defined on the parent element.

### Tailwind CSS 4 Custom Colors

Custom colors are defined in `resources/css/public.css` under `@theme`:
- `lumastake-green`, `lumastake-pink`, `lumastake-orange`, `lumastake-dark`, `lumastake-gray`, `lumastake-accent`
- `cabinet-green`, `cabinet-orange`, `cabinet-red`
- `cabinet-level-1` through `cabinet-level-5` (referral tier colors)
- `cabinet-header-bg`, `cabinet-card-border`, `cabinet-table-row`

**Opacity in Tailwind 4**: Use `/opacity` syntax: `bg-red-500/20` (20% opacity) or `bg-red-500/[0.63]` (custom opacity). Increments are in steps of 10.

**Color Usage**: Prefer using predefined theme colors from `@theme`. Only use arbitrary colors like `text-[#...]` or `bg-[#...]` for one-off colors that aren't defined and won't be reused.

### Key Services

- **CryptoService** (`app/Services/CryptoService.php`) - Wrapper for CryptocurrencyAPI.net
- **CryptoDepositService** (`app/Services/CryptoDepositService.php`) - Handles deposit processing logic
- **ReferralService** (`app/Services/ReferralService.php`) - Manages referral rewards
- **TierService** (`app/Services/TierService.php`) - Calculates user tier based on balance

### Queue Jobs

All business logic operations run asynchronously:
- `ProcessDepositJob` - Processes incoming USDT deposits
- `ProcessWithdrawJob` - Processes withdrawal requests
- `ProcessStakingJob` - Creates new staking deposits
- `ProcessStakingCompletionJob` - Handles staking completion + profit distribution
- `ProcessUnstakingJob` - Early unstaking with penalty
- `ProcessProfitJob` - Daily profit accrual for active stakes
- `ProcessWithdrawCancellationJob` - Cancels withdrawal requests

### Balance System

**Balance:**
- `balance` - User's total available balance
- All funds are available for withdrawal or staking

**Balance Rules:**
- Deposits increase `balance`
- Staking moves funds from balance to staking deposit
- When staking completes, principal + profit return to balance
- Early unstaking penalty is 10% (user receives 90% back)
- Referral rewards are immediately available on balance
- Withdrawals deduct from `balance`

See `docs/BUSINESS_LOGIC.md` for detailed balance examples.

### Models & Relationships

Key models:
- `User` - has many `StakingDeposit`, `Transaction`, `Earning`, `ToastMessage`
- `StakingDeposit` - belongs to `User`, `Tier`
- `Transaction` - belongs to `User` (deposit/withdrawal records)
- `Earning` - belongs to `User` (profit/referral earning records)
- `Tier` - has many `TierPercentage` (APR rates by duration)
- `CryptoAddress` - belongs to `User` (generated USDT deposit addresses)
- `CryptoTransaction` - tracks blockchain transactions

### Email System

Mailable classes in `app/Mail/`:
- Views in `resources/views/emails/`
- Emails are queued automatically

### API Webhooks

CryptocurrencyAPI.net sends webhooks to:
- `POST /api/v1/webhooks/crypto/deposit` - USDT deposit confirmations
- `POST /api/v1/webhooks/crypto/withdraw` - USDT withdrawal status

Environment variables required:
```
CRYPTO_API_KEY=
CRYPTO_WEBHOOK_SECRET=
CRYPTO_CALLBACK_URL=
```

### Development Notes

- **Test Login**: `GET /login/test?user_id=1` bypasses authentication (dev only)
- **Test Deposit**: `POST /dashboard/deposit/test` simulates deposit webhook (dev only)
- Scheduled job checks USDT addresses every minute in development (polling fallback)
- Production uses webhooks exclusively

### Admin System

Admin users have `is_admin = true` flag. Admin routes are at `/admin/*`:
- Dashboard, Users, Promo Codes, Tiers, Staking, Payments, FAQ, Blog, Analytics

### File Operations

- Use `rm` command for deleting files (Windows environment)
- Avoid running `npm run build` - Vite is continuously running via `npm run dev`

## Documentation

Comprehensive business logic documentation:
- `docs/BUSINESS_LOGIC.md` - Complete functional specification (balance system, staking, referrals, tiers)
- `docs/CRYPTO_API.md` - CryptocurrencyAPI.net integration guide

## Database

- **Primary DB**: SQLite (development) or MySQL/PostgreSQL (production)
- Migrations are timestamped: `2025_MM_DD_HHMMSS_description.php`
- Observer classes in `app/Observers/` for model lifecycle events

## Common Patterns

### Creating a new cabinet page:
1. Add route in `routes/cabinet.php` with `cabinet.` name prefix
2. Create controller in `app/Http/Controllers/Cabinet/`
3. Create view in `resources/views/cabinet/`
4. If using Vue table: create component in `resources/js/components/`, add mount logic to `cabinet.js`

### Creating a new queue job:
1. Create job in `app/Jobs/`
2. Dispatch using `ClassName::dispatch($params)` from controller
3. Job runs via `php artisan queue:listen`

### Toast notifications:
- Backend: `ToastMessage::create(['user_id' => $userId, 'message' => '...', 'type' => 'success'])`
- Frontend: `window.showToast('message', 'success')` or dispatch `show-toast` event
- Types: `success`, `error`, `info`, `warning`

## Rules

- Controllers: `CamelCaseController.php`
- Views: Blade files in `kebab-case.blade.php`
- Routes: `kebab-case` with dot notation (`cabinet.staking.index`)
- CSS classes: Use Tailwind utilities, component classes with `js-*` prefix for Vue mounting
- Формат дат 23, Aug, 2025 в кабинете
- текущий проект это локальная dev версия с не настоящей базой которую можно пересоздавать при острой необходимости или обнулять для повторной загрузки сидов
- все баджи в кабинете у нас с прозрачностью фонового цвета но не всегда одинаковой, все кнопки кабинета у нас без прозрачностей цвета
- Все табличные модули (где таблица является существенной частью страницы в кабинете) реализуем только через @resources\js\components\DataTable.vue
- Действия в личном кабинете сопровождаем сообщениями в Toast
