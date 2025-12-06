<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotSetting;
use App\Models\ToastMessage;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class BotSettingsController extends Controller
{
    public function __construct(
        private TelegramBotService $telegramBotService
    ) {}

    /**
     * Отображение страницы настроек бота
     */
    public function index()
    {
        $botSettings = BotSetting::first();

        return view('admin.bot-settings.index', compact('botSettings'));
    }

    /**
     * Обновление настроек бота
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'bot_name' => 'nullable|string|max:255',
            'bot_token' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $botSettings = BotSetting::first();

        if ($botSettings) {
            $botSettings->update($validated);
            $message = 'Настройки бота успешно обновлены';
        } else {
            BotSetting::create($validated);
            $message = 'Настройки бота успешно созданы';
        }

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => $message,
            'type' => 'success',
        ]);

        return redirect()->route('admin.bot-settings.index');
    }

    /**
     * Тестирование подключения к боту
     */
    public function test()
    {
        $result = $this->telegramBotService->testConnection();

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => $result['message'],
            'type' => $result['success'] ? 'success' : 'error',
        ]);

        return redirect()->route('admin.bot-settings.index');
    }
}
