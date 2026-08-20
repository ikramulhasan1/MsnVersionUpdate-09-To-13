<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase O2 (Keyword Data Service Layer) — App\KeywordData\KeywordDataService's
 * own cache, checked BEFORE ever calling a real provider adapter. A
 * cache hit costs nothing and makes no external HTTP call at all; only
 * a genuine miss reaches App\Models\ApiUsageLog (this table's own
 * sibling migration).
 *
 * The composite unique key (keyword, country, language, capability) is
 * deliberately NOT the same shape as the Keyword Magic Tool's own
 * "seed keyword -> many related keywords" request — this table caches
 * per-INDIVIDUAL-keyword facts (one keyword's own volume, one
 * keyword's own difficulty, ...), not a whole related-keyword result
 * SET. getRelatedKeywords()/getSerpData() results (Phase O2's own
 * KeywordDataService) are cached under 'related_keywords'/'serp_data'
 * as their own capability value with the SEED keyword as $keyword —
 * still one row per (seed, country, language, capability) — response
 * stored as JSON either way, this table's own shape doesn't need to
 * change to support that.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_data_cache', function (Blueprint $table): void {
            $table->id();
            $table->string('keyword');
            $table->string('country');
            $table->string('language');
            $table->string('capability');
            $table->json('response');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['keyword', 'country', 'language', 'capability'], 'keyword_data_cache_lookup_unique');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_data_cache');
    }
};