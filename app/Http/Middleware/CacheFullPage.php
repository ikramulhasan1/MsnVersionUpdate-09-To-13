<?php

namespace App\Http\Middleware;

use App\Services\PageCacheService;
use Closure;
use Illuminate\Http\Request;

/**
 * এই মিডলওয়্যারের কাজ শুধু "write" করা — পেজ রেন্ডার হওয়ার পর HTML ডিস্কে
 * সেভ করে রাখে। পরের ভিজিটরের জন্য "read/serve" অংশটুকু .htaccess নিজেই করে
 * ফেলে (PHP/Laravel বুট হওয়ার আগেই), তাই real speed win oখান থেকেই আসে।
 * এই মিডলওয়্যার শুধু cache miss হলে (নতুন/আপডেট হওয়া পেজ) কাজ করে।
 */
class CacheFullPage
{
    public function __construct(protected PageCacheService $pageCache)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (
            $this->pageCache->isEnabled()
            && $this->pageCache->isCacheableRequest($request)
            && $this->pageCache->isCacheableResponse($response)
        ) {
            $this->pageCache->put($request, $response->getContent());
        }

        return $response;
    }
}
