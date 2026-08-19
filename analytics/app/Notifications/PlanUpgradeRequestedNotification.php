<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PlanUpgradeRequest;
use Illuminate\Notifications\Notification;

/**
 * Phase N5 (Dynamic Pricing/Subscription) — sent to EVERY user holding
 * the Admin role (see
 * App\Http\Controllers\SubscriptionController::requestUpgrade()'s own
 * dispatch loop) when someone submits a new PlanUpgradeRequest. The
 * SAME 'database' notification channel/bell dropdown every other
 * notification in this app already uses (Phase N2) — nothing new to
 * learn for whoever's reading it.
 */
final class PlanUpgradeRequestedNotification extends Notification
{
    public function __construct(
        private readonly PlanUpgradeRequest $request,
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
        $user = $this->request->user;
        $plan = $this->request->plan;

        return [
            'title' => 'Plan upgrade requested',
            'message' => "{$user->name} ({$user->email}) requested to upgrade to {$plan->name}.",
            'url' => route('admin.users.edit', $user),
            'icon' => 'trending-up',
        ];
    }
}