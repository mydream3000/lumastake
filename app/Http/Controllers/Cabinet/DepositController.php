<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDepositJob;
use App\Models\Transaction;
use App\Services\CryptoService;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DepositController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Генерация адреса через CryptoAPI (или статичный)
        $depositAddress = config('crypto.deposit_address'); // или через API

        // Pending депозиты
        $pendingDeposits = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->latest()->get();

        return view('cabinet.deposit', compact('depositAddress', 'pendingDeposits'));
    }

    /**
     * Генерация/получение адреса для пополнения USDT/USDC
     */
    public function acceptUSDT(Request $request, CryptoService $cryptoService)
    {
        try {
            $user = auth()->user();
            $network = $request->input('network', 'tron');
            $token = $request->input('token', 'USDT');

            // Валидация: проверяем соответствие токена и сети
            if ($token === 'USDT' && !in_array($network, ['ethereum', 'tron'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'USDT is only available on Ethereum (ERC-20) and TRON (TRC-20) networks',
                ], 422);
            }

            if ($token === 'USDC' && !in_array($network, ['bsc', 'ethereum'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'USDC is only available on Ethereum (ERC-20) and BNB Chain (BEP-20) networks',
                ], 422);
            }

            // Ключ для кеша: user_id + network + token
            $cacheKey = "crypto_deposit_address_{$user->id}_{$network}_{$token}";

            // Проверяем кеш или получаем адрес через API
            $addressData = Cache::remember($cacheKey, 3600, function () use ($user, $network, $token, $cryptoService) {
                return $cryptoService->getOrCreateDepositAddress($user, $network, $token);
            });

            // Генерируем QR код для адреса
            $qrCode = (string) QrCode::size(200)
                ->format('svg')
                ->generate($addressData['address']);

            return response()->json([
                'success' => true,
                'address' => $addressData['address'],
                'network' => $addressData['network'],
                'token' => $addressData['token'],
                'qr_code' => $qrCode,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate deposit address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Подтверждение отправки средств пользователем
     */
    public function confirmPayment(Request $request, TelegramBotService $telegramBotService)
    {
        $request->validate([
            'network' => 'required|string|in:ethereum,tron,bsc',
            'address' => 'required|string',
            'token' => 'nullable|string|in:USDT,USDC',
        ]);

        $user = auth()->user();

        $token = $request->input('token', 'USDT');

        // Отправляем уведомление в Telegram что пользователь выбрал сеть
        $telegramBotService->sendDepositNetworkSelected($user, $request->network, $token);

        return response()->json([
            'success' => true,
            'message' => 'Confirmation sent',
        ]);
    }

    /**
     * Test endpoint for manual deposit (dev only)
     */
    public function testDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        // Запускаем Job для обработки тестового депозита
        ProcessDepositJob::dispatch(
            $user->id,
            $request->amount,
            'test-' . uniqid()
        );

        return response()->json([
            'success' => true,
            'message' => 'Test deposit initiated'
        ]);
    }
}
