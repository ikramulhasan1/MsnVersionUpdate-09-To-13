<?php

declare(strict_types=1);

namespace App\Audit\Contacts\DTO;

final readonly class ContactInfoResult implements \JsonSerializable
{
    /**
     * @param array<int, string> $emails deduplicated email addresses found
     *        on-site, with common false positives (image filenames like
     *        photo@2x.png, placeholder addresses like example@example.com)
     *        already excluded
     * @param array<int, string> $phones deduplicated phone numbers found
     *        on-site, in whatever format they appeared in — see
     *        ContactInfoExtractor's docblock for this field's known
     *        limitations (no international-format normalization)
     * @param array<string, string> $socialProfiles keyed by platform
     *        (linkedin, facebook, instagram, twitter, youtube) — value is
     *        the profile URL found on-site. A platform is omitted
     *        entirely, not present with an empty value, when no link to
     *        it was found.
     * @param array<int, array{name: string, title: ?string, linkedinUrl: ?string, sourceUrl: string}> $teamMembers
     *        only ever populated from pages whose URL/title clearly
     *        signals a team/about page, and only where the page's own
     *        structure gave a clear name+role signal — never guessed
     *        from arbitrary prose (see ContactInfoExtractor's docblock:
     *        a wrong name attributed to a real person is a real-world
     *        harm, not just a data-quality bug, so under-extracting is
     *        preferred over guessing).
     */
    public function __construct(
        public string $url,
        public array $emails,
        public array $phones,
        public array $socialProfiles,
        public array $teamMembers,
        public string $analyzedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'emails' => $this->emails,
            'phones' => $this->phones,
            'social_profiles' => $this->socialProfiles,
            'team_members' => $this->teamMembers,
            'analyzed_at' => $this->analyzedAt,
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}