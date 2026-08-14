<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * What kind of site a discovered domain is — one of the "Technology"-
 * adjacent advanced filters the Website Discovery module's search UI
 * exposes (Industry/Niche is the business's subject matter; this is
 * the site's own structural category, e.g. an accounting firm's
 * corporate brochure site vs. its client-facing e-commerce storefront).
 * Cast on App\Models\DiscoveredWebsite::$website_type.
 */
enum WebsiteType: string
{
    case ECOMMERCE = 'ecommerce';
    case CORPORATE = 'corporate';
    case BLOG = 'blog';
    case PORTFOLIO = 'portfolio';
    case SAAS = 'saas';
    case DIRECTORY = 'directory';
    case NONPROFIT = 'nonprofit';
    case LANDING_PAGE = 'landing_page';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ECOMMERCE => 'E-commerce',
            self::CORPORATE => 'Corporate',
            self::BLOG => 'Blog',
            self::PORTFOLIO => 'Portfolio',
            self::SAAS => 'SaaS',
            self::DIRECTORY => 'Directory',
            self::NONPROFIT => 'Nonprofit',
            self::LANDING_PAGE => 'Landing Page',
            self::OTHER => 'Other',
        };
    }
}