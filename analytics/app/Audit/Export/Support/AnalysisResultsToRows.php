<?php

declare(strict_types=1);

namespace App\Audit\Export\Support;

use App\Audit\AIRecommendation\DTO\AnalysisResults;
use App\Audit\Export\DTO\AnalysisRow;
use App\Audit\Export\DTO\BusinessAnalysisRow;
use App\Audit\Export\DTO\LeadIntelligenceRow;
use App\Audit\Export\DTO\ScoreRow;
use App\Audit\Export\DTO\TechnologyRow;
use App\Audit\Technology\DTO\TechnologyUpgradeOpportunity;
use Illuminate\Support\Collection;

/**
 * Flattens an AnalysisResults bundle into the row shapes the Excel
 * export worksheets need (see ScoreRow, AnalysisRow, TechnologyRow, and
 * BusinessAnalysisRow).
 *
 * Kept separate from the Sheet export classes so the Excel-specific
 * concerns (headings, titles, sheet layout) never have to know how each
 * analyzer's DTO is structured, and so the analyzer DTOs never have to
 * know anything about Excel — this class is the only thing that
 * translates between the two.
 *
 * scores() and analysis() (Prompt 16.1) are unchanged; technologyStack()
 * and businessAnalysis() (Prompt 16.2) are purely additive alongside
 * them. Recommendations are deliberately not mapped here — they come
 * from a separate AIRecommendationResult object, not a field on
 * AnalysisResults, and are handled by RecommendationResultToRows
 * instead. leadIntelligence() (Group U) is likewise purely additive —
 * see its own docblock.
 *
 * Deliberately excludes, for this export pass:
 *   - every `recommendation` / `suggestions` field on the analyzers
 *     mapped by scores()/analysis()
 *   - anything chart- or summary-shaped
 *
 * Extend the relevant method here (not the Sheet classes) when those are
 * ready to be exported.
 */
final class AnalysisResultsToRows
{
    /**
     * @return Collection<int, ScoreRow>
     */
    public function scores(AnalysisResults $results): Collection
    {
        return collect([
            $results->seo === null ? null : new ScoreRow(
                category: 'SEO',
                score: $results->seo->averageScore,
                grade: null,
                analyzedAt: $results->seo->analyzedAt,
            ),
            $results->security === null ? null : new ScoreRow(
                category: 'Security',
                score: $results->security->score,
                grade: $results->security->grade,
                analyzedAt: $results->security->analyzedAt,
            ),
            $results->performance === null ? null : new ScoreRow(
                category: 'Performance',
                score: $results->performance->score,
                grade: $results->performance->grade,
                analyzedAt: $results->performance->analyzedAt,
            ),
            $results->accessibility === null ? null : new ScoreRow(
                category: 'Accessibility',
                score: $results->accessibility->score,
                grade: $results->accessibility->grade,
                analyzedAt: $results->accessibility->analyzedAt,
            ),
            $results->content === null ? null : new ScoreRow(
                category: 'Content',
                score: $results->content->score,
                grade: $results->content->grade,
                analyzedAt: $results->content->analyzedAt,
            ),
            $results->uiUx === null ? null : new ScoreRow(
                category: 'UI/UX',
                score: $results->uiUx->score,
                grade: $results->uiUx->grade,
                analyzedAt: $results->uiUx->analyzedAt,
            ),
        ])->filter()->values();
    }

