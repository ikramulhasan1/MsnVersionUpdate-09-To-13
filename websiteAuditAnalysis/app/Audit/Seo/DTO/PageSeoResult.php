<?php

declare(strict_types=1);

namespace App\Audit\Seo\DTO;

final readonly class PageSeoResult implements \JsonSerializable
{
    /**
     * @param array<int, SeoIssue> $issues
     */
    public function __construct(
        public string $url,
        public int $score,
        public array $issues,
        public int $criticalCount,
        public int $warningCount,
        public int $noticeCount,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'score' => $this->score,
            'issue_counts' => [
                'critical' => $this->criticalCount,
                'warning' => $this->warningCount,
                'notice' => $this->noticeCount,
            ],
            'issues' => array_map(static fn (SeoIssue $i): array => $i->toArray(), $this->issues),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
