<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class XSSProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get all request data
        $input = $request->all();

        // Sanitize input
        array_walk_recursive($input, function (&$input) {
            $input = strip_tags(
                str_replace(["&lt;", "&gt;"], '', $input), // Corrected function closure
                '<span><p><a><b><i><u><strong><br><hr><table><tr><th><td><ul><ol><li><h1><h2><h3><h4><h5><h6><del><ins><sup><sub><pre><address><img><figure><embed><iframe><video><style>>'
            );
        });

        // Merge sanitized input back into request
        $request->merge($input);

        // Process the request through the next middleware/controller
        $response = $next($request);

        // Ensure we are modifying only valid response content
        if ($response instanceof Response && is_string($response->getContent())) {
            $cleanContent = strip_tags($response->getContent());
            $response->setContent($cleanContent);
        }

        return $response;
    }
}