    /**
     * @return Collection<int, AnalysisRow>
     */
    public function analysis(AnalysisResults $results): Collection
    {
        $rows = collect();

        if ($results->security !== null) {
            foreach ($results->security->checks as $check) {
                $rows->push(new AnalysisRow(
                    'Security',
                    $check->check,
                    $check->value,
                    $check->status->value,
                    $check->pageUrl,
                    $this->flattenLocation(null, $check->affectedElements),
                ));
            }
        }

        if ($results->accessibility !== null) {
            foreach ($results->accessibility->checks as $check) {
                $rows->push(new AnalysisRow(
                    'Accessibility',
                    $check->check,
                    $check->value,
                    $check->status->value,
                    $check->pageUrl,
                    $this->flattenLocation(null, $check->affectedElements),
                ));
            }
        }

        if ($results->content !== null) {
            foreach ($results->content->checks as $check) {
                $rows->push(new AnalysisRow(
                    'Content',
                    $check->metric,
                    $check->value,
                    $check->status->value,
                    $check->pageUrl,
                    $this->flattenLocation(null, $check->affectedElements),
                ));
            }
        }

        if ($results->uiUx !== null) {
            foreach ($results->uiUx->elements as $element) {
                $rows->push(new AnalysisRow(
                    category: 'UI/UX',
                    check: $element->element,
                    value: $element->issues === [] ? null : implode('; ', $element->issues),
                    status: $element->status->value,
                    pageUrl: $element->pageUrl,
                    elementLocation: $this->flattenLocation(null, $element->affectedElements),
                ));
            }
        }

        if ($results->performance !== null) {
            foreach ($results->performance->metrics as $metric => $value) {
                $affectedResource = is_array($value) ? ($value['affected_resource'] ?? null) : null;

                $rows->push(new AnalysisRow(
                    category: 'Performance',
                    check: (string) $metric,
                    value: is_scalar($value) ? (string) $value : json_encode($value),
                    status: '',
                    pageUrl: $results->performance->url,
                    elementLocation: $affectedResource,
                ));
            }
        }

        if ($results->seo !== null) {
            foreach ($results->seo->pages as $page) {
                foreach ($page->issues as $issue) {
                    $rows->push(new AnalysisRow(
                        'SEO',
                        $issue->check,
                        $issue->message,
                        $issue->severity->value,
                        $issue->pageUrl,
                        $this->flattenLocation(
                            null,
                            $issue->elementUrl !== null || $issue->domPath !== null || $issue->context !== null
                                ? [['url' => $issue->elementUrl, 'domPath' => $issue->domPath, 'detail' => $issue->context]]
                                : null,
                        ),
                    ));
                }
            }
        }

        return $rows;
    }

    /**
     * Flattens a check's DOM path / affected-elements location data into
     * a single human-readable string for a worksheet cell — the Excel
     * counterpart to AnalysisResultsToDashboardCategories::location(),
     * which keeps the same data structured for the dashboard view
     * instead. Each affected element renders as "domPath — detail —
     * url" (whichever parts are present), multiple elements joined with
     * "; ". Null when there's nothing to show.
     *
     * @param ?array<int, array{url?: ?string, domPath?: ?string, detail?: ?string}> $affectedElements
     */
    private function flattenLocation(?string $domPath, ?array $affectedElements): ?string
    {
        $parts = [];

        if ($domPath !== null && $domPath !== '') {
            $parts[] = $domPath;
        }

        foreach ($affectedElements ?? [] as $element) {
            $bits = array_filter([
                $element['domPath'] ?? null,
                $element['detail'] ?? null,
                $element['url'] ?? null,
            ], static fn (?string $bit): bool => $bit !== null && $bit !== '');

            if ($bits !== []) {
                $parts[] = implode(' — ', $bits);
            }
        }

        $parts = array_values(array_unique($parts));

        return $parts === [] ? null : implode('; ', $parts);
    }

    /**
     * Every detected entry from TechnologyResult::$technologyStack,
     * enriched with that technology's confidence score from
     * $detections (looked up by slug). Empty when technology detection
     * hasn't run.
     *
     * @return Collection<int, TechnologyRow>
     */
    public function technologyStack(AnalysisResults $results): Collection
    {
        if ($results->technology === null) {
            return collect();
        }

        $detections = $results->technology->detections;

        return collect($results->technology->technologyStack)
            ->map(static function (array $entry) use ($detections): TechnologyRow {
                $slug = $entry['slug'] ?? null;

                return new TechnologyRow(
                    technology: (string) ($entry['technology'] ?? ''),
                    category: (string) ($entry['category'] ?? ''),
                    version: $entry['version'] ?? null,
                    confidenceScore: $slug !== null ? ($detections[$slug]->confidenceScore ?? null) : null,
                );
            })
            ->values();
    }

