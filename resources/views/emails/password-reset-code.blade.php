<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #101221; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #101221; border-radius: 12px; overflow: hidden;">
                    <!-- Logo Section -->
                    <tr>
                        <td align="center" style="padding: 40px 20px 30px;">
                            <img src="https://lumastake.com/images/sidebar/logo-white.png" alt="Lumastake" style="height: 50px; width: auto;">
                        </td>
                    </tr>

                    <!-- Gradient Title Section -->
                    <tr>
                        <td align="center" style="padding: 0 40px 30px;">
                            <h1 style="margin: 0; font-size: 32px; font-weight: 700; color: #000000;">
                                RESET YOUR PASSWORD
                            </h1>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">Hi {{ $userName }},</p>

                            <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 30px;">
                                We received a request to reset your password. Use the code below to reset your password:
                            </p>
                        </td>
                    </tr>

                    <!-- Reset Code Section -->
                    <tr>
                        <td align="center" style="padding: 0 40px 30px;">
                            <table cellpadding="0" cellspacing="0" style="background: #f9fafb; border: 2px solid #d1d5db; border-radius: 12px; padding: 30px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px; color: #000000; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Your Reset Code</p>
                                        <h2 style="margin: 0; color: #000000; font-size: 48px; font-weight: 700; letter-spacing: 12px; font-family: 'Courier New', monospace;">{{ $code }}</h2>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Important Notice -->
                    <tr>
                        <td style="padding: 0 40px 40px;">
                            <table cellpadding="0" cellspacing="0" style="background: #fef2f2; border-left: 3px solid #FF451C; border-radius: 4px; padding: 15px 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; color: #000000; font-size: 14px; line-height: 1.5;">
                                            <strong style="color: #FF451C;">Important:</strong> This code will expire in 15 minutes. If you did not request a password reset, please ignore this email and your password will remain unchanged.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 30px 40px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #000000; font-size: 12px;">
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
