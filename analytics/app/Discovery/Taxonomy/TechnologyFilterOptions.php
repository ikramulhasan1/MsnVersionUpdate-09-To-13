<?php

declare(strict_types=1);

namespace App\Discovery\Taxonomy;

use App\Audit\Technology\TechnologyDetector;

/**
 * Groups App\Audit\Technology\TechnologyDetector's own known
 * technology vocabulary (TechnologyDetector::CATEGORY_MAP /
 * ::TECHNOLOGY_NAMES) into the Website Discovery module's Technology
 * filter checkbox groups — CMS, Framework, E-commerce Platform, CDN —
 * so the audit engine and the discovery filter always agree on what a
 * "CMS" or "Framework" is, with no second list to keep in sync by
 * hand: adding a new detectable technology to TechnologyDetector (a
 * new CATEGORY_MAP/TECHNOLOGY_NAMES entry) makes it available here
 * automatically, no Discovery-module change required.
 *
 * Framework deliberately merges three of TechnologyDetector's own
 * category labels (Backend Framework, JavaScript Framework, CSS
 * Framework) into one filter group: discovered_websites.framework
 * (see database/migrations/2026_08_14_000000_create_discovered_websites_table.php)
 * is a single free-text column, not split into backend/frontend/css
 * sub-columns, so the filter group matches that one-column shape
 * rather than TechnologyDetector's finer-grained categorization.
 *
 * Server is NOT covered here: TechnologyDetector has no enumerated
 * server-software vocabulary of its own — TechnologyDetector::serverInfo()
 * only ever surfaces the raw, free-text Server response header, not a
 * fixed list of named products. See App\Discovery\Enums\ServerSoftware
 * for that filter group's own, separately curated (not audit-engine-
 * derived) list.
 */
final class TechnologyFilterOptions
{
    /**
     * Discovery filter group => the TechnologyDetector::CATEGORY_MAP
     * category label(s) it draws from.
     *
     * @var array<string, array<int, string>>
     */
    private const array CATEGORY_GROUPS = [
        'cms' => ['CMS'],
        'framework' => ['Backend Framework', 'JavaScript Framework', 'CSS Framework'],
        'ecommerce_platform' => ['Ecommerce'],
        'cdn' => ['Infrastructure'],
    ];

    /**
     * Every {slug, name} option for one filter group (e.g. 'cms'),
     * drawn live from TechnologyDetector's own vocabulary — an
     * unrecognized group name simply yields no options rather than an
     * exception, the same "unrecognized input has no options, not an
     * error" convention IndustryTaxonomyService::subNiches() and
     * GeoLookupServiceInterface::regionsFor() already use.
     *
     * @return array<int, array{slug: string, name: string}>
     */
    public function forGroup(string $group): array
    {
        $categories = self::CATEGORY_GROUPS[$group] ?? [];

        if ($categories === []) {
            return [];
        }

        $options = [];

        foreach (TechnologyDetector::CATEGORY_MAP as $slug => $category) {
            if (in_array($category, $categories, true)) {
                $options[] = [
                    'slug' => $slug,
                    'name' => TechnologyDetector::TECHNOLOGY_NAMES[$slug] ?? ucfirst($slug),
                ];
            }
        }

        return $options;
    }

    /**
     * Every filter group at once, keyed by group name — for rendering
     * the whole Technology filter section in one pass rather than
     * calling forGroup() once per group.
     *
     * @return array<string, array<int, array{slug: string, name: string}>>
     */
    public function all(): array
    {
        $result = [];

        foreach (array_keys(self::CATEGORY_GROUPS) as $group) {
            $result[$group] = $this->forGroup($group);
        }

        return $result;
    }
}