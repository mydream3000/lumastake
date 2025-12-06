<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Received - Lumastake</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #101221; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #101221; border-radius: 12px; overflow: hidden;">

                    <!-- Logo Section -->
                    <tr>
                        <td align="center" style="padding: 40px 20px 30px;">
                            <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" style="height: 50px; width: auto;">
                        </td>
                    </tr>

                    <!-- Title Section -->
                    <tr>
                        <td align="center" style="padding: 0 40px 30px;">
                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #ffffff;">
                                MESSAGE RECEIVED
                            </h1>
                            <p style="margin: 10px 0 0; font-size: 16px; color: #05C982;">
                                ✅ Your request has been submitted
                            </p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Hello <strong>{{ $user_name }}</strong>,
                            </p>

                            <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                Thank you for reaching out to us! We have successfully received your message and our support team will review it shortly.
                            </p>
                        </td>
                    </tr>

                    <!-- Message Preview Box -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; background: rgba(249, 250, 251, 0.04); border-left: 3px solid #05C982; border-radius: 6px; padding: 18px 20px;">
                                <tr>
                                    <td>
                                        <h2 style="margin: 0 0 10px; color: #05C982; font-size: 16px;">
                                            📩 Your Message
                                        </h2>
                                        <p style="margin: 0; color: #e5e7eb; font-size: 14px; line-height: 1.6;">
                                            {{ $message_preview }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Info Grid Section -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <!-- Reference Number -->
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                        <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Reference Number
                                        </div>
                                        <div style="font-size: 15px; color: #f9fafb;">
                                            #{{ rand(100000, 999999) }}
                                        </div>
                                    </td>
                                </tr>
                                <!-- Submitted At -->
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                                        <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Submitted At
                                        </div>
                                        <div style="font-size: 15px; color: #f9fafb;">
                                            {{ $submitted_at }}
                                        </div>
                                    </td>
                                </tr>
                                <!-- Contact Email -->
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <div style="font-size: 11px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Contact Email
                                        </div>
                                        <div style="font-size: 15px; color: #f9fafb;">
                                            {{ $user_email }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px 10px;">
                            <div style="height: 1px; background: linear-gradient(to right, rgba(156, 163, 175, 0), rgba(156, 163, 175, 0.5), rgba(156, 163, 175, 0));"></div>
                        </td>
                    </tr>

                    <!-- What Happens Next -->
                    <tr>
                        <td style="padding: 0 40px 10px;">
                            <p style="margin: 20px 0 10px; color: #ffffff; font-size: 15px;">
                                <strong>What happens next?</strong>
                            </p>
                            <ul style="color: #d1d5db; font-size: 14px; line-height: 1.8; padding-left: 20px; margin: 0 0 10px;">
                                <li>Our support team will review your message</li>
                                <li>You'll receive a response within 24–48 hours</li>
                                <li>We'll contact you via email at <strong>{{ $user_email }}</strong></li>
                            </ul>
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td align="center" style="padding: 10px 40px 30px;">
                            <a href="https://lumastake.com/dashboard"
                               style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #FF451C 0%, #05C982 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px;">
                                Go to Dashboard
                            </a>
                        </td>
                    </tr>

                    <!-- Notice / Info -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                <strong>Lumastake Support Team</strong>
                            </p>
                            <p style="margin: 0 0 8px; color: #6b7280; font-size: 12px; line-height: 1.6;">
                                This is an automated confirmation email. Please do not reply to this message.
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.6;">
                                Need urgent assistance? Visit our
                                <a href="https://lumastake.com/contact" style="color: #FF451C; text-decoration: none;">Help Center</a>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px 40px 40px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <p style="margin: 0; color: #666666; font-size: 12px;">
                                © {{ date('Y') }} Lumastake. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
