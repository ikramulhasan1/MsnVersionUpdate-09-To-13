<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DomainData\DomainDataService;
use App\DomainData\Exceptions\NoAvailableProviderException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase Q2 (Competitor Analysis page) — the first user-facing consumer
 * of Phase Q1's own DomainDataService, following the SAME
 * "every section fetched independently, one section's failure never
 * blanks out the whole page" pattern
 * App\Http\Controllers\KeywordResearchController already established
 * for Phase O3 — see that class's own docblock for the full
 * reasoning, identical here: an Admin might have DataForSEO Labs
 * active (covering domain_overview/organic_competitors/
 * ranking_keywords/top_pages) but no backlink provider configured at
 * all, or vice versa, and every section on this page needs to degrade
 * independently for that to work correctly.
 */
final class CompetitorAnalysisController extends Controller
{
    public function index(): View
    {
        return view('competitor-analysis.index', ['result' => null, 'domain' => null]);
    }

    public function show(Request $request): View
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
        ]);

        // PRODUCTION-INTENDED NORMALIZATION — this page's own explicit
        // requirement is "একটা domain (URL না, শুধু domain যেমন
        // example.com)", but people paste full URLs into forms like
        // this constantly regardless of the label. Strips a leading
        // scheme/www/trailing path rather than rejecting the input
        // outright with a validation error over something this easy
        // to just handle.
        $domain = $this->normalizeDomain(trim($validated['domain']));
        $country = $validated['country'];

        return view('competitor-analysis.index', [
            'domain' => $domain,
            'country' => $country,
            'result' => $this->gatherResult($domain, $country),
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
    private function gatherResult(string $domain, string $country): array
    {
        $service = app(DomainDataService::class);

        $overview = $this->attempt(fn () => $service->getDomainOverview($domain, $country));
        $competitors = $this->attempt(fn () => $service->getOrganicCompetitors($domain, $country, limit: 15));
        $rankingKeywords = $this->attempt(fn () => $service->getRankingKeywords($domain, $country, limit: 30));
        $topPages = $this->attempt(fn () => $service->getTopPages($domain, $country, limit: 15));

        return [
            'overview' => $overview,
            'competitors' => $competitors,
            'ranking_keywords' => $rankingKeywords,
            'top_pages' => $topPages,
            // Same "everything failed, show one message" shortcut
            // Phase O3's own gatherResult() already uses.
            'all_failed' => $overview === null && $competitors === null
                && $rankingKeywords === null && $topPages === null,
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