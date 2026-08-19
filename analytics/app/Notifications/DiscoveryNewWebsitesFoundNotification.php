<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Phase N2 (Dynamic Notification System) — dispatched from
 * App\Discovery\Jobs\DiscoverWebsitesJob once ingestion finishes, ONLY
 * when $newWebsiteCount > 0 (a "Discover More" click that genuinely
 * found nothing new stays silent — a notification saying "found 0 new
 * websites" would be pure noise, never useful information). See that
 * job's own docblock for how $userId is threaded through from
 * whoever's browser actually clicked "Discover More"
 * (App\Http\Controllers\DiscoveryController::discover()) — a
 * SCHEDULED search run (no one's browser involved) has no $userId to
 * notify at all, so this notification is skipped entirely for that
 * case rather than guessing a recipient.
 */
final class DiscoveryNewWebsitesFoundNotification extends Notification
{
    public function __construct(
        private readonly int $newWebsiteCount,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New websites discovered',
            'message' => $this->newWebsiteCount === 1
                ? 'Discover More found 1 new website matching your filters.'
                : "Discover More found {$this->newWebsiteCount} new websites matching your filters.",
            'url' => route('discovery.index'),
            'icon' => 'search',
        ];
    }
}