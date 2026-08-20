<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DomainData\DomainDataService;
use App\DomainData\Exceptions\NoAvailableProviderException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase Q3 (Backlink Analysis page) — same "every section fetched
 * independently, one section's failure never blanks out the whole
 * page" pattern App\Http\Controllers\CompetitorAnalysisController
 * (Phase Q2) and App\Http\Controllers\KeywordResearchController (Phase
 * O3) already established. The backlinks LIST is fetched once, at a
 * bounded limit (see RESULT_LIMIT below), and every filter/sort/page
 * interaction on it then happens entirely client-side — the same
 * cost-conscious design
 * App\Http\Controllers\KeywordMagicToolController's own class docblock
 * explains for Phase O4's bulk keyword table, applied here identically
 * so re-sorting or paging through backlinks never triggers a second
 * paid API call.
 */
final class BacklinkAnalysisController extends Controller
{
    private const int BACKLINKS_LIMIT = 200;
    private const int REFERRING_DOMAINS_LIMIT = 100;
    private const int ANCHOR_TEXT_LIMIT = 50;

    public function index(): View
    {
        return view('backlink-analysis.index', ['result' => null, 'domain' => null]);
    }

    public function show(Request $request): View
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
        ]);

        // Same URL-to-bare-domain normalization
        // App\Http\Controllers\CompetitorAnalysisController::normalizeDomain()
        // already applies, for the identical reason — this page's own
        // requirement explicitly allows "domain বা নির্দিষ্ট URL" as
        // input, so a full URL needs to reduce to a bare domain before
        // reaching DomainDataService, which only ever deals in domains.
        $domain = $this->normalizeDomain(trim($validated['domain']));

        return view('backlink-analysis.index', [
            'domain' => $domain,
            'result' => $this->gatherResult($domain),
        ]);
    }

    private function normalizeDomain(string $input): string
    {
        $host = parse_url($input, PHP_URL_HOST);
        $domain = is_string($host) && $host !== '' ? $host : $input;

        return preg_replace('/^www\./i', '', $domain);
    }

    /**
     * @return array<string, mixed>
     */
    private function gatherResult(string $domain): array
    {
        $service = app(DomainDataService::class);

        $summary = $this->attempt(fn () => $service->getBacklinksSummary($domain));
        $backlinks = $this->attempt(fn () => $service->getBacklinksList($domain, limit: self::BACKLINKS_LIMIT));
        $referringDomains = $this->attempt(fn () => $service->getReferringDomains($domain, limit: self::REFERRING_DOMAINS_LIMIT));
        $anchorText = $this->attempt(fn () => $service->getAnchorTextDistribution($domain, limit: self::ANCHOR_TEXT_LIMIT));

        return [
            'summary' => $summary,
            'backlinks' => $backlinks,
            'referring_domains' => $referringDomains,
            'anchor_text' => $anchorText,
            'all_failed' => $summary === null && $backlinks === null
                && $referringDomains === null && $anchorText === null,
        ];
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return ?T
     */
    private function attempt(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (NoAvailableProviderException|\Throwable $exception) {
            if (! $exception instanceof NoAvailableProviderException) {
                report($exception);
            }

            return null;
        }
    }
}