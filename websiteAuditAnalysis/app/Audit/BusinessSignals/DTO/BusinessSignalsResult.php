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
     */
    public function __construct(
        public string $url,
        public array $signals,
        public array $signalDetails,
        public string $analyzedAt,
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