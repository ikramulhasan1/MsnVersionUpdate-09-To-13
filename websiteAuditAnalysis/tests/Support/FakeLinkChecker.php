<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Audit\Crawler\Contracts\LinkCheckerInterface;
use App\Audit\Crawler\DTO\LinkCheckResult;

final class FakeLinkChecker implements LinkCheckerInterface
{
    /** @var array<int, string> */
    public array $checkedUrls = [];

    /**
     * @param array<string, LinkCheckResult> $results keyed by the exact URL that will be checked
     */
    public function __construct(private readonly array $results)
    {
    }

    public function check(string $url): LinkCheckResult
    {
        $this->checkedUrls[] = $url;

        return $this->results[$url] ?? new LinkCheckResult(exists: false, statusCode: null, error: 'No fake result registered');
    }

    /**
     * @param array<int, string> $urls
     * @return array<string, LinkCheckResult>
     */
    public function checkMany(array $urls): array
    {
        $results = [];

        foreach ($urls as $url) {
            $results[$url] = $this->check($url);
        }

        return $results;
    }
}
