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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Website Discovery module — external discovery sources
    |--------------------------------------------------------------------------
    |
    | Credentials for App\Discovery\Sources\Contracts\DiscoverySourceInterface
    | implementations that call a real external API. See
    | config/discovery.php's own 'sources' array for which of these are
    | actually active — a source class listed there but missing its key
    | here is expected to no-op safely (return an empty Collection)
    | rather than throw, so a not-yet-configured source never breaks
    | discovery for the others.
    |
    | Adding a new source (Bing, Clearbit, ...) later is the same
    | two-step pattern Google Places establishes here: add its
    | credentials block below, then list its DiscoverySourceInterface
    | implementation class in config/discovery.php's 'sources' array —
    | nothing else in this module needs to change.
    */

    'google_places' => [
        'api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

];
