<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportEmail;
use App\Models\TelegramBot;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportTeamController extends Controller
{
    /**
     * Показать страницу управления Support Team
     */
    public function index()
    {
        $supportEmails = SupportEmail::orderBy('created_at', 'desc')->get();
        $telegramBots = TelegramBot::orderBy('created_at', 'desc')->get();
        $contactInfo = ContactInfo::where('key', 'main')->first();

        // Create default if not exists
        if (!$contactInfo) {
            $contactInfo = ContactInfo::create([
                'key' => 'main',
                'email' => 'support@lumastake.com',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, Country',
                'telegram' => '@lumastake_support',
            ]);
        }

        return view('admin.support-team.index', compact('supportEmails', 'telegramBots', 'contactInfo'));
    }

    // ========== SUPPORT EMAILS ==========

    /**
     * Создать новый email для поддержки
     */
    public function storeEmail(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:support_emails,email',
            'name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        SupportEmail::create($data);

        return back()->with('success', 'Email адрес успешно добавлен!');
    }

    /**
     * Обновить email для поддержки
     */
    public function updateEmail(Request $request, SupportEmail $supportEmail)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:support_emails,email,' . $supportEmail->id,
            'name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $supportEmail->update($data);

        return back()->with('success', 'Email адрес успешно обновлен!');
    }

    /**
     * Удалить email для поддержки
     */
    public function destroyEmail(SupportEmail $supportEmail)
    {
        $supportEmail->delete();

        return back()->with('success', 'Email адрес успешно удален!');
    }

    /**
     * Переключить статус активности email
     */
    public function toggleEmailStatus(SupportEmail $supportEmail)
    {
        $supportEmail->update([
            'is_active' => !$supportEmail->is_active,
        ]);

        return back()->with('success', 'Статус успешно изменен!');
    }

    // ========== TELEGRAM BOTS ==========

    /**
     * Создать нового Telegram бота
     */
    public function storeBot(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bot_token' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        TelegramBot::create($data);

        return back()->with('success', 'Telegram бот успешно добавлен!');
    }

    /**
     * Обновить Telegram бота
     */
    public function updateBot(Request $request, TelegramBot $telegramBot)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bot_token' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $telegramBot->update($data);

        return back()->with('success', 'Telegram бот успешно обновлен!');
    }

    /**
     * Удалить Telegram бота
     */
    public function destroyBot(TelegramBot $telegramBot)
    {
        $telegramBot->delete();

        return back()->with('success', 'Telegram бот успешно удален!');
    }

    /**
     * Переключить статус активности бота
     */
    public function toggleBotStatus(TelegramBot $telegramBot)
    {
        $telegramBot->update([
            'is_active' => !$telegramBot->is_active,
        ]);

        return back()->with('success', 'Статус успешно изменен!');
    }

    /**
     * Протестировать подключение Telegram бота
     */
    public function testBot(TelegramBot $telegramBot)
    {
        try {
            $url = "https://api.telegram.org/bot{$telegramBot->bot_token}/sendMessage";

            $testMessage = "🤖 <b>Тестовое сообщение</b>\n\n";
            $testMessage .= "✅ Бот <b>{$telegramBot->name}</b> успешно подключен и работает!\n";
            $testMessage .= "📅 Дата: " . now()->format('d.m.Y H:i:s');

            $response = \Illuminate\Support\Facades\Http::post($url, [
                'chat_id' => $telegramBot->chat_id,
                'text' => $testMessage,
                'parse_mode' => 'HTML',
            ]);

            $responseData = $response->json();
            $success = $response->successful() && ($responseData['ok'] ?? false);

            if ($success) {
                return back()->with('success', 'Тестовое сообщение успешно отправлено в Telegram!');
            } else {
                $errorMsg = $responseData['description'] ?? 'Неизвестная ошибка';
                return back()->with('error', 'Ошибка отправки: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            Log::error('Telegram bot test failed', [
                'bot_id' => $telegramBot->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Исключение: ' . $e->getMessage());
        }
    }

    // ========== CONTACT INFO ==========

    /**
     * Обновить контактную информацию
     */
    public function updateContactInfo(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'telegram' => 'nullable|string|max:255',
        ]);

        $contactInfo = ContactInfo::where('key', 'main')->first();

        if ($contactInfo) {
            $contactInfo->update($data);
        } else {
            ContactInfo::create(array_merge($data, ['key' => 'main']));
        }

        return back()->with('success', 'Контактная информация успешно обновлена!');
    }
}
