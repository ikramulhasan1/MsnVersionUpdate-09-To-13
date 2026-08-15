<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

use App\Audit\Security\SecurityAnalyzer;
use App\Audit\Seo\SeoAnalyzerService;
use Illuminate\Support\Str;

/**
 * Reuses the Audit engine's own issue vocabulary —
 * SeoAnalyzerService::ISSUE_LABELS and SecurityAnalyzer::CHECK_NAMES —
 * for the Website Discovery module's Advanced Filters "Specific
 * Issues" checkboxes, so a discovered site's issues are described with
 * exactly the same terms an audit of that same site would use. No
 * separate, hand-duplicated issue list lives in this module; a new SEO
 * code or security check added to either analyzer becomes available
 * here automatically.
 *
 * SEO issues already carry a stable, snake_case machine code
 * (SeoIssue::$code) to use directly as each checkbox's value.
 * Security checks have no such code (see SecurityAnalyzer::CHECK_NAMES's
 * own docblock) — Str::slug() derives one from the check name (e.g.
 * "XSS Protection" -> "xss_protection") purely so the checkbox has a
 * stable, URL/query-string-safe value; it carries no meaning beyond
 * that.
 */
final class IssueFilterOptions
{
    /**
     * @return array<int, array{code: string, label: string}>
     */
    public function seoIssues(): array
    {
        $options = [];

        foreach (SeoAnalyzerService::ISSUE_LABELS as $code => $label) {
            $options[] = ['code' => $code, 'label' => $label];
        }

        return $options;
    }

    /**
     * @return array<int, array{code: string, label: string}>
     */
    public function securityIssues(): array
    {
        $options = [];

        foreach (SecurityAnalyzer::CHECK_NAMES as $checkName) {
            $options[] = ['code' => Str::slug($checkName, '_'), 'label' => $checkName];
        }

        return $options;
    }
}