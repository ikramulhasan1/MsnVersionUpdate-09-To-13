<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Cache\Contracts\AuditCacheServiceInterface;
use App\Audit\DTO\CreateAuditData;
use App\Audit\Enums\AuditMode;
use App\Audit\Enums\AuditStatus;
use App\Audit\Exceptions\PlanLimitExceededException;
use App\Audit\Export\AuditReportExport;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Export\Support\AnalysisResultsToDashboardCategories;
use App\Audit\Services\Contracts\AuditServiceInterface;
use App\Http\Requests\StoreAuditRequest;
use App\Models\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Phase N1.5 (Homepage + Quick Audit Hero) — feeds the
        // homepage's own Pricing preview section
        // (resources/views/home/index.blade.php). Real rows from
        // database/seeders/PlansSeeder today; Phase N5's own Admin
        // Pricing UI is what lets these change without touching this
        // controller or that seeder again.
        return view('home.index', [
            'plans' => \App\Models\Plan::query()
                ->where('is_public', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    /**
     * Phase N1.5 (Homepage + Quick Audit Hero) — the ONE way to submit
     * an audit with NO login at all (routes/web.php's own 'home' group
     * — see that file's own docblock for why this single route stays
     * public while everything else requires auth). Always
     * AuditMode::QUICK, never whatever a caller might pass — the
     * Hero's own form (resources/views/home/index.blade.php) has no
     * mode selector at all, unlike store()'s own Full/Quick radio, by
     * this phase's own explicit design ("শুধু একটি কুইক অডিট করার
     * সিস্টেম থাকবে").
     *
     * auditService->submit()'s own user_id assignment
     * (auth()->id()) naturally comes back null here — this route has
     * no authenticated session at all — so the resulting Audit is
     * genuinely ownerless until someone logs in and claims it (see
     * App\Http\Controllers\Auth\Concerns\RedirectsToPendingAudit for
     * the other half of this flow). session('pending_audit_uuid') is
     * what connects the two: set here, read there.
     *
     * Redirects to login (not straight to the result page) even
     * though the audit is real and already running — viewing
     * audits.show itself requires the 'auth' + 'verified' +
     * 'permission:run-audit' middleware stack
     * (routes/web.php), so an anonymous visitor couldn't see it yet
     * regardless; this redirect is what tells them WHY, with a
     * concrete reason to sign up rather than a generic "please log in".
     */
    public function quickAudit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048', 'starts_with:http://,https://'],
        ], [
            'url.required' => 'Please enter a website URL to audit.',
            'url.url' => 'Enter a valid URL, including http:// or https://.',
            'url.starts_with' => 'The URL must start with http:// or https://.',
        ]);

        $audit = $this->auditService->submit(
            new CreateAuditData(url: rtrim($validated['url'], '/'), mode: AuditMode::QUICK),
        );

        session(['pending_audit_uuid' => $audit->uuid]);

        $response = redirect()
            ->route('register')
            ->with('status', 'Your quick audit is running — sign up free to see your results!');

        // Same fastcgi_finish_request early-response pattern as
        // store() below — see that method's own comment for the full
        // explanation; identical reasoning applies here.
        if (function_exists('fastcgi_finish_request')) {
            $response->prepare($request);
            $response->send();
            fastcgi_finish_request();
        }

        if ($audit->wasRecentlyCreated) {
            $this->auditService->run($audit);
        }

        return $response;
    }

    public function store(StoreAuditRequest $request): RedirectResponse
    {
        // Phase N1.5 (Free Trial) — see
        // App\Audit\Services\AuditService::submit()'s own docblock for
        // exactly when this throws (an expired trial, or a real daily
        // limit reached). Turned into a normal validation-style
        // redirect-back-with-error here — the SAME "always a real,
        // actionable message, never a raw 500" standard every other
        // form in this app already follows.
        try {
            $audit = $this->auditService->submit(
                CreateAuditData::fromArray($request->validated())
            );
        } catch (PlanLimitExceededException $exception) {
            return back()
                ->withInput()
                ->with('plan_limit_message', $exception->getMessage());
        }

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