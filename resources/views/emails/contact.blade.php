<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
                            New Contact Form Submission
                        </h1>
                    </td>
                </tr>

                <!-- Content Section -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
                            A new message has been received through the contact form. Please review the details below.
                        </p>
                    </td>
                </tr>

                <!-- Details Grid -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <table cellpadding="0" cellspacing="0" width="100%" style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Name</p>
                                    <p style="color: #000000; font-size: 15px; font-weight: 500; margin: 0;">{{ $data['name'] ?? ($data['first_name'] . ' ' . $data['last_name']) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Email</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">
                                        <a href="mailto:{{ $data['email'] }}" style="color: #4da3ff; text-decoration: underline;">{{ $data['email'] }}</a>
                                    </p>
                                </td>
                            </tr>
                            @if(!empty($data['phone']))
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Phone</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">{{ $data['phone'] }}</p>
                                </td>
                            </tr>
                            @endif
                            @if(!empty($data['country']))
                            <tr>
                                <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Country</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">{{ $data['country'] }}</p>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 4px;">Submitted At</p>
                                    <p style="color: #000000; font-size: 15px; margin: 0;">{{ now()->format('F d, Y \\a\\t H:i') }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Message Section -->
                <tr>
                    <td style="padding: 0 40px 30px;">
                        <p style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin: 0 0 8px;">Message</p>
                        <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 16px 20px;">
                            <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0;">{{ $data['message'] }}</p>
                        </div>
                    </td>
                </tr>

                <!-- Action Note -->
                <tr>
                    <td style="padding: 0 40px 30px;">
                        <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px;">
                            <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">
                                <strong style="color: #4da3ff;">Action Required:</strong> Please reply to this inquiry at your earliest convenience. Use the "Reply" button in your email client to respond directly to the sender.
                            </p>
                        </div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px 40px; border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0; color: #000000; font-size: 12px;">
                            &copy; {{ date('Y') }} Lumastake. All rights reserved.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
