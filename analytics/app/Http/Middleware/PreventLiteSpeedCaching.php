<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied to the entire `discovery` route group (see routes/web.php).
 *
 * A real production incident, not a precaution: this app is deployed
 * behind LiteSpeed Web Server with LiteSpeed Cache active (confirmed by
 * an `x-lscache: 1` response header seen on this exact host). LSCache's
 * full-page cache can cache an entire dynamic HTML response — not just
 * static JS/CSS files — keyed by URL. Once that happens, deploying a
 * bug fix (new PHP, new JS, new cache-busted asset URLs baked into the
 * HTML) has no effect until the STALE cached page itself expires or is
 * purged: the browser is shown the old page, script tags and all, no
 * matter how correct the files on disk now are. This exact scenario is
 * what made an earlier round of fixes to
 * public/js/discovery-search-panel.js (the Sub-Niche/Region/City
 * cascade, "Save this search") appear not to have taken effect in
 * production even though the deployed files were correct.
 *
 * Every discovery/* response now carries standard Cache-Control
 * directives instructing browsers/proxies never to cache it, plus
 * LiteSpeed's own `X-LiteSpeed-Cache-Control: no-cache` response header
 * — LSCache reads that header specifically to decide whether to store
 * a response, and honors it going forward. This does NOT retroactively
 * clear anything already cached from before this middleware existed —
 * see this module's own deployment notes for the one-time manual
 * LSCache purge (via hosting control panel) still needed after first
 * deploying this fix.
 */
final class PreventLiteSpeedCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

        return $response;
    }
}