<?php

declare(strict_types=1);

namespace App\Models;

use App\Discovery\Enums\BusinessSize;
use App\Discovery\Enums\WebsiteConnectivityStatus;
use App\Discovery\Enums\WebsiteType;
use App\Discovery\Normalization\DomainNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A single discovered site — the Website Discovery module's
 * counterpart to App\Models\Audit, following the same conventions:
 * uuid route-key binding, and a url_hash derived/normalized at
 * creation time for fast lookups (see database/migrations/
 * 2026_08_14_000000_create_discovered_websites_table.php's own
 * docblock for why url_hash is unique here, unlike Audit's own
 * indexed-but-not-unique url_hash).
 *
 * Phase I2: url_hash is computed via
 * App\Discovery\Normalization\DomainNormalizer, not a plain md5() of
 * the raw url — see that class's own docblock for exactly what it
 * normalizes (scheme, www, trailing slash) and why this table's
 * already-unique url_hash column needed that extra step to actually
 * prevent the same site being saved twice under superficially
 * different URLs.
 *
 * Every technographic/scoring/contact field is nullable at the
 * database level and populated incrementally by later phases'
 * enrichment/discovery logic — this model itself does not fetch,
 * score, or enrich anything; it is only the persisted shape of
 * whatever a future discovery/enrichment service writes into it.
 */
final class DiscoveredWebsite extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        // PRODUCTION INCIDENT — see this column's own migration
        // (database/migrations/2026_08_20_000002_add_user_id_to_discovered_websites_table.php)
        // for the full "why". Nullable — null for every row from a
        // REAL Discovery search/crawl (this column only ever matters
        // for a row created from a private audit).
        'user_id',
        'domain',
        'business_name',
        'url',
        'url_hash',
        'industry',
        'sub_niche',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
        'website_type',
        'business_size',
        'cms',
        'framework',
        'ecommerce_platform',
        'server',
        'cdn',
        'ssl_status',
        'connectivity_status',
        'domain_age_days',
        'last_updated_at',
        'seo_score',
        'seo_grade',
        'performance_score',
        'performance_grade',
        'security_score',
        'security_grade',
        'accessibility_score',
        'accessibility_grade',
        'mobile_score',
        'opportunity_score',
        'estimated_traffic_range',
        'email',
        'phone',
        'contact_page_url',
        'social_profiles',
        'discovery_source',
        'discovered_at',
    ];

    /**
     * url_hash is an internal lookup optimization, not part of any
     * public contract — hidden defensively in case this model is ever
     * serialized directly, the same reasoning Audit::$hidden documents
     * for its own url_hash.
     */
    protected $hidden = [
        'url_hash',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'website_type' => WebsiteType::class,
        'business_size' => BusinessSize::class,
        'connectivity_status' => WebsiteConnectivityStatus::class,
        'domain_age_days' => 'integer',
        'last_updated_at' => 'datetime',
        'seo_score' => 'integer',
        'performance_score' => 'integer',
        'security_score' => 'integer',
        'accessibility_score' => 'integer',
        'mobile_score' => 'integer',
        'opportunity_score' => 'integer',
        'social_profiles' => 'array',
        'discovered_at' => 'datetime',
    ];

    /**
     * Use the UUID for route model binding instead of the numeric id,
     * mirroring Audit::getRouteKeyName() — so discovered-site URLs
     * never leak internal database ids.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function watchlistItem(): HasOne
    {
        return $this->hasOne(DiscoveryWatchlistItem::class);
    }

    /**
     * PRODUCTION INCIDENT — see this column's own migration docblock.
     * Null for every row from a real Discovery search/crawl; only ever
     * meaningfully set for a row created from a private audit.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Always (re)computes url_hash from the final url attribute at
     * creation time, mirroring Audit::booted() exactly — this covers
     * every creation path (factory, a future repository, or a direct
     * ::create()/save()) rather than relying on each caller to compute
     * it correctly themselves.
     *
     * Phase I2: hashes DomainNormalizer::normalize($website->url)
     * rather than the raw url — see that class's own docblock for
     * exactly why this table's already-unique url_hash column (set
     * ->unique() since Phase A1) needed one more step Audit's own
     * url_hash never did, to actually stop
     * "http://example.com"/"https://www.example.com/" from being
     * saved as two "different" sites.
     *
     * Unlike Audit::booted(), this does NOT auto-generate `uuid` —
     * Audit's own uuid is likewise never auto-generated by the model
     * (see AuditService::create(), which sets it explicitly via
     * Str::uuid() before calling the repository); a future Discovery
     * service is expected to do the same for consistency, rather than
     * this model silently taking over that responsibility.
     */
    protected static function booted(): void
    {
        self::creating(function (self $website): void {
            $website->url_hash = (new DomainNormalizer)->hash($website->url);
        });
    }
}