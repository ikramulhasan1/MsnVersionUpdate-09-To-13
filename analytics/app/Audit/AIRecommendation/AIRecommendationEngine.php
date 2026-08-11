<?php

declare(strict_types=1);

namespace App\Audit\AIRecommendation;

use App\Audit\AIRecommendation\DTO\AIRecommendationResult;
use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\AIRecommendation\DTO\BusinessRecommendation;
use App\Audit\AIRecommendation\DTO\DevelopmentTimeEstimate;
use App\Audit\AIRecommendation\DTO\EstimatedCost;
use App\Audit\AIRecommendation\DTO\ExecutiveSummary;
use App\Audit\AIRecommendation\DTO\IssuePriority;
use App\Audit\AIRecommendation\DTO\LongTermFixes;
use App\Audit\AIRecommendation\DTO\PriorityIssue;
use App\Audit\AIRecommendation\DTO\QuickWins;
use App\Audit\AIRecommendation\DTO\RecommendedServices;
use App\Audit\AIRecommendation\DTO\ServiceRecommendation;
use App\Audit\AIRecommendation\DTO\TechnicalRecommendationItem;
use App\Audit\BusinessOpportunity\DTO\BusinessOpportunityResult;
use App\Audit\Enums\BusinessOpportunityCheckStatus;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Performance\DTO\PerformanceResult;
use App\Audit\Seo\DTO\SeoAuditResult;
use App\Audit\UiUx\DTO\UiUxResult;

/**
 * Synthesizes every existing analyzer's completed result — Security,
 * Accessibility, Content, UI/UX, Performance, Business Opportunity,
 * Technology, and site-wide SEO, bundled via {@see AnalysisResults} —
 * into AI-generated recommendations for a page: an executive summary,
 * issue prioritization, quick wins, long-term fixes, a development
 * time estimate, a business recommendation, a cost estimate, and
 * recommended services.
 *
 * Implemented so far: 'executive_summary', 'issue_priority' (Prompt
 * 14.2); 'quick_wins', 'long_term_fixes', 'estimated_development_time'
 * (Prompt 14.3); and 'business_recommendation', 'estimated_cost',
 * 'recommended_services' (Prompt 14.4) — every category this engine
 * supports. All eight are built from the same normalized issue list
 * (see {@see self::collectIssues()}): Quick Wins and Long-Term Fixes
 * split that list by estimated effort (see
 * {@see self::classifyEffort()}); Estimated Development Time and
 * Estimated Cost roll up their hour/dollar totals; Recommended
 * Services ranks issue categories by weighted severity; and Business
 * Recommendation synthesizes all of the above into one priority and
 * narrative. No category recomputes what an earlier one already
 * built, so none of them can disagree with another about how many
 * issues exist, how severe they are, how long they take to fix, or
 * what they cost.
 *
 * Takes an AnalysisResults rather than any single analyzer's result or
 * a raw FetchResult, since this engine's entire purpose is to reason
 * over what every other analyzer already found, not to inspect the page
 * itself. Every analyzer result is optional, so every extraction method
 * below tolerates a null result (or an empty checks/issues list) and
 * simply contributes no issues for that category.
 */
final class AIRecommendationEngine
{
    /**
     * Points-averaging and letter-grade thresholds for the overall
     * score, matching SecurityAnalyzer/AccessibilityAnalyzer's
     * score()/grade() convention so an "A" means the same thing here
     * as it does in every other analyzer.
     */
    public function __construct(
        private readonly int $gradeAThreshold = 90,
        private readonly int $gradeBThreshold = 75,
        private readonly int $gradeCThreshold = 60,
        private readonly int $gradeDThreshold = 40,
    ) {
    }

