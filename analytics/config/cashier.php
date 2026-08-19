<?php

declare(strict_types=1);

/**
 * Phase N6 (Multiple Payment Methods) — laravel/cashier's own default
 * published config, written by hand for the same reason
 * config/permission.php's own docblock explains (this app's deploy
 * process couldn't run `vendor:publish` directly). Every value below
 * matches that package's own out-of-the-box default.
 */
return [

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'path' => env('CASHIER_PATH', 'stripe'),

    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    'currency' => env('CASHIER_CURRENCY', 'usd'),

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    'logger' => env('CASHIER_LOGGER'),

];