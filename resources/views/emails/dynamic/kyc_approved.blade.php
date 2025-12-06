@extends('emails.layouts.base')

@section('title', 'Identity Verification Approved')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear Othmanz,
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Great news! Your identity verification has been <strong style="color:#4FD1C5;">Approved</strong>. 
        </p>
        <div style="background: rgba(79, 209, 197, 0.1); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                You now have access to all features requiring verification.
            </p>
        </div>
    </td>
</tr>
@endsection