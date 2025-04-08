<?php

namespace App\Http\Controllers\Web;

use App\Models\Page;
use App\Models\Client;
use App\Models\Slider;
use App\Models\Article;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Technology;
use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    public function index(){
        $data['articles'] = Article::where('status', '1')->orderBy('id', 'desc')->get();
        // $data['services'] = Service::where('status', '1')->orderBy('id', 'asc')->get();
        $data['portfolios'] = Portfolio::where('status', '1')->orderBy('id', 'desc')->get();
        $data['ArticleCategory'] = ArticleCategory::where('status', '1')->orderBy('id', 'desc')->get();
        $data['pages'] = Page::where('status', '1')->orderBy('id', 'desc')->get();
       
       
        // $data['services'] = Service::where('status', '1')->orderBy('id', 'desc')->get();
        $data['services'] = Service::with('subservices')->where('status', '1')->orderBy('id', 'desc')->get();
        $data['technology'] = Technology::where('status', '1')->orderBy('id', 'desc')->get();
        // $data['portfolios'] = Portfolio::where('status', '1')->orderBy('id', 'desc')->get();
        $data['sliders'] = Slider::where('status', '1')->orderBy('id', 'desc')->get();
        $data['clients'] = Client::where('status', '1')->orderBy('id', 'desc')->get();
       
        return response()->view('sitemap.index', $data)->header('Content-Type', 'text/xml');
    }

   
}
