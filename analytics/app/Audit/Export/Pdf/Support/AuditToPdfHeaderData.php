<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf\Support;

use App\Audit\Export\Pdf\DTO\PdfHeaderData;
use App\Models\Audit;

/**
 * Builds the PDF header's data from an Audit model.
 *
 * A single, focused responsibility (Single Responsibility Principle):
 * decide the header's company name, logo, website URL and formatted
 * date. AuditPdfExportService stays unaware of any of that — it only
 * asks this mapper for a PdfHeaderData and hands it to the view.
 *
 * The logo is read once here and turned into a base64 `data:` URI,
 * because dompdf resolves relative/public URLs unreliably depending on
 * server configuration, while an inline data URI always renders. When
 * no logo file exists at the configured path, $logoDataUri is simply
 * null and the header partial falls back to a text mark — the header
 * still renders cleanly either way.
 */
final class AuditToPdfHeaderData
{
    public function __construct(
        private readonly string $companyName,
        private readonly ?string $logoAbsolutePath,
    ) {
    }

    public function map(Audit $audit): PdfHeaderData
    {
        return new PdfHeaderData(
            companyName: $this->companyName,
            logoDataUri: $this->resolveLogoDataUri(),
            websiteUrl: $audit->url,
            auditDate: ($audit->created_at ?? now())->format('F j, Y'),
        );
    }

    private function resolveLogoDataUri(): ?string
    {
        if ($this->logoAbsolutePath === null || ! is_file($this->logoAbsolutePath)) {
            return null;
        }

        $contents = file_get_contents($this->logoAbsolutePath);

        if ($contents === false) {
            return null;
        }

        $mimeType = mime_content_type($this->logoAbsolutePath) ?: 'image/png';

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($contents));
    }
}
