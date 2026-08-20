<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\KeywordData\Exceptions\NoAvailableProviderException;
use App\KeywordData\KeywordDataCacheRepository;
use App\KeywordData\KeywordDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase O4 (Keyword Magic Tool page) — COST-CONSCIOUS DESIGN, read
 * before "improving" this to fetch more per-keyword data live: a
 * real Keyword Magic Tool page can show hundreds of keyword rows at
 * once, and this app's own KeywordDataService bills real money per
 * API call (see App\Models\ApiUsageLog's own migration docblock).
 * Fetching per-keyword trend/SERP data for every row on every
 * submission would multiply cost by the row count on EVERY search —
 * unacceptable. This controller makes exactly TWO real API calls per
 * submission (getRelatedKeywords() once, getSearchIntent() once, both
 * already batch-shaped for many keywords at once) and OPPORTUNISTICALLY
 * reads trend/SERP data ONLY from cache (App\KeywordData\KeywordDataCacheRepository::get(),
 * which NEVER triggers a fresh fetch on a miss) — a keyword someone
 * already looked up individually on the Keyword Research page (Phase
 * O3) shows its real trend/SERP data here for free; one that hasn't
 * been looked up yet simply shows nothing for those two columns,
 * rather than this page silently spending money to fill them in.
 *
 * Every OTHER interaction on this page — filtering by volume/KD/CPC,
 * switching Match Type tabs, sorting, paging, exporting selected rows
 * to CSV — happens entirely in the browser
 * (resources/views/keyword-magic-tool/index.blade.php's own JS) against
 * the ONE payload this controller returns; none of those interactions
 * ever hits this controller or any API again.
 */
final class KeywordMagicToolController extends Controller
{
    private const int RESULT_LIMIT = 200;

    public function index(): View
    {
        return view('keyword-magic-tool.index', ['result' => null, 'seed' => null]);
    }

    public function show(Request $request): View
    {
        $validated = $request->validate([
            'seed' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:100'],
        ]);

        $seed = trim($validated['seed']);
        $country = $validated['country'];
        $language = $validated['language'];

        return view('keyword-magic-tool.index', [
            'seed' => $seed,
            'country' => $country,
            'language' => $language,
            'result' => $this->gatherResult($seed, $country, $language),
        ]);
    }

    /**
     * @return array{keywords: array<int, array<string, mixed>>, error: ?string}
     */
    private function gatherResult(string $seed, string $country, string $language): array
    {
        $service = app(KeywordDataService::class);
        $cache = app(KeywordDataCacheRepository::class);

        try {
            $related = $service->getRelatedKeywords($seed, $country, $language, limit: self::RESULT_LIMIT);
        } catch (NoAvailableProviderException $exception) {
            return ['keywords' => [], 'error' => $exception->getMessage()];
        }

        if ($related === []) {
            return ['keywords' => [], 'error' => null];
        }

        $keywordStrings = array_column($related, 'keyword');

        // ONE bulk call for intent, covering every returned keyword at
        // once — see this class's own docblock for why this is the
        // one extra live call this page makes beyond the related-
        // keywords fetch itself.
        $intents = [];

        try {
            $intents = $service->getSearchIntent($keywordStrings, $country, $language);
        } catch (NoAvailableProviderException) {
            // Intent simply stays unavailable for every row — not
            // fatal to the rest of the page, same "degrade this one
            // piece, not the whole page" principle Phase O3 already
            // established.
        }

        $rows = array_map(function (array $item) use ($cache, $country, $language, $intents): array {
            $keyword = $item['keyword'];
            $cachedSerpData = $cache->get($keyword, $country, $language, 'serp_data');

            return [
                'keyword' => $keyword,
                'volume' => $item['volume'],
                'cpc' => $item['cpc'],
                'difficulty' => $item['difficulty'],
                'intent' => $intents[$keyword] ?? null,
                // Cache-only peeks — see this controller's own class
                // docblock for why these NEVER trigger a fresh API
                // call here.
                'trend' => $cache->get($keyword, $country, $language, 'volume_trend'),
                'serp_features' => is_array($cachedSerpData) ? ($cachedSerpData['features'] ?? null) : null,
                'word_count' => str_word_count($keyword),
            ];
        }, $related);

        return ['keywords' => $rows, 'error' => null];
    }
}