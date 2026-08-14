<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuditServiceProvider;
use App\Providers\DiscoveryServiceProvider;

return [
    AppServiceProvider::class,
    AuditServiceProvider::class,
    DiscoveryServiceProvider::class,
];