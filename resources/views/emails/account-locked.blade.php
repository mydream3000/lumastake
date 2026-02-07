<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Temporarily Locked</title>
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
                            <h1 style="margin: 0; font-size: 32px; font-weight: 700; color: #FF451C;">
                                ACCOUNT TEMPORARILY LOCKED
                            </h1>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">Hi {{ $userName }},</p>

                            <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 30px;">
                                Your account has been temporarily locked due to multiple failed login attempts. This is a security measure to protect your account from unauthorized access.
                            </p>
                        </td>
                    </tr>

                    <!-- Security Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table cellpadding="0" cellspacing="0" style="background: #fef2f2; border-left: 3px solid #FF451C; border-radius: 4px; padding: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px; color: #000000; font-size: 14px; line-height: 1.6;">
                                            <strong style="color: #FF451C;">Security Alert:</strong> Your account was locked on {{ $lockedAt->format('F j, Y \a\t g:i A') }} after three unsuccessful login attempts.
                                        </p>
                                        <p style="margin: 0; color: #000000; font-size: 14px; line-height: 1.6;">
                                            If this was you, please contact our support team to unlock your account. If you did not attempt to log in, your account may be at risk.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Contact Support Section -->
                    <tr>
                        <td style="padding: 0 40px 40px;">
                            <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                To unlock your account, please contact our technical support team:
                            </p>

                            <table cellpadding="0" cellspacing="0" style="background: #f0f7ff; border: 1px solid rgba(79, 209, 197, 0.3); border-radius: 8px; padding: 20px; width: 100%;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px; color: #000000; font-size: 13px;">Email Support:</p>
                                        <p style="margin: 0 0 20px; color: #4da3ff; font-size: 16px; font-weight: 600;">
                                            <a href="mailto:support@lumastake.com" style="color: #4da3ff; text-decoration: none;">support@lumastake.com</a>
                                        </p>
                                        <p style="margin: 0; color: #000000; font-size: 13px; line-height: 1.5;">
                                            Our support team is available 24/7 to assist you with unlocking your account and securing it from unauthorized access.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Security Tips -->
                    <tr>
                        <td style="padding: 0 40px 40px;">
                            <p style="margin: 0 0 15px; color: #000000; font-size: 15px; font-weight: 600;">Security Tips:</p>
                            <ul style="margin: 0; padding-left: 20px; color: #000000; font-size: 14px; line-height: 1.8;">
                                <li>Use a strong, unique password for your account</li>
                                <li>Enable two-factor authentication if available</li>
                                <li>Never share your password with anyone</li>
                                <li>Be cautious of phishing attempts</li>
                            </ul>
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
