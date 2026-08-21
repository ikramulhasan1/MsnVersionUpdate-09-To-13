<?php

declare(strict_types=1);

namespace App\TechnicalSeo\Jobs;

use App\Audit\Crawler\Contracts\WebsiteCrawlerServiceInterface;
use App\Audit\Performance\PerformanceAnalyzer;
use App\Enums\TechnicalSeoScanStatus;
use App\Models\TechnicalSeoScan;
use App\TechnicalSeo\TechnicalSeoAnalyzer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Phase R2 (Technical SEO Audit) — deliberately does NOT extend
 * App\Audit\Jobs\AuditJob (that base class is tightly bound to the
 * `audits` table via AuditRepositoryInterface — see
 * technical_seo_scans' own migration docblock). A single job doing
 * everything sequentially (crawl, then PageSpeed for every crawled
 * page, then analyze, then save) rather than the full Audit
 * pipeline's own multi-job Bus::batch chunking — Technical SEO's own
 * checks don't need that parallel-analyzer-chunk complexity, since
 * this is fundamentally ONE analysis pass over ONE crawl, not eight
 * independent analyzers each needing their own job.
 *
 * Status transitions QUEUED -> CRAWLING -> ANALYZING -> COMPLETED (or
 * FAILED at any point, with $error_message always set — see this
 * job's own docblock on failed() for why a silent crash is never
 * acceptable here, matching this app's own established "never leave a
 * scan stuck 'processing' forever" principle from the Audit pipeline's
 * own incident history).
 */
final class RunTechnicalSeoScanJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    private const int MAX_PAGES = 50;

    public function __construct(
        private readonly string $scanUuid,
    ) {
    }

    public function handle(
        WebsiteCrawlerServiceInterface $crawler,
        PerformanceAnalyzer $performanceAnalyzer,
        TechnicalSeoAnalyzer $analyzer,
    ): void {
        $scan = TechnicalSeoScan::query()->where('uuid', $this->scanUuid)->first();

        if ($scan === null) {
            return;
        }

        try {
            $scan->update(['status' => TechnicalSeoScanStatus::CRAWLING]);

            $startUrl = "https://{$scan->domain}";
            $crawlResult = $crawler->crawl($startUrl, maxPages: self::MAX_PAGES);

            $scan->update(['status' => TechnicalSeoScanStatus::ANALYZING]);

            // Reuses this app's own EXISTING PageSpeed Insights
            // integration entirely — see
            // App\TechnicalSeo\TechnicalSeoAnalyzer's own docblock for
            // why this call happens HERE (in the job) rather than
            // inside the analyzer itself.
            $performanceResult = $performanceAnalyzer->analyzeAll($crawlResult);

            $result = $analyzer->analyze($scan->domain, $crawlResult, $performanceResult);

            $scan->update([
                'status' => TechnicalSeoScanStatus::COMPLETED,
                'health_score' => $result->healthScore,
                'health_grade' => $result->healthGrade,
                'result' => $result->toArray(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            $scan->update([
                'status' => TechnicalSeoScanStatus::FAILED,
                'error_message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Laravel's own final failure callback — covers the case where
     * handle() itself never got to run at all (e.g. this job's own
     * constructor/dependency resolution failed) or every retry was
     * exhausted, neither of which the try/catch inside handle() above
     * would have caught. Without this, a scan could get stuck showing
     * "Analyzing..." forever with no explanation — the same "never
     * silently stuck processing" incident this app's own Audit
     * pipeline already learned from.
     */
    public function failed(\Throwable $exception): void
    {
        $scan = TechnicalSeoScan::query()->where('uuid', $this->scanUuid)->first();

        if ($scan !== null && ! $scan->status->isFinished()) {
            $scan->update([
                'status' => TechnicalSeoScanStatus::FAILED,
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}