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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'epsm' => [
        'url' => env('EPSM_API_URL', 'https://api.epsm.gov.my'),
        'api_key' => env('EPSM_API_KEY', ''),
        'timeout' => env('EPSM_API_TIMEOUT', 10),
        'retries' => env('EPSM_API_RETRIES', 3),
    ],

    'idaftar' => [
        'url' => env('IDAFTAR_API_URL', 'https://api.idaftar.gov.my'),
        'api_key' => env('IDAFTAR_API_KEY', ''),
        'timeout' => env('IDAFTAR_API_TIMEOUT', 10),
        'cache_ttl' => env('IDAFTAR_CACHE_TTL', 10080), // 7 days in minutes
    ],

];
