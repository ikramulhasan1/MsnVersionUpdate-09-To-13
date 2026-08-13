<?php

declare(strict_types=1);

namespace App\Audit\Technology\DTO;

final readonly class TechnologyDetectionResult implements \JsonSerializable
{
    /**
     * @param  ?string  $evidenceUrl  the specific script/CSS asset URL whose
     *                                presence contributed most to this detection, when the
     *                                strongest evidence was a linked resource rather than an
     *                                inline HTML marker, cookie, or header — see
     *                                TechnologyDetector::primaryEvidence(). Null when nothing was
     *                                detected, or when the strongest evidence was not a resource
     *                                URL.
     * @param  ?string  $evidenceSnippet  a short raw excerpt of the specific
     *                                    signal that contributed most to this detection — a matched
     *                                    HTML fragment, a reconstructed meta tag, a Set-Cookie
     *                                    segment, or a response header line — when the strongest
     *                                    evidence was not a resource URL (evidenceUrl and
     *                                    evidenceSnippet are mutually exclusive: exactly one is set
     *                                    per detected technology, whichever kind its strongest
     *                                    signal was). Null when nothing was detected, or when the
     *                                    strongest evidence was a resource URL instead.
     */
    public function __construct(
        public string $technology,
        public bool $detected,
        public ?string $version,
        public int $confidenceScore,
        public ?string $detectionMethod,
        public ?string $evidenceUrl = null,
        public ?string $evidenceSnippet = null,
    ) {}

    /**
     * @return array{
     *     technology: string,
     *     detected: bool,
     *     version: ?string,
     *     confidence_score: int,
     *     detection_method: ?string,
     *     evidence_url: ?string,
     *     evidence_snippet: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'technology' => $this->technology,
            'detected' => $this->detected,
            'version' => $this->version,
            'confidence_score' => $this->confidenceScore,
            'detection_method' => $this->detectionMethod,
            'evidence_url' => $this->evidenceUrl,
            'evidence_snippet' => $this->evidenceSnippet,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
