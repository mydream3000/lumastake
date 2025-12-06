<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CryptocurrencyAPI.net Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for CryptocurrencyAPI.net integration
    | See docs/CRYPTO_API.md for detailed documentation
    |
    */

    // API ключ из личного кабинета
    'api_key' => env('CRYPTO_API_KEY'),

    // URL вебхука для IPN уведомлений
    'webhook_url' => env('CRYPTO_WEBHOOK_URL', env('APP_URL') . '/api/v1/webhooks/crypto/deposit'),

    // Базовый URL API
    'base_url' => 'https://new.cryptocurrencyapi.net/api/',

    // Сеть по умолчанию для USDT
    'default_network' => env('CRYPTO_DEFAULT_NETWORK', 'tron'),

        // Минимальная сумма депозита (USDT/USDC)
        'min_deposit_amount' => env('CRYPTO_MIN_DEPOSIT', 50),

    // Минимальные подтверждения для разных сетей
    'min_confirmations' => [
        'tron' => 3,
        'ethereum' => 3,
        'bsc' => 3,
        'polygon' => 128,
        'bitcoin' => 2,
    ],
];
