<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase O5 (Keyword List/Project Management) — one row per keyword
 * saved into a keyword_lists row. volume/difficulty/cpc are a SNAPSHOT
 * taken at the moment the keyword was added (from whatever Phase O3's
 * Keyword Research page or Phase O4's Keyword Magic Tool already had
 * on screen) — never re-fetched live from
 * App\KeywordData\KeywordDataService on save, so adding a keyword to a
 * list costs nothing extra in API calls. These numbers can go stale
 * over time exactly the way a saved note can; this app makes no
 * attempt to silently refresh them, and
 * resources/views/keyword-lists/show.blade.php's own docblock says so
 * plainly rather than implying they're always current.
 *
 * The unique constraint on (keyword_list_id, keyword) means adding the
 * SAME keyword to a list it's already in updates that existing row's
 * own snapshot rather than creating a duplicate entry — see
 * App\Http\Controllers\KeywordListController::addItem()'s own
 * updateOrCreate() call.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_list_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('keyword_list_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->unsignedInteger('volume')->nullable();
            $table->unsignedInteger('difficulty')->nullable();
            $table->decimal('cpc', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['keyword_list_id', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_list_items');
    }
};