<?php

declare(strict_types=1);

namespace App\Audit\Export\Pdf\DTO;

/**
 * Everything the PDF header partial needs to render — nothing more.
 *
 * Kept deliberately narrow (Interface Segregation): as later prompts add
 * Scores, Charts, Recommendations and Summary sections to the PDF, each
 * gets its own DTO instead of this one growing to carry unrelated data.
 *
 * $logoDataUri is pre-resolved (already a base64 `data:` URI, or null)
 * rather than a filesystem path, so the header partial never needs to
 * know how the logo was loaded — see AuditToPdfHeaderData, the only
 * place that decides whether/how a logo file is read.
 */
final readonly class PdfHeaderData
{
    public function __construct(
        public string $companyName,
        public ?string $logoDataUri,
        public string $websiteUrl,
        public string $auditDate,
    ) {
    }
}
