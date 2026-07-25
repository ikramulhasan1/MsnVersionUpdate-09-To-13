<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PageCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Toastr;

class CacheManagementController extends Controller
{
    protected PageCacheService $pageCache;

    public function __construct(PageCacheService $pageCache)
    {
        $this->pageCache = $pageCache;
    }

    /**
     * Cache management ড্যাশবোর্ড দেখানো।
     */
    public function index()
    {
        $data['title'] = 'Cache Management';
        $data['enabled'] = $this->pageCache->isEnabled();
        $data['stats'] = $this->pageCache->stats();

        return view('admin.cache.index', $data);
    }

    /**
     * Full-page caching চালু করা (Admin panel টগল)।
     */
    public function enable()
    {
        $this->pageCache->enable();
        Toastr::success('Page caching চালু করা হয়েছে।', 'সফল!');

        return back();
    }

    /**
     * Full-page caching বন্ধ করা (debugging/dev-এর সময়)।
     */
    public function disable()
    {
        $this->pageCache->disable();
        Toastr::success('Page caching বন্ধ করা হয়েছে — এখন থেকে প্রতিটা রিকোয়েস্ট সরাসরি Laravel থেকে যাবে।', 'সফল!');

        return back();
    }

    /**
     * পুরো সাইটের cache মুছে ফেলা (hard flush) — full-page HTML + versioned
     * query/fragment cache দুটোই।
     */
    public function clearAll()
    {
        $this->pageCache->flushAll();
        $this->pageCache->bumpVersion('content');

        Toastr::success('পুরো ওয়েবসাইটের cache মুছে ফেলা হয়েছে।', 'সফল!');

        return back();
    }

    /**
     * নির্দিষ্ট একটা URL path-এর cache মুছে ফেলা।
     * উদাহরণ: /about অথবা /service/web-design
     */
    public function clearPath(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $this->pageCache->forgetPath($request->input('path'));
        Toastr::success('"'.$request->input('path').'" পেজের cache মুছে ফেলা হয়েছে।', 'সফল!');

        return back();
    }

    /**
     * শুধু হোমপেজের cache মুছে ফেলা — শর্টকাট বাটন।
     */
    public function clearHome()
    {
        $this->pageCache->forgetPath('/');
        Toastr::success('হোমপেজের cache মুছে ফেলা হয়েছে।', 'সফল!');

        return back();
    }

    /**
     * Laravel-এর নিজস্ব config/route/view cache ও ক্লিয়ার করার শর্টকাট
     * (deploy করার পর ম্যানুয়ালি SSH/terminal লাগবে না)।
     */
    public function clearFramework()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        Toastr::success('Laravel framework cache (view/config/route) ক্লিয়ার করা হয়েছে।', 'সফল!');

        return back();
    }
}
