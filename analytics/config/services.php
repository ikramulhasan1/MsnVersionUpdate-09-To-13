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
    | two-step pattern Google Places/Yelp establish here: add its
    | credentials block below, then list its DiscoverySourceInterface
    | implementation class in config/discovery.php's 'sources' array —
    | nothing else in this module needs to change.
    */

    'google_places' => [
        'api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    'yelp' => [
        'api_key' => env('YELP_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Sign-In (Phase N1, Authentication Foundation)
    |--------------------------------------------------------------------------
    |
    | Consumed by App\Http\Controllers\Auth\GoogleAuthController via
    | laravel/socialite (composer require laravel/socialite — see that
    | controller's own docblock). client_id/client_secret come from a
    | Google Cloud OAuth 2.0 Client ID (console.cloud.google.com ->
    | APIs & Services -> Credentials); redirect must EXACTLY match one
    | of that client's own "Authorized redirect URIs" or Google will
    | reject the callback outright — typically
    | https://your-domain.example/auth/google/callback (see
    | routes/auth.php's own 'auth.google.callback' route).
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz (Phase N6, Multiple Payment Methods)
    |--------------------------------------------------------------------------
    |
    | Consumed by App\Payments\SslCommerzGateway. store_id/store_password
    | come from an SSLCommerz merchant account (sslcommerz.com — a
    | sandbox account is free and instant for testing, at
    | sandbox.sslcommerz.com). sandbox=true uses
    | https://sandbox.sslcommerz.com's own API host; sandbox=false uses
    | the real https://securepay.sslcommerz.com host — this app must
    | NEVER go live with sandbox=true, real customer payments would
    | never actually process.
    */
    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
    ],

];