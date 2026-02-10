<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')
            ->where('key', 'contact_received')
            ->update([
                'subject' => 'Your Request Has Been Received - Lumastake Support',
                'sender_name' => 'Lumastake Support Team',
                'content' => $this->getEmailContent(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No rollback needed
    }

    private function getEmailContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Received - Lumastake</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; padding: 40px 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                <!-- Logo Section -->
                <tr>
                    <td align="center" style="padding: 40px 20px 30px;">
                        <img src="https://lumastake.com/images/sidebar/logo-white.png" alt="Lumastake" style="height: 50px; width: auto;">
                    </td>
                </tr>

                <!-- Title Section -->
                <tr>
                    <td align="center" style="padding: 0 40px 30px;">
                        <h1 style="margin: 0; font-size: 28px; font-weight: bold; color: #4da3ff;">
                            Request Received
                        </h1>
                    </td>
                </tr>

                <!-- Content Section -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
                            Dear {{ $user_name }},
                        </p>
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
                            Thank you for contacting Lumastake Support. We have received your inquiry and it has been assigned to our technical support team for review.
                        </p>
                    </td>
                </tr>

                <!-- Reference Info -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <table cellpadding="0" cellspacing="0" width="100%" style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Request Reference Number</p>
                                    <p style="color: #4da3ff; font-size: 20px; font-weight: 700; margin: 0; font-family: 'Courier New', monospace;">#{{ $reference }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Submitted At</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">{{ $submitted_at }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Contact Email</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">{{ $user_email }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Message Preview -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 8px;">Your Message</p>
                        <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 16px 20px;">
                            <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">{{ $message_preview }}</p>
                        </div>
                    </td>
                </tr>

                <!-- What Happens Next -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <div style="height: 1px; background: linear-gradient(to right, transparent, #e5e7eb, transparent); margin: 10px 0;"></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #000000; font-size: 15px; font-weight: 600; margin: 0 0 12px;">What happens next?</p>
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="padding: 6px 0; color: #000000; font-size: 14px; line-height: 1.6;">
                                    &bull;&nbsp; Our support team will review your request promptly
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: #000000; font-size: 14px; line-height: 1.6;">
                                    &bull;&nbsp; A representative will contact you within 24 business hours
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color: #000000; font-size: 14px; line-height: 1.6;">
                                    &bull;&nbsp; All correspondence will be sent to <strong>{{ $user_email }}</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- CTA Button -->
                <tr>
                    <td align="center" style="padding: 10px 40px 30px;">
                        <a href="https://lumastake.com/dashboard" style="display: inline-block; background: linear-gradient(135deg, #4da3ff 0%, #3b82f6 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">
                            Go to Dashboard
                        </a>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px 40px; border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 12px;">
                            <strong>Lumastake Support Team</strong>
                        </p>
                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 12px;">
                            This is an automated confirmation. Please do not reply directly to this email.
                        </p>
                        <p style="margin: 0; color: #000000; font-size: 12px;">
                            Support: <a href="mailto:support@lumastake.com" style="color: #4da3ff; text-decoration: underline;">support@lumastake.com</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
HTML;
    }
};