    public function analyze(AnalysisResults $results): AIRecommendationResult
    {
        $issues = $this->collectIssues($results);
        $quickWins = $this->quickWins($issues);
        $longTermFixes = $this->longTermFixes($issues);
        $recommendedServices = $this->recommendedServices($issues);

        $recommendations = [
            'executive_summary' => $this->executiveSummary($results, $issues)->toArray(),
            'issue_priority' => $this->issuePriority($issues)->toArray(),
            'quick_wins' => $quickWins->toArray(),
            'long_term_fixes' => $longTermFixes->toArray(),
            'estimated_development_time' => $this->developmentTimeEstimate($quickWins, $longTermFixes)->toArray(),
            'business_recommendation' => $this
                ->businessRecommendation($results, $issues, $quickWins, $longTermFixes, $recommendedServices)
                ->toArray(),
            'estimated_cost' => $this->estimatedCost($quickWins, $longTermFixes)->toArray(),
            'recommended_services' => $recommendedServices->toArray(),
        ];

        return new AIRecommendationResult(
            url: $results->url,
            recommendations: $recommendations,
            summary: $this->summary($recommendations),
            analyzedAt: (new \DateTimeImmutable())->format(DATE_ATOM),
        );
    }

    /**
     * Convenience wrapper returning the complete result — every
     * category from {@see self::analyze()} combined — as a JSON
     * string. Delegates entirely to {@see AIRecommendationResult::toJson()}
     * rather than re-implementing serialization here, so the engine
     * stays responsible only for building the result and the DTO
     * stays solely responsible for encoding it.
     */
    public function analyzeToJson(AnalysisResults $results, int $options = 0): string
    {
        return $this->analyze($results)->toJson($options);
    }

    /**
     * @param array<string, mixed> $recommendations
     */
    private function summary(array $recommendations): string
    {
        return $recommendations === []
            ? 'No AI recommendations have been generated yet.'
            : sprintf('%d recommendation categor(y/ies) generated.', count($recommendations));
    }

    // -----------------------------------------------------------------
    // Executive Summary
    // -----------------------------------------------------------------

    /**
     * @param array<int, PriorityIssue> $issues
     */
    private function executiveSummary(AnalysisResults $results, array $issues): ExecutiveSummary
    {
        $scores = array_values(array_filter(
            [
                $results->security?->score,
                $results->accessibility?->score,
                $results->content?->score,
                $results->uiUx?->score,
                $results->performance?->score,
                $results->businessOpportunity?->score,
                $results->seo?->averageScore,
            ],
            static fn (?int $score): bool => $score !== null,
        ));

        $overallScore = $scores === [] ? null : (int) round(array_sum($scores) / count($scores));
        $overallGrade = $overallScore === null ? null : $this->grade($overallScore);

        $categoriesAnalyzed = count(array_filter([
            $results->security,
            $results->accessibility,
            $results->content,
            $results->uiUx,
            $results->performance,
            $results->businessOpportunity,
            $results->technology,
            $results->seo,
        ], static fn (?object $result): bool => $result !== null));

        [$critical, $warning, $notice] = $this->countBySeverity($issues);

        return new ExecutiveSummary(
            url: $results->url,
            overallScore: $overallScore,
            overallGrade: $overallGrade,
            categoriesAnalyzed: $categoriesAnalyzed,
            totalIssues: count($issues),
            criticalCount: $critical,
            warningCount: $warning,
            noticeCount: $notice,
            narrative: $this->narrative($results, $overallScore, $overallGrade, $categoriesAnalyzed, $critical, $warning, $notice),
        );
    }

    private function grade(int $score): string
    {
        return match (true) {
            $score >= $this->gradeAThreshold => 'A',
            $score >= $this->gradeBThreshold => 'B',
            $score >= $this->gradeCThreshold => 'C',
            $score >= $this->gradeDThreshold => 'D',
            default => 'F',
        };
    }

    private function narrative(
        AnalysisResults $results,
        ?int $overallScore,
        ?string $overallGrade,
        int $categoriesAnalyzed,
        int $critical,
        int $warning,
        int $notice,
    ): string {
        if ($categoriesAnalyzed === 0) {
            return sprintf('No completed analyses are available yet for %s.', $results->url);
        }

        $scoreText = $overallScore === null
            ? 'no overall score could be computed (no completed analyzer returned a numeric score)'
            : sprintf('an overall score of %d/100 (grade %s)', $overallScore, $overallGrade);

        $totalIssues = $critical + $warning + $notice;

        $issueText = $totalIssues === 0
            ? 'no outstanding issues were found'
            : sprintf(
                '%d issue(s) were found (%d critical, %d warning, %d notice)',
                $totalIssues,
                $critical,
                $warning,
                $notice,
            );

        $callToAction = match (true) {
            $critical > 0 => 'Critical issues should be addressed first, as they carry the greatest risk or impact.',
            $warning > 0 => 'No critical issues were found, but warnings are worth addressing to improve the overall score.',
            default => 'The site is in good standing across the analyzed categories.',
        };

        return sprintf(
            'Based on %d completed analysis categor(y/ies) for %s, the site has %s, and %s. %s',
            $categoriesAnalyzed,
            $results->url,
            $scoreText,
            $issueText,
            $callToAction,
        );
    }

