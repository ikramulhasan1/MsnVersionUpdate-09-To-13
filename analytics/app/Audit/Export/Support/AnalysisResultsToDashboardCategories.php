<?php

declare(strict_types=1);

namespace App\Audit\Export\Support;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\BusinessOpportunity\DTO\WebsiteHealthIssue;
use App\Audit\BusinessSignals\DTO\BusinessSignalsResult;
use App\Audit\Contacts\DTO\ContactInfoResult;
use App\Audit\Content\DTO\ContentCheckResult;
use App\Audit\Enums\ContentCheckStatus;
use App\Audit\Enums\SeoSeverity;
use App\Audit\Lead\DTO\ProspectQualificationResult;
use App\Audit\ReviewPresence\DTO\ReviewPresenceResult;
use App\Audit\Security\DTO\SecurityCheckResult;
use App\Audit\Seo\DTO\PageSeoResult;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use App\Audit\UiUx\DTO\UiUxElementResult;

/**
 * Translates an AnalysisResults bundle into the category-card shape
 * resources/views/audit/result.blade.php renders (key, abbr, label,
 * score, grade, summary, checks: [name, status, note],
 * recommendations: string[]) — the same shape currently hardcoded
 * there as placeholder data, which AuditController::show() and this
 * class will replace it with (see the matching TODO in that view).
 *
 * One category item per analyzer that is non-null on $results;
 * analyzers that haven't run yet are skipped entirely rather than
 * rendered with fabricated scores or checks — an audit still in
 * progress simply shows fewer cards, never invented ones.
 *
 * Every field is read directly from that analyzer's own DTO — this
 * class does not compute new scores, invent summary text beyond
 * concatenating the analyzer's own real counts (SEO has no summary
 * field of its own, so its summary sentence is built only from
 * $seo->pagesAnalyzed/$pagesFailed/$averageScore, never a canned
 * phrase), and does not report a "pass" for anything the analyzer
 * itself didn't report as passing.
 *
 * Each analyzer status vocabulary is normalised to the dashboard's
 * pass/warning/fail:
 *   - Security/Accessibility/UI-UX/Business Opportunity already use
 *     pass/warning/fail (SecurityCheckStatus, AccessibilityCheckStatus,
 *     UiUxElementStatus, BusinessOpportunityCheckStatus) — used as-is.
 *   - Content uses good/warning/critical (ContentCheckStatus) — mapped
 *     good→pass, critical→fail.
 *   - SEO has no per-check status at all, only issues (things found
 *     wrong) with a severity of critical/warning/notice — mapped
 *     critical→fail, warning→warning, notice→warning (a notice is
 *     still a flagged issue, never promoted to a fabricated "pass").
 *   - Performance's metrics are a plain array with a 'status' string
 *     of good/warning/critical/unknown (not an enum) — mapped the same
 *     as Content, with unknown→warning (flagging "not determinable" as
 *     worth a look, never silently dropped or reported as passing).
 *   - Lead Intelligence (ProspectQualificationResult / BusinessSignalsResult /
 *     ContactInfoResult / ReviewPresenceResult / TechnologyUpgradeOpportunity)
 *     has no shared status vocabulary of its own at all — see
 *     self::leadIntelligence() for how each of those five real, already-
 *     computed inputs is normalised to pass/warning independently, since
 *     none of them carry a genuine "fail" concept (a missing signal or
 *     contact detail is "not found", not a broken check).
 *
 * Every check entry also carries a 'location' key — shape
 * ['page_url' => ?string, 'dom_path' => ?string, 'affected_elements' =>
 * array<int, array{url: ?string, domPath: ?string, detail: ?string}>] —
 * populated from whichever of the underlying DTO's own pageUrl/domPath/
 * elementUrl/affectedElements fields exist for that check (see
 * self::location()). Always present, even when every part of it is
 * null/empty, so the dashboard view/Excel/PDF layers can read
 * $check['location'] unconditionally rather than checking whether the
 * key exists first; those layers are themselves responsible for hiding
 * empty parts, not this class.
 */
