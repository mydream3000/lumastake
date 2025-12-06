<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('email_templates')->insert([
            'key' => 'contact_received',
            'name' => 'Contact Form Received Confirmation',
            'subject' => 'We received your message - Lumastake Support',
            'sender_name' => 'Lumastake Support Team',
            'content' => $this->getEmailContent(),
            'variables' => json_encode([
                'user_name' => 'Name of the user who submitted the form',
                'user_email' => 'Email address of the user',
                'message_preview' => 'Preview of the submitted message',
                'submitted_at' => 'Date and time when message was submitted',
                'reference' => 'Unique reference number for this message (6-digit number)'
            ]),
            'enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('email_templates')->where('key', 'contact_received')->delete();
    }

    /**
     * Get the default email template content
     */
    private function getEmailContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Received - Lumastake</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF451C 0%, #05C982 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .message-box {
            background: #f9fafb;
            border-left: 4px solid #05C982;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
        }
        .message-box h2 {
            margin: 0 0 10px 0;
            color: #05C982;
            font-size: 18px;
        }
        .message-box p {
            margin: 0;
            color: #555;
        }
        .info-grid {
            margin: 25px 0;
        }
        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 15px;
            color: #1f2937;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #FF451C 0%, #05C982 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 25px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #FF451C;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✅</div>
            <h1>Message Received!</h1>
        </div>
        <div class="content">
            <p style="font-size: 16px; margin: 0 0 20px 0;">Hello <strong>{{ $user_name }}</strong>,</p>

            <p style="margin: 0 0 20px 0;">
                Thank you for reaching out to us! We have successfully received your message and our support team will review it shortly.
            </p>

            <div class="message-box">
                <h2>📩 Your Message</h2>
                <p>{{ $message_preview }}</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Reference Number</div>
                    <div class="info-value">#{{ $reference }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submitted At</div>
                    <div class="info-value">{{ $submitted_at }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact Email</div>
                    <div class="info-value">{{ $user_email }}</div>
                </div>
            </div>

            <div class="divider"></div>

            <p style="margin: 20px 0;">
                <strong>What happens next?</strong>
            </p>
            <ul style="color: #555; line-height: 1.8; padding-left: 20px;">
                <li>Our support team will review your message</li>
                <li>You'll receive a response within 24-48 hours</li>
                <li>We'll contact you via email at <strong>{{ $user_email }}</strong></li>
            </ul>

            <div style="text-align: center; margin-top: 30px;">
                <a href="https://lumastake.com/dashboard" class="button">Go to Dashboard</a>
            </div>
        </div>
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Lumastake Support Team</strong></p>
            <p style="margin: 0 0 10px 0;">This is an automated confirmation email. Please do not reply to this message.</p>
            <p style="margin: 0;">
                Need urgent assistance? Visit our <a href="https://lumastake.com/contact">Help Center</a>
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
};