    // -----------------------------------------------------------------
    // Issue Priority
    // -----------------------------------------------------------------

    /**
     * @param array<int, PriorityIssue> $issues
     */
    private function issuePriority(array $issues): IssuePriority
    {
        [$critical, $warning, $notice] = $this->countBySeverity($issues);

        return new IssuePriority(
            items: $issues,
            criticalCount: $critical,
            warningCount: $warning,
            noticeCount: $notice,
            summary: $this->issuePrioritySummary($issues, $critical, $warning, $notice),
        );
    }

    /**
     * @param array<int, PriorityIssue> $issues
     */
    private function issuePrioritySummary(array $issues, int $critical, int $warning, int $notice): string
    {
        if ($issues === []) {
            return 'No issues were found across the completed analyses.';
        }

        return sprintf(
            '%d issue(s) prioritized across all completed analyses: %d critical, %d warning, %d notice.',
            count($issues),
            $critical,
            $warning,
            $notice,
        );
    }

    /**
     * @param array<int, PriorityIssue> $issues
     * @return array{0: int, 1: int, 2: int} [criticalCount, warningCount, noticeCount]
     */
    private function countBySeverity(array $issues): array
    {
        $critical = 0;
        $warning = 0;
        $notice = 0;

        foreach ($issues as $issue) {
            match ($issue->severity) {
                SeoSeverity::CRITICAL => $critical++,
                SeoSeverity::WARNING => $warning++,
                SeoSeverity::NOTICE => $notice++,
            };
        }

        return [$critical, $warning, $notice];
    }

    // -----------------------------------------------------------------
    // Issue collection — one extractor per analyzer, normalized into
    // PriorityIssue and merged, then sorted most severe first.
    // -----------------------------------------------------------------

    /**
     * @return array<int, PriorityIssue>
     */
    private function collectIssues(AnalysisResults $results): array
    {
        $issues = [
            ...$this->fromPassWarnFailChecks($results->security?->checks ?? [], 'security'),
            ...$this->fromPassWarnFailChecks($results->accessibility?->checks ?? [], 'accessibility'),
            ...$this->fromContentChecks($results->content?->checks ?? []),
            ...$this->fromUiUx($results->uiUx),
            ...$this->fromPerformance($results->performance),
            ...$this->fromBusinessOpportunity($results->businessOpportunity),
            ...$this->fromSeo($results->seo),
        ];

        usort(
            $issues,
            static fn (PriorityIssue $a, PriorityIssue $b): int => $b->severity->scoreWeight() <=> $a->severity->scoreWeight(),
        );

        return $issues;
    }

    /**
     * Shared by Security and Accessibility: both key their checks by
     * name and use an (identically-shaped but distinct) pass/warning/fail
     * status enum with ->check, ->value, ->status, ->recommendation.
     *
     * @param array<string, object{check: string, value: ?string, status: object{value: string, label: callable(): string}, recommendation: ?string}> $checks
     * @return array<int, PriorityIssue>
     */
    private function fromPassWarnFailChecks(array $checks, string $category): array
    {
        $issues = [];

        foreach ($checks as $check) {
            $severity = $this->severityFromPassWarnFail($check->status->value);

            if ($severity === null) {
                continue;
            }

            $issues[] = new PriorityIssue(
                category: $category,
                issue: $check->check,
                severity: $severity,
                status: $check->status->label(),
                value: $check->value,
                recommendation: $check->recommendation,
            );
        }

        return $issues;
    }

