<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('staking:process-completed')->everyFiveMinutes();
Schedule::command('toast:clean')->daily();

// Send staking expiring reminders (24 hours before end)
Schedule::command('staking:send-expiring-reminders')->dailyAt('09:00');

// Автоматическая проверка депозитов через публичные blockchain explorers
// Работает БЕЗ CryptocurrencyAPI.net - использует TronScan, Etherscan, BscScan
// Schedule::command('crypto:check-blockchain')->everyMinute();
