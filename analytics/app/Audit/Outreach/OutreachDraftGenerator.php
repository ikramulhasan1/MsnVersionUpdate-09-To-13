<?php

declare(strict_types=1);

namespace App\Audit\Outreach;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\Enums\AccessibilityCheckStatus;
use App\Audit\Enums\SecurityCheckStatus;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\Outreach\DTO\OutreachDraftResult;
use App\Audit\Seo\DTO\PageSeoResult;
use App\Audit\Seo\DTO\SeoIssue;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use Closure;

/**
 * Turns an already-assembled AnalysisResults into a DRAFT cold-outreach
 * email for the Lead Intelligence features (dashboard card, Excel/PDF
 * export). Entirely template-based from real data already on $results —
 * never an external LLM API call, the same deterministic approach
 * AIRecommendationEngine already uses.
 *
 * PRODUCTION INCIDENT (Phase M7) — read before narrowing this back down
 * to BusinessOpportunityResult alone: this class used to require
 * $results->businessOpportunity to be non-null, and drew its top 2-3
 * referenced issues ENTIRELY from
 * BusinessOpportunityResult::$websiteHealth — meaning a draft could
 * only ever be about Business Opportunity's own check categories
 * (website_modernization, marketing_analysis, ...), never once
 * mentioning a real, often more compelling finding sitting right there
 * on $results->seo, $results->performance, $results->security, or
 * $results->accessibility. An audit with a genuinely bad Security
 * grade but an unremarkable Business Opportunity score produced an
 * outreach draft that said nothing about the security problem at all —
 * the single most sale-relevant fact about that site.
 *
 * The fix: coreFindings() below gathers real findings from ALL FOUR of
 * SEO / Performance / Security / Accessibility (see that method's own
 * docblock for exactly how each is normalized onto one comparable
 * severity scale, since none of the four share a single DTO shape),
 * ranks them together, and keeps the $maxCoreFindings most severe —
 * NOT one-per-category, whichever 2-3 (by default) are genuinely the
 * worst across all four combined, which is what "the most important
 * findings" honestly means. Business Opportunity's own top issue and a
 * brief Technology Upgrade Opportunity mention are then added
 * separately (see businessOpportunityFinding()/technologyNote() below)
 * — every one of these five sources is now optional on its own; a
 * draft generates as long as ANY of them has real data, not only when
 * Business Opportunity specifically does.
 *
 * Returns null (never a generic, non-personalized template) only when
 * literally none of the five sources above have anything real to
 * reference — the same "under-generating is fine, fabricating isn't"
 * rule this class already followed before Phase M7, just checked
 * across a wider set of inputs now.
 *
 * DRAFT FOR HUMAN REVIEW ONLY. This class must never be wired to an
 * auto-send path anywhere in this codebase — see OutreachDraftResult's
 * docblock. CAN-SPAM/GDPR compliance for whatever a human does with the
 * draft afterward is that human's responsibility, not this generator's.
 */
final class OutreachDraftGenerator
{
    /**
     * Synthetic severity weights for Security/Accessibility's own
     * simple PASS/WARNING/FAIL status — chosen to land on the exact
     * same numeric scale App\Audit\Enums\SeoSeverity::scoreWeight()
     * already uses (CRITICAL=15, WARNING=6, NOTICE=2), so a Security
     * FAIL and an SEO CRITICAL issue are genuinely comparable when
     * ranking findings from different analyzers together — see
     * coreFindings()'s own docblock.
     */
    private const int FAIL_WEIGHT = 15;

    private const int WARNING_WEIGHT = 6;

    /**
     * Below this Performance score, a mention is worth including at
     * all — a healthy Performance score (this or above) simply isn't a
     * compelling outreach point, the same reasoning
     * businessOpportunityFinding() applies by only ever referencing a
     * REAL WebsiteHealthIssue, never a passing check.
     */
    private const int PERFORMANCE_MENTION_THRESHOLD = 70;

    public function __construct(
        private readonly int $maxCoreFindings = 3,
    ) {
    }

