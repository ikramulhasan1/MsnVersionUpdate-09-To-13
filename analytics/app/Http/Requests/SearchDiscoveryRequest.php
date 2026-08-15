<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Discovery\Enums\BusinessSize;
use App\Discovery\Enums\ContactAvailability;
use App\Discovery\Enums\LastUpdatedRange;
use App\Discovery\Enums\OpportunityFilter;
use App\Discovery\Enums\ServerSoftware;
use App\Discovery\Enums\TrafficRange;
use App\Discovery\Enums\WebsiteConnectivityStatus;
use App\Discovery\Enums\WebsiteType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the Website Discovery search form
 * (resources/views/discovery/partials/search-panel.blade.php) before
 * App\Http\Controllers\DiscoveryController::search() redirects its
 * values onto the index page's query string — this is what keeps
 * App\Discovery\Search\DTO\DiscoveryFilterCriteria::fromRequestFilters()
 * safe to write leniently (is_string()/is_array() checks, tryFrom()
 * for enums, no thrown exceptions on a bad value): every value it
 * receives has already passed through here first, so its own defensive
 * checks are a second line of defense, not the only one.
 *
 * Every enum-backed field uses Rule::enum() rather than a hand-written
 * in:'value1,value2,...' list, so this file never drifts out of sync
 * with the enum it's validating against — a new
 * App\Discovery\Enums\WebsiteType case, for example, is automatically
 * a valid submission here the moment it's added to that enum, with no
 * change needed in this class.
 */
final class SearchDiscoveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'industry' => ['nullable', 'string', 'max:150'],
            'sub_niche' => ['nullable', 'string', 'max:150'],
            'country' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'radius' => ['nullable', 'integer', 'min:0'],

            'status' => ['nullable', 'array'],
            'status.*' => ['string', Rule::enum(WebsiteConnectivityStatus::class)],

            'website_type' => ['nullable', 'array'],
            'website_type.*' => ['string', Rule::enum(WebsiteType::class)],

            'technology' => ['nullable', 'array'],
            'technology.cms' => ['nullable', 'array'],
            'technology.cms.*' => ['string', 'max:100'],
            'technology.framework' => ['nullable', 'array'],
            'technology.framework.*' => ['string', 'max:100'],
            'technology.ecommerce_platform' => ['nullable', 'array'],
            'technology.ecommerce_platform.*' => ['string', 'max:100'],
            'technology.cdn' => ['nullable', 'array'],
            'technology.cdn.*' => ['string', 'max:100'],
            'technology.server' => ['nullable', 'array'],
            'technology.server.*' => ['string', Rule::enum(ServerSoftware::class)],

            'quality' => ['nullable', 'array'],
            'quality.seo.min' => ['nullable', 'integer', 'between:0,100'],
            'quality.seo.max' => ['nullable', 'integer', 'between:0,100'],
            'quality.performance.min' => ['nullable', 'integer', 'between:0,100'],
            'quality.performance.max' => ['nullable', 'integer', 'between:0,100'],
            'quality.security.min' => ['nullable', 'integer', 'between:0,100'],
            'quality.security.max' => ['nullable', 'integer', 'between:0,100'],
            'quality.accessibility.min' => ['nullable', 'integer', 'between:0,100'],
            'quality.accessibility.max' => ['nullable', 'integer', 'between:0,100'],

            'issue' => ['nullable', 'array'],
            'issue.seo' => ['nullable', 'array'],
            'issue.seo.*' => ['string', 'max:100'],
            'issue.security' => ['nullable', 'array'],
            'issue.security.*' => ['string', 'max:100'],

            'opportunity' => ['nullable', 'array'],
            'opportunity.*' => ['string', Rule::enum(OpportunityFilter::class)],

            'domain_age' => ['nullable', 'array'],
            'domain_age.min' => ['nullable', 'integer', 'min:0'],
            'domain_age.max' => ['nullable', 'integer', 'min:0'],

            'employees' => ['nullable', 'array'],
            'employees.min' => ['nullable', 'integer', 'min:0'],
            'employees.max' => ['nullable', 'integer', 'min:0'],

            'last_updated' => ['nullable', 'string', Rule::enum(LastUpdatedRange::class)],

            'traffic' => ['nullable', 'array'],
            'traffic.*' => ['string', Rule::enum(TrafficRange::class)],

            'business_size' => ['nullable', 'array'],
            'business_size.*' => ['string', Rule::enum(BusinessSize::class)],

            'social' => ['nullable', 'array'],
            'social.facebook' => ['nullable', 'string', 'in:has,missing'],
            'social.instagram' => ['nullable', 'string', 'in:has,missing'],
            'social.twitter' => ['nullable', 'string', 'in:has,missing'],
            'social.linkedin' => ['nullable', 'string', 'in:has,missing'],
            'social.youtube' => ['nullable', 'string', 'in:has,missing'],

            'contact_availability' => ['nullable', 'string', Rule::enum(ContactAvailability::class)],

            'boolean_query' => ['nullable', 'string', 'max:500'],
        ];
    }
}