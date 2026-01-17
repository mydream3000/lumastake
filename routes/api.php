<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Lumastake)
|--------------------------------------------------------------------------
| Скелет API v1 согласно спецификации. Эндпоинты возвращают 501 Not Implemented
| до реализации бизнес-логики. Подключено из bootstrap/app.php с префиксом /api.
*/

// GeoIP endpoints (без префикса для обратной совместимости)
Route::get('/geoip/country', [\App\Http\Controllers\Api\GeoIpController::class, 'getCountry']);
Route::get('/geoip/countries', [\App\Http\Controllers\Api\GeoIpController::class, 'getAllCountries']);

Route::prefix('v1')->group(function () {
    // Reference/public data
    Route::get('/tiers', fn () => response()->json(['message' => 'Not implemented'], 501));
    Route::get('/faq', fn () => response()->json(['message' => 'Not implemented'], 501));
    Route::get('/blog', fn () => response()->json(['message' => 'Not implemented'], 501));
    Route::get('/blog/{slug}', fn () => response()->json(['message' => 'Not implemented'], 501));

    // GeoIP endpoints
    Route::get('/geoip/country', [\App\Http\Controllers\Api\GeoIpController::class, 'getCountry']);
    Route::get('/geoip/countries', [\App\Http\Controllers\Api\GeoIpController::class, 'getAllCountries']);

    // Account-related (placeholders)
    Route::get('/staking', fn () => response()->json(['message' => 'Not implemented'], 501));
    Route::get('/transactions', fn () => response()->json(['message' => 'Not implemented'], 501));

    // Webhooks for CryptocurrencyAPI.net
    Route::post('/webhooks/crypto/deposit', [\App\Http\Controllers\Api\CryptoWebhookController::class, 'deposit']);
    Route::post('/webhooks/crypto/withdraw', [\App\Http\Controllers\Api\CryptoWebhookController::class, 'withdraw']);

    // Veriff webhooks (KYC)
    Route::post('/webhooks/veriff/events', [\App\Http\Controllers\Api\VeriffWebhookController::class, 'events']);
    Route::post('/webhooks/veriff/decisions', [\App\Http\Controllers\Api\VeriffWebhookController::class, 'decisions']);
    Route::post('/webhooks/veriff/pep-sanctions', [\App\Http\Controllers\Api\VeriffWebhookController::class, 'pepSanctions']);

    // Test endpoint to check webhook accessibility
    Route::any('/webhooks/test', function(\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Log::info('Test webhook received', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);
        return response()->json(['status' => 'ok', 'message' => 'Test webhook received']);
    });
});
