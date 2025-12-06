<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\WithdrawRequest;
use App\Jobs\ProcessWithdrawJob;
use App\Mail\WithdrawCodeMail;
use App\Models\PendingWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $availableBalance = $user->available_balance;

        return view('cabinet.withdraw', compact('availableBalance'));
    }

    public function request(WithdrawRequest $request)
    {
        $user = auth()->user();

        // Check if withdrawals are blocked for this user
        if ($user->withdrawal_blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawals are currently unavailable in your account. Please contact technical support.',
            ], 403);
        }

        $code = rand(100000, 999999);

        // Удаляем старые pending withdrawals этого пользователя
        PendingWithdrawal::where('user_id', $user->id)->delete();

        // Создаём новую запись с TTL 60 секунд
        PendingWithdrawal::create([
            'user_id' => $user->id,
            'token' => $request->token,
            'amount' => $request->amount,
            'receiver_address' => $request->receiver_address,
            'network' => $request->network,
            'verification_code' => (string)$code,
            'code_expires_at' => now()->addSeconds(60),
        ]);

        // Отправка email
        Mail::mailer('failover')->to($user->email)->send(new WithdrawCodeMail($code, $user->name));

        return response()->json([
            'success' => true,
            'message' => 'Confirmation code sent to your email',
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();

        // Находим pending withdrawal для этого пользователя
        $pendingWithdrawal = PendingWithdrawal::where('user_id', $user->id)->first();

        if (!$pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'No pending withdrawal found. Please try again.',
            ], 422);
        }

        // Проверка срока действия кода
        if ($pendingWithdrawal->isExpired()) {
            $pendingWithdrawal->delete();
            return response()->json([
                'success' => false,
                'message' => 'Code has expired. Please request a new one.',
            ], 422);
        }

        // Проверка кода
        if ($request->code != $pendingWithdrawal->verification_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid confirmation code',
            ], 422);
        }

        // Запускаем Job для обработки вывода
        ProcessWithdrawJob::dispatch(
            $user->id,
            $pendingWithdrawal->amount,
            $pendingWithdrawal->receiver_address,
            $pendingWithdrawal->network,
            $pendingWithdrawal->token ?? 'USDT'
        );

        // Удаляем pending withdrawal после успешного подтверждения
        $pendingWithdrawal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request is being processed',
        ]);
    }

    public function resendCode(Request $request)
    {
        $user = auth()->user();

        // Находим pending withdrawal для этого пользователя
        $pendingWithdrawal = PendingWithdrawal::where('user_id', $user->id)->first();

        if (!$pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'No pending withdrawal request found',
            ], 422);
        }

        // Генерируем новый код
        $code = rand(100000, 999999);

        // Обновляем запись с новым кодом и временем
        $pendingWithdrawal->update([
            'verification_code' => (string)$code,
            'code_expires_at' => now()->addSeconds(60),
        ]);

        Mail::mailer('failover')->to($user->email)->send(new WithdrawCodeMail($code, $user->name));

        return response()->json([
            'success' => true,
            'message' => 'New confirmation code sent to your email',
        ]);
    }
}