    /**
     * @param array<string, \App\Audit\Content\DTO\ContentCheckResult> $checks
     * @return array<int, PriorityIssue>
     */
    private function fromContentChecks(array $checks): array
    {
        $issues = [];

        foreach ($checks as $check) {
            $severity = $this->severityFromGoodWarnCritical($check->status->value);

            if ($severity === null) {
                continue;
            }

            $issues[] = new PriorityIssue(
                category: 'content',
                issue: $check->metric,
                severity: $severity,
                status: $check->status->label(),
                value: $check->value,
                recommendation: $check->recommendation,
            );
        }

        return $issues;
    }

    /**
     * @return array<int, PriorityIssue>
     */
    private function fromUiUx(?UiUxResult $uiUx): array
    {
        if ($uiUx === null) {
            return [];
        }

        $issues = [];

        foreach ($uiUx->elements as $element) {
            $severity = $this->severityFromPassWarnFail($element->status->value);

            if ($severity === null) {
                continue;
            }

            if ($element->issues === []) {
                $issues[] = new PriorityIssue(
                    category: 'ui_ux',
                    issue: $element->element,
                    severity: $severity,
                    status: $element->status->label(),
                    value: null,
                    recommendation: $element->suggestions[0] ?? null,
                );

                continue;
            }

            foreach ($element->issues as $index => $issueText) {
                $issues[] = new PriorityIssue(
                    category: 'ui_ux',
                    issue: sprintf('%s: %s', $element->element, $issueText),
                    severity: $severity,
                    status: $element->status->label(),
                    value: null,
                    recommendation: $element->suggestions[$index] ?? ($element->suggestions[0] ?? null),
                );
            }
        }

        return $issues;
    }

    /**
     * Performance's per-metric checks aren't standardized yet (see
     * PerformanceResult's docblock: $metrics is left empty until each
     * metric check is implemented in a later phase), so the only
     * reliable signal available in this phase is the overall score —
     * one issue is raised for the page's performance as a whole rather
     * than guessing at a per-metric structure that doesn't exist yet.
     *
     * @return array<int, PriorityIssue>
     */
    private function fromPerformance(?PerformanceResult $performance): array
    {
        if ($performance === null || $performance->score === null) {
            return [];
        }

        $severity = match (true) {
            $performance->score < $this->gradeDThreshold => SeoSeverity::CRITICAL,
            $performance->score < $this->gradeCThreshold => SeoSeverity::WARNING,
            default => null,
        };

        if ($severity === null) {
            return [];
        }

        return [
            new PriorityIssue(
                category: 'performance',
                issue: 'Overall page performance',
                severity: $severity,
                status: $performance->grade ?? (string) $performance->score,
                value: (string) $performance->score,
                recommendation: $performance->summary,
            ),
        ];
    }

    /**
     * Draws from $businessOpportunity->websiteHealth rather than
     * ->checks: websiteHealth is the Website Problems/SEO Issues/
     * Performance Issues structure purpose-built with a severity
     * dimension (see WebsiteHealthIssue), so it is the correct source
     * for prioritizable issues without re-deriving severity from
     * ->checks and risking double-counting the same problems.
     *
     * @return array<int, PriorityIssue>
     */
    private function fromBusinessOpportunity(?BusinessOpportunityResult $businessOpportunity): array
    {
        if ($businessOpportunity === null) {
            return [];
        }

        $issues = [];

        foreach ($businessOpportunity->websiteHealth as $category => $healthIssues) {
            foreach ($healthIssues as $healthIssue) {
                if ($healthIssue->status === BusinessOpportunityCheckStatus::PASS) {
                    continue;
                }

                $issues[] = new PriorityIssue(
                    category: 'business_opportunity',
                    issue: sprintf('%s: %s', str_replace('_', ' ', $category), $healthIssue->issue),
                    severity: $healthIssue->severity,
                    status: $healthIssue->status->label(),
                    value: null,
                    recommendation: $healthIssue->recommendation,
                );
            }
        }

        return $issues;
    }

