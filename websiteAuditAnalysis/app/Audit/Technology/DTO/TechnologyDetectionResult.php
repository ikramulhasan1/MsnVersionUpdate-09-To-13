<?php

declare(strict_types=1);

namespace App\Audit\Technology\DTO;

final readonly class TechnologyDetectionResult implements \JsonSerializable
{
    public function __construct(
        public string $technology,
        public bool $detected,
        public ?string $version,
        public int $confidenceScore,
        public ?string $detectionMethod,
    ) {
    }

    /**
     * @return array{
     *     technology: string,
     *     detected: bool,
     *     version: ?string,
     *     confidence_score: int,
     *     detection_method: ?string
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
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
