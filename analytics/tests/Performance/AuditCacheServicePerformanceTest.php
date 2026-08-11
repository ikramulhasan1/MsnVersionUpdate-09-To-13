<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Audit\Cache\AuditCacheService;
use App\Audit\Jobs\AnalyzeChunkJob;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Support\FetchResultFactory;

/**
 * AuditCacheService sits on the hot path between every AnalyzeChunkJob
 * and AssembleAnalysisResultsJob — a queue worker pool runs many
 * audits' chunks concurrently, each writing/reading fragments under
 * the same cache store. This checks that per-audit fragment
 * operations stay cheap and correctly isolated as the number of
 * simultaneously in-flight audits grows, since a shared-key
 * collision here would silently corrupt a different audit's report.
 */
final class AuditCacheServicePerformanceTest extends TestCase
{
    private function service(): AuditCacheService
    {
        return new AuditCacheService(new Repository(new ArrayStore()));
    }

    public function test_fragment_round_trip_scales_across_many_concurrent_audits(): void
    {
        $service = $this->service();
        $auditCount = 500;
        $uuids = [];

        for ($i = 0; $i < $auditCount; $i++) {
            $uuids[] = sprintf('%08d-perf-audit-uuid', $i);
        }

        $fetchResult = FetchResultFactory::make();

        $start = microtime(true);
        foreach ($uuids as $uuid) {
            foreach (AnalyzeChunkJob::ANALYZER_KEYS as $key) {
                // security/accessibility/etc. all implement toArray()/jsonSerialize
                // via their own DTOs; FetchResult itself is a stand-in payload
                // here since only cache write/read cost is under test, not
                // analyzer output shape.
                $service->putAnalysisFragment($uuid, $key, $fetchResult);
            }
        }
        $writeMs = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        foreach ($uuids as $uuid) {
            $service->getAnalysisFragments($uuid);
        }
        $readMs = (microtime(true) - $start) * 1000;

        self::assertLessThan(2000, $writeMs, "Writing fragments for {$auditCount} concurrent audits took {$writeMs}ms (budget: 2000ms).");
        self::assertLessThan(1000, $readMs, "Reading fragments for {$auditCount} concurrent audits took {$readMs}ms (budget: 1000ms).");

        // Isolation check: every audit's fragments must be exactly its
        // own 8 keys — a shared/collapsed cache key would show up here
        // as cross-contamination between audits.
        $sample = $service->getAnalysisFragments($uuids[250]);
        self::assertCount(count(AnalyzeChunkJob::ANALYZER_KEYS), $sample);

        $service->forgetFragments($uuids[250]);
        self::assertCount(
            count(AnalyzeChunkJob::ANALYZER_KEYS),
            $service->getAnalysisFragments($uuids[249]),
            'Forgetting one audit\'s fragments affected a neighboring audit — cache keys may not be sufficiently namespaced per audit UUID.'
        );
    }
}
