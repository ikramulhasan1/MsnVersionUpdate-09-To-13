<?php

declare(strict_types=1);

namespace App\Audit\ReviewPresence\DTO;

final readonly class ReviewPresenceResult implements \JsonSerializable
{
    /**
     * @param array<string, ?string> $platforms keyed by clutch, g2,
     *        goodfirms, google — value is the profile/listing URL found
     *        linked from the site, or null when no such link was found.
     *        This is presence detection only (does the site itself link
     *        out to a review profile) — it is NOT review data (rating,
     *        review count) fetched from those platforms, since that
     *        would require each platform's own API and ToS agreement,
     *        which this class does not have.
     */
    public function __construct(
        public string $url,
        public array $platforms,
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
            'platforms' => $this->platforms,
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