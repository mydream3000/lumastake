<?php

namespace App\Services;

use App\Helpers\GeoIpHelper;
use App\Models\BotSetting;
use App\Models\TelegramBot;
use App\Models\TelegramNotification;
use App\Models\Transaction;
use App\Models\CryptoTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    /**
     * Escape user-provided strings for Telegram HTML parse mode.
     */
    private function esc(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function networkTag(?string $network): string
    {
        return match($network) {
            'tron' => 'TRC-20',
            'ethereum', 'eth' => 'ERC-20',
            'bsc', 'bnb' => 'BEP-20',
            default => strtoupper((string) $network),
        };
    }

    private function formatTokenWithNetwork(string $token, ?string $network): string
    {
        $tag = $this->networkTag($network);
        return $tag ? "$token ($tag)" : $token;
    }

    private function explorerUrl(?string $network, string $tx): string
    {
        return match($network) {
            'tron' => "https://tronscan.org/#/transaction/{$tx}",
            'ethereum', 'eth' => "https://etherscan.io/tx/{$tx}",
            'bsc', 'bnb' => "https://bscscan.com/tx/{$tx}",
            default => '#',
        };
    }
    /**
     * Отправить уведомление о создании депозита
     */
    public function sendDepositCreated(Transaction $transaction, ?CryptoTransaction $cryptoTransaction = null): bool
    {
        $message = "💰 <b>Новая заявка на пополнение</b>\n\n";
        $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
        $meta = is_array($transaction->meta) ? $transaction->meta : [];
        $token = $cryptoTransaction?->token ?? ($meta['token'] ?? 'USDT');
        $net = $cryptoTransaction?->network ?? ($meta['network'] ?? null);
        $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " " . $this->formatTokenWithNetwork($token, $net) . "\n";
        $message .= "📅 Время: " . $transaction->created_at->format('d.m.Y H:i') . "\n";

        if ($cryptoTransaction && $cryptoTransaction->tx_hash) {
            $link = $this->explorerUrl($net, $cryptoTransaction->tx_hash);
            $message .= "🔗 TxID: <a href=\"{$link}\">{$this->shortHash($cryptoTransaction->tx_hash)}</a>\n";
        }

        $message .= "\n⏳ <i>Ожидаем подтверждения сети...</i>";

        return $this->sendMessage(
            $message,
            'deposit_created',
            $transaction,
            $cryptoTransaction
        );
    }

    /**
     * Отправить уведомление о подтверждении депозита
     */
    public function sendDepositConfirmed(Transaction $transaction, ?CryptoTransaction $cryptoTransaction = null): bool
    {
        $message = "✅ <b>Пополнение подтверждено!</b>\n\n";
        $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
        $meta = is_array($transaction->meta) ? $transaction->meta : [];
        $token = $cryptoTransaction?->token ?? ($meta['token'] ?? 'USDT');
        $net = $cryptoTransaction?->network ?? ($transaction->network ?? ($meta['network'] ?? null));
        $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " " . $this->formatTokenWithNetwork($token, $net) . "\n";
        $message .= "📅 Время: " . $transaction->updated_at->format('d.m.Y H:i') . "\n";

        $txHash = $cryptoTransaction?->tx_hash ?? $transaction->tx_hash;
        if ($txHash) {
            $link = $this->explorerUrl($net, $txHash);
            $message .= "🔗 TxID: <a href=\"{$link}\">{$this->shortHash($txHash)}</a>\n";
        }

        $message .= "\n✨ <i>Средства зачислены на счет пользователя</i>";

        return $this->sendMessage(
            $message,
            'deposit_confirmed',
            $transaction,
            $cryptoTransaction
        );
    }

    /**
     * Отправить уведомление о создании заявки на вывод
     */
    public function sendWithdrawCreated(Transaction $transaction): bool
    {
        $message = "🔴 <b>Новая заявка на вывод</b>\n\n";
        $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
        $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " USDT\n";
        $message .= "📍 Адрес: <code>" . $this->esc($transaction->wallet_address) . "</code>\n";
        $message .= "🌐 Сеть: " . $this->networkTag($transaction->network ?? null) . "\n";
        $message .= "📅 Время: " . $transaction->created_at->format('d.m.Y H:i') . "\n";

        $message .= "\n⏳ <i>Ожидает обработки администратором...</i>";

        return $this->sendMessage(
            $message,
            'withdraw_created',
            $transaction
        );
    }

    /**
     * Уведомление: пользователь выбрал сеть для депозита (получение адреса)
     */
    public function sendDepositNetworkSelected($user, string $network, string $token): bool
    {
        try {
            $message = "🟢 <b>Выбор сети для депозита</b>\n\n";
            $message .= "👤 Пользователь: " . $this->esc($user->name) . "\n";
            $message .= "🌐 Сеть: " . $this->networkTag($network) . "\n";
            $message .= "💠 Токен: {$token}\n";
            $message .= "\nℹ️ Пользователь запросил адрес для пополнения";

            // фиктивная транзакция только для логирования и связи с пользователем
            $tmp = new Transaction();
            $tmp->user = $user;

            return $this->sendMessage(
                $message,
                'deposit_created', // используем существующий тип для минимальных изменений схемы
                $tmp,
                null
            );
        } catch (\Exception $e) {
            Log::error('Failed to send deposit network selected notification', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Отправить уведомление о подтверждении вывода администратором
     */
    public function sendWithdrawConfirmed(Transaction $transaction, ?CryptoTransaction $cryptoTransaction = null): bool
    {
        $message = "💸 <b>Вывод успешно обработан!</b>\n\n";
        $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
        $token = $cryptoTransaction?->token ?? 'USDT';
        $net = $cryptoTransaction?->network ?? ($transaction->network ?? null);
        $message .= "💵 Сумма: \\$" . number_format($transaction->amount, 2) . " " . $this->formatTokenWithNetwork($token, $net) . "\n";
        $message .= "📍 Адрес: <code>" . $this->esc($transaction->wallet_address) . "</code>\n";
        $message .= "🌐 Сеть: " . ($net ? $this->networkTag($net) : strtoupper($transaction->network ?? 'TRC20')) . "\n";
        $message .= "📅 Время: " . $transaction->updated_at->format('d.m.Y H:i') . "\n";

        if ($cryptoTransaction && $cryptoTransaction->tx_hash) {
            $link = $this->explorerUrl($net, $cryptoTransaction->tx_hash);
            $message .= "🔗 TxID: <a href=\"{$link}\">{$this->shortHash($cryptoTransaction->tx_hash)}</a>\n";
        }

        $message .= "\n✅ <i>Деньги успешно выведены пользователю!</i>";

        return $this->sendMessage(
            $message,
            'withdraw_confirmed',
            $transaction,
            $cryptoTransaction
        );
    }

    /**
     * Отправить уведомление об отмене вывода администратором
     */
    public function sendWithdrawRejected(Transaction $transaction, string $reason): bool
    {
        $message = "❌ <b>Админ отменил вывод</b>\n\n";
        $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . " (ID: {$transaction->user_id})\n";
        $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " USDT\n";
        $message .= "📍 Адрес: <code>" . $this->esc($transaction->wallet_address) . "</code>\n";
        $message .= "🌐 Сеть: " . strtoupper($transaction->network ?? 'TRC20') . "\n";
        $message .= "📅 Время: " . $transaction->updated_at->format('d.m.Y H:i') . "\n";
        $message .= "📝 Причина: <i>" . $this->esc($reason) . "</i>\n";

        $message .= "\n🚫 <i>Заявка отклонена администратором</i>";

        return $this->sendMessage(
            $message,
            'withdraw_rejected',
            $transaction
        );
    }

    /**
     * Отправить сообщение в Telegram
     */
    private function sendMessage(
        string $text,
        string $messageType,
        Transaction $transaction,
        ?CryptoTransaction $cryptoTransaction = null
    ): bool {
        try {
            $botSettings = BotSetting::getActive();

            if (!$botSettings || !$botSettings->bot_token || !$botSettings->chat_id) {
                Log::warning('Telegram bot settings not configured or inactive');
                return false;
            }

            $url = "https://api.telegram.org/bot{$botSettings->bot_token}/sendMessage";

            $response = Http::post($url, [
                'chat_id' => $botSettings->chat_id,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            $responseData = $response->json();
            $success = $response->successful() && ($responseData['ok'] ?? false);

            // Логируем отправленное уведомление
            TelegramNotification::create([
                'transaction_id' => $transaction->id,
                'crypto_transaction_id' => $cryptoTransaction?->id,
                'message_type' => $messageType,
                'message_text' => $text,
                'tx_hash' => $cryptoTransaction?->tx_hash,
                'sent_at' => now(),
                'response' => $responseData,
            ]);

            if (!$success) {
                Log::error('Failed to send Telegram notification', [
                    'message_type' => $messageType,
                    'transaction_id' => $transaction->id,
                    'response' => $responseData,
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Exception while sending Telegram notification', [
                'message_type' => $messageType,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Сократить хеш транзакции для отображения
     */
    private function shortHash(string $hash): string
    {
        if (strlen($hash) <= 16) {
            return $hash;
        }

        return substr($hash, 0, 8) . '...' . substr($hash, -8);
    }

    /**
     * Отправить уведомление об обновлении статуса депозита
     */
    public function sendDepositStatusUpdate(Transaction $transaction, string $newStatus): bool
    {
        $meta = is_array($transaction->meta) ? $transaction->meta : [];
        $token = $meta['token'] ?? 'USDT';
        $net = $meta['network'] ?? $transaction->network;

        if ($newStatus === 'pending') {
            $message = "🟡 <b>Депозит обнаружен в блокчейне!</b>\n\n";
            $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
            $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " " . $this->formatTokenWithNetwork($token, $net) . "\n";
            $message .= "🌐 Сеть: " . $this->networkTag($net) . "\n";

            if ($transaction->tx_hash) {
                $link = $this->explorerUrl($net, $transaction->tx_hash);
                $message .= "🔗 TxID: <a href=\"{$link}\">{$this->shortHash($transaction->tx_hash)}</a>\n";
            }

            $message .= "\n⏳ <i>Ожидаем подтверждения сети...</i>";

            $messageType = 'deposit_pending';
        } elseif ($newStatus === 'confirmed') {
            $message = "✅ <b>Депозит подтвержден!</b>\n\n";
            $message .= "👤 Пользователь: " . $this->esc($transaction->user->name) . "\n";
            $message .= "💵 Сумма: \$" . number_format($transaction->amount, 2) . " " . $this->formatTokenWithNetwork($token, $net) . "\n";
            $message .= "🌐 Сеть: " . $this->networkTag($net) . "\n";
            $message .= "📅 Время: " . $transaction->updated_at->format('d.m.Y H:i') . "\n";

            if ($transaction->tx_hash) {
                $link = $this->explorerUrl($net, $transaction->tx_hash);
                $message .= "🔗 TxID: <a href=\"{$link}\">{$this->shortHash($transaction->tx_hash)}</a>\n";
            }

            $message .= "\n✨ <i>Средства зачислены на счет пользователя</i>";

            $messageType = 'deposit_confirmed';
        } else {
            return false;
        }

        return $this->sendMessage(
            $message,
            $messageType,
            $transaction
        );
    }

    /**
     * Проверить подключение к боту (тестовое сообщение)
     */
    public function testConnection(): array
    {
        try {
            $botSettings = BotSetting::getActive();

            if (!$botSettings || !$botSettings->bot_token || !$botSettings->chat_id) {
                return [
                    'success' => false,
                    'message' => 'Настройки бота не сконфигурированы или неактивны',
                ];
            }

            $url = "https://api.telegram.org/bot{$botSettings->bot_token}/sendMessage";

            $testMessage = "🤖 <b>Тестовое сообщение</b>\n\n";
            $testMessage .= "✅ Бот успешно подключен и работает!\n";
            $testMessage .= "📅 " . now()->format('d.m.Y H:i:s');

            $response = Http::post($url, [
                'chat_id' => $botSettings->chat_id,
                'text' => $testMessage,
                'parse_mode' => 'HTML',
            ]);

            $responseData = $response->json();
            $success = $response->successful() && ($responseData['ok'] ?? false);

            return [
                'success' => $success,
                'message' => $success
                    ? 'Тестовое сообщение успешно отправлено!'
                    : 'Ошибка отправки: ' . ($responseData['description'] ?? 'Неизвестная ошибка'),
                'response' => $responseData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Исключение: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Отправить сообщение из контактной формы в Telegram
     */
    public function sendContactForm(array $data): bool
    {
        try {
            // Получить все активные боты для контактных форм
            $bots = TelegramBot::where('is_active', true)->get();

            if ($bots->isEmpty()) {
                Log::warning('No active Telegram bots configured for contact form');
                return false;
            }

            $message = "📧 <b>Новое сообщение с сайта</b>\n\n";
            $message .= "👤 Имя: " . htmlspecialchars($data['name'] ?? '-', ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "📧 Email: " . htmlspecialchars($data['email'] ?? '-', ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "📞 Телефон: " . htmlspecialchars($data['phone'] ?? '-', ENT_QUOTES, 'UTF-8') . "\n";

            if (!empty($data['country'])) {
                $message .= "🌍 Страна: " . htmlspecialchars($data['country'], ENT_QUOTES, 'UTF-8') . "\n";
            }

            $message .= "\n💬 <b>Сообщение:</b>\n" . htmlspecialchars($data['message'] ?? '-', ENT_QUOTES, 'UTF-8') . "\n";
            $message .= "\n📅 Дата: " . now()->format('d.m.Y H:i:s');

            $success = true;

            foreach ($bots as $bot) {
                $url = "https://api.telegram.org/bot{$bot->bot_token}/sendMessage";

                $response = Http::post($url, [
                    'chat_id' => $bot->chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

                $responseData = $response->json();
                $botSuccess = $response->successful() && ($responseData['ok'] ?? false);

                if (!$botSuccess) {
                    Log::error('Failed to send contact form to Telegram bot', [
                        'bot_id' => $bot->id,
                        'bot_name' => $bot->name,
                        'response' => $responseData,
                    ]);
                    $success = false;
                }
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Exception while sending contact form to Telegram', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function sendAnalyticsMessage(string $text, string $logContext, array $logPayload): bool
    {
        try {
            $botSettings = \App\Models\AnalyticsBotSetting::getActive();

            if (!$botSettings || !$botSettings->bot_token || !$botSettings->chat_id) {
                Log::info($logContext . ' bot settings not configured or inactive');
                return false;
            }

            $url = "https://api.telegram.org/bot{$botSettings->bot_token}/sendMessage";

            $response = Http::post($url, [
                'chat_id' => $botSettings->chat_id,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            $responseData = $response->json();
            $success = $response->successful() && ($responseData['ok'] ?? false);

            if (!$success) {
                Log::error('Failed to send ' . $logContext . ' notification', array_merge($logPayload, ['response' => $responseData]));
            } else {
                Log::info($logContext . ' notification sent successfully', $logPayload);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Exception while sending ' . $logContext . ' notification', array_merge($logPayload, ['error' => $e->getMessage()]));
            return false;
        }
    }

    /**
     * Отправить уведомление о новой регистрации пользователя
     */
    public function sendUserRegistration(\App\Models\User $user, ?string $ipAddress = null, string $registrationType = 'form'): bool
    {
        $message = "👤 <b>Новая регистрация пользователя</b>\n\n";

        // Тип регистрации
        if ($registrationType === 'google') {
            $message .= "🔐 Тип: <b>Google OAuth</b>\n";
        } else {
            $message .= "📝 Тип: <b>Обычная регистрация</b>\n";
        }

        // Тип аккаунта
        $message .= "📋 Тип аккаунта: <b>" . ucfirst($user->account_type) . "</b>\n\n";


        // Основные данные
        if ($user->name) {
            $message .= "👤 Имя: " . $this->esc($user->name) . "\n";
        }
        $message .= "📧 Email: " . $this->esc($user->email) . "\n";

        // Телефон (только для обычной регистрации)
        if ($user->phone && $user->country_code) {
            $message .= "📞 Телефон: {$user->country_code} {$user->phone}\n";
        } elseif ($user->phone) {
            $message .= "📞 Телефон: {$user->phone}\n";
        }

        // Страна
        if ($user->country) {
            $message .= "🌍 Страна: " . strtoupper($user->country) . "\n";
        }

        // Дата регистрации
        $message .= "📅 Дата: " . $user->created_at->format('d.m.Y H:i:s') . "\n";

        // IP адрес и страна
        if ($ipAddress) {
            $country = GeoIpHelper::getCountryByIp($ipAddress);
            $message .= "🌐 IP: <code>{$ipAddress}</code>\n";
            if ($country && $country !== 'Unknown') {
                $message .= "🌍 Страна (по IP): {$country}\n";
            }
        }

        // ID пользователя
        $message .= "\n🆔 User ID: <code>{$user->id}</code>";

        return $this->sendAnalyticsMessage($message, 'user registration', ['user_id' => $user->id]);
    }

    /**
     * Отправить уведомление о входе администратора
     */
    public function sendAdminLogin(\App\Models\User $user, ?string $ipAddress = null, string $loginType = 'standard'): bool
    {
        $message = "🔑 <b>Вход администратора</b>\n\n";

        // Тип входа
        if ($loginType === 'test') {
            $message .= "⚠️ Тип: <b>Тестовый вход</b>\n";
        } elseif ($loginType === '2fa') {
            $message .= "🔐 Тип: <b>Вход через 2FA</b>\n";
        } else {
            $message .= "✅ Тип: <b>Стандартный вход</b>\n";
        }
        $message .= "\n";

        // Основные данные
        if ($user->name) {
            $message .= "👤 Имя: " . $this->esc($user->name) . "\n";
        }
        $message .= "📧 Email: " . $this->esc($user->email) . "\n";

        // Телефон
        if ($user->phone && $user->country_code) {
            $message .= "📞 Телефон: {$user->country_code} {$user->phone}\n";
        } elseif ($user->phone) {
            $message .= "📞 Телефон: {$user->phone}\n";
        }

        // Страна
        if ($user->country) {
            $message .= "🌍 Страна: " . strtoupper($user->country) . "\n";
        }

        // Время входа
        $message .= "📅 Время: " . now()->format('d.m.Y H:i:s') . "\n";

        // IP адрес и страна
        if ($ipAddress) {
            $country = GeoIpHelper::getCountryByIp($ipAddress);
            $message .= "🌐 IP: <code>{$ipAddress}</code>\n";
            if ($country && $country !== 'Unknown') {
                $message .= "🌍 Страна (по IP): {$country}\n";
            }
        }

        // ID пользователя
        $message .= "\n🆔 User ID: <code>{$user->id}</code>";

        return $this->sendAnalyticsMessage($message, 'admin login', ['user_id' => $user->id]);
    }
}
