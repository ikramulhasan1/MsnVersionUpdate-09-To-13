<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase O1 (API Provider Management System) — the foundation Phase
 * O2's own KeywordDataService reads from to decide which real API
 * (DataForSEO Keywords Data, DataForSEO Labs, Google Ads) actually
 * handles a given request, and Admin\ApiProviderController's own CRUD
 * pages manage directly.
 *
 * `credentials` is a single JSON column, ENCRYPTED as a whole blob
 * (see App\Models\ApiProvider's own `encrypted:array` cast) — never
 * plain text in the database. Its own internal shape differs by
 * `type` (see App\Enums\ApiProviderType's own docblock for exactly
 * which keys each type expects): DataForSEO's own two products share
 * one simple {login, password} shape (HTTP Basic Auth, the same
 * account credentials work for both), Google Ads needs a full OAuth2
 * credential set instead.
 *
 * `capabilities` is a JSON array of capability-name strings this
 * PARTICULAR provider row can answer (a subset of App\Enums\KeywordCapability's
 * own cases) — not every provider offers every capability (Google Ads
 * has no keyword-difficulty capability at all, for instance), so this
 * is how App\KeywordData\KeywordDataService (Phase O2) knows which
 * rows are even eligible candidates for a given request.
 *
 * `priority` breaks ties when MORE THAN ONE active provider offers the
 * SAME capability — lower number tries first. Two providers offering
 * the identical capability is a deliberate, supported case (e.g.
 * wanting DataForSEO Keywords Data to be tried before Google Ads for
 * search volume, with automatic fallback to the second if the first
 * fails) — see Phase O2's own KeywordDataService docblock for exactly
 * how that fallback works.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_providers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('credentials')->nullable();
            $table->json('capabilities');
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamp('last_tested_at')->nullable();
            $table->boolean('last_test_succeeded')->nullable();
            $table->text('last_test_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_providers');
    }
};