    public function generate(AnalysisResults $results, ?ProspectQualificationResult $qualification): ?OutreachDraftResult
    {
        $coreFindings = $this->coreFindings($results);
        $businessOpportunityFinding = $this->businessOpportunityFinding($results->businessOpportunity);
        $technologyNote = $this->technologyNote($results->technologyUpgradeOpportunities);

        if ($coreFindings === [] && $businessOpportunityFinding === null && $technologyNote === null) {
            // No real findings anywhere to personalize against — a
            // generic template that isn't actually personalized would
            // be worse than no draft at all.
            return null;
        }

        $companyLabel = $this->companyLabel($results->url);
        $contactName = $this->firstTeamMemberName($results->contactInfo?->teamMembers ?? []);

        $basedOnIssues = array_map(
            static fn (array $finding): string => $finding['label'],
            $coreFindings,
        );

        if ($businessOpportunityFinding !== null) {
            $basedOnIssues[] = $businessOpportunityFinding->issue;
        }

        if ($technologyNote !== null) {
            $basedOnIssues[] = $technologyNote;
        }

        $referencedCount = count($coreFindings) + ($businessOpportunityFinding !== null ? 1 : 0) + ($technologyNote !== null ? 1 : 0);

        return new OutreachDraftResult(
            url: $results->url,
            subject: $this->subject($companyLabel, $referencedCount),
            body: $this->body($companyLabel, $contactName, $coreFindings, $businessOpportunityFinding, $technologyNote, $qualification),
            basedOnIssues: $basedOnIssues,
        );
    }

    /**
     * Gathers real findings from SEO / Performance / Security /
     * Accessibility, each normalized to a common
     * {label, detail, weight} shape so they can be ranked TOGETHER on
     * one scale (see self::FAIL_WEIGHT/self::WARNING_WEIGHT's own
     * docblock) rather than one-per-category — the $maxCoreFindings
     * returned are simply whichever are most severe across all four,
     * which is what "the 2-3 most important findings" means honestly.
     * At most ONE candidate is drawn from each of the four sources
     * (this generator's top-line email references a handful of
     * standout points, not an exhaustive per-category list — the full
     * per-category breakdown already lives in the dashboard/PDF/Excel
     * report this draft points a prospect toward), so a site with
     * severe SEO problems but nothing else notable still only
     * contributes its own single worst SEO issue here, never crowding
     * out the other three categories by design.
     *
     * @return array<int, array{label: string, detail: ?string, weight: int}>
     */
    private function coreFindings(AnalysisResults $results): array
    {
        $candidates = array_values(array_filter([
            $this->seoFinding($results),
            $this->performanceFinding($results),
            $this->securityFinding($results),
            $this->accessibilityFinding($results),
        ]));

        usort(
            $candidates,
            static fn (array $a, array $b): int => $b['weight'] <=> $a['weight'],
        );

        return array_slice($candidates, 0, $this->maxCoreFindings);
    }

    /**
     * @return ?array{label: string, detail: ?string, weight: int}
     */
    private function seoFinding(AnalysisResults $results): ?array
    {
        if ($results->seo === null) {
            return null;
        }

        $entryPage = $this->entryPageSeoResult($results);

        if ($entryPage === null || $entryPage->issues === []) {
            return null;
        }

        $topIssue = null;

        foreach ($entryPage->issues as $issue) {
            /** @var SeoIssue $issue */
            if ($topIssue === null || $issue->severity->scoreWeight() > $topIssue->severity->scoreWeight()) {
                $topIssue = $issue;
            }
        }

        if ($topIssue === null) {
            return null;
        }

        return [
            'label' => "SEO: {$topIssue->message}",
            'detail' => $topIssue->recommendation,
            'weight' => $topIssue->severity->scoreWeight(),
        ];
    }

    /**
     * $results->seo is the multi-page SeoAuditResult wrapper (pages
     * indexed positionally, not keyed by URL — see that DTO's own
     * docblock), so the entry page's own PageSeoResult is found by
     * matching $results->url rather than assumed to be pages[0].
     * Falls back to the first page only if no exact match is found
     * (defensive — every real crawl includes the entry page itself,
     * but a mismatch here should degrade gracefully, not throw).
     */
    private function entryPageSeoResult(AnalysisResults $results): ?PageSeoResult
    {
        if ($results->seo === null) {
            return null;
        }

        foreach ($results->seo->pages as $page) {
            /** @var PageSeoResult $page */
            if ($page->url === $results->url) {
                return $page;
            }
        }

        return $results->seo->pages[0] ?? null;
    }

