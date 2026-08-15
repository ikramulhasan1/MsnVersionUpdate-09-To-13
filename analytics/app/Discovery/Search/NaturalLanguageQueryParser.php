<?php

declare(strict_types=1);

namespace App\Discovery\Search;

use App\Audit\Technology\TechnologyDetector;
use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\SocialPlatform;
use App\Discovery\Geo\Contracts\GeoLookupServiceInterface;
use App\Discovery\Search\Contracts\NaturalLanguageQueryParserInterface;
use App\Discovery\Search\DTO\DiscoveryFilterCriteria;
use App\Discovery\Taxonomy\IndustryTaxonomyService;

/**
 * A rule-based (regex/keyword) implementation of
 * NaturalLanguageQueryParserInterface — see that interface's own
 * docblock for why it exists as a swappable contract rather than this
 * class being called directly, and for the future LLM-backed
 * implementation it's meant to make an easy swap.
 *
 * What this class can recognize, and how:
 *   - Industry/Sub-Niche: a literal substring match (case-insensitive)
 *     against every name IndustryTaxonomyService knows, tried as a
 *     full match first ("Restaurant & Food Service") and falling back
 *     to just the name's first word ("Restaurant") so a query doesn't
 *     have to spell out the taxonomy's full label.
 *   - Country: a literal substring match against every country name
 *     GeoLookupServiceInterface::countries() knows — resolves to that
 *     country's ISO code (matching what the manual Country <select>
 *     filter itself submits — see search-panel.blade.php — so an
 *     NL-parsed country behaves identically to a manually-picked one).
 *   - City: a "in <Capitalized Word(s)>" pattern, taken as a best-
 *     effort free-text guess (unverified against any known list — see
 *     GeoLookupServiceInterface's own docblock for why no city list
 *     exists in this module yet) UNLESS that phrase is itself a
 *     recognized country name, in which case it's left to the country
 *     matcher instead.
 *   - Technology: a literal substring match against every name in
 *     TechnologyDetector::TECHNOLOGY_NAMES, bucketed into a filter
 *     group (cms/framework/ecommerce_platform/cdn) via
 *     TechnologyDetector::CATEGORY_MAP — the exact same vocabulary/
 *     grouping App\Discovery\Taxonomy\TechnologyFilterOptions already
 *     reuses for the manual Technology filter checkboxes.
 *   - Website Quality: a "<category> below|under|less than <N>" or
 *     "<category> above|over|greater than <N>" pattern, one per
 *     SEO/Performance/Security/Accessibility.
 *   - Social Media presence: a "with|has|have <platform>" or
 *     "without|no|missing <platform>" pattern, one per known
 *     App\Discovery\Enums\SocialPlatform.
 *   - Contact Availability: "no contact information", "has an email",
 *     "has a phone", "has a contact form".
 *
 * Deliberately does NOT attempt: Region, Business Size, Traffic, Last
 * Updated, Domain Age, Radius, Website Status, Opportunity, or specific
 * SEO/Security issues — none of these have a reliable enough
 * keyword/phrase pattern to recognize without meaningfully more rules
 * (or the LLM-backed implementation this interface is designed to make
 * swappable later); a query mentioning them simply won't set that
 * field, the same "don't represent what can't honestly be built"
 * principle this module's other filters already follow.
 */
final class NaturalLanguageQueryParser implements NaturalLanguageQueryParserInterface
{
    /**
     * Search phrase (lowercase, as typed by a user) => the
     * App\Discovery\Enums\SocialPlatform it refers to. A separate map
     * from SocialPlatform::label() rather than matching labels
     * directly: "Twitter / X" (the label shown in the filter UI) isn't
     * how someone would type it in a sentence, and "x" alone is too
     * short/ambiguous to safely regex-match without this explicit list.
     *
     * @var array<string, SocialPlatform>
     */
    private const array SOCIAL_SEARCH_TERMS = [
        'facebook' => SocialPlatform::FACEBOOK,
        'instagram' => SocialPlatform::INSTAGRAM,
        'twitter' => SocialPlatform::TWITTER,
        'x' => SocialPlatform::TWITTER,
        'linkedin' => SocialPlatform::LINKEDIN,
        'youtube' => SocialPlatform::YOUTUBE,
    ];

    /**
     * Website Quality keyword => the DiscoveryFilterCriteria::$qualityRanges
     * category key it maps to (currently identical, kept as an explicit
     * map rather than assuming so a future synonym, e.g. "SEO"
     * vs. "search", can be added without changing the matching logic).
     *
     * @var array<string, string>
     */
    private const array QUALITY_KEYWORDS = [
        'seo' => 'seo',
        'performance' => 'performance',
        'security' => 'security',
        'accessibility' => 'accessibility',
    ];

    public function __construct(
        private readonly IndustryTaxonomyService $industryTaxonomy,
        private readonly GeoLookupServiceInterface $geoLookup,
    ) {
    }

    public function parse(string $query): DiscoveryFilterCriteria
    {
        $normalized = trim($query);
        $industry = $this->matchIndustry($normalized);

        return new DiscoveryFilterCriteria(
            industry: $industry,
            subNiche: $industry !== null ? $this->matchSubNiche($normalized, $industry) : null,
            country: $this->matchCountry($normalized),
            city: $this->matchCity($normalized),
            technology: $this->matchTechnology($normalized),
            qualityRanges: $this->matchQualityRanges($normalized),
            socialPlatforms: $this->matchSocialPlatforms($normalized),
            contactAvailability: $this->matchContactAvailability($normalized),
        );
    }

