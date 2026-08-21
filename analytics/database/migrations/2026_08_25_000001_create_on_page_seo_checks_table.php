<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION GAP CLOSED — read before removing this table: Phase R1's
 * own original design ran On-Page SEO Checker fully synchronously
 * (fetch -> analyze -> render, nothing saved) since a single-page
 * check is fast enough for one request — correct reasoning for WHY it
 * doesn't need a queue/job the way technical_seo_scans does, but it
 * left this feature with no history at all, unlike every other
 * analysis tool in this app (Website Audit, Technical SEO Audit,
 * Competitor Analysis's own underlying cache, ...). This app's own
 * explicit requirement was that On-Page SEO Checker show a browsable
 * list of past checks the same way Technical SEO Audit already does.
 *
 * Still synchronous — this table's own row is created and immediately
 * fully populated in the SAME request that ran the check (see
 * App\Http\Controllers\OnPageSeoController::show()), never left in a
 * "queued"/"processing" state the way technical_seo_scans can be —
 * there is no status column here at all, on purpose, since one was
 * never needed for a check that always completes (or fails outright)
 * within a single request.
 *
 * user_id is nullable — unlike App\Models\TechnicalSeoScan/App\Models\KeywordList,
 * On-Page SEO Checker has no established per-user ownership requirement
 * from its own original Phase R1 prompt (it was designed with no
 * permission/ownership gating at all, matching every other page from
 * that era before Phase R-permissions closed that gap — see this
 * app's own permission migration for the fix to THAT specific gap).
 * Still recorded here when a real logged-in user ran the check (this
 * app's own routes require auth either way), so
 * App\Http\Controllers\OnPageSeoController::index() can show "your own
 * history" scoped the same way every other per-user page in this app
 * already does.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('on_page_seo_checks', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('url');
            $table->string('target_keyword')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->json('result');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('on_page_seo_checks');
    }
};