<?php

declare(strict_types=1);

namespace App\Audit\Outreach;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Outreach\DTO\OutreachDraftResult;

/**
 * Turns an already-assembled AnalysisResults into a DRAFT cold-outreach
 * email for the Lead Intelligence features (dashboard card, Excel/PDF
 * export). Entirely template-based from real data already on $results —
 * the top 2-3 real issues by severity from
 * BusinessOpportunityResult::$websiteHealth, plus (when available) the
 * site's own host as a stand-in for a company name and a contact name
 * from ContactInfoResult::$teamMembers — the same deterministic,
 * no-external-model approach AIRecommendationEngine already uses,
 * rather than an external LLM API call.
 *
 * $results->seo (PageSeoResult) does not actually carry a page-title or
 * company-name string anywhere on its DTO in this codebase (only
 * url/score/issues/issue-counts), so despite the original spec
 * referencing "$results->seo" for a company name, this class falls back
 * to the audited URL's own host instead — a real fact already on
 * $results, not a guess. If a title/company-name field is ever added to
 * PageSeoResult, prefer that over the host here.
 *
 * Returns null (never a generic, non-personalized template) whenever
 * there isn't enough real data to personalize meaningfully — currently:
 * no BusinessOpportunityResult, or a BusinessOpportunityResult with zero
 * real WebsiteHealthIssue findings across every category.
 *
 * DRAFT FOR HUMAN REVIEW ONLY. This class must never be wired to an
 * auto-send path anywhere in this codebase — see OutreachDraftResult's
 * docblock. CAN-SPAM/GDPR compliance for whatever a human does with the
 * draft afterward is that human's responsibility, not this generator's.
 */
final class OutreachDraftGenerator
{
    public function __construct(
        private readonly int $maxIssuesReferenced = 3,
    ) {
    }

    public function generate(AnalysisResults $results, ?ProspectQualificationResult $qualification): ?OutreachDraftResult
    {
        if ($results->businessOpportunity === null) {
            return null;
        }

        $topIssues = $this->topIssuesBySeverity($results->businessOpportunity->websiteHealth);

        if ($topIssues === []) {
            // No real findings to personalize against — a generic
            // template that isn't actually personalized would be worse
            // than no draft at all.
            return null;
        }

        $companyLabel = $this->companyLabel($results->url);
        $contactName = $this->firstTeamMemberName($results->contactInfo?->teamMembers ?? []);

        $basedOnIssues = array_map(
            static fn (WebsiteHealthIssue $issue): string => $issue->issue,
            $topIssues,
        );

        return new OutreachDraftResult(
            url: $results->url,
            subject: $this->subject($companyLabel, count($topIssues)),
            body: $this->body($companyLabel, $contactName, $topIssues, $qualification),
            basedOnIssues: $basedOnIssues,
        );
    }

    /**
     * Flattens every WebsiteHealthIssue across every $websiteHealth
     * category into one list, sorted by SeoSeverity::scoreWeight()
     * (most severe first, ties kept in their original discovery order
     * — usort() is stable as of PHP 8.0), then trimmed to
     * $maxIssuesReferenced. Never invents an issue: every entry
     * returned is a real WebsiteHealthIssue already computed elsewhere.
     *
     * @param  array<string, array<int, WebsiteHealthIssue>>  $websiteHealth
     * @return array<int, WebsiteHealthIssue>
     */
    private function topIssuesBySeverity(array $websiteHealth): array
    {
        $allIssues = array_merge([], ...array_values($websiteHealth));

        usort(
            $allIssues,
            static fn (WebsiteHealthIssue $a, WebsiteHealthIssue $b): int => $b->severity->scoreWeight() <=> $a->severity->scoreWeight(),
        );

        return array_slice($allIssues, 0, $this->maxIssuesReferenced);
    }

    /**
     * A real, already-available fact (the audited URL's own host) used
     * as a stand-in for a company name — never a guessed/invented name.
     * See this class's docblock for why $results->seo can't supply one.
     */
    private function companyLabel(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return $url;
        }

        return preg_replace('/^www\./i', '', $host) ?? $host;
    }

    /**
     * @param  array<int, array{name: string, title: ?string, linkedinUrl: ?string, sourceUrl: string}>  $teamMembers
     */
    private function firstTeamMemberName(array $teamMembers): ?string
    {
        $first = $teamMembers[0] ?? null;

        if ($first === null || ! is_string($first['name'] ?? null) || $first['name'] === '') {
            return null;
        }

        return $first['name'];
    }

    private function subject(string $companyLabel, int $issueCount): string
    {
        return sprintf(
            '%d quick %s we spotted on %s',
            $issueCount,
            $issueCount === 1 ? 'issue' : 'issues',
            $companyLabel,
        );
    }

    /**
     * @param  array<int, WebsiteHealthIssue>  $topIssues
     */
    private function body(
        string $companyLabel,
        ?string $contactName,
        array $topIssues,
        ?ProspectQualificationResult $qualification,
    ): string {
        $greeting = $contactName !== null ? "Hi {$contactName}," : 'Hi there,';

        $lines = [
            $greeting,
            '',
            "I ran a quick audit of {$companyLabel} and found a few things worth a look:",
            '',
        ];

        foreach ($topIssues as $issue) {
            $line = "- {$issue->issue}";

            if ($issue->recommendation !== null && $issue->recommendation !== '') {
                $line .= " ({$issue->recommendation})";
            }

            $lines[] = $line;
        }

        $lines[] = '';

        // $qualification only ever adjusts tone/urgency of the closing
        // line below — it never adds or removes which issues are
        // referenced above, since $basedOnIssues must stay traceable to
        // $topIssues alone.
        $lines[] = $qualification?->priority() === 'High'
            ? "Happy to walk through these in a quick call this week if that's useful — no pressure either way."
            : 'Happy to share more detail if useful — no pressure either way.';

        $lines[] = '';
        $lines[] = 'Best,';

        return implode("\n", $lines);
    }
}