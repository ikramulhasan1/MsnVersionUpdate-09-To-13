<?php

declare(strict_types=1);

namespace App\Audit\Crawler\DTO;

/**
 * One distinct URL discovered anywhere during the crawl — either an
 * internal page (crawled, or merely linked-to beyond the depth/page
 * budget) or an external link. Status comes from one of two sources:
 * "crawled" means the crawler actually fetched the full page as part of
 * the traversal; "head_check" / "get_check" means a lightweight
 * reachability probe was used instead (external links are never crawled,
 * and internal links beyond the depth/page budget aren't either).
 */
final readonly class LinkInventoryEntry implements \JsonSerializable
{
    /**
     * @param array<int, string> $foundOnPages
     */
    public function __construct(
        public string $url,
        public bool $isInternal,
        public array $foundOnPages,
        public ?bool $exists,
        public ?int $statusCode,
        public ?string $error,
        public string $checkedVia,
    ) {
    }

    public function isBroken(): bool
    {
        return $this->exists === false;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'is_internal' => $this->isInternal,
            'found_on_pages' => $this->foundOnPages,
            'exists' => $this->exists,
            'status_code' => $this->statusCode,
            'error' => $this->error,
            'checked_via' => $this->checkedVia,
            'broken' => $this->isBroken(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
