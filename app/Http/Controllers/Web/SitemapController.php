<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index(){
        return response()->view('sitemap.index')->header('Content-Type', 'text/xml');
    }
}
