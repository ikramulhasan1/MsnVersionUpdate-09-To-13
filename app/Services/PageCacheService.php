<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Redis/Memcached ছাড়া (শেয়ার্ড হোস্টিং) super-fast full-page caching +
 * "versioned key" প্যাটার্নে query/fragment cache invalidation।
 *
 * তিনটা কাজ করে:
 *  ১) পুরো রেন্ডার করা HTML ডিস্কে (public_path('cache-html')) সেভ করে,
 *     যেটা .htaccess থেকে সরাসরি সার্ভ হয় (PHP/Laravel বাইপাস করে)।
 *  ২) Cache::tags() না থাকায় "version number" দিয়ে tag-flush simulate করে —
 *     কোনো সেকশনের ডাটা বদলালে শুধু সেই সেকশনের version বাড়িয়ে দিলেই
 *     পুরনো cache key গুলো স্বয়ংক্রিয়ভাবে অচল হয়ে যায়।
 *  ৩) Admin panel থেকে ON/OFF, Clear All, Clear by URL করার জন্য মেথড দেয়।
 */
class PageCacheService
{
    public function isEnabled(): bool
    {
        return File::exists(config('pagecache.flag_file'));
    }

    public function enable(): void
    {
        $flag = config('pagecache.flag_file');
        File::ensureDirectoryExists(dirname($flag));
        File::put($flag, now()->toDateTimeString());
    }

    public function disable(): void
    {
        $flag = config('pagecache.flag_file');
        if (File::exists($flag)) {
            File::delete($flag);
        }
    }

    /**
     * এই রিকোয়েস্টটা full-page cache করার যোগ্য কিনা।
     * শুধু GET, শুধু query-string ছাড়া URL, লগইন করা ইউজার না, excluded route না।
     */
    public function isCacheableRequest(Request $request): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->query() !== []) {
            // query string থাকলে .htaccess-এর সাথে সহজ ম্যাচিং সম্ভব না,
            // তাই এই অংশটুকু normal Laravel handling-এই থাকবে
            return false;
        }

        if ($request->user()) {
            return false;
        }

        $routeName = optional($request->route())->getName();
        if ($routeName) {
            foreach (config('pagecache.excluded_route_names', []) as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function isCacheableResponse(Response $response): bool
    {
        return $response->getStatusCode() === 200
            && stripos((string) $response->headers->get('Content-Type'), 'text/html') !== false;
    }

    /**
     * এই রিকোয়েস্টের HTML ডিস্কে কোথায় সেভ হবে (একই path যেটা .htaccess চেক করে)।
     */
    public function filePathForRequest(Request $request): string
    {
        $path = $request->getPathInfo(); // e.g. "/", "/about", "/service/web-design"
        $path = $path === '/' ? '/index' : rtrim($path, '/');

        return rtrim(config('pagecache.html_dir'), '/').$path.'.html';
    }

    public function put(Request $request, string $html): void
    {
        $file = $this->filePathForRequest($request);
        File::ensureDirectoryExists(dirname($file));
        File::put($file, $html);
    }

    /**
     * নির্দিষ্ট একটা URL path-এর cache মুছে ফেলা (যেমন "/service/web-design")।
     */
    public function forgetPath(string $path): void
    {
        $path = $path === '' || $path === '/' ? '/index' : '/'.ltrim(rtrim($path, '/'), '/');
        $file = rtrim(config('pagecache.html_dir'), '/').$path.'.html';

        if (File::exists($file)) {
            File::delete($file);
        }
    }

    /**
     * সব cached HTML মুছে ফেলা (Admin-এর "Clear All")।
     */
    public function flushAll(): void
    {
        $dir = config('pagecache.html_dir');
        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }
        File::ensureDirectoryExists($dir);
    }

    /**
     * Cache পরিসংখ্যান — admin dashboard-এ দেখানোর জন্য।
     */
    public function stats(): array
    {
        $dir = config('pagecache.html_dir');
        if (!File::isDirectory($dir)) {
            return ['count' => 0, 'size' => 0, 'size_human' => '0 KB', 'files' => []];
        }

        $files = File::allFiles($dir);
        $totalSize = 0;
        $list = [];

        foreach ($files as $f) {
            $totalSize += $f->getSize();
            $list[] = [
                'path' => Str::after($f->getPathname(), rtrim($dir, '/').'/'),
                'size' => $f->getSize(),
                'modified' => \Carbon\Carbon::createFromTimestamp($f->getMTime()),
            ];
        }

        usort($list, fn ($a, $b) => $b['modified'] <=> $a['modified']);

        return [
            'count' => count($files),
            'size' => $totalSize,
            'size_human' => $this->humanSize($totalSize),
            'files' => $list,
        ];
    }

    protected function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }

    /*
    |--------------------------------------------------------------------------
    | Versioned key প্যাটার্ন — DB query / Blade fragment cache-এর জন্য
    | (Cache::tags() এর বদলে, যেহেতু file driver-এ tags সাপোর্ট নেই)
    |--------------------------------------------------------------------------
    */

    /**
     * একটা section/tag-এর বর্তমান version নম্বর। প্রথমবার ডিফল্ট ১।
     */
    public function version(string $tag): int
    {
        return (int) Cache::rememberForever(
            config('pagecache.version_cache_prefix').$tag,
            fn () => 1
        );
    }

    /**
     * version নম্বর বাড়িয়ে দেওয়া মানে সেই tag-এর সব পুরনো cache key
     * স্বয়ংক্রিয়ভাবে "অচল" (unreachable) হয়ে যাওয়া — এটাই Cache::tags()->flush()
     * এর ফাইল-ড্রাইভার-বান্ধব বিকল্প।
     */
    public function bumpVersion(string $tag): void
    {
        $key = config('pagecache.version_cache_prefix').$tag;
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }

    /**
     * versioned cache key বানানো — যেমন: cacheKey('services', 'list') -> "services:v3:list"
     */
    public function cacheKey(string $tag, string $suffix = ''): string
    {
        $key = $tag.':v'.$this->version($tag);

        return $suffix === '' ? $key : $key.':'.$suffix;
    }

    /**
     * versioned key দিয়ে সরাসরি remember করার shortcut।
     * উদাহরণ: PageCache::remember('services', 'active-list', 3600, fn () => Service::active()->get());
     */
    public function remember(string $tag, string $suffix, int $seconds, \Closure $callback)
    {
        return Cache::remember($this->cacheKey($tag, $suffix), $seconds, $callback);
    }
}
