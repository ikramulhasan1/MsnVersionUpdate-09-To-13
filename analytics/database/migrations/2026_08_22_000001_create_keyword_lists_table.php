<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase O5 (Keyword List/Project Management) — a named collection of
 * keywords, owned by exactly one user (user_id is NOT nullable here,
 * unlike App\Models\DiscoveredWebsite's own — every KeywordList is
 * created by a real, logged-in person taking an explicit action;
 * there is no equivalent "legacy row from before ownership existed"
 * case the way Discovery had). See
 * App\Http\Controllers\KeywordListController's own docblock for how
 * ownership is enforced on every single action, not just relied on as
 * a query default — the SAME "an Admin sees only their own, never
 * another real user's" principle
 * App\Discovery\Search\WebsiteSearchService::applyOwnershipVisibility()
 * already established applies here identically, including for an
 * Admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_lists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_lists');
    }
};