    /**
     * @return array<int, PriorityIssue>
     */
    private function fromSeo(?SeoAuditResult $seo): array
    {
        if ($seo === null) {
            return [];
        }

        $issues = [];

        foreach ($seo->pages as $page) {
            foreach ($page->issues as $issue) {
                $issues[] = new PriorityIssue(
                    category: 'seo',
                    issue: $issue->message,
                    severity: $issue->severity,
                    status: $issue->severity->label(),
                    value: null,
                    recommendation: $issue->recommendation,
                    pageUrl: $page->url,
                );
            }
        }

        return $issues;
    }

    private function severityFromPassWarnFail(string $status): ?SeoSeverity
    {
        return match ($status) {
            'fail' => SeoSeverity::CRITICAL,
            'warning' => SeoSeverity::WARNING,
            default => null,
        };
    }

    private function severityFromGoodWarnCritical(string $status): ?SeoSeverity
    {
        return match ($status) {
            'critical' => SeoSeverity::CRITICAL,
            'warning' => SeoSeverity::WARNING,
            default => null,
        };
    }

    // -----------------------------------------------------------------
    // Quick Wins / Long-Term Fixes / Estimated Development Time
    // -----------------------------------------------------------------

    /**
     * Assumed productive developer hours per day, used only to convert
     * the hour totals in {@see self::developmentTimeSummary()} into a
     * rough day count — a lower figure than a full 8-hour day to leave
     * room for meetings, review, and QA, matching how estimates are
     * padded elsewhere in the codebase (e.g. SeoSeverity::scoreWeight()
     * deliberately steep for critical issues).
     */
    private const PRODUCTIVE_HOURS_PER_DAY = 6;

    /**
     * UI/UX elements that are structural/layout work rather than a
     * copy or markup tweak — rebuilding navigation or a mobile layout
     * takes materially longer than fixing a color-contrast or spacing
     * issue, so these are classified as Long-Term Fixes rather than
     * Quick Wins even though both come from the same analyzer.
     */
    private const STRUCTURAL_UIUX_ELEMENTS = ['navigation', 'hero_section', 'mobile_design', 'forms', 'footer'];

    /**
     * @param array<int, PriorityIssue> $issues
     */
    private function quickWins(array $issues): QuickWins
    {
        $items = [];

        foreach ($issues as $issue) {
            [$isQuickWin, $hoursMin, $hoursMax] = $this->classifyEffort($issue);

            if ($isQuickWin) {
                $items[] = $this->toTechnicalRecommendationItem($issue, $hoursMin, $hoursMax);
            }
        }

        [$hoursMin, $hoursMax] = $this->sumHours($items);

        return new QuickWins(
            items: $items,
            totalEstimatedHoursMin: $hoursMin,
            totalEstimatedHoursMax: $hoursMax,
            summary: $items === []
                ? 'No quick wins identified — no low-effort issues were found across the completed analyses.'
                : sprintf(
                    '%d quick win(s) identified, estimated at %d-%d hour(s) of development time in total.',
                    count($items),
                    $hoursMin,
                    $hoursMax,
                ),
        );
    }

    /**
     * @param array<int, PriorityIssue> $issues
     */
    private function longTermFixes(array $issues): LongTermFixes
    {
        $items = [];

        foreach ($issues as $issue) {
            [$isQuickWin, $hoursMin, $hoursMax] = $this->classifyEffort($issue);

            if (! $isQuickWin) {
                $items[] = $this->toTechnicalRecommendationItem($issue, $hoursMin, $hoursMax);
            }
        }

        [$hoursMin, $hoursMax] = $this->sumHours($items);

        return new LongTermFixes(
            items: $items,
            totalEstimatedHoursMin: $hoursMin,
            totalEstimatedHoursMax: $hoursMax,
            summary: $items === []
                ? 'No long-term fixes identified — no higher-effort issues were found across the completed analyses.'
                : sprintf(
                    '%d long-term fix(es) identified, estimated at %d-%d hour(s) of development time in total.',
                    count($items),
                    $hoursMin,
                    $hoursMax,
                ),
        );
    }

