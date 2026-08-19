<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuditServiceProvider;
use App\Providers\DiscoveryServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;

return [
    AppServiceProvider::class,
    AuditServiceProvider::class,
    DiscoveryServiceProvider::class,
    SocialiteServiceProvider::class,
];