<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phase N2 (Dynamic Notification System) — every method here operates
 * ONLY on auth()->user()->notifications (Laravel's own built-in
 * morphMany relation, provided automatically by the Notifiable trait
 * already on App\Models\User) — never a raw DatabaseNotification
 * query, so there is no way for one person to read, mark, or delete
 * another person's own notifications through any route this
 * controller exposes.
 */
final class NotificationController extends Controller
{
    /**
     * Backs the "All Notifications" page — every notification, newest
     * first, paginated. Unlike recent()/unreadCount() below (used by
     * the sidebar's own bell dropdown, which only ever needs a quick
     * recent slice), this is the one place a person can see their
     * FULL notification history.
     */
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(20),
        ]);
    }

    /**
     * Backs the sidebar's own bell dropdown — polled periodically by
     * public/js/notifications.js rather than rendered server-side on
     * every page load, so the unread badge/count stays current without
     * needing a full page reload. Capped at 8: a dropdown showing more
     * than a handful defeats its own "quick glance" purpose — the
     * "View All" link at its bottom is the real path to everything
     * older.
     */
    public function recent(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->limit(8)->get();

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications->map(static fn ($notification): array => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'icon' => $notification->data['icon'] ?? null,
                'read' => $notification->read_at !== null,
                'created_at' => $notification->created_at?->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Marks ONE notification read, then redirects to its own ->url —
     * the actual behavior clicking a notification in the bell dropdown
     * or the All Notifications list triggers (a plain <a> POSTing here
     * first via a tiny form, not an AJAX call, so this works even with
     * JavaScript disabled).
     */
    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url !== null ? redirect()->to($url) : redirect()->route('notifications.index');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    public function destroy(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->findOrFail($notification)->delete();

        return back();
    }
}