final class AnalysisResultsToDashboardCategories
{
    /**
     * @return array<int, array{
     *     key: string,
     *     abbr: string,
     *     label: string,
     *     score: ?int,
     *     grade: ?string,
     *     summary: string,
     *     checks: array<int, array{name: string, status: string, note: ?string, location: array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}}>,
     *     recommendations: array<int, string>,
     * }>
     */
    public function categories(AnalysisResults $results): array
    {
        return array_values(array_filter([
            $this->seo($results),
            $this->performance($results),
            $this->security($results),
            $this->accessibility($results),
            $this->content($results),
            $this->uiUx($results),
            $this->businessOpportunity($results),
            $this->leadIntelligence($results),
        ]));
    }

    private function seo(AnalysisResults $results): ?array
    {
        if ($results->seo === null) {
            return null;
        }

        $seo = $results->seo;

        $checks = [];

        foreach ($seo->pages as $page) {
            /** @var PageSeoResult $page */
            foreach ($page->issues as $issue) {
                $checks[] = [
                    'name' => $issue->check,
                    'status' => match ($issue->severity) {
                        SeoSeverity::CRITICAL => 'fail',
                        SeoSeverity::WARNING, SeoSeverity::NOTICE => 'warning',
                    },
                    'note' => $issue->message,
                    'location' => $this->location(
                        $issue->pageUrl,
                        $issue->domPath,
                        $issue->elementUrl !== null || $issue->context !== null
                            ? [['url' => $issue->elementUrl, 'domPath' => $issue->domPath, 'detail' => $issue->context]]
                            : null,
                    ),
                ];
            }
        }

        return [
            'key' => 'seo',
            'abbr' => 'SEO',
            'label' => 'SEO',
            'score' => $seo->averageScore,
            'grade' => null,
            'summary' => sprintf(
                '%d of %d crawled pages analyzed (average score %d).',
                $seo->pagesAnalyzed,
                $seo->pagesAnalyzed + $seo->pagesFailed,
                $seo->averageScore,
            ),
            'checks' => $checks,
            'recommendations' => $seo->recommendations,
        ];
    }

    private function performance(AnalysisResults $results): ?array
    {
        if ($results->performance === null) {
            return null;
        }

        $performance = $results->performance;

        $checks = [];

        foreach ($performance->metrics as $metric => $data) {
            $status = is_array($data) ? ($data['status'] ?? 'unknown') : 'unknown';
            $value = is_array($data) ? ($data['value'] ?? null) : null;
            $unit = is_array($data) ? ($data['unit'] ?? null) : null;
            $message = is_array($data) ? ($data['message'] ?? null) : null;
            $affectedResource = is_array($data) ? ($data['affected_resource'] ?? null) : null;

            $note = $message ?? ($value !== null ? trim($value.' '.(string) $unit) : null);

            $checks[] = [
                'name' => (string) $metric,
                'status' => match ($status) {
                    'good' => 'pass',
                    'critical' => 'fail',
                    default => 'warning', // 'warning' and 'unknown' both surface as a flagged, non-passing check
                },
                'note' => $note,
                'location' => $this->location(
                    $performance->url,
                    null,
                    $affectedResource !== null
                        ? [['url' => $affectedResource, 'domPath' => null, 'detail' => null]]
                        : null,
                ),
            ];
        }

        return [
            'key' => 'performance',
            'abbr' => 'PERF',
            'label' => 'Performance',
            'score' => $performance->score,
            'grade' => $performance->grade,
            'summary' => $performance->summary,
            'checks' => $checks,
            'recommendations' => [],
        ];
    }

