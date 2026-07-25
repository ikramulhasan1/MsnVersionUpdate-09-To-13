<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static void enable()
 * @method static void disable()
 * @method static void flushAll()
 * @method static void forgetPath(string $path)
 * @method static array stats()
 * @method static int version(string $tag)
 * @method static void bumpVersion(string $tag)
 * @method static string cacheKey(string $tag, string $suffix = '')
 * @method static mixed remember(string $tag, string $suffix, int $seconds, \Closure $callback)
 *
 * @see \App\Services\PageCacheService
 */
class PageCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\PageCacheService::class;
    }
}
