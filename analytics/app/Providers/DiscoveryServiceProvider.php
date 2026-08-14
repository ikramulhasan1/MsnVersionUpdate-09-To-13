<?php

declare(strict_types=1);

namespace App\Providers;

use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Geo\JsonGeoLookupService;
use Illuminate\Support\ServiceProvider;

/**
 * Website Discovery module bindings — kept separate from
 * AuditServiceProvider since this is a distinct module with its own
 * growing set of bindings, not an extension of the audit pipeline.
 *
 * GeoLookupServiceInterface is bound to JsonGeoLookupService today
 * (see that class's own docblock for exactly what it can/can't do
 * yet). Swapping in a real geocoding/places API implementation later
 * — for full region and city coverage — is a one-line change here;
 * no caller of the interface needs to change.
 */
final class DiscoveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeoLookupServiceInterface::class, JsonGeoLookupService::class);
    }

    public function boot(): void
    {
        //
    }
}