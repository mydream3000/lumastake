<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsBotSetting;
use App\Models\ToastMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Отображение страницы аналитики с настройками бота
     */
    public function index()
    {
        $botSettings = AnalyticsBotSetting::getInstance();

        return view('admin.analytics.index', compact('botSettings'));
    }

    /**
     * Обновление настроек бота аналитики
     */
    public function updateBotSettings(Request $request)
    {
        $validated = $request->validate([
            'bot_token' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $botSettings = AnalyticsBotSetting::getInstance();
        $botSettings->update($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Настройки бота аналитики успешно обновлены',
            'type' => 'success',
        ]);

        return redirect()->route('admin.analytics.index');
    }

    /**
     * Тестирование подключения к боту аналитики
     */
    public function testBot()
    {
        try {
            $botSettings = AnalyticsBotSetting::getInstance();

            if (!$botSettings->bot_token || !$botSettings->chat_id) {
                ToastMessage::create([
                    'user_id' => auth()->id(),
                    'message' => 'Настройки бота не заполнены',
                    'type' => 'error',
                ]);

                return redirect()->route('admin.analytics.index');
            }

            $url = "https://api.telegram.org/bot{$botSettings->bot_token}/sendMessage";

            $testMessage = "🤖 <b>Тестовое сообщение</b>\n\n";
            $testMessage .= "✅ Бот аналитики успешно подключен и работает!\n";
            $testMessage .= "📅 " . now()->format('d.m.Y H:i:s');

            $response = Http::post($url, [
                'chat_id' => $botSettings->chat_id,
                'text' => $testMessage,
                'parse_mode' => 'HTML',
            ]);

            $responseData = $response->json();
            $success = $response->successful() && ($responseData['ok'] ?? false);

            ToastMessage::create([
                'user_id' => auth()->id(),
                'message' => $success
                    ? 'Тестовое сообщение успешно отправлено!'
                    : 'Ошибка отправки: ' . ($responseData['description'] ?? 'Неизвестная ошибка'),
                'type' => $success ? 'success' : 'error',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to test analytics bot', [
                'error' => $e->getMessage(),
            ]);

            ToastMessage::create([
                'user_id' => auth()->id(),
                'message' => 'Исключение: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }

        return redirect()->route('admin.analytics.index');
    }
}
