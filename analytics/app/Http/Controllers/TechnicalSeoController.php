<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Audit\AIRecommendation\AIRecommendationEngine;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Enums\TechnicalSeoScanStatus;
use App\Models\TechnicalSeoScan;
use App\TechnicalSeo\DTO\TechnicalSeoResult;
use App\TechnicalSeo\Jobs\RunTechnicalSeoScanJob;
use App\TechnicalSeo\TechnicalSeoAnalyzer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

/**
 * Phase R2 (Technical SEO Audit) — PRODUCTION-CRITICAL OWNERSHIP
 * PATTERN, same as App\Http\Controllers\KeywordListController's own
 * docblock: every action receiving a TechnicalSeoScan verifies
 * $scan->user_id === $request->user()->id and aborts 403 otherwise,
 * including for an Admin — no "legacy unowned row" exception applies
 * here (every scan is created by a real logged-in action).
 */
final class TechnicalSeoController extends Controller
{
    public function index(Request $request): View
    {
        return view('technical-seo.index', [
            'scans' => $request->user()->technicalSeoScans()->latest()->limit(20)->get(),
        ]);
    }

    /**
     * Creates the scan row immediately (status: queued) and dispatches
     * the background job, then redirects straight to the status/result
     * page — the SAME "submit now, watch progress on the next page"
     * flow this app's own existing Audit feature already uses, not a
     * synchronous wait.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $host = parse_url($validated['domain'], PHP_URL_HOST);
        $domain = is_string($host) && $host !== '' ? $host : $validated['domain'];
        $domain = preg_replace('/^www\./i', '', $domain);

        $scan = $request->user()->technicalSeoScans()->create([
            'domain' => $domain,
            'status' => TechnicalSeoScanStatus::QUEUED,
        ]);

        RunTechnicalSeoScanJob::dispatch($scan->uuid);

        return redirect()->route('technical-seo.show', $scan);
    }

    public function show(Request $request, TechnicalSeoScan $technicalSeoScan): View
    {
        $this->authorizeOwner($request, $technicalSeoScan);

        $result = $technicalSeoScan->status === TechnicalSeoScanStatus::COMPLETED && $technicalSeoScan->result !== null
            ? TechnicalSeoResult::fromArray($technicalSeoScan->result)
            : null;

        $aiResult = null;

        if ($result !== null) {
            $analyzer = app(TechnicalSeoAnalyzer::class);
            $seoAuditResult = $analyzer->toSeoAuditResult($result);
            $aiResult = app(AIRecommendationEngine::class)->analyze(
                new AnalysisResults(url: $technicalSeoScan->domain, seo: $seoAuditResult),
            );
        }

        // Score Trend — this user's own PAST completed scans for the
        // SAME domain, per-user ownership already enforced by the
        // ->technicalSeoScans() relation itself (see
        // App\Models\User::technicalSeoScans()) — never another real
        // user's scans, matching this app's own established
        // ownership principle throughout (Website Discovery, Keyword
        // Lists).
        $trend = $request->user()->technicalSeoScans()
            ->where('domain', $technicalSeoScan->domain)
            ->where('status', TechnicalSeoScanStatus::COMPLETED)
            ->orderBy('created_at')
            ->get(['health_score', 'created_at']);

        return view('technical-seo.show', [
            'scan' => $technicalSeoScan,
            'result' => $result,
            'aiResult' => $aiResult,
            'trend' => $trend,
        ]);
    }

    /**
     * Polled by resources/views/technical-seo/show.blade.php's own JS
     * (public/js/technical-seo-progress.js) while a scan is still in
     * progress — a small JSON status payload, not a full page reload,
     * matching the same "poll a lightweight endpoint" principle this
     * app's own existing Audit progress page already established.
     */
    public function progress(Request $request, TechnicalSeoScan $technicalSeoScan): JsonResponse
    {
        $this->authorizeOwner($request, $technicalSeoScan);

        return response()->json([
            'status' => $technicalSeoScan->status->value,
            'label' => $technicalSeoScan->status->label(),
            'finished' => $technicalSeoScan->status->isFinished(),
        ]);
    }

    public function exportCsv(Request $request, TechnicalSeoScan $technicalSeoScan): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorizeOwner($request, $technicalSeoScan);

        abort_if($technicalSeoScan->status !== TechnicalSeoScanStatus::COMPLETED || $technicalSeoScan->result === null, 409, 'This scan has not finished yet.');

        $result = TechnicalSeoResult::fromArray($technicalSeoScan->result);

        return Response::streamDownload(function () use ($result): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Check', 'Severity', 'Message', 'Recommendation']);

            foreach ($result->issues as $issue) {
                fputcsv($handle, [$issue->check, $issue->severity, $issue->message, $issue->recommendation]);
            }

            fclose($handle);
        }, "technical-seo-{$technicalSeoScan->domain}.csv");
    }

    private function authorizeOwner(Request $request, TechnicalSeoScan $technicalSeoScan): void
    {
        abort_unless($technicalSeoScan->user_id === $request->user()->id, 403);
    }
}