<?php

declare(strict_types=1);

namespace App\Discovery\Ingestion;

use App\Discovery\Ingestion\DTO\IngestionResult;
use App\Discovery\Jobs\EnrichDiscoveredWebsiteJob;
use App\Discovery\Normalization\DomainNormalizer;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Sources\Contracts\DiscoverySourceInterface;
use App\Discovery\Sources\DTO\DiscoveredWebsiteDTO;
use App\Models\DiscoveredWebsite;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * The missing link this module never had until now: every earlier
 * phase (D-I) built the search/filter/display/scoring/export machinery
 * around whatever was ALREADY in discovered_websites — nothing before
 * this class ever put real candidate data INTO that table.
 * DiscoverySourceInterface (Phase I3) always had a real implementation
 * to call (App\Discovery\Sources\InternalCrawlSource, and now
 * App\Discovery\Sources\GooglePlacesSource /
 * App\Discovery\Sources\YelpBusinessSource), but that interface
 * deliberately stops at "here are some candidate URLs"
 * (DiscoveredWebsiteDTO) — see its own docblock. This class is the
 * "future ingestion step" that docblock always pointed to.
 *
 * Two entry points:
 *   - discoverAndIngest(): the common case — given a search's own
 *     DiscoveryFilterCriteria and a list of active sources, calls
 *     discover() on each and ingests everything they return in one
 *     pass. One source throwing (a real API outage, not just "no key
 *     configured yet" — DiscoverySourceInterface implementations are
 *     expected to already collapse THAT to an empty Collection, per
 *     GooglePlacesSource's own docblock) is reported and skipped
 *     rather than aborting the other sources' own results.
 *   - ingest(): the lower-level primitive, for a caller that already
 *     has a Collection<DiscoveredWebsiteDTO> from somewhere (e.g. a
 *     test, or a future source that isn't itself a
 *     DiscoverySourceInterface).
 *
 * Deduplication reuses App\Discovery\Normalization\DomainNormalizer —
 * the exact same normalize-then-hash logic DiscoveredWebsite::booted()
 * already applies before storing url_hash (Phase I2) — via an
 * explicit existence check BEFORE inserting, so an already-known site
 * (by any of scheme/www/trailing-slash) a source re-discovers is
 * silently skipped rather than attempted as a duplicate row. That
 * pre-check is still inherently racy under concurrent ingestion runs
 * (two sources finding the same new site in the same discoverAndIngest()
 * call, or two ingestion runs overlapping) — the database's own unique
 * constraint on url_hash (Phase A1/I2) is the actual guarantee; a
 * UniqueConstraintViolationException from create() is caught and
 * counted as "already existing" too, rather than allowed to abort the
 * rest of the batch.
 *
 * Every newly created row immediately gets
 * App\Discovery\Jobs\EnrichDiscoveredWebsiteJob dispatched for it — a
 * freshly discovered site with no seo_score/technology/etc is barely
 * useful in this module's own search/filter UI (most filters are
 * score-range or technology based) until that job has run.
 */
final class DiscoveryIngestionService
{
    public function __construct(
        private readonly DomainNormalizer $domainNormalizer,
    ) {}

    /**
     * @param  array<int, DiscoverySourceInterface>  $sources
     */
    /**
     * PRODUCTION INCIDENT (Website Discovery per-user privacy) — read
     * before dropping $userId back to nothing: this app's own explicit
     * requirement changed Website Discovery from a shared lead-gen
     * pool (its ORIGINAL design, back when this app was single-tenant)
     * to fully per-user data — every website a search discovers now
     * belongs to whoever triggered that search
     * (App\Http\Controllers\DiscoveryController::discover() passes
     * auth()->id() for an ad-hoc "Discover More" click;
     * App\Discovery\Jobs\RunScheduledDiscoverySearchJob passes the
     * saved DiscoverySearch's own user_id for an automated scheduled
     * run), never shared with anyone else — see
     * App\Discovery\Search\WebsiteSearchService::applyOwnershipVisibility()
     * for how that ownership is actually enforced on every read.
     */
    public function discoverAndIngest(DiscoveryFilterCriteria $criteria, array $sources, ?int $userId = null): IngestionResult
    {
        $candidates = collect();

        foreach ($sources as $source) {
            try {
                $candidates = $candidates->merge($source->discover($criteria));
            } catch (Throwable $exception) {
                report($exception);
                // One source having a genuinely bad moment shouldn't stop
                // the others from still contributing their own results —
                // see this class's own docblock.
            }
        }

        return $this->ingest($candidates, $userId);
    }

    /**
     * @param  Collection<int, DiscoveredWebsiteDTO>  $candidates
     */
    public function ingest(Collection $candidates, ?int $userId = null): IngestionResult
    {
        $created = 0;
        $skippedExisting = 0;

        foreach ($candidates as $candidate) {
            $hash = $this->domainNormalizer->hash($candidate->url);

            $alreadyExists = DiscoveredWebsite::query()->where('url_hash', $hash)->exists();

            if ($alreadyExists) {
                $skippedExisting++;

                continue;
            }

            try {
                $website = DiscoveredWebsite::query()->create([
                    'uuid' => (string) Str::uuid(),
                    // PRODUCTION INCIDENT — see this class's own
                    // discoverAndIngest() docblock. Null only when
                    // this method is called with no real owner at all
                    // (there is no such call site in this app today,
                    // but the parameter itself stays optional rather
                    // than required, so a future caller that genuinely
                    // has no user to attribute to doesn't need to
                    // invent one).
                    'user_id' => $userId,
                    'domain' => $candidate->domain,
                    'url' => $candidate->url,
                    'industry' => $candidate->industry,
                    'sub_niche' => $candidate->subNiche,
                    'country' => $candidate->country,
                    'city' => $candidate->city,
                    'discovery_source' => $candidate->discoverySource,
                    'discovered_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException) {
                // The pre-check above raced with another insert of the
                // same normalized URL between the exists() check and
                // this create() call — the database's own unique
                // constraint on url_hash is what actually caught it;
                // this is the correct outcome (already known), not a
                // failure to surface.
                $skippedExisting++;

                continue;
            }

            EnrichDiscoveredWebsiteJob::dispatch($website);

            $created++;
        }

        return new IngestionResult(created: $created, skippedExisting: $skippedExisting);
    }
}