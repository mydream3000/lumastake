<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lumastake')</title>
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
                @hasSection('title')
                <tr>
                    <td align="center" style="padding: 0 40px 30px;">
                        <h1 style="margin: 0; font-size: 28px; font-weight: bold; color: #4da3ff;">
                            @yield('title')
                        </h1>
                    </td>
                </tr>
                @endif

                <!-- Main Content -->
                @yield('content')

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px 40px; border-top: 1px solid #e5e7eb;">
                        @if($footerSupport ?? true)
                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 12px;">
                            Support: <a href="mailto:{{ $supportEmail ?? 'support@lumastake.com' }}" style="color: #4da3ff; text-decoration: underline;">{{ $supportEmail ?? 'support@lumastake.com' }}</a>
                        </p>
                        @endif
                        <p style="margin: 0; color: #000000; font-size: 12px;">
                            {{ $footerText ?? '© ' . date('Y') . ' Lumastake. All rights reserved.' }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
