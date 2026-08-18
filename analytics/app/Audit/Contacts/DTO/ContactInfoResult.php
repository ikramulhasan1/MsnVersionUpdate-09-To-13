<?php

declare(strict_types=1);

namespace App\Audit\Contacts\DTO;

final readonly class ContactInfoResult implements \JsonSerializable
{
    /**
     * @param  array<int, array{value: string, sourceUrl: string, source: string, isBusinessDomain: bool}>  $emails  deduplicated
     *                                                                       email addresses found on-site, with common false positives (image
     *                                                                       filenames like photo@2x.png, placeholder addresses like
     *                                                                       example@example.com) already excluded. Each entry's sourceUrl is
     *                                                                       the first crawled page the address was found linked from (a
     *                                                                       mailto: link) — deduplication is by address only, so an address
     *                                                                       repeated across multiple pages keeps the first page it appeared
     *                                                                       on, the same "first occurrence wins" rule teamMembers already
     *                                                                       follows for a given name.
     *
     *                                                                       Phase M5 added two fields: `source` is either 'mailto' (found
     *                                                                       via a real &lt;a href="mailto:..."&gt; link — higher
     *                                                                       confidence) or 'plain_text' (found via a regex scan of the
     *                                                                       page's own visible text with no mailto: link around it — see
     *                                                                       App\Audit\Fetching\HtmlParser::parsePlainTextEmails()'s own
     *                                                                       docblock for why that source exists at all). `isBusinessDomain`
     *                                                                       is true when the email's own domain matches the audited
     *                                                                       website's own domain (e.g. info@example.com found while
     *                                                                       auditing example.com), as opposed to a personal/free-provider
     *                                                                       address that happens to be published on the site. $emails is
     *                                                                       sorted with isBusinessDomain=true entries first
     *                                                                       (mailto-sourced before plain-text-sourced within each group),
     *                                                                       so `$emails[0] ?? null` is always this extractor's own single
     *                                                                       best guess at the real business contact address, not merely
     *                                                                       whichever mailto: link happened to appear first in the HTML.
     * @param  array<int, array{value: string, sourceUrl: string}>  $phones  deduplicated
     *                                                                       phone numbers found on-site, in whatever format they appeared in
     *                                                                       — see ContactInfoExtractor's docblock for this field's known
     *                                                                       limitations (no international-format normalization). Each
     *                                                                       entry's sourceUrl follows the same first-occurrence rule as
     *                                                                       $emails above.
     * @param  array<string, string>  $socialProfiles  keyed by platform
     *                                                 (linkedin, facebook, instagram, twitter, youtube) — value is
     *                                                 the profile URL found on-site. A platform is omitted
     *                                                 entirely, not present with an empty value, when no link to
     *                                                 it was found.
     * @param  array<int, array{name: string, title: ?string, linkedinUrl: ?string, sourceUrl: string}>  $teamMembers
     *                                                                                                                 only ever populated from pages whose URL/title clearly
     *                                                                                                                 signals a team/about page, and only where the page's own
     *                                                                                                                 structure gave a clear name+role signal — never guessed
     *                                                                                                                 from arbitrary prose (see ContactInfoExtractor's docblock:
     *                                                                                                                 a wrong name attributed to a real person is a real-world
     *                                                                                                                 harm, not just a data-quality bug, so under-extracting is
     *                                                                                                                 preferred over guessing).
     */
    public function __construct(
        public string $url,
        public array $emails,
        public array $phones,
        public array $socialProfiles,
        public array $teamMembers,
        public string $analyzedAt,
    ) {}

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