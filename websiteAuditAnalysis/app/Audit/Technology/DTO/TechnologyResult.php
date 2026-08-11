<?php

declare(strict_types=1);

namespace App\Audit\Technology\DTO;

final readonly class TechnologyResult implements \JsonSerializable
{
    /**
     * @param  array<string, TechnologyDetectionResult>  $detections  keyed by technology slug
     *                                                                (laravel, wordpress, woocommerce, shopify, react, vue, angular, nextjs, nuxt,
     *                                                                tailwind, bootstrap, jquery, google_analytics, google_tag_manager,
     *                                                                facebook_pixel, microsoft_clarity, google_ads, cloudflare)
     * @param  array<int, array{slug: string, technology: string, category: string, version: ?string}>  $technologyStack
     *                                                                                                                    the "Complete Technology Stack": every entry from $detections where detected === true.
     *                                                                                                                    Deliberately excludes $serverHeader below — that's a raw informational value with no
     *                                                                                                                    confidence/detected judgement behind it, unlike everything else in this array.
     * @param  array{total_detected: int, total_checked: int, by_category: array<string, int>}  $technologySummary
     *                                                                                                              the "Technology Summary": detected/checked counts and a per-category breakdown
     * @param  ?string  $serverHeader  the raw Server response header value (e.g. "nginx", "Apache",
     *                                 "cloudflare"), or null when the response didn't include one. Kept as its own field
     *                                 rather than folded into $technologyStack: it's an informational value ("what does the
     *                                 Server header say"), not a weighted-signal detection like every $technologyStack entry.
     */
    public function __construct(
        public string $url,
        public array $detections,
        public array $technologyStack,
        public array $technologySummary,
        public int $overallDetectionConfidence,
        public ?string $serverHeader,
        public string $analyzedAt,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'detections' => array_map(
                static fn (TechnologyDetectionResult $detection): array => $detection->toArray(),
                $this->detections,
            ),
            'technology_stack' => $this->technologyStack,
            'technology_summary' => $this->technologySummary,
            'overall_detection_confidence' => $this->overallDetectionConfidence,
            'server_header' => $this->serverHeader,
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
