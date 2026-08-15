<?php

declare(strict_types=1);

namespace App\Discovery\Enums;

/**
 * The Advanced Filters panel's "Social Media" filter group — the exact
 * same five platforms App\Audit\Contacts\ContactInfoExtractor::SOCIAL_PLATFORM_PATTERNS
 * already recognizes when extracting a site's social profile links,
 * kept as matching case values so a discovered site's social presence
 * is described with the same platform vocabulary an audit of that same
 * site would use. PHP enum cases can't be generated from that array
 * directly (enums are fixed at compile time, that array is a runtime
 * value), so these five cases are hand-kept in sync with it rather
 * than derived — update both together if a platform is ever added or
 * removed there.
 *
 * Each platform is filtered independently as a three-state choice (Any
 * / Has / Doesn't Have) rather than a plain checkbox — see
 * resources/views/discovery/partials/search-panel.blade.php's Social
 * Media group — so a search can specifically ask for "has Instagram
 * but no Facebook", not just "has some of these platforms".
 */
enum SocialPlatform: string
{
    case FACEBOOK = 'facebook';
    case INSTAGRAM = 'instagram';
    case TWITTER = 'twitter';
    case LINKEDIN = 'linkedin';
    case YOUTUBE = 'youtube';

    public function label(): string
    {
        return match ($this) {
            self::FACEBOOK => 'Facebook',
            self::INSTAGRAM => 'Instagram',
            self::TWITTER => 'Twitter / X',
            self::LINKEDIN => 'LinkedIn',
            self::YOUTUBE => 'YouTube',
        };
    }
}