    private function developmentTimeEstimate(QuickWins $quickWins, LongTermFixes $longTermFixes): DevelopmentTimeEstimate
    {
        $totalMin = $quickWins->totalEstimatedHoursMin + $longTermFixes->totalEstimatedHoursMin;
        $totalMax = $quickWins->totalEstimatedHoursMax + $longTermFixes->totalEstimatedHoursMax;
        $quickWinsCount = count($quickWins->items);
        $longTermCount = count($longTermFixes->items);

        return new DevelopmentTimeEstimate(
            quickWinsHoursMin: $quickWins->totalEstimatedHoursMin,
            quickWinsHoursMax: $quickWins->totalEstimatedHoursMax,
            longTermHoursMin: $longTermFixes->totalEstimatedHoursMin,
            longTermHoursMax: $longTermFixes->totalEstimatedHoursMax,
            totalHoursMin: $totalMin,
            totalHoursMax: $totalMax,
            quickWinsCount: $quickWinsCount,
            longTermCount: $longTermCount,
            summary: $this->developmentTimeSummary($totalMin, $totalMax, $quickWinsCount, $longTermCount),
        );
    }

    private function developmentTimeSummary(int $totalMin, int $totalMax, int $quickWinsCount, int $longTermCount): string
    {
        if ($totalMin === 0 && $totalMax === 0) {
            return 'No development time is required — no quick wins or long-term fixes were identified.';
        }

        $daysMin = (int) ceil($totalMin / self::PRODUCTIVE_HOURS_PER_DAY);
        $daysMax = (int) ceil($totalMax / self::PRODUCTIVE_HOURS_PER_DAY);

        return sprintf(
            'Estimated %d-%d hour(s) of total development time (%d quick win(s), %d long-term fix(es)), '
                . 'roughly %d-%d business day(s) at %d productive hour(s)/day.',
            $totalMin,
            $totalMax,
            $quickWinsCount,
            $longTermCount,
            $daysMin,
            $daysMax,
            self::PRODUCTIVE_HOURS_PER_DAY,
        );
    }

    private function toTechnicalRecommendationItem(PriorityIssue $issue, int $hoursMin, int $hoursMax): TechnicalRecommendationItem
    {
        return new TechnicalRecommendationItem(
            category: $issue->category,
            issue: $issue->issue,
            severity: $issue->severity,
            status: $issue->status,
            recommendation: $issue->recommendation,
            pageUrl: $issue->pageUrl,
            estimatedHoursMin: $hoursMin,
            estimatedHoursMax: $hoursMax,
        );
    }

    /**
     * @param array<int, TechnicalRecommendationItem> $items
     * @return array{0: int, 1: int} [hoursMin, hoursMax]
     */
    private function sumHours(array $items): array
    {
        $min = 0;
        $max = 0;

        foreach ($items as $item) {
            $min += $item->estimatedHoursMin;
            $max += $item->estimatedHoursMax;
        }

        return [$min, $max];
    }

    /**
     * Classifies a single issue's fix effort by category — config/copy/
     * markup-level categories (Security, Accessibility, SEO) default to
     * Quick Win; structural/strategic categories (Content, Performance)
     * default to Long-Term Fix; UI/UX and Business Opportunity vary by
     * the specific element/sub-category involved, so they get their own
     * lookup rather than one blanket answer.
     *
     * @return array{0: bool, 1: int, 2: int} [isQuickWin, hoursMin, hoursMax]
     */
    private function classifyEffort(PriorityIssue $issue): array
    {
        return match ($issue->category) {
            'security' => [true, 1, 3],
            'accessibility' => [true, 1, 3],
            'seo' => [true, 1, 2],
            'content' => [false, 4, 12],
            'performance' => [false, 6, 24],
            'ui_ux' => $this->classifyUiUxEffort($issue->issue),
            'business_opportunity' => $this->classifyBusinessOpportunityEffort($issue->issue),
            default => [true, 1, 4],
        };
    }

    /**
     * @return array{0: bool, 1: int, 2: int}
     */
    private function classifyUiUxEffort(string $issueText): array
    {
        $element = strstr($issueText, ':', true);
        $element = $element === false ? $issueText : $element;

        return in_array($element, self::STRUCTURAL_UIUX_ELEMENTS, true)
            ? [false, 8, 20]
            : [true, 2, 4];
    }

