<?php

declare(strict_types=1);

namespace App\Audit\BusinessSignals\DTO;

final readonly class BusinessSignalsResult implements \JsonSerializable
{
    /**
     * @param array<string, bool> $signals keyed by signal name
     *        (careers, hiring, blog_update, funding, new_product —
     *        social_presence is added later, once ContactInfoExtractor
     *        exists, by an enrichment step in AssembleAnalysisResultsJob
     *        rather than by the detector itself)
     * @param array<string, ?string> $signalDetails a short factual note
     *        per signal, keyed the same as $signals — e.g.
     *        "Careers page found at /careers with 4 open listings" for
     *        a detected signal, or null when that signal wasn't
     *        detected. Every non-null note must cite the specific
     *        page/evidence it came from — never a generic guess.
     * @param array<string, ?string> $signalPageUrls the SAME specific
     *        page URL already cited in prose inside $signalDetails
     *        (Phase M4) — e.g. $signalDetails['careers'] might read
     *        "Careers-related URL found: https://example.com/careers",
     *        and $signalPageUrls['careers'] holds that same
     *        "https://example.com/careers" as its own structured
     *        value. Added so a caller (a blade view building a link,
     *        an export row, a future integration) never has to parse a
     *        URL back out of a human-readable sentence to get it —
     *        the exact problem dashboard-components.blade.php's own
     *        page_url handling for Security/Accessibility/Content/
     *        UI/UX/Business Opportunity checks already avoids by
     *        having a dedicated field, which this class lacked until
     *        now. Null for a signal that wasn't detected, or one
     *        (funding, new_product) that has no page to point at at
     *        all.
     */
    public function __construct(
        public string $url,
        public array $signals,
        public array $signalDetails,
        public string $analyzedAt,
        public array $signalPageUrls = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'signals' => $this->signals,
            'signal_details' => $this->signalDetails,
            'signal_page_urls' => $this->signalPageUrls,
            'analyzed_at' => $this->analyzedAt,
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}