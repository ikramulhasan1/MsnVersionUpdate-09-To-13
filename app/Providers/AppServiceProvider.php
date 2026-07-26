<?php

namespace App\Providers;

use App\Facades\PageCache;
use App\Models\About;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CaseStudy;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Counter;
use App\Models\Designation;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Industry;
use App\Models\Language;
use App\Models\LiveChat;
use App\Models\Member;
use App\Models\Page;
use App\Models\PageSetup;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use App\Models\Pricing;
use App\Models\Processwork;
use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Social;
use App\Models\Subservice;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\WhyChooseUs;
use App\Models\Whywe;
use App\Models\WorkModel;
use App\Models\WorkProcess;
use App\Observers\CacheInvalidatingObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * এই মডেলগুলোর যেকোনো পরিবর্তনে (create/update/delete) সাইটের full-page
     * cache আর versioned query-cache স্বয়ংক্রিয়ভাবে invalidate হবে।
     * Admin panel থেকে নতুন কোনো content-type (model) যোগ হলে এখানেই যোগ করুন।
     */
    protected array $cacheableModels = [
        About::class,
        Article::class,
        ArticleCategory::class,
        CaseStudy::class,
        Client::class,
        Contact::class,
        Counter::class,
        Designation::class,
        Faq::class,
        FaqCategory::class,
        Industry::class,
        Language::class,
        LiveChat::class,
        Member::class,
        Page::class,
        PageSetup::class,
        Portfolio::class,
        PortfolioCategory::class,
        Pricing::class,
        Processwork::class,
        Section::class,
        Service::class,
        Setting::class,
        Slider::class,
        Social::class,
        Subservice::class,
        Technology::class,
        Testimonial::class,
        WhyChooseUs::class,
        Whywe::class,
        WorkModel::class,
        WorkProcess::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 🔧 public_path() স্পষ্টভাবে project root কে পয়েন্ট করে দেওয়া হলো, যাতে
        // সব আপলোড (subservices, article, portfolio, client ইত্যাদি) সবসময়
        // root ফোল্ডারের "uploads/" এই সেভ হয় — index.php এর কোনো hidden
        // override এর উপর নির্ভর করতে হবে না।
        $this->app->usePublicPath(base_path());

        $this->app->singleton(\App\Services\PageCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // যেকোনো content model-এ change হলেই cache auto-invalidate হবে
        foreach ($this->cacheableModels as $model) {
            $model::observe(CacheInvalidatingObserver::class);
        }

        if (app()->runningInConsole()) {
            return;
        }

        // এই globals প্রতিটা পেজে (header/footer/nav) দরকার হয় বলে আগে প্রতি
        // রিকোয়েস্টে ৯টা আলাদা query চলত। এখন versioned cache-এ রাখা হচ্ছে,
        // তাই content না বদলানো পর্যন্ত এই query গুলো বারবার চলবে না —
        // ক্যাশ hit হলে DB টাচই হয় না।
        $globals = PageCache::remember('content', 'layout-globals', 3600, function () {
            return [
                'setting' => Setting::where('status', 1)->first(),
                'social' => Social::where('status', 1)->first(),
                'livechat' => LiveChat::first(),
                'sections' => Section::where('status', 1)->get(),
                'pages' => Page::where('status', 1)->get(),
                'article_subnavs' => ArticleCategory::where('status', 1)->get(),
                'service_subnavs' => Service::with('subservices')->where('status', 1)->get(),
                'related_service_subnavs' => Subservice::where('status', 1)->get(),
                'technologies' => Technology::where('status', 1)->get(),
                'recents' => Article::where('status', 1)->latest()->take(3)->get(),
            ];
        });

        View::share($globals);
    }
}
