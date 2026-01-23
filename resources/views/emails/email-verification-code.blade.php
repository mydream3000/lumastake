@extends('emails.layouts.base')

@section('title', 'Verify Your Email')

@section('content')
<!-- Content Section -->
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Hi {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Thank you for registering with Lumastake! To complete your registration, please verify your email address using the code below:
        </p>
    </td>
</tr>

<!-- Verification Code Section -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <table cellpadding="0" cellspacing="0" style="background: rgba(255, 255, 255, 0.05); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 30px;">
            <tr>
                <td align="center">
                    <p style="margin: 0 0 10px; color: #999999; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Your Verification Code</p>
                    <h2 style="margin: 0; color: #ffffff; font-size: 48px; font-weight: 700; letter-spacing: 12px; font-family: 'Courier New', monospace;">{{ $code }}</h2>
                </td>
            </tr>
        </table>
    </td>
</tr>

<!-- Important Notice -->
<tr>
    <td style="padding: 0 40px 30px;">
        <table cellpadding="0" cellspacing="0" style="background: rgba(255, 69, 28, 0.1); border-left: 3px solid #FF451C; border-radius: 4px; padding: 15px 20px;">
            <tr>
                <td>
                    <p style="margin: 0; color: #ffffff; font-size: 14px; line-height: 1.5;">
                        <strong style="color: #FF451C;">Important:</strong> This code will expire in 60 minutes. If you did not create an account with Lumastake, please ignore this email.
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection
