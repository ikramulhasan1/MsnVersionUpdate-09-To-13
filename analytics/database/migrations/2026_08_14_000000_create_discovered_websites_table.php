<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase A1 (foundation).
 *
 * One row per discovered site: the firmographic/technographic/scoring
 * data a lead-gen search-and-filter workflow needs (Industry/Niche
 * search; Location, Technology, and score-range filters; audit/export/
 * lead-scoring afterward — see the Website Discovery module's own
 * planning notes).
 *
 * Mirrors audits' conventions (see create_audits_table /
 * add_url_hash_to_audits_table):
 *   - `uuid`, unique, is this table's public/route-bindable identity —
 *     the numeric `id` never leaks into a URL.
 *   - `url_hash` is a fixed-length (char(32)) md5() digest of `url`,
 *     the same normalize-for-lookup strategy AuditRepository/
 *     AuditCacheService already use, since indexing the full `url`
 *     column directly is impractical at this length (MySQL/InnoDB caps
 *     index key length well below 2048 bytes for utf8mb4). Unlike
 *     audits.url_hash (indexed but not unique, since the same URL can
 *     legitimately be audited more than once), this one IS unique:
 *     discovered_websites has exactly one row per discovered site, so
 *     a repeat discovery of the same URL should update the existing
 *     row, not create a duplicate.
 *
 *     Phase I2 note: at the time this migration was written, `url_hash`
 *     really was a plain md5(url) digest — App\Discovery\Normalization\DomainNormalizer
 *     (added later, once duplicate near-identical URLs turned out to
 *     slip past this same ->unique() constraint) now normalizes scheme/
 *     www/trailing-slash before DiscoveredWebsite::booted() hashes it,
 *     so this column's own type/uniqueness here is unchanged, only what
 *     App\Models\DiscoveredWebsite feeds into md5() before storing it.
 *
 * Every technographic/scoring/contact column is nullable: a site can
 * be discovered (and therefore searchable/filterable on whatever is
 * already known) well before every enrichment signal has been
 * collected for it — this table is filled in incrementally, never
 * required to be complete before a row can exist.
 *
 * Indexes are placed on the columns the module's own filter set calls
 * out by name (Industry/Niche, Location, Technology, and every score),
 * plus a composite (industry, country) index for the single most
 * common combination (an industry search narrowed by location).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovered_websites', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            // Identity ---------------------------------------------------
            $table->string('domain')->index();
            $table->string('url', 2048);
            $table->char('url_hash', 32)->unique();

            // Industry / Niche --------------------------------------------
            $table->string('industry', 100)->nullable()->index();
            $table->string('sub_niche', 150)->nullable()->index();

            // Location — country is left a free-form string (rather than
            // forced to an ISO-3166 alpha-2 code) since the eventual
            // discovery data source(s) for this module aren't finalized
            // yet; whichever source is wired up in a later phase can
            // normalize the value it writes here without a schema change.
            $table->string('country', 100)->nullable()->index();
            $table->string('region', 150)->nullable();
            $table->string('city', 150)->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Firmographics -------------------------------------------------
            $table->string('website_type', 50)->nullable()->index();
            $table->string('business_size', 50)->nullable()->index();

            // Technology ---------------------------------------------------
            $table->string('cms', 100)->nullable()->index();
            $table->string('framework', 100)->nullable()->index();
            $table->string('ecommerce_platform', 100)->nullable()->index();
            $table->string('server', 100)->nullable();
            $table->string('cdn', 100)->nullable();
            $table->string('ssl_status', 30)->nullable()->index();
            $table->unsignedInteger('domain_age_days')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            // Scores — each 0-100, the same scale every Audit analyzer
            // already scores on, so a discovered site's numbers read
            // consistently against an audit run for that same site.
            $table->unsignedTinyInteger('seo_score')->nullable()->index();
            $table->unsignedTinyInteger('performance_score')->nullable()->index();
            $table->unsignedTinyInteger('security_score')->nullable()->index();
            $table->unsignedTinyInteger('accessibility_score')->nullable()->index();
            $table->unsignedTinyInteger('mobile_score')->nullable()->index();
            $table->unsignedTinyInteger('opportunity_score')->nullable()->index();
            $table->string('estimated_traffic_range', 50)->nullable();

            // Contact / Lead intelligence ------------------------------------
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->json('social_profiles')->nullable();

            // Provenance -----------------------------------------------------
            $table->string('discovery_source', 100)->nullable()->index();
            $table->timestamp('discovered_at')->nullable();

            $table->timestamps();

            $table->index(['industry', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovered_websites');
    }
};