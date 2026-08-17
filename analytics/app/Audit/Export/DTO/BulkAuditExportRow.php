<?php

declare(strict_types=1);

namespace App\Audit\Export\DTO;

/**
 * Phase K5 (Bulk Audit) — one audit's worth of export columns for a
 * BulkAuditBatch's own results table/export, mirroring
 * App\Discovery\Export\DTO\DiscoveryExportRow's own "passive DTO,
 * separate mapper class" split for a completely different module's
 * export pipeline.
 *
 * Deliberately a much narrower column set than AuditReportExport's own
 * multi-sheet single-audit workbook (Security/SEO/Performance/
 * Accessibility score+grade, not every analyzer's full detail) — this
 * is a side-by-side COMPARISON of many websites at once, not a deep
 * report on one; anyone who wants the full report for a specific
 * audit already has "View Full Report" linking to it right there in
 * the same results table.
 */
final readonly class BulkAuditExportRow implements \JsonSerializable
{
    public function __construct(
        public string $url,
        public string $status,
        public ?int $seoScore,
        public ?int $performanceScore,
        public ?string $performanceGrade,
        public ?int $securityScore,
        public ?string $securityGrade,
        public ?int $accessibilityScore,
        public ?string $accessibilityGrade,
    ) {
    }

    /**
     * @return array{
     *     url: string, status: string, seo_score: ?int,
     *     performance_score: ?int, performance_grade: ?string,
     *     security_score: ?int, security_grade: ?string,
     *     accessibility_score: ?int, accessibility_grade: ?string,
     * }
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'status' => $this->status,
            'seo_score' => $this->seoScore,
            'performance_score' => $this->performanceScore,
            'performance_grade' => $this->performanceGrade,
            'security_score' => $this->securityScore,
            'security_grade' => $this->securityGrade,
            'accessibility_score' => $this->accessibilityScore,
            'accessibility_grade' => $this->accessibilityGrade,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}