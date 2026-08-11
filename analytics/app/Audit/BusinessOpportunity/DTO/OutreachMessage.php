<?php

declare(strict_types=1);

namespace App\Audit\BusinessOpportunity\DTO;

/**
 * A draft outreach message a sales rep could send to this site's owner —
 * templated purely from data already computed elsewhere on
 * BusinessOpportunityResult: the WebsiteHealthIssue findings across
 * $websiteHealth, the BusinessOpportunityScore, and the SalesOpportunity.
 * Generates no new findings, scores, or recommendations of its own; it
 * only assembles what already exists into outreach copy.
 */
final readonly class OutreachMessage implements \JsonSerializable
{
    public function __construct(
        public string $subject,
        public string $message,
    ) {
    }

    /**
     * @return array{subject: string, message: string}
     */
    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
