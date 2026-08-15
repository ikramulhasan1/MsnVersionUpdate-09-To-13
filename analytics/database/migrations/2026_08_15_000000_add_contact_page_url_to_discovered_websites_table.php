<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Discovery — Phase C6 (Contact Information Detection &
 * Availability filter).
 *
 * discovered_websites already has `email` and `phone` (see
 * 2026_08_14_000000_create_discovered_websites_table.php), but no
 * column for a detected "contact page" URL (e.g. /contact-us) — added
 * here rather than in that original migration since this need only
 * became concrete once App\Discovery\Enrichment\DiscoveryContactExtractor
 * (which detects it) and the "Contact Availability" filter (which
 * reads it) were being built.
 *
 * Placed right after `phone` to keep it grouped with the other contact
 * fields it was added alongside conceptually, and nullable like every
 * other enrichment column in this table: a site can be discovered
 * before this specific signal has been collected for it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->string('contact_page_url', 2048)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('discovered_websites', function (Blueprint $table): void {
            $table->dropColumn('contact_page_url');
        });
    }
};