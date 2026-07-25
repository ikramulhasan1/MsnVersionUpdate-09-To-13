<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\PageSetup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('web.master', function ($view) {
            $view->with([
                'page_home' => Cache::remember('page_home', 3600, fn () => PageSetup::page('home')),
                'page_about' => Cache::remember('page_about', 3600, fn () => PageSetup::page('about-us')),
                'page_faqs' => Cache::remember('page_faqs', 3600, fn () => PageSetup::page('faqs')),
                'page_contact' => Cache::remember('page_contact', 3600, fn () => PageSetup::page('contact-us')),
                'page_services' => Cache::remember('page_services', 3600, fn () => PageSetup::page('services')),
                'page_portfolio' => Cache::remember('page_portfolio', 3600, fn () => PageSetup::page('portfolio')),
                'page_quote' => Cache::remember('page_quote', 3600, fn () => PageSetup::page('get-quote')),
                'all_pages' => Cache::remember('casestudy_pages', 3600, fn () => Page::where('type', 'casestudy')->get()),
                're_page' => Cache::remember('resource_pages', 3600, fn () => Page::where('type', 'resources')->get()),
            ]);
        });
    }
}
