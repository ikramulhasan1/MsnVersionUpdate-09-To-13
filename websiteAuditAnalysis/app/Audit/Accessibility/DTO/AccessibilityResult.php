<?php

declare(strict_types=1);

namespace App\Audit\Accessibility\DTO;

final readonly class AccessibilityResult implements \JsonSerializable
{
    /**
     * @param array<string, AccessibilityCheckResult> $checks keyed by check name (aria, alt, label,
     *        button, contrast, font_size, keyboard_navigation, tab_index, heading_order, wcag_compliance)
     * @param string $grade letter grade (A-F) derived from score
     * @param string $summary human-readable overview of the accessibility result
     */
    public function __construct(
        public string $url,
        public array $checks,
        public int $score,
        public string $grade,
        public string $summary,
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
            'checks' => array_map(
                static fn (AccessibilityCheckResult $check): array => $check->toArray(),
                $this->checks,
            ),
            'score' => $this->score,
            'grade' => $this->grade,
            'summary' => $this->summary,
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
