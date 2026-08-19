<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discovery_watchlist', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('discovery_watchlist', function (Blueprint $table): void {
            $table->unique(['discovered_website_id', 'user_id'], 'discovery_watchlist_website_user_unique');
        });

        Schema::table('discovery_watchlist', function (Blueprint $table): void {
            $table->dropUnique('discovery_watchlist_discovered_website_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('discovery_watchlist', function (Blueprint $table): void {
            $table->unique('discovered_website_id');
        });

        Schema::table('discovery_watchlist', function (Blueprint $table): void {
            $table->dropUnique('discovery_watchlist_website_user_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
