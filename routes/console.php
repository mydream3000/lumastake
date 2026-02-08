<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('staking:process-completed')->everyFiveMinutes();
Schedule::command('toast:clean')->daily();

// Send staking expiring reminders (24 hours before end)
Schedule::command('staking:send-expiring-reminders')->dailyAt('09:00');

// Резервная проверка депозитов через blockchain explorers (TronGrid, Etherscan, BscScan)
// Основной механизм — IPN callback от CryptocurrencyAPI.net
// Эта команда нужна только как fallback на случай пропущенных вебхуков
Schedule::command('crypto:check-blockchain')->everyTenMinutes();
