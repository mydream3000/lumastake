<?php

namespace App\Console\Commands;

use App\Services\CryptoDepositService;
use Illuminate\Console\Command;

/**
 * Команда проверки криптовалютных депозитов
 *
 * Проверяет наличие входящих USDT-транзакций для адресов, сгенерированных в последние 3 часа.
 * Используется как fallback-механизм на случай сбоя webhook от CryptocurrencyAPI.net.
 *
 * Как работает:
 * 1. Получает список адресов, созданных в последние 3 часа
 * 2. Опрашивает API для каждого адреса на наличие транзакций
 * 3. При обнаружении депозита запускает ProcessDepositJob для обработки
 *
 * В production webhook обрабатывает большинство депозитов моментально.
 * Эта команда нужна для подстраховки и запускается через scheduler каждую минуту.
 *
 * Связанные компоненты:
 * - CryptoDepositService::checkRecentAddresses() - логика проверки
 * - ProcessDepositJob - обработка найденных депозитов
 * - Webhook: /api/v1/webhooks/crypto/deposit (основной способ получения депозитов)
 */
class CheckCryptoDeposits extends Command
{
    protected $signature = 'crypto:check-deposits {--address= : Specific address to check}';
    protected $description = 'Check crypto deposits for addresses requested in the last 3 hours';

    public function handle(CryptoDepositService $service): int
    {
        $address = $this->option('address');
        $this->info($address ? "Checking crypto deposits for address: {$address}..." : 'Checking crypto deposits...');

        $processedCount = $service->checkRecentAddresses($address);

        $this->info("Processed {$processedCount} deposits");

        return self::SUCCESS;
    }
}