    /**
     * Unlike the other three, Performance has no discrete pass/fail
     * "check" list on PerformanceResult — just a score, a grade, and
     * raw Core Web Vitals metrics — so this references the score
     * itself directly rather than a specific named issue, and only
     * when it's low enough to be a genuinely compelling point (see
     * self::PERFORMANCE_MENTION_THRESHOLD's own docblock).
     *
     * @return ?array{label: string, detail: ?string, weight: int}
     */
    private function performanceFinding(AnalysisResults $results): ?array
    {
        $score = $results->performance?->score;

        if ($score === null || $score >= self::PERFORMANCE_MENTION_THRESHOLD) {
            return null;
        }

        // Scaled onto the same weight range self::FAIL_WEIGHT/
        // self::WARNING_WEIGHT use (2-15) — a 0 score weighs the same
        // as a CRITICAL SEO issue, a score just under the threshold
        // weighs closer to a NOTICE-level one.
        $weight = (int) round((self::PERFORMANCE_MENTION_THRESHOLD - $score) / self::PERFORMANCE_MENTION_THRESHOLD * 15);

        return [
            'label' => "Performance: page speed score is only {$score}/100",
            'detail' => 'Slow-loading pages commonly cost real conversions and search ranking.',
            'weight' => max($weight, 1),
        ];
    }

    /**
     * @return ?array{label: string, detail: ?string, weight: int}
     */
    private function securityFinding(AnalysisResults $results): ?array
    {
        return $this->worstSimpleCheckFinding(
            'Security',
            $results->security?->checks ?? [],
            static fn (mixed $check): SecurityCheckStatus => $check->status,
            static fn (mixed $check): string => $check->check,
            static fn (mixed $check): ?string => $check->recommendation,
        );
    }

    /**
     * @return ?array{label: string, detail: ?string, weight: int}
     */
    private function accessibilityFinding(AnalysisResults $results): ?array
    {
        return $this->worstSimpleCheckFinding(
            'Accessibility',
            $results->accessibility?->checks ?? [],
            static fn (mixed $check): AccessibilityCheckStatus => $check->status,
            static fn (mixed $check): string => $check->check,
            static fn (mixed $check): ?string => $check->recommendation,
        );
    }

    /**
     * Shared by securityFinding()/accessibilityFinding() above — both
     * SecurityCheckResult and AccessibilityCheckResult share the exact
     * same simple PASS/WARNING/FAIL shape (see each analyzer's own DTO
     * — genuinely identical case names, just two separately-declared
     * enums), so one generic method handles both rather than
     * duplicating the same "find the worst non-passing check" loop
     * twice. Prefers a FAIL over any WARNING, and among same-status
     * checks keeps the first found (stable, deterministic — never
     * arbitrary).
     *
     * @param  array<int, mixed>  $checks
     * @param  Closure(mixed): (SecurityCheckStatus|AccessibilityCheckStatus)  $statusOf
     * @param  Closure(mixed): string  $labelOf
     * @param  Closure(mixed): ?string  $recommendationOf
     * @return ?array{label: string, detail: ?string, weight: int}
     */
    private function worstSimpleCheckFinding(
        string $categoryLabel,
        array $checks,
        Closure $statusOf,
        Closure $labelOf,
        Closure $recommendationOf,
    ): ?array {
        $worst = null;
        $worstWeight = 0;

        foreach ($checks as $check) {
            $status = $statusOf($check);
            $weight = match ($status->value) {
                'fail' => self::FAIL_WEIGHT,
                'warning' => self::WARNING_WEIGHT,
                default => 0,
            };

            if ($weight > $worstWeight) {
                $worst = $check;
                $worstWeight = $weight;
            }
        }

        if ($worst === null) {
            return null;
        }

        return [
            'label' => "{$categoryLabel}: {$labelOf($worst)}",
            'detail' => $recommendationOf($worst),
            'weight' => $worstWeight,
        ];
    }

