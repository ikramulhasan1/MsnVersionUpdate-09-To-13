<?php

declare(strict_types=1);

namespace App\Providers;

use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Geo\JsonGeoLookupService;
use App\Discovery\Search\Contracts\NaturalLanguageQueryParserInterface;
use App\Discovery\Search\NaturalLanguageQueryParser;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use App\Discovery\Sources\InternalCrawlSource;
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
 *
 * DiscoverySourceInterface (Phase I3) is bound to InternalCrawlSource
 * today — the module's first and only discovery source. A future
 * GoogleSearchSource/BusinessDirectorySource/... implementation is
 * the same one-line rebind here; a caller wanting several sources at
 * once would resolve each concrete class directly (or a small array of
 * bindings tagged for that purpose) rather than this single default
 * binding trying to represent more than one source simultaneously.
 */
final class DiscoveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeoLookupServiceInterface::class, JsonGeoLookupService::class);
        $this->app->bind(NaturalLanguageQueryParserInterface::class, NaturalLanguageQueryParser::class);
        $this->app->bind(DiscoverySourceInterface::class, InternalCrawlSource::class);
    }

    public function boot(): void
    {
        //
    }
}
