<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\KeywordData\Exceptions\NoAvailableProviderException;
use App\KeywordData\KeywordDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase O3 (Keyword Research page) — the first real, user-facing
 * consumer of Phase O2's own KeywordDataService. Every metric on this
 * page is fetched independently and wrapped in its own try/catch —
 * see result()'s own docblock for why a failure in ONE section (say,
 * no provider configured for Keyword Difficulty) must never blank out
 * the whole page when other sections (say, Search Volume) succeeded.
 */
final class KeywordResearchController extends Controller
{
    public function index(): View
    {
        return view('keyword-research.index', ['result' => null, 'keyword' => null]);
    }

    public function show(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:100'],
        ]);

        $keyword = trim($validated['keyword']);
        $country = $validated['country'];
        $language = $validated['language'];

        return view('keyword-research.index', [
            'keyword' => $keyword,
            'country' => $country,
            'language' => $language,
            'result' => $this->gatherResult($keyword, $country, $language),
        ]);
    }

    /**
     * PRODUCTION-INTENDED PARTIAL FAILURE — read before making this
     * method throw/abort on the first failure it hits: this app's own
     * explicit requirement was that missing data shows "a clear,
     * understandable error message, never a blank page" — for ONE
     * section, not the whole page. An Admin might genuinely have
     * DataForSEO Labs configured but not Google Ads (or vice versa),
     * meaning SOME capabilities work and others don't — every metric
     * below is fetched in its OWN try/catch specifically so a working
     * capability still renders normally even when a different one has
     * no active provider. Each result key is either the real data or
     * null; resources/views/keyword-research/index.blade.php's own
     * @if ($result['xyz'] !== null) checks are what turn a null into
     * an honest "temporarily unavailable" message per section.
     *
     * @return array<string, mixed>
     */
    private function gatherResult(string $keyword, string $country, string $language): array
    {
        $service = app(KeywordDataService::class);

        $volume = $this->attempt(fn () => $service->getSearchVolume([$keyword], $country, $language)[$keyword] ?? null);
        $cpc = $this->attempt(fn () => $service->getCpc([$keyword], $country, $language)[$keyword] ?? null);
        $difficulty = $this->attempt(fn () => $service->getKeywordDifficulty([$keyword], $country, $language)[$keyword] ?? null);
        $competitiveDensity = $this->attempt(fn () => $service->getCompetitiveDensity([$keyword], $country, $language)[$keyword] ?? null);
        $intent = $this->attempt(fn () => $service->getSearchIntent([$keyword], $country, $language)[$keyword] ?? null);
        $trend = $this->attempt(fn () => $service->getSearchVolumeTrend($keyword, $country, $language));
        $serp = $this->attempt(fn () => $service->getSerpData($keyword, $country, $language));
        $related = $this->attempt(fn () => $service->getRelatedKeywords($keyword, $country, $language, limit: 8));

        return [
            'volume' => $volume,
            'cpc' => $cpc,
            'difficulty' => $difficulty,
            'competitive_density' => $competitiveDensity,
            'intent' => $intent,
            'trend' => $trend,
            'serp_features' => $serp['features'] ?? null,
            'top_results' => $serp['top_results'] ?? null,
            'questions' => $serp['questions'] ?? null,
            'related_keywords' => $related,
            // True only when EVERY section failed — the view uses this
            // to show one single "nothing could be loaded, check your
            // API Providers setup" message instead of eight separate
            // identical per-section ones, which would be repetitive
            // when the real cause (no provider configured at all) is
            // the same for all of them.
            'all_failed' => $volume === null && $cpc === null && $difficulty === null
                && $competitiveDensity === null && $intent === null && $trend === null
                && $serp === null && $related === null,
        ];
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return ?T null on ANY failure (NoAvailableProviderException — no
     *         active provider — or any other real error the underlying
     *         adapter threw, already report()ed by
     *         KeywordDataService::tryProvidersInOrder() before it ever
     *         reaches here)
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