    /**
     * The single most severe real WebsiteHealthIssue across every
     * Business Opportunity check category — kept separate from
     * coreFindings() above (rather than folded into that same ranked
     * list) since Business Opportunity is conceptually a distinct
     * "here's the business case" point in the email, not competing for
     * one of the $maxCoreFindings technical-finding slots.
     */
    private function businessOpportunityFinding(?BusinessOpportunityResult $businessOpportunity): ?WebsiteHealthIssue
    {
        if ($businessOpportunity === null) {
            return null;
        }

        $allIssues = array_merge([], ...array_values($businessOpportunity->websiteHealth));

        if ($allIssues === []) {
            return null;
        }

        usort(
            $allIssues,
            static fn (WebsiteHealthIssue $a, WebsiteHealthIssue $b): int => $b->severity->scoreWeight() <=> $a->severity->scoreWeight(),
        );

        return $allIssues[0];
    }

    /**
     * A brief, single-sentence mention — never a per-opportunity list
     * (that level of detail belongs in the dashboard/PDF/Excel report
     * this draft points a prospect toward, not a cold-outreach email).
     *
     * @param  array<int, TechnologyUpgradeOpportunity>  $opportunities
     */
    private function technologyNote(array $opportunities): ?string
    {
        if ($opportunities === []) {
            return null;
        }

        $first = $opportunities[0];
        $remaining = count($opportunities) - 1;

        return $remaining > 0
            ? "Technology: {$first->technology} looks due for an upgrade ({$first->reason}), plus {$remaining} more opportunit".($remaining === 1 ? 'y' : 'ies').' like it.'
            : "Technology: {$first->technology} looks due for an upgrade ({$first->reason}).";
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

    private function subject(string $companyLabel, int $findingCount): string
    {
        return sprintf(
            '%d quick %s we spotted on %s',
            $findingCount,
            $findingCount === 1 ? 'thing' : 'things',
            $companyLabel,
        );
    }

    /**
     * Weaves every available source into one cohesive draft — see this
     * class's own docblock for why the sources below (core technical
     * findings, the Business Opportunity case, the technology note)
     * are each optional on their own, so this renders sensibly however
     * many of the three are actually present for a given audit.
     *
     * @param  array<int, array{label: string, detail: ?string, weight: int}>  $coreFindings
     */
    private function body(
        string $companyLabel,
        ?string $contactName,
        array $coreFindings,
        ?WebsiteHealthIssue $businessOpportunityFinding,
        ?string $technologyNote,
        ?ProspectQualificationResult $qualification,
    ): string {
        $greeting = $contactName !== null ? "Hi {$contactName}," : 'Hi there,';

        $lines = [
            $greeting,
            '',
            "I ran a quick audit of {$companyLabel} and found a few things worth a look:",
            '',
        ];

        foreach ($coreFindings as $finding) {
            $line = "- {$finding['label']}";

            if ($finding['detail'] !== null && $finding['detail'] !== '') {
                $line .= " ({$finding['detail']})";
            }

            $lines[] = $line;
        }

        if ($businessOpportunityFinding !== null) {
            $line = "- Business Opportunity: {$businessOpportunityFinding->issue}";

            if ($businessOpportunityFinding->recommendation !== null && $businessOpportunityFinding->recommendation !== '') {
                $line .= " ({$businessOpportunityFinding->recommendation})";
            }

            $lines[] = $line;
        }

        if ($technologyNote !== null) {
            $lines[] = "- {$technologyNote}";
        }

        $lines[] = '';

        // $qualification only ever adjusts tone/urgency of the closing
        // line below — it never adds or removes which findings are
        // referenced above, since basedOnIssues must stay traceable to
        // the findings actually rendered.
        $lines[] = $qualification?->priority() === 'High'
            ? "Happy to walk through these in a quick call this week if that's useful — no pressure either way."
            : 'Happy to share more detail if useful — no pressure either way.';

        $lines[] = '';
        $lines[] = 'Best,';

        return implode("\n", $lines);
    }
}