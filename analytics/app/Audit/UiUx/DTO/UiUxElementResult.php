<?php

declare(strict_types=1);

namespace App\Audit\UiUx\DTO;

use App\Audit\Enums\UiUxElementStatus;

final readonly class UiUxElementResult implements \JsonSerializable
{
    /**
     * @param  array<int, string>  $issues  problems found with this element; empty when status is Pass
     * @param  array<int, string>  $suggestions  improvement suggestions; empty when status is Pass
     * @param  ?array<int, array{domPath: ?string, detail: ?string}>  $affectedElements
     *                                                                                   The specific DOM element(s) behind the issues above — e.g.
     *                                                                                   which <form> is missing a submit button, or which
     *                                                                                   inline-styled element has both margin and padding set to
     *                                                                                   zero. Deliberately NOT index-aligned one-to-one with
     *                                                                                   $issues: a single issue string can summarize several
     *                                                                                   affected elements (e.g. "3 forms have more than 10
     *                                                                                   fields"), so affectedElements is its own flat list rather
     *                                                                                   than a per-issue slot — cleaner than forcing a 1:1 mapping
     *                                                                                   that count-based issue messages don't naturally have.
     *                                                                                   Still backward compatible: $issues keeps its original
     *                                                                                   plain-string shape, and affectedElements is purely
     *                                                                                   additive. Entries use domPath: null (with a descriptive
     *                                                                                   detail) for page-level absences that have no single
     *                                                                                   element to point at (e.g. "no navigation landmark found").
     *                                                                                   Null only when the element passed.
     */
    public function __construct(
        public string $element,
        public UiUxElementStatus $status,
        public array $issues,
        public array $suggestions,
        public ?string $pageUrl = null,
        public ?array $affectedElements = null,
    ) {}

    /**
     * @return array{element: string, status: string, issues: array<int, string>, suggestions: array<int, string>, page_url: ?string, affected_elements: ?array<int, array{domPath: ?string, detail: ?string}>}
     */
    public function toArray(): array
    {
        return [
            'element' => $this->element,
            'status' => $this->status->value,
            'issues' => $this->issues,
            'suggestions' => $this->suggestions,
            'page_url' => $this->pageUrl,
            'affected_elements' => $this->affectedElements,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
