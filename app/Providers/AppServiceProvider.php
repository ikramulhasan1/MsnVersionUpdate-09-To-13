<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\LiveChat;
use App\Models\Page;
use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Social;
use App\Models\Subservice;
use App\Models\Technology;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        if (app()->runningInConsole()) {
            return;
        }

        $setting = Setting::where('status', 1)->first();
        $social = Social::where('status', 1)->first();
        $livechat = LiveChat::first();
        $sections = Section::where('status', 1)->get();
        $pages = Page::where('status', 1)->get();
        $article_subnavs = ArticleCategory::where('status', 1)->get();
        $service_subnavs = Service::with('subservices')->where('status', 1)->get();
        $related_service_subnavs = Subservice::where('status', 1)->get();
        $technologies = Technology::where('status', 1)->get();
        $recents = Article::where('status', 1)
            ->latest()
            ->take(3)
            ->get();

        View::share([
            'setting' => $setting,
            'social' => $social,
            'livechat' => $livechat,
            'pages' => $pages,
            'recents' => $recents,
            'sections' => $sections,
            'article_subnavs' => $article_subnavs,
            'service_subnavs' => $service_subnavs,
            'related_service_subnavs' => $related_service_subnavs,
            'technologies' => $technologies,
        ]);
    }
}