    /**
     * $issueText carries the websiteHealth category as a human-readable
     * prefix (see {@see self::fromBusinessOpportunity()}'s
     * str_replace('_', ' ', $category)), so that prefix is read back
     * here to tell config-level "website problems"/"seo issues" apart
     * from deeper "performance issues" work, without re-deriving or
     * duplicating the category key itself.
     *
     * @return array{0: bool, 1: int, 2: int}
     */
    private function classifyBusinessOpportunityEffort(string $issueText): array
    {
        return match (true) {
            str_starts_with($issueText, 'performance issues') => [false, 6, 20],
            str_starts_with($issueText, 'seo issues') => [true, 1, 3],
            str_starts_with($issueText, 'website problems') => [true, 1, 4],
            default => [false, 3, 8],
        };
    }

    // -----------------------------------------------------------------
    // Business Recommendation / Estimated Cost / Recommended Services
    // -----------------------------------------------------------------

    /**
     * Assumed hourly rate range applied to Quick Wins/Long-Term Fixes
     * hours to derive Estimated Cost — the same kind of band assumption
     * as self::PRODUCTIVE_HOURS_PER_DAY, and the same min/max-band shape
     * BusinessOpportunityAnalyzer::salesOpportunity() already uses for
     * Estimated Deal Potential.
     */
    private const HOURLY_RATE_MIN = 75;

    private const HOURLY_RATE_MAX = 150;

    /**
     * Suggested service offering per issue category. A separate lookup
     * from BusinessOpportunityAnalyzer's own $serviceByCategory: that
     * one maps websiteHealth sub-categories (website_problems, seo_issues,
     * ...) to services within the Business Opportunity analyzer alone,
     * while this one maps the seven top-level categories PriorityIssue
     * actually uses across the whole engine.
     */
    private const SERVICE_BY_CATEGORY = [
        'security' => 'Security Hardening Package',
        'accessibility' => 'Accessibility (WCAG) Compliance Package',
        'content' => 'Content Strategy & Copywriting Package',
        'ui_ux' => 'UI/UX Redesign Package',
        'performance' => 'Performance Optimization Package',
        'business_opportunity' => 'Business Growth & Conversion Package',
        'seo' => 'SEO Optimization Package',
    ];

    /**
     * Ranks every category present in $issues by total weighted
     * severity (via the existing SeoSeverity::scoreWeight()) and maps
     * each to a suggested service from self::SERVICE_BY_CATEGORY, most
     * impacted category first.
     *
     * @param array<int, PriorityIssue> $issues
     */
    private function recommendedServices(array $issues): RecommendedServices
    {
        /** @var array<string, array{count: int, weight: int, top_severity: SeoSeverity}> $byCategory */
        $byCategory = [];

        foreach ($issues as $issue) {
            $category = $issue->category;
            $current = $byCategory[$category] ?? ['count' => 0, 'weight' => 0, 'top_severity' => $issue->severity];

            $current['count']++;
            $current['weight'] += $issue->severity->scoreWeight();

            if ($issue->severity->scoreWeight() > $current['top_severity']->scoreWeight()) {
                $current['top_severity'] = $issue->severity;
            }

            $byCategory[$category] = $current;
        }

        uasort($byCategory, static fn (array $a, array $b): int => $b['weight'] <=> $a['weight']);

        $items = [];

        foreach ($byCategory as $category => $data) {
            $service = self::SERVICE_BY_CATEGORY[$category] ?? 'General Website Consultation';

            $items[] = new ServiceRecommendation(
                category: $category,
                service: $service,
                issueCount: $data['count'],
                topSeverity: $data['top_severity'],
                recommendation: sprintf(
                    '%d issue(s) found in %s — %s is recommended.',
                    $data['count'],
                    str_replace('_', ' ', $category),
                    $service,
                ),
            );
        }

        return new RecommendedServices(
            items: $items,
            summary: $items === []
                ? 'No services recommended — no issues were found across the completed analyses.'
                : sprintf(
                    '%d service(s) recommended across %d categor(y/ies) with outstanding issues.',
                    count($items),
                    count($items),
                ),
        );
    }

