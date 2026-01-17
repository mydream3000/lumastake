<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL').'/auth/google/callback'),
    ],

    // Veriff (KYC)
    'veriff' => [
        'base_url' => env('VERIFF_BASE_URL', 'https://stationapi.veriff.com'),
        'api_key' => 'f6dfe357-c340-4b53-8022-c97a8b6de9db',
        'signature_key' => '696b8dfd-b8c6-4f11-9eff-1d0fb32f5bb3',
        // Return/Callback URL after user completes verification flow (for reference)
        'callback_url' => env('VERIFF_CALLBACK_URL'),
        // Our webhook endpoints (configured at Veriff dashboard)
        'webhook_events_url' => env('VERIFF_WEBHOOK_EVENTS_URL'),
        'webhook_decisions_url' => env('VERIFF_WEBHOOK_DECISIONS_URL'),
        'webhook_peps_url' => env('VERIFF_WEBHOOK_PEPS_URL'),
    ],

    // Intercom live chat
    'intercom' => [
        'app_id' => env('INTERCOM_APP_ID'),
    ],

];
