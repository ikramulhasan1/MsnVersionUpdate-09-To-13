<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\Pdf\Contracts\AuditPdfExportServiceInterface;
use App\Audit\Export\Pdf\Support\AnalysisResultsToPdfContentData;
use App\Audit\Export\Pdf\Support\AuditToPdfHeaderData;
use App\Models\Audit;
use Barryvdh\DomPDF\PDF;
use Symfony\Component\HttpFoundation\Response;

final class AuditPdfExportService implements AuditPdfExportServiceInterface
{
    public function __construct(
        private readonly PDF $pdf,
        private readonly AuditToPdfHeaderData $headerDataMapper,
        private readonly AnalysisResultsToPdfContentData $contentDataMapper,
        private readonly string $paperSize = 'a4',
        private readonly string $orientation = 'portrait',
    ) {}

    public function download(
        Audit $audit,
        AnalysisResults $results,
        ?AIRecommendationResult $recommendationResult = null
    ): Response {
        return $this->build(
            $audit,
            $results,
            $recommendationResult
        )->download($this->fileName($audit));
    }

    public function stream(
        Audit $audit,
        AnalysisResults $results,
        ?AIRecommendationResult $recommendationResult = null
    ): Response {
        return $this->build(
            $audit,
            $results,
            $recommendationResult
        )->stream($this->fileName($audit));
    }

    private function build(
        Audit $audit,
        AnalysisResults $results,
        ?AIRecommendationResult $recommendationResult = null
    ): PDF {
        return $this->pdf
            ->loadView('audit.pdf.report', [
                'header' => $this->headerDataMapper->map($audit),
                'content' => $this->contentDataMapper->map($results, $recommendationResult),
            ])
            ->setPaper($this->paperSize, $this->orientation);
    }

    private function fileName(Audit $audit): string
    {
        return sprintf('audit-report-%s.pdf', $audit->uuid);
    }
}