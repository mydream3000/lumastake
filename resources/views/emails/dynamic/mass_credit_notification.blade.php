@extends('emails.layouts.base')

@section('title', 'Funds credited to your balance')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            We have credited <strong style="color:#4FD1C5;">${{ $amount }}</strong> to your account balance.
        </p>
        @if(!empty($comment))
            <div style="background: rgba(255, 255, 255, 0.05); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                <p style="color: #ffffff; font-size: 14px; margin: 0;">
                    Comment from administrator: {{ $comment }}
                </p>
            </div>
        @endif
    </td>
</tr>
@endsection