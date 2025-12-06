<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CryptoDepositService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CryptoWebhookController extends Controller
{
    public function deposit(Request $request, CryptoDepositService $service)
    {
        $ipnData = $request->all();

        // Детальное логирование для отладки
        Log::channel('crypto')->info('=== CRYPTO DEPOSIT WEBHOOK RECEIVED ===', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'data' => $ipnData,
            'raw_content' => $request->getContent(),
        ]);

        // Обрабатываем депозит
        try {
            $processed = $service->processIPN($ipnData);

            Log::channel('crypto')->info('IPN processing result', [
                'processed' => $processed,
                'data' => $ipnData,
            ]);

            if ($processed) {
                return response('OK', 200);
            }

            return response('Failed to process', 400);
        } catch (\Exception $e) {
            Log::channel('crypto')->error('IPN processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $ipnData,
            ]);

            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle incoming withdraw webhook from CryptocurrencyAPI.net
     */
    public function withdraw(Request $request)
    {
        // Заглушка для вывода средств
        Log::channel('crypto')->info('Crypto withdraw webhook received', $request->all());

        return response()->json([
            'message' => 'Not implemented'
        ], 501);
    }
}
