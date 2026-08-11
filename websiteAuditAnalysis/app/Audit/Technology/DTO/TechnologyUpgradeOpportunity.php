<?php

declare(strict_types=1);

namespace App\Audit\Technology\DTO;

/**
 * A single "this detected technology is old enough to be worth
 * upgrading" finding, produced by TechnologyUpgradeAnalyzer from an
 * already-computed TechnologyResult.
 *
 * $detectedVersion is typed ?string to match the field's meaning
 * elsewhere in the codebase (TechnologyDetectionResult::$version), but
 * in practice TechnologyUpgradeAnalyzer only ever constructs this DTO
 * once a real, non-null version string is known — see its docblock.
 */
final readonly class TechnologyUpgradeOpportunity implements \JsonSerializable
{
    public function __construct(
        public string $slug,
        public string $technology,
        public ?string $detectedVersion,
        public string $reason,
        public string $suggestedService,
    ) {
    }

    /**
     * @return array{
     *     slug: string,
     *     technology: string,
     *     detected_version: ?string,
     *     reason: string,
     *     suggested_service: string
     * }
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'technology' => $this->technology,
            'detected_version' => $this->detectedVersion,
            'reason' => $this->reason,
            'suggested_service' => $this->suggestedService,
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