    private function matchIndustry(string $query): ?string
    {
        foreach ($this->industryTaxonomy->industries() as $industry) {
            if ($this->containsPhrase($query, $industry)) {
                return $industry;
            }
        }

        // Fall back to just the industry name's first word ("Restaurant"
        // rather than the taxonomy's full "Restaurant & Food Service"
        // label) so a query doesn't have to spell out the exact label.
        foreach ($this->industryTaxonomy->industries() as $industry) {
            $firstWord = preg_split('/[\s&]+/', $industry)[0] ?? '';

            if ($firstWord !== '' && $this->containsPhrase($query, $firstWord)) {
                return $industry;
            }
        }

        return null;
    }

    private function matchSubNiche(string $query, string $industry): ?string
    {
        foreach ($this->industryTaxonomy->subNiches($industry) as $subNiche) {
            if ($this->containsPhrase($query, $subNiche)) {
                return $subNiche;
            }
        }

        return null;
    }

    private function matchCountry(string $query): ?string
    {
        foreach ($this->geoLookup->countries() as $country) {
            if ($this->containsPhrase($query, $country['name'])) {
                return $country['code'];
            }
        }

        return null;
    }

    private function matchCity(string $query): ?string
    {
        if (preg_match('/\bin\s+([A-Z][a-zA-Z]*(?:\s+[A-Z][a-zA-Z]*)*)/', $query, $matches) !== 1) {
            return null;
        }

        $candidate = trim($matches[1]);

        // A recognized country name following "in" belongs to
        // matchCountry(), not here — e.g. "restaurants in Canada"
        // shouldn't also produce a (wrong) city guess of "Canada".
        return $this->matchCountry($candidate) === null ? $candidate : null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function matchTechnology(string $query): array
    {
        $result = [];

        foreach (TechnologyDetector::TECHNOLOGY_NAMES as $slug => $name) {
            if (! $this->containsPhrase($query, $name)) {
                continue;
            }

            $group = $this->technologyGroupForSlug($slug);

            if ($group !== null) {
                $result[$group][] = $slug;
            }
        }

        return $result;
    }

    /**
     * Mirrors App\Discovery\Taxonomy\TechnologyFilterOptions's own
     * CATEGORY_GROUPS mapping exactly, so a technology mentioned in a
     * natural-language query lands in the same filter group a manual
     * checkbox selection for it would.
     */
    private function technologyGroupForSlug(string $slug): ?string
    {
        return match (TechnologyDetector::CATEGORY_MAP[$slug] ?? null) {
            'CMS' => 'cms',
            'Backend Framework', 'JavaScript Framework', 'CSS Framework' => 'framework',
            'Ecommerce' => 'ecommerce_platform',
            'Infrastructure' => 'cdn',
            default => null,
        };
    }

    /**
     * @return array<string, array{min: int, max: int}>
     */
    private function matchQualityRanges(string $query): array
    {
        $result = [];

        foreach (self::QUALITY_KEYWORDS as $keyword => $category) {
            $pattern = '/\b'.preg_quote($keyword, '/').'\b\s+(below|under|less than|above|over|greater than)\s+(\d{1,3})/i';

            if (preg_match($pattern, $query, $matches) !== 1) {
                continue;
            }

            $direction = strtolower($matches[1]);
            $threshold = min(100, max(0, (int) $matches[2]));

            $result[$category] = in_array($direction, ['below', 'under', 'less than'], true)
                ? ['min' => 0, 'max' => max(0, $threshold - 1)]
                : ['min' => min(100, $threshold + 1), 'max' => 100];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private function matchSocialPlatforms(string $query): array
    {
        $result = [];

        foreach (self::SOCIAL_SEARCH_TERMS as $term => $platform) {
            $quotedTerm = preg_quote($term, '/');

            if (preg_match('/\b(without|no|missing|don\'?t have)\s+(?:an?\s+)?'.$quotedTerm.'\b/i', $query) === 1) {
                $result[$platform->value] = 'missing';

                continue;
            }

            if (preg_match('/\b(with|has|have)\s+(?:an?\s+)?'.$quotedTerm.'\b/i', $query) === 1) {
                $result[$platform->value] = 'has';
            }
        }

        return $result;
    }

    private function matchContactAvailability(string $query): ?ContactAvailability
    {
        if (preg_match('/\b(no|without|missing)\s+contact\s+information\b/i', $query) === 1) {
            return ContactAvailability::NONE;
        }

        if (preg_match('/\b(has|with|have)\s+(?:an?\s+)?email\b/i', $query) === 1) {
            return ContactAvailability::EMAIL;
        }

        if (preg_match('/\b(has|with|have)\s+(?:an?\s+)?phone\b/i', $query) === 1) {
            return ContactAvailability::PHONE;
        }

        if (preg_match('/\b(has|with|have)\s+(?:an?\s+)?contact\s+form\b/i', $query) === 1) {
            return ContactAvailability::CONTACT_FORM;
        }

        return null;
    }

    private function containsPhrase(string $haystack, string $phrase): bool
    {
        return stripos($haystack, $phrase) !== false;
    }
}