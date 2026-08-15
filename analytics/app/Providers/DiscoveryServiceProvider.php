<?php

declare(strict_types=1);

namespace App\Providers;

use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Geo\JsonGeoLookupService;
use App\Discovery\Search\Contracts\NaturalLanguageQueryParserInterface;
use App\Discovery\Search\NaturalLanguageQueryParser;
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
 *
 * NaturalLanguageQueryParserInterface (Phase F2) is bound to the
 * rule-based NaturalLanguageQueryParser today — see that class's own
 * docblock for exactly what it can/can't recognize. Swapping in a
 * future LLM-backed implementation is the same one-line change here.
 */
final class DiscoveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeoLookupServiceInterface::class, JsonGeoLookupService::class);
        $this->app->bind(NaturalLanguageQueryParserInterface::class, NaturalLanguageQueryParser::class);
    }

    public function boot(): void
    {
        // This whole app is built on Bootstrap 5 (see the CDN link in
        // resources/views/layouts/app.blade.php) — Laravel's own
        // pagination views default to Tailwind CSS classes, which would
        // render completely unstyled here. Website Discovery's result
        // list (Phase D3, resources/views/discovery/index.blade.php's
        // $websites->links() call) is this app's first use of Laravel's
        // paginator, so this wasn't needed until now.
        //
        // (Note: Paginator::useBootstrapFive() lives in AppServiceProvider,
        // not here — this boot() is intentionally empty; Discovery-specific
        // service registration happens in register() above.)
    }
}