    private function security(AnalysisResults $results): ?array
    {
        if ($results->security === null) {
            return null;
        }

        $security = $results->security;

        $checks = [];
        $recommendations = [];

        foreach ($security->checks as $check) {
            /** @var SecurityCheckResult $check */
            $checks[] = [
                'name' => $check->check,
                'status' => $check->status->value,
                'note' => $check->value ?? $check->recommendation,
                'location' => $this->location($check->pageUrl, null, $check->affectedElements),
            ];

            if ($check->recommendation !== null) {
                $recommendations[] = $check->recommendation;
            }
        }

        return [
            'key' => 'security',
            'abbr' => 'SEC',
            'label' => 'Security',
            'score' => $security->score,
            'grade' => $security->grade,
            'summary' => $security->summary,
            'checks' => $checks,
            'recommendations' => array_values(array_unique($recommendations)),
        ];
    }

    private function accessibility(AnalysisResults $results): ?array
    {
        if ($results->accessibility === null) {
            return null;
        }

        $accessibility = $results->accessibility;

        $checks = [];
        $recommendations = [];

        foreach ($accessibility->checks as $check) {
            $checks[] = [
                'name' => $check->check,
                'status' => $check->status->value,
                'note' => $check->value ?? $check->recommendation,
                'location' => $this->location($check->pageUrl, null, $check->affectedElements),
            ];

            if ($check->recommendation !== null) {
                $recommendations[] = $check->recommendation;
            }
        }

        return [
            'key' => 'accessibility',
            'abbr' => 'A11Y',
            'label' => 'Accessibility',
            'score' => $accessibility->score,
            'grade' => $accessibility->grade,
            'summary' => $accessibility->summary,
            'checks' => $checks,
            'recommendations' => array_values(array_unique($recommendations)),
        ];
    }

    private function content(AnalysisResults $results): ?array
    {
        if ($results->content === null) {
            return null;
        }

        $content = $results->content;

        $checks = [];
        $recommendations = [];

        foreach ($content->checks as $check) {
            /** @var ContentCheckResult $check */
            $checks[] = [
                'name' => $check->metric,
                'status' => match ($check->status) {
                    ContentCheckStatus::GOOD => 'pass',
                    ContentCheckStatus::WARNING => 'warning',
                    ContentCheckStatus::CRITICAL => 'fail',
                },
                'note' => $check->value ?? $check->recommendation,
                'location' => $this->location($check->pageUrl, null, $check->affectedElements),
            ];

            if ($check->recommendation !== null) {
                $recommendations[] = $check->recommendation;
            }
        }

        return [
            'key' => 'content',
            'abbr' => 'CONT',
            'label' => 'Content',
            'score' => $content->score,
            'grade' => $content->grade,
            'summary' => $content->summary,
            'checks' => $checks,
            'recommendations' => array_values(array_unique($recommendations)),
        ];
    }

    private function uiUx(AnalysisResults $results): ?array
    {
        if ($results->uiUx === null) {
            return null;
        }

        $uiUx = $results->uiUx;

        $checks = [];

        foreach ($uiUx->elements as $element) {
            /** @var UiUxElementResult $element */
            $checks[] = [
                'name' => $element->element,
                'status' => $element->status->value,
                'note' => $element->issues === [] ? null : implode('; ', $element->issues),
                'location' => $this->location($element->pageUrl, null, $element->affectedElements),
            ];
        }

        return [
            'key' => 'ui_ux',
            'abbr' => 'UX',
            'label' => 'UI/UX',
            'score' => $uiUx->score,
            'grade' => $uiUx->grade,
            'summary' => $uiUx->summary,
            'checks' => $checks,
            'recommendations' => $uiUx->prioritizedSuggestions,
        ];
    }

    private function businessOpportunity(AnalysisResults $results): ?array
    {
        if ($results->businessOpportunity === null) {
            return null;
        }

        $business = $results->businessOpportunity;

        $checks = [];
        $recommendations = [];

        foreach ($business->websiteHealth as $issues) {
            foreach ($issues as $issue) {
                /** @var WebsiteHealthIssue $issue */
                $checks[] = [
                    'name' => $issue->issue,
                    'status' => $issue->status->value,
                    'note' => $issue->recommendation,
                    'location' => $this->location(
                        $issue->pageUrl,
                        null,
                        $issue->elementUrl !== null
                            ? [['url' => $issue->elementUrl, 'domPath' => null, 'detail' => null]]
                            : null,
                    ),
                ];

                if ($issue->recommendation !== null) {
                    $recommendations[] = $issue->recommendation;
                }
            }
        }

        return [
            'key' => 'business_opportunity',
            'abbr' => 'BIZ',
            'label' => 'Business Opportunity',
            'score' => $business->score,
            'grade' => $business->grade,
            'summary' => $business->summary,
            'checks' => $checks,
            'recommendations' => array_values(array_unique($recommendations)),
        ];
    }

    /**
     * Built from five independent, already-computed inputs — none of
     * which is required, so this card only skips entirely (returns
     * null) when every single one of them is absent/empty. Any subset
     * being present is enough to render a (necessarily partial) card,
     * matching this class's "fewer cards / fewer checks, never invented
     * ones" rule at the per-input level rather than only per-card.
     */
    private function leadIntelligence(AnalysisResults $results): ?array
    {
        $prospectQualification = $results->prospectQualification;
        $businessSignals = $results->businessSignals;
        $contactInfo = $results->contactInfo;
        $reviewPresence = $results->reviewPresence;
        $technologyUpgradeOpportunities = $results->technologyUpgradeOpportunities;

        if (
            $prospectQualification === null
            && $businessSignals === null
            && $contactInfo === null
            && $reviewPresence === null
            && $technologyUpgradeOpportunities === []
        ) {
            return null;
        }

        $checks = [
            ...$this->businessSignalsChecks($businessSignals),
            ...$this->contactInfoChecks($contactInfo),
            ...$this->reviewPresenceChecks($reviewPresence),
            ...$this->technologyUpgradeChecks($technologyUpgradeOpportunities),
        ];

        $recommendations = array_values(array_unique(array_map(
            static fn (TechnologyUpgradeOpportunity $opportunity): string => sprintf(
                '%s: %s (%s)',
                $opportunity->technology,
                $opportunity->suggestedService,
                $opportunity->reason,
            ),
            $technologyUpgradeOpportunities,
        )));

        return [
            'key' => 'lead_intelligence',
            'abbr' => 'LEAD',
            'label' => 'Lead Intelligence',
            'score' => $prospectQualification?->score,
            'grade' => $prospectQualification?->grade,
            'summary' => $this->leadIntelligenceSummary(
                $prospectQualification,
                $businessSignals,
                $contactInfo,
                $reviewPresence,
                $technologyUpgradeOpportunities,
            ),
            'checks' => $checks,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * @return array<int, array{name: string, status: string, note: ?string, location: array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}}>
     */
    private function businessSignalsChecks(?BusinessSignalsResult $businessSignals): array
    {
        if ($businessSignals === null) {
            return [];
        }

        $checks = [];

        foreach ($businessSignals->signals as $signal => $detected) {
            $checks[] = [
                'name' => 'Signal: '.str_replace('_', ' ', $signal),
                // A detected positive business signal is a 'pass'; an
                // undetected one is not a broken check, just nothing
                // found — 'warning' flags it as worth a look rather
                // than silently dropping it, same reasoning as
                // Performance's unknown→warning above, never 'fail'
                // since absence of a signal isn't itself a problem.
                'status' => $detected ? 'pass' : 'warning',
                'note' => $businessSignals->signalDetails[$signal] ?? null,
                // Phase M4 — BusinessSignalsResult now DOES carry its own
                // per-signal page location (BusinessSignalsResult::$signalPageUrls
                // — see that property's own docblock for the incident this
                // fixes), so this no longer needs to always be the empty
                // shape the way it used to.
                'location' => $this->location($businessSignals->signalPageUrls[$signal] ?? null),
            ];
        }

        return $checks;
    }

    /**
     * @return array<int, array{name: string, status: string, note: ?string, location: array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}}>
     */
    private function contactInfoChecks(?ContactInfoResult $contactInfo): array
    {
        if ($contactInfo === null) {
            return [];
        }

        return [
            [
                'name' => 'Emails found',
                'status' => $contactInfo->emails === [] ? 'warning' : 'pass',
                'note' => $contactInfo->emails === []
                    ? null
                    : implode(', ', array_column($contactInfo->emails, 'value')),
                'location' => $this->location(null, null, array_map(
                    static fn (array $email): array => [
                        'url' => null,
                        'domPath' => null,
                        'detail' => "{$email['value']} — found on {$email['sourceUrl']}",
                    ],
                    $contactInfo->emails,
                )),
            ],
            [
                'name' => 'Phones found',
                'status' => $contactInfo->phones === [] ? 'warning' : 'pass',
                'note' => $contactInfo->phones === []
                    ? null
                    : implode(', ', array_column($contactInfo->phones, 'value')),
                'location' => $this->location(null, null, array_map(
                    static fn (array $phone): array => [
                        'url' => null,
                        'domPath' => null,
                        'detail' => "{$phone['value']} — found on {$phone['sourceUrl']}",
                    ],
                    $contactInfo->phones,
                )),
            ],
            [
                'name' => 'Social profiles found',
                'status' => $contactInfo->socialProfiles === [] ? 'warning' : 'pass',
                'note' => $contactInfo->socialProfiles === []
                    ? null
                    : implode(', ', array_keys($contactInfo->socialProfiles)),
                // Social profile links aren't tracked per-source-page —
                // ContactInfoExtractor::extractSocialProfiles() only
                // records the first matching URL per platform, not which
                // page it was found on — so there's no page-level location
                // to surface here. The profile URLs themselves are real
                // data though, so they're still surfaced as affected
                // elements (one per platform) so the dashboard's existing
                // "N affected elements" link list shows each clickable
                // profile URL, not just the bare platform name.
                'location' => $this->location(null, null, array_map(
                    static fn (string $platform, string $url): array => [
                        'url' => $url,
                        'domPath' => null,
                        'detail' => ucfirst($platform),
                    ],
                    array_keys($contactInfo->socialProfiles),
                    array_values($contactInfo->socialProfiles),
                )),
            ],
            [
                'name' => 'Team members identified',
                'status' => $contactInfo->teamMembers === [] ? 'warning' : 'pass',
                'note' => $contactInfo->teamMembers === []
                    ? null
                    : implode(', ', array_column($contactInfo->teamMembers, 'name')),
                'location' => $this->location(null, null, array_map(
                    static fn (array $member): array => [
                        'url' => $member['linkedinUrl'] ?? null,
                        'domPath' => null,
                        'detail' => trim(($member['name'] ?? '').' — found on '.($member['sourceUrl'] ?? '')),
                    ],
                    $contactInfo->teamMembers,
                )),
            ],
        ];
    }

    /**
     * @return array<int, array{name: string, status: string, note: ?string, location: array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}}>
     */
    private function reviewPresenceChecks(?ReviewPresenceResult $reviewPresence): array
    {
        if ($reviewPresence === null) {
            return [];
        }

        $checks = [];

        foreach ($reviewPresence->platforms as $platform => $profileUrl) {
            $checks[] = [
                'name' => 'Review presence: '.ucfirst($platform),
                'status' => $profileUrl === null ? 'warning' : 'pass',
                'note' => $profileUrl,
                'location' => $this->location(
                    $reviewPresence->platformSourcePages[$platform] ?? null,
                    null,
                    $profileUrl !== null ? [['url' => $profileUrl, 'domPath' => null, 'detail' => null]] : null,
                ),
            ];
        }

        return $checks;
    }

    /**
     * @param  array<int, TechnologyUpgradeOpportunity>  $opportunities
     * @return array<int, array{name: string, status: string, note: ?string, location: array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}}>
     */
    private function technologyUpgradeChecks(array $opportunities): array
    {
        return array_map(
            fn (TechnologyUpgradeOpportunity $opportunity): array => [
                'name' => $opportunity->technology.($opportunity->detectedVersion !== null
                    ? ' ('.$opportunity->detectedVersion.')'
                    : ''),
                // Every entry here only exists because
                // TechnologyUpgradeAnalyzer already decided it's an
                // upgrade-worthy finding — 'warning' flags it as a real
                // finding worth a look, without inventing a severity
                // (critical/etc.) TechnologyUpgradeOpportunity itself
                // doesn't carry.
                'status' => 'warning',
                'note' => $opportunity->reason,
                // TechnologyUpgradeOpportunity carries no page/element
                // location data of its own, so this is always the empty
                // shape.
                'location' => $this->location(null),
            ],
            $opportunities,
        );
    }

    /**
     * Built only from real counts already on the five inputs — never a
     * canned phrase — same rule the SEO summary above follows. Prefers
     * $prospectQualification->summary when available since that's
     * already a real, already-computed sentence covering the same
     * ground; otherwise falls back to listing whichever of the other
     * four real counts are non-zero.
     *
     * @param  array<int, TechnologyUpgradeOpportunity>  $technologyUpgradeOpportunities
     */
    private function leadIntelligenceSummary(
        ?ProspectQualificationResult $prospectQualification,
        ?BusinessSignalsResult $businessSignals,
        ?ContactInfoResult $contactInfo,
        ?ReviewPresenceResult $reviewPresence,
        array $technologyUpgradeOpportunities,
    ): string {
        if ($prospectQualification !== null) {
            return $prospectQualification->summary;
        }

        $parts = [];

        if ($businessSignals !== null) {
            $detected = count(array_filter($businessSignals->signals));
            $parts[] = sprintf('%d business signal(s) detected', $detected);
        }

        if ($contactInfo !== null) {
            $contactCount = count($contactInfo->emails) + count($contactInfo->phones)
                + count($contactInfo->socialProfiles) + count($contactInfo->teamMembers);
            $parts[] = sprintf('%d contact detail(s) found', $contactCount);
        }

        if ($reviewPresence !== null) {
            $linked = count(array_filter($reviewPresence->platforms));
            $parts[] = sprintf('%d review platform(s) linked', $linked);
        }

        if ($technologyUpgradeOpportunities !== []) {
            $parts[] = sprintf('%d technology upgrade opportunity(ies) identified', count($technologyUpgradeOpportunities));
        }

        return $parts === [] ? 'No lead intelligence data available yet.' : implode('; ', $parts).'.';
    }

    /**
     * Builds the standard 'location' shape every check entry carries —
     * see this class's own docblock. $affectedElements entries are
     * normalized to always carry 'url'/'domPath'/'detail' keys (even
     * when the source DTO's own shape omits one, e.g. UiUxElementResult
     * only ever supplies domPath/detail, never url) so every consumer
     * of this shape can rely on all three keys always being present.
     *
     * @param  ?array<int, array{url?: ?string, domPath?: ?string, detail?: ?string}>  $affectedElements
     * @return array{page_url: ?string, dom_path: ?string, affected_elements: array<int, array{url: ?string, domPath: ?string, detail: ?string}>}
     */
    private function location(?string $pageUrl, ?string $domPath = null, ?array $affectedElements = null): array
    {
        return [
            'page_url' => $pageUrl,
            'dom_path' => $domPath,
            'affected_elements' => array_map(
                static fn (array $element): array => [
                    'url' => $element['url'] ?? null,
                    'domPath' => $element['domPath'] ?? null,
                    'detail' => $element['detail'] ?? null,
                ],
                $affectedElements ?? [],
            ),
        ];
    }
}