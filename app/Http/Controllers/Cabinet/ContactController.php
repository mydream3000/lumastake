<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\SupportEmail;
use App\Models\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Rules\SafeString;
use App\Rules\NoUrlForName;
use App\Mail\ContactReceivedMail;
use App\Models\ContactSubmission;

class ContactController extends Controller
{
    public function index()
    {
        return view('cabinet.feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255', new SafeString(false), new NoUrlForName()],
            'last_name' => ['required', 'string', 'max:255', new SafeString(false), new NoUrlForName()],
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => ['nullable', 'string', 'max:255', new SafeString(false)],
            'country_code' => ['nullable', 'string', 'max:10', new SafeString(false)],
            'country' => ['nullable', 'string', 'max:2', new SafeString(false)],
            'message' => ['required', 'string', 'max:5000', new SafeString()],
        ]);

        // Combine first and last name for backwards compatibility
        $data = $request->all();
        $data['name'] = $request->first_name . ' ' . $request->last_name;

        // Format phone with country code if available
        if ($request->country_code && $request->phone) {
            $data['phone'] = $request->country_code . ' ' . $request->phone;
        }

        $emailSent = false;
        $telegramSent = false;

        try {
            // Отправка на все активные Support Emails
            $supportEmails = SupportEmail::where('is_active', true)->get();

            if ($supportEmails->isNotEmpty()) {
                foreach ($supportEmails as $supportEmail) {
                    try {
                        Mail::to($supportEmail->email)->send(new \App\Mail\ContactMail($data));
                        $emailSent = true;
                    } catch (\Exception $e) {
                        Log::error('Failed to send contact email', [
                            'support_email' => $supportEmail->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Отправка уведомлений во все активные Telegram боты
            $telegramBots = TelegramBot::where('is_active', true)->get();

            if ($telegramBots->isNotEmpty()) {
                foreach ($telegramBots as $bot) {
                    try {
                        $this->sendTelegramNotification($bot, $data);
                        $telegramSent = true;
                    } catch (\Exception $e) {
                        Log::error('Failed to send telegram notification', [
                            'bot_id' => $bot->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Generate unique reference number
            $reference = rand(100000, 999999);

            // Save submission to database
            ContactSubmission::create([
                'reference' => $reference,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'country' => $data['country'] ?? null,
                'message' => $data['message'],
                'user_id' => auth()->id(),
            ]);

            // Отправка подтверждения пользователю
            if ($emailSent || $telegramSent) {
                try {
                    Log::info('Attempting to send confirmation email', [
                        'user_email' => $data['email'],
                        'user_name' => $data['name'],
                        'reference' => $reference
                    ]);

                    Mail::to($data['email'])->send(new ContactReceivedMail(
                        $data['name'],
                        $data['email'],
                        $data['message'],
                        $reference
                    ));

                    Log::info('Confirmation email sent successfully', [
                        'user_email' => $data['email'],
                        'reference' => $reference
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send confirmation email to user', [
                        'user_email' => $data['email'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                return redirect()->back()->with([
                    'success' => 'Your message has been sent successfully!',
                    'show_confirmation_modal' => true
                ]);
            } else {
                return redirect()->back()->with('error', 'Failed to send the message. Please try again later.');
            }
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'An error occurred while sending the message. Please try again later.');
        }
    }

    /**
     * Отправить уведомление в Telegram
     */
    private function sendTelegramNotification(TelegramBot $bot, array $data)
    {
        $url = "https://api.telegram.org/bot{$bot->bot_token}/sendMessage";

        $message = "📧 <b>Новое сообщение из формы обратной связи</b>\n\n";
        $message .= "👤 <b>Имя:</b> " . htmlspecialchars($data['name']) . "\n";
        $message .= "📧 <b>Email:</b> " . htmlspecialchars($data['email']) . "\n";

        if (!empty($data['phone'])) {
            $message .= "📞 <b>Телефон:</b> " . htmlspecialchars($data['phone']) . "\n";
        }

        if (!empty($data['country'])) {
            $message .= "🌍 <b>Страна:</b> " . htmlspecialchars($data['country']) . "\n";
        }

        $message .= "\n💬 <b>Сообщение:</b>\n";
        $message .= htmlspecialchars($data['message']) . "\n\n";
        $message .= "📅 <b>Дата:</b> " . now()->format('d.m.Y H:i:s');

        $response = Http::post($url, [
            'chat_id' => $bot->chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Telegram API error: ' . $response->body());
        }
    }
}
