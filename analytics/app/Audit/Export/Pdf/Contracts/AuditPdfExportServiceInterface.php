<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf\Contracts;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Models\Audit;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generates the audit's downloadable PDF report.
 *
 * Depending on this contract (Dependency Inversion) rather than the
 * concrete AuditPdfExportService lets AuditController — and any future
 * caller — request a PDF without knowing which PDF library builds it.
 *
 * $results/$recommendationResult mirror exactly what AuditReportExport
 * (the Excel export) already takes, so both exports are driven by the
 * same data contract: the caller supplies whatever AnalysisResults
 * (and, once the AI Recommendation Engine has run, AIRecommendationResult)
 * exist for this audit, and every PDF section renders from those —
 * never from data hardcoded in the PDF module itself.
 */
interface AuditPdfExportServiceInterface
{
    /**
     * Build the PDF and return it as a forced-download response.
     */
    public function download(Audit $audit, AnalysisResults $results, ?AIRecommendationResult $recommendationResult = null): Response;

    /**
     * Build the PDF and return it as an inline (browser-viewable) response.
     */
    public function stream(Audit $audit, AnalysisResults $results, ?AIRecommendationResult $recommendationResult = null): Response;
}
