<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\SupportEmail;
use App\Models\ContactInfo;
use App\Services\TelegramBotService;
use App\Rules\SafeString;
use App\Rules\NoUrlForName;
use App\Mail\ContactReceivedMail;

class ContactController extends BaseController
{
    public function index()
    {
        $contactInfo = ContactInfo::where('key', 'main')->first();

        // Create default if not exists
        if (!$contactInfo) {
            $contactInfo = (object)[
                'email' => 'support@lumastake.com',
                'phone' => '+971 12 345 6789',
                'address' => 'Dubai - UAE',
                'telegram' => '@lumastake_support',
            ];
        }

        $seoKey = 'contact';

        return view('public.contact', compact('contactInfo', 'seoKey'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', new SafeString(false), new NoUrlForName()],
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => ['nullable', 'string', 'max:50', new SafeString(false)],
            'country_code' => ['nullable', 'string', 'max:10', new SafeString(false)],
            'country' => ['nullable', 'string', 'max:5', new SafeString(false)],
            'message' => ['required', 'string', 'max:5000', new SafeString()],
        ]);

        // Получить все активные email-адреса для поддержки
        $supportEmails = SupportEmail::where('is_active', true)->pluck('email')->toArray();

        // Если нет активных email-адресов в БД, использовать fallback из .env
        if (empty($supportEmails)) {
            $supportEmails = [env('SUPPORT_EMAIL', config('mail.from.address'))];
        }

        // Format phone with country code if available
        $phone = $data['phone'] ?? '-';
        if (!empty($data['country_code']) && !empty($phone) && $phone !== '-') {
            $phone = $data['country_code'] . ' ' . $phone;
        }

        $emailBody = "New contact message from Lumastake website\n\n".
            "Name: {$data['name']}\n".
            "Email: {$data['email']}\n".
            "Phone: {$phone}\n".
            (!empty($data['country']) ? "Country: {$data['country']}\n" : "").
            "\nMessage:\n{$data['message']}\n";

        // Отправить на все email-адреса
        foreach ($supportEmails as $email) {
            try {
                Mail::raw($emailBody, function ($m) use ($email) {
                    $m->to($email)->subject('Contact form submission - Lumastake');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send contact form email', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Prepare data for Telegram with formatted phone
        $telegramData = $data;
        $telegramData['phone'] = $phone;

        // Отправить в Telegram
        try {
            $telegramService = app(TelegramBotService::class);
            $telegramService->sendContactForm($telegramData);
        } catch (\Exception $e) {
            Log::error('Failed to send contact form to Telegram', [
                'error' => $e->getMessage(),
            ]);
        }

        // Generate unique reference number for user confirmation email
        $reference = rand(100000, 999999);

        // Send confirmation email to user
        try {
            Mail::to($data['email'])->send(new ContactReceivedMail(
                $data['name'],
                $data['email'],
                $data['message'],
                $reference
            ));

            Log::info('Confirmation email sent to user', [
                'email' => $data['email'],
                'reference' => $reference
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email to user', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);
            // Не прерываем процесс, если не удалось отправить подтверждение
        }

        return redirect()->route('contact')->with('status', 'Your message has been sent.');
    }
}