    /**
     * Derives cost purely from QuickWins/LongTermFixes' existing hour
     * totals multiplied by self::HOURLY_RATE_MIN/MAX — no new effort
     * estimation happens here.
     */
    private function estimatedCost(QuickWins $quickWins, LongTermFixes $longTermFixes): EstimatedCost
    {
        $quickWinsCostMin = $quickWins->totalEstimatedHoursMin * self::HOURLY_RATE_MIN;
        $quickWinsCostMax = $quickWins->totalEstimatedHoursMax * self::HOURLY_RATE_MAX;
        $longTermCostMin = $longTermFixes->totalEstimatedHoursMin * self::HOURLY_RATE_MIN;
        $longTermCostMax = $longTermFixes->totalEstimatedHoursMax * self::HOURLY_RATE_MAX;
        $totalCostMin = $quickWinsCostMin + $longTermCostMin;
        $totalCostMax = $quickWinsCostMax + $longTermCostMax;

        return new EstimatedCost(
            quickWinsCostMin: $quickWinsCostMin,
            quickWinsCostMax: $quickWinsCostMax,
            longTermCostMin: $longTermCostMin,
            longTermCostMax: $longTermCostMax,
            totalCostMin: $totalCostMin,
            totalCostMax: $totalCostMax,
            hourlyRateMin: self::HOURLY_RATE_MIN,
            hourlyRateMax: self::HOURLY_RATE_MAX,
            summary: $totalCostMin === 0 && $totalCostMax === 0
                ? 'No development cost is estimated — no quick wins or long-term fixes were identified.'
                : sprintf(
                    'Estimated cost of $%s - $%s (quick wins: $%s - $%s, long-term fixes: $%s - $%s), '
                        . 'based on an assumed $%d-$%d/hour rate.',
                    number_format($totalCostMin),
                    number_format($totalCostMax),
                    number_format($quickWinsCostMin),
                    number_format($quickWinsCostMax),
                    number_format($longTermCostMin),
                    number_format($longTermCostMax),
                    self::HOURLY_RATE_MIN,
                    self::HOURLY_RATE_MAX,
                ),
        );
    }

    /**
     * Synthesizes priority + a narrative purely from data already
     * computed elsewhere: severity counts (via the existing
     * self::countBySeverity()), Quick Wins/Long-Term Fixes hour totals,
     * and RecommendedServices' ranking — plus, when available, the
     * Business Opportunity analyzer's own SalesOpportunity, referenced
     * rather than recomputed so the two engines' outreach never
     * contradict each other.
     *
     * @param array<int, PriorityIssue> $issues
     */
    private function businessRecommendation(
        AnalysisResults $results,
        array $issues,
        QuickWins $quickWins,
        LongTermFixes $longTermFixes,
        RecommendedServices $recommendedServices,
    ): BusinessRecommendation {
        [$critical, $warning] = $this->countBySeverity($issues);

        $priority = match (true) {
            $critical > 0 => 'High',
            $warning > 0 => 'Medium',
            default => 'Low',
        };

        $topFocusAreas = array_slice(
            array_map(
                static fn (ServiceRecommendation $service): string => $service->service,
                $recommendedServices->items,
            ),
            0,
            3,
        );

        $salesNote = '';
        $salesOpportunity = $results->businessOpportunity?->salesOpportunity;

        if ($salesOpportunity !== null) {
            $salesNote = sprintf(
                ' The Business Opportunity analysis already suggests a %s, with an estimated deal potential of %s.',
                $salesOpportunity->suggestedService,
                $salesOpportunity->estimatedDealPotential,
            );
        }

        $recommendation = sprintf(
            '%s priority: %d quick win(s) (%d-%d hour(s)) and %d long-term fix(es) (%d-%d hour(s)) were '
                . 'identified across %d service categor(y/ies).%s',
            $priority,
            count($quickWins->items),
            $quickWins->totalEstimatedHoursMin,
            $quickWins->totalEstimatedHoursMax,
            count($longTermFixes->items),
            $longTermFixes->totalEstimatedHoursMin,
            $longTermFixes->totalEstimatedHoursMax,
            count($recommendedServices->items),
            $salesNote,
        );

        return new BusinessRecommendation(
            priority: $priority,
            topFocusAreas: $topFocusAreas,
            recommendation: $recommendation,
        );
    }
}