    /**
     * Every Website Health issue across every category on
     * BusinessOpportunityResult::$websiteHealth (Website Problems, SEO
     * Issues, Performance Issues, Website Modernization, Marketing
     * Analysis, Content & Conversion Analysis), with the category slug
     * turned into a readable label. Empty when the Business Opportunity
     * analyzer hasn't run.
     *
     * @return Collection<int, BusinessAnalysisRow>
     */
    public function businessAnalysis(AnalysisResults $results): Collection
    {
        if ($results->businessOpportunity === null) {
            return collect();
        }

        $rows = collect();

        foreach ($results->businessOpportunity->websiteHealth as $categorySlug => $issues) {
            $categoryLabel = ucwords(str_replace('_', ' ', (string) $categorySlug));

            foreach ($issues as $issue) {
                $rows->push(new BusinessAnalysisRow(
                    category: $categoryLabel,
                    issue: $issue->issue,
                    status: $issue->status->value,
                    severity: $issue->severity->value,
                    recommendation: $issue->recommendation,
                ));
            }
        }

        return $rows->values();
    }

    /**
     * Every fact from ProspectQualificationResult, BusinessSignalsResult,
     * ContactInfoResult, and AnalysisResults::$technologyUpgradeOpportunities
     * — the same four real, already-computed inputs
     * AnalysisResultsToDashboardCategories::leadIntelligence() draws
     * from for the dashboard card — flattened into LeadIntelligenceRow's
     * (section, item, value, detail) shape. Each source section is
     * simply omitted (not padded with blank rows) when that source is
     * null/empty, so this worksheet can be empty overall when none of
     * the four ran, the same way businessAnalysis() above is empty when
     * the Business Opportunity analyzer hasn't run.
     *
     * @return Collection<int, LeadIntelligenceRow>
     */
    public function leadIntelligence(AnalysisResults $results): Collection
    {
        $rows = collect();

        if ($results->prospectQualification !== null) {
            $qualification = $results->prospectQualification;

            $rows->push(new LeadIntelligenceRow('Prospect Qualification', 'Score', (string) $qualification->score, $qualification->summary));
            $rows->push(new LeadIntelligenceRow('Prospect Qualification', 'Grade', $qualification->grade, null));
            $rows->push(new LeadIntelligenceRow('Prospect Qualification', 'Priority', $qualification->priority(), null));

            foreach ($qualification->breakdown as $bucket => $points) {
                $rows->push(new LeadIntelligenceRow(
                    'Prospect Qualification',
                    ucwords(str_replace('_', ' ', (string) $bucket)).' Points',
                    (string) $points,
                    null,
                ));
            }
        }

        if ($results->businessSignals !== null) {
            $businessSignals = $results->businessSignals;

            foreach ($businessSignals->signals as $signal => $detected) {
                $rows->push(new LeadIntelligenceRow(
                    'Business Signals',
                    ucwords(str_replace('_', ' ', (string) $signal)),
                    $detected ? 'Yes' : 'No',
                    $businessSignals->signalDetails[$signal] ?? null,
                ));
            }
        }

        if ($results->contactInfo !== null) {
            $contactInfo = $results->contactInfo;

            $rows->push(new LeadIntelligenceRow(
                'Contacts Found',
                'Emails',
                (string) count($contactInfo->emails),
                $contactInfo->emails === [] ? null : implode(', ', array_column($contactInfo->emails, 'value')),
            ));
            $rows->push(new LeadIntelligenceRow(
                'Contacts Found',
                'Phones',
                (string) count($contactInfo->phones),
                $contactInfo->phones === [] ? null : implode(', ', array_column($contactInfo->phones, 'value')),
            ));
            $rows->push(new LeadIntelligenceRow(
                'Contacts Found',
                'Social Profiles',
                (string) count($contactInfo->socialProfiles),
                $contactInfo->socialProfiles === [] ? null : implode(', ', array_map(
                    static fn (string $platform, string $url): string => "{$platform}: {$url}",
                    array_keys($contactInfo->socialProfiles),
                    array_values($contactInfo->socialProfiles),
                )),
            ));

            foreach ($contactInfo->teamMembers as $member) {
                $rows->push(new LeadIntelligenceRow(
                    'Contacts Found',
                    'Team Member',
                    (string) ($member['name'] ?? ''),
                    $member['title'] ?? null,
                ));
            }
        }

        foreach ($results->technologyUpgradeOpportunities as $opportunity) {
            /** @var TechnologyUpgradeOpportunity $opportunity */
            $rows->push(new LeadIntelligenceRow(
                'Technology Upgrade Opportunities',
                $opportunity->technology,
                $opportunity->detectedVersion,
                trim($opportunity->reason.' — Suggested: '.$opportunity->suggestedService),
            ));
        }

        return $rows->values();
    }
}