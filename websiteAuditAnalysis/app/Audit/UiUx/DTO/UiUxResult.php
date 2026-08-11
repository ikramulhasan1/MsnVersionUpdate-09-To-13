<?php

declare(strict_types=1);

namespace App\Audit\UiUx\DTO;

final readonly class UiUxResult implements \JsonSerializable
{
    /**
     * @param array<string, UiUxElementResult> $elements keyed by element name (navigation, hero_section, cta,
     *        forms, spacing, color, typography, button, footer, trust_signals, testimonials, reviews,
     *        mobile_design)
     * @param string $grade letter grade (A-F) derived from score
     * @param string $summary human-readable overview of the UI/UX result
     * @param array<int, string> $prioritizedSuggestions improvement suggestions across every element, ordered
     *        Fail-status elements first, then Warning-status elements, with duplicates removed
     */
    public function __construct(
        public string $url,
        public array $elements,
        public int $score,
        public string $grade,
        public string $summary,
        public array $prioritizedSuggestions,
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
            'elements' => array_map(
                static fn (UiUxElementResult $element): array => $element->toArray(),
                $this->elements,
            ),
            'score' => $this->score,
            'grade' => $this->grade,
            'summary' => $this->summary,
            'prioritized_suggestions' => $this->prioritizedSuggestions,
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
