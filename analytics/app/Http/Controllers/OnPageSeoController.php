<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface;
use App\KeywordData\Exceptions\NoAvailableProviderException;
use App\KeywordData\KeywordDataService;
use App\OnPageSeo\OnPageSeoAnalyzer;
use App\OnPageSeo\DTO\OnPageSeoResult;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Response;

/**
 * Phase R1 (On-Page SEO Checker) — orchestrates three EXISTING pieces
 * of this app into one page, writing no new fetching/parsing/AI logic
 * of its own:
 *   1. App\Audit\Fetching\Contracts\WebsiteFetcherServiceInterface (the
 *      Audit module's own fetch/parse engine) — fetches and parses the
 *      one URL.
 *   2. App\OnPageSeo\OnPageSeoAnalyzer (Phase R1's own new analysis
 *      logic) — turns that parse result into on-page findings.
 *   3. App\KeywordData\KeywordDataService (Phase O2) — OPTIONAL, only
 *      called when the person provided a target keyword; see
 *      show()'s own try/catch for why a missing/failed keyword
 *      provider degrades gracefully rather than failing the whole
 *      page (the same principle
 *      App\Http\Controllers\KeywordResearchController's own docblock
 *      already established).
 *   4. App\Audit\AIRecommendation\AIRecommendationEngine (existing,
 *      unmodified) — produces the Priority Fix List, fed a minimal
 *      AnalysisResults with ONLY ->seo populated (see
 *      App\OnPageSeo\OnPageSeoAnalyzer::toSeoAuditResult()'s own
 *      docblock for exactly how that bridge works).
 */
final class OnPageSeoController extends Controller
{
    public function index(): View
    {
        return view('on-page-seo.index', ['result' => null, 'url' => null, 'aiResult' => null]);
    }

    public function show(
        Request $request,
        WebsiteFetcherServiceInterface $fetcher,
        OnPageSeoAnalyzer $analyzer,
        AIRecommendationEngine $recommendationEngine,
    ): View {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048', 'starts_with:http://,https://'],
            'target_keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $url = rtrim($validated['url'], '/');
        $targetKeyword = ! empty($validated['target_keyword']) ? trim($validated['target_keyword']) : null;

        $fetchResult = $fetcher->fetch($url);

        if (! $fetchResult->success) {
            return view('on-page-seo.index', [
                'result' => null,
                'url' => $url,
                'aiResult' => null,
                'fetchError' => $fetchResult->errors[0] ?? 'Could not fetch this URL.',
            ]);
        }

        $result = $analyzer->analyze($fetchResult, $targetKeyword);

        // Phase R1's own explicit "unique differentiator" — see
        // App\OnPageSeo\OnPageSeoAnalyzer::analyzeKeywordOptimization()'s
        // own docblock for why volume/difficulty are fetched HERE
        // (this controller), not inside the analyzer itself.
        $keywordMetrics = null;

        if ($targetKeyword !== null) {
            try {
                $service = app(KeywordDataService::class);
                $volumeData = $service->getSearchVolume([$targetKeyword], 'United States', 'English');
                $difficultyData = $service->getKeywordDifficulty([$targetKeyword], 'United States', 'English');

                $keywordMetrics = [
                    'volume' => $volumeData[$targetKeyword] ?? null,
                    'difficulty' => $difficultyData[$targetKeyword] ?? null,
                ];
            } catch (NoAvailableProviderException) {
                // Left null — resources/views/on-page-seo/index.blade.php's
                // own keyword section shows "temporarily unavailable"
                // for JUST the volume/difficulty figures; the
                // placement-based Keyword Optimization Score itself
                // (computed entirely locally, no API needed) still
                // displays normally regardless.
            }
        }

        // See App\OnPageSeo\OnPageSeoAnalyzer::toSeoAuditResult()'s own
        // docblock — this is the ENTIRE bridge into the existing,
        // unmodified AIRecommendationEngine; every other AnalysisResults
        // field stays null on purpose.
        $seoAuditResult = $analyzer->toSeoAuditResult($result);
        $analysisResults = new AnalysisResults(url: $url, seo: $seoAuditResult);
        $aiResult = $recommendationEngine->analyze($analysisResults);

        return view('on-page-seo.index', [
            'result' => $result,
            'url' => $url,
            'targetKeyword' => $targetKeyword,
            'keywordMetrics' => $keywordMetrics,
            'aiResult' => $aiResult,
            'fetchError' => null,
        ]);
    }

    /**
     * PDF export — a dedicated, lightweight template
     * (resources/views/on-page-seo/pdf.blade.php), not this app's own
     * existing full-Audit PDF system
     * (App\Audit\Export\Pdf\AuditPdfExportService), which expects the
     * FULL multi-category Audit shape (Security, Accessibility,
     * Performance, ...) this standalone page never has. Uses the SAME
     * underlying barryvdh/laravel-dompdf package that existing system
     * already depends on — no new PDF library added to this app.
     */
    public function exportPdf(
        Request $request,
        WebsiteFetcherServiceInterface $fetcher,
        OnPageSeoAnalyzer $analyzer,
        AIRecommendationEngine $recommendationEngine,
        \Barryvdh\DomPDF\PDF $pdf,
    ): \Symfony\Component\HttpFoundation\Response {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'target_keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $url = rtrim($validated['url'], '/');
        $targetKeyword = ! empty($validated['target_keyword']) ? trim($validated['target_keyword']) : null;

        $fetchResult = $fetcher->fetch($url);
        abort_if(! $fetchResult->success, 422, 'Could not fetch this URL to export.');

        $result = $analyzer->analyze($fetchResult, $targetKeyword);
        $seoAuditResult = $analyzer->toSeoAuditResult($result);
        $aiResult = $recommendationEngine->analyze(new AnalysisResults(url: $url, seo: $seoAuditResult));

        $pdf->loadView('on-page-seo.pdf', ['result' => $result, 'aiResult' => $aiResult, 'url' => $url]);

        $filename = 'on-page-seo-'.preg_replace('/[^a-z0-9]+/i', '-', parse_url($url, PHP_URL_HOST) ?? 'report').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * CSV export of the Priority Fix List — the one part of this
     * page's own output that's genuinely tabular/exportable; the rest
     * (title/heading/image analysis etc.) reads better as the PDF
     * above than as spreadsheet rows.
     */
    public function exportCsv(
        Request $request,
        WebsiteFetcherServiceInterface $fetcher,
        OnPageSeoAnalyzer $analyzer,
    ): \Symfony\Component\HttpFoundation\StreamedResponse {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'target_keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $url = rtrim($validated['url'], '/');
        $targetKeyword = ! empty($validated['target_keyword']) ? trim($validated['target_keyword']) : null;

        $fetchResult = $fetcher->fetch($url);
        abort_if(! $fetchResult->success, 422, 'Could not fetch this URL to export.');

        $result = $analyzer->analyze($fetchResult, $targetKeyword);

        return Response::streamDownload(function () use ($result): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Check', 'Severity', 'Message', 'Recommendation']);

            foreach ($result->issues as $issue) {
                fputcsv($handle, [$issue->check, $issue->severity, $issue->message, $issue->recommendation]);
            }

            fclose($handle);
        }, 'on-page-seo-issues.csv');
    }
}