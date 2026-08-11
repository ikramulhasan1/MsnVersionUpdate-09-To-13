<?php

declare(strict_types=1);

namespace App\Audit\Outreach\DTO;

/**
 * A DRAFT cold-outreach email a sales rep could send about this audited
 * site, assembled purely from real data already computed elsewhere on
 * AnalysisResults (the top real issues from
 * BusinessOpportunityResult::$websiteHealth, the site's own URL, and —
 * when available — a contact name from ContactInfoResult::$teamMembers).
 * See OutreachDraftGenerator for exactly how each field is derived.
 *
 * This is a DRAFT FOR HUMAN REVIEW ONLY. Nothing in this codebase sends
 * $body anywhere automatically — see OutreachDraftGenerator's class
 * docblock. CAN-SPAM/GDPR compliance for whatever a human ultimately
 * does with this draft is that human's responsibility, not this tool's.
 */
final readonly class OutreachDraftResult implements \JsonSerializable
{
    /**
     * @param  string  $url  the audited site this draft is about.
     * @param  string  $subject  draft email subject line.
     * @param  string  $body  draft email body. A draft for human review —
     *         never auto-sent by this codebase.
     * @param  array<int, string>  $basedOnIssues  the specific real
     *         WebsiteHealthIssue::$issue strings this draft references,
     *         in the order they appear in $body, so the draft is
     *         auditable back to real findings rather than a black box.
     */
    public function __construct(
        public string $url,
        public string $subject,
        public string $body,
        public array $basedOnIssues,
    ) {
    }

    /**
     * @return array{url: string, subject: string, body: string, based_on_issues: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'subject' => $this->subject,
            'body' => $this->body,
            'based_on_issues' => $this->basedOnIssues,
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