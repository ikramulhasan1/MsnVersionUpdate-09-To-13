<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\DTO\CreateAuditData;
use App\Audit\Enums\AuditStatus;
use App\Audit\Export\AuditReportExport;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Export\Support\AnalysisResultsToDashboardCategories;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Http\Requests\StoreAuditRequest;
use App\Models\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\Response;

final class AuditController extends Controller
{
    public function __construct(
        private readonly AuditServiceInterface $auditService,
        private readonly Excel $excel,
        private readonly AuditCacheServiceInterface $cache,
        private readonly AnalysisResultsToDashboardCategories $dashboardCategoryMapper,
    ) {}

    public function index(): View
    {
        return view('home.index');
    }

    public function store(StoreAuditRequest $request): RedirectResponse
    {
        $audit = $this->auditService->submit(
            CreateAuditData::fromArray($request->validated())
        );

        $response = redirect()
            ->route('audits.show', $audit->uuid)
            ->with('status', 'Your audit has been queued.');

        // No queue worker in this deployment: the audit pipeline (fetch,
        // crawl, analyze, assemble) runs directly in this same PHP
        // process/request rather than being picked up later by a
        // separate `queue:work` process. fastcgi_finish_request() (only
        // available under PHP-FPM) flushes this redirect to the browser
        // and closes its connection *before* the line below runs, so
        // the browser lands on the result page immediately and its
        // progress bar (GET /audits/{audit}/progress, polled by
        // public/js/audit-progress.js) has something to show while this
        // same process keeps working in the background.
        //
        // Where that function isn't available (e.g. `php artisan serve`,
        // or Octane's request lifecycle), there's no way to send the
        // response early — this falls back to finishing the whole audit
        // before the redirect goes out, same as it would without this
        // comment's optimization at all.
        if (function_exists('fastcgi_finish_request')) {
            $response->prepare($request);
            $response->send();
            fastcgi_finish_request();
        }

        // wasRecentlyCreated is false when submit() returned an
        // already-in-flight audit instead of creating a new one (see
        // AuditService::submit()'s duplicate-URL check) — running the
        // pipeline again for that case would start a second, redundant
        // pass over an audit that's already being processed.
        if ($audit->wasRecentlyCreated) {
            $this->auditService->run($audit);
        }

        return $response;
    }

    public function show(Audit $audit): View
    {
        $categories = [];
        $prospectQualification = null;
        $outreachDraft = null;

        if ($audit->status === AuditStatus::COMPLETED) {
            $results = $this->cache->getAnalysisResults($audit->uuid);

            if ($results !== null) {
                $categories = $this->dashboardCategoryMapper->categories($results);
                // Group U: surfaced as their own dedicated blocks on
                // result.blade.php (not only via the generic
                // Lead Intelligence category card above), since a draft
                // email body and a qualification breakdown don't fit
                // that card's generic checks/recommendations shape.
                // Both stay null exactly when $results carries them as
                // null — never fabricated when a subset of the pipeline
                // hasn't run.
                $prospectQualification = $results->prospectQualification;
                $outreachDraft = $results->outreachDraft;
            }
        }

        // Guarded rather than the bare division the old placeholder @php
        // block used: $categories can be empty (audit not completed yet,
        // or a cache miss) or contain a null-scored category (e.g.
        // Business Opportunity before its checks are implemented), and
        // neither should silently become a fabricated "0" average.
        $scoredCategories = array_filter($categories, static fn (array $category): bool => $category['score'] !== null);
        $overallScore = $scoredCategories === []
            ? null
            : (int) round(array_sum(array_column($scoredCategories, 'score')) / count($scoredCategories));

        return view('audit.result', [
            'audit' => $audit,
            'status' => $audit->status,
            'isFinished' => $audit->status->isFinished(),
            'categories' => $categories,
            'overallScore' => $overallScore,
            'prospectQualification' => $prospectQualification,
            'outreachDraft' => $outreachDraft,
            'generatedAt' => $audit->updated_at?->format('M j, Y \a\t g:i A') ?? 'just now',
        ]);
    }

    /**
     * Polled by the result page's loading state (see resources/js —
     * vanilla JS, no framework) roughly once a second while an audit is
     * still processing. Percent/label come from AuditCacheService's
     * progress cache, written at each pipeline stage; a finished audit
     * always reports 100 regardless of what (if anything) was cached,
     * so a request that lands just after completion — before/without a
     * final progress write — still resolves the polling loop correctly.
     */
    public function progress(Audit $audit): JsonResponse
    {
        if ($audit->status->isFinished()) {
            return response()->json([
                'status' => $audit->status->value,
                'percent' => 100,
                'label' => $audit->status === AuditStatus::COMPLETED ? 'Done.' : 'Audit failed.',
                'finished' => true,
            ]);
        }

        $progress = $this->cache->getProgress($audit->uuid);

        return response()->json([
            'status' => $audit->status->value,
            'percent' => $progress['percent'] ?? 0,
            'label' => $progress['label'] ?? $audit->status->label(),
            'finished' => false,
        ]);
    }

    public function export(Audit $audit, AuditPdfExportServiceInterface $pdfExportService): Response
    {
        abort_if(
            $audit->status !== AuditStatus::COMPLETED,
            409,
            'This audit has not finished processing yet.',
        );

        $results = $this->cache->getAnalysisResults($audit->uuid) ?? new AnalysisResults(url: $audit->url);
        $recommendationResult = $this->cache->getRecommendations($audit->uuid);

        return $pdfExportService->download($audit, $results, $recommendationResult);
    }

    public function exportExcel(Audit $audit): Response
    {
        abort_if(
            $audit->status !== AuditStatus::COMPLETED,
            409,
            'This audit has not finished processing yet.',
        );

        $results = $this->cache->getAnalysisResults($audit->uuid) ?? new AnalysisResults(url: $audit->url);
        $recommendationResult = $this->cache->getRecommendations($audit->uuid);

        return $this->excel->download(
            new AuditReportExport($results, $recommendationResult),
            sprintf('audit-report-%s.xlsx', $audit->uuid),
        );
    }
}
