<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase D3 (result card).
 *
 * No prior migration added a column for a discovered site's own
 * business/company name — only `domain` and `url` (see
 * 2026_08_14_000000_create_discovered_websites_table.php) — but the
 * result card design explicitly calls for a business name distinct
 * from the raw domain (e.g. "Acme Dental Care" rather than
 * "acmedentalcare.com"). Nullable, like every other enrichment column
 * in this table: no current job populates it yet (a future enrichment
 * step — e.g. reading a site's <title>, schema.org Organization
 * markup, or an og:site_name tag — is the natural place to fill it
 * in), so partials/result-card.blade.php falls back to $website->domain
 * whenever this is null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->string('business_name')->nullable()->after('domain');
        });
    }

    public function down(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->dropColumn('business_name');
        });
    }
};