<?php

namespace App\Observers;

use App\Services\PageCacheService;

/**
 * এই একটা Observer ক্লাসই সব কনটেন্ট Model-এ (Service, Page, Article, Portfolio,
 * Testimonial, ইত্যাদি) রেজিস্টার করা হয় (দেখুন AppServiceProvider::boot())।
 *
 * Admin panel থেকে কেউ যখনই কোনো ডাটা add/edit/delete করেন — created,
 * updated, deleted, restored যেকোনো ইভেন্টেই এটা কাজ করে:
 *
 *   ১) পুরো site-এর full-page HTML cache flush করে দেয় (public_path('cache-html')),
 *      যাতে ভিজিটর সাথে সাথে নতুন ডাটা দেখেন।
 *   ২) global "content" version বাড়িয়ে দেয়, যাতে PageCache::remember() দিয়ে
 *      করা যেকোনো query/fragment cache-ও স্বয়ংক্রিয়ভাবে নতুন করে জেনারেট হয়।
 *
 * শেয়ার্ড হোস্টিং-এ Redis/Cache-tags না থাকায় এই "flush broad, regenerate cheap"
 * পদ্ধতিই সবচেয়ে নিরাপদ — পরের ভিজিটরের রিকোয়েস্টেই পেজ আবার dynamically
 * জেনারেট হয়ে নতুন করে cache হয়ে যাবে, কোনো ম্যানুয়াল কাজ লাগে না।
 */
class CacheInvalidatingObserver
{
    protected function invalidate(): void
    {
        $service = app(PageCacheService::class);
        $service->flushAll();
        $service->bumpVersion('content');
    }

    public function created($model): void
    {
        $this->invalidate();
    }

    public function updated($model): void
    {
        $this->invalidate();
    }

    public function deleted($model): void
    {
        $this->invalidate();
    }

    public function restored($model): void
    {
        $this->invalidate();
    }
}
