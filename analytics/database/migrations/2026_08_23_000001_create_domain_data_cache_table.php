<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase Q1 (Domain Data Service Layer) — the domain-data counterpart
 * to keyword_data_cache (Phase O2). App\DomainData\DomainDataService's
 * own cache, checked BEFORE ever calling a real provider adapter — a
 * cache hit costs nothing and makes no external HTTP call at all.
 *
 * No 'language' column, unlike keyword_data_cache — domain-level
 * metrics (traffic, backlinks, rankings) are looked up per country
 * only; DataForSEO/Majestic/Moz's own domain endpoints don't take a
 * separate language parameter the way keyword lookups do.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_data_cache', function (Blueprint $table): void {
            $table->id();
            $table->string('domain');
            $table->string('country');
            $table->string('capability');
            $table->json('response');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['domain', 'country', 'capability'], 'domain_data_cache_lookup_unique');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_data_cache');
    }
};