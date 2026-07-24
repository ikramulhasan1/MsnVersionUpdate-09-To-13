<?php

namespace App\Http\Middleware;

use App\Models\RedirectUrl;
use Closure;
use Illuminate\Http\Request;

class RedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currentUrl = url($request->path()); // Get the full URL path

        // Check if the submitted URL exists in the database
        $redirect = RedirectUrl::where('submitted_url', $currentUrl)->first();

        if ($redirect) {
            return redirect()->away($redirect->redirect_to, 301); // 301 Permanent Redirect
        }

        return $next($request);
    }
}
