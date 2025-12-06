@extends('emails.layouts.base')

@section('title', 'Identity Verification Started')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            You have started the identity verification process. Please complete all steps in the opened window.
        </p>
        <div style="background: rgba(255, 255, 255, 0.05); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                If the window was closed, you can restart verification from your profile page.
            </p>
        </div>
    </td>
</tr>
@endsection