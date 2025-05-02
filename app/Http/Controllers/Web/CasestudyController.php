<?php

namespace App\Http\Controllers\web;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Models\PortfolioCategory;
use App\Http\Controllers\Controller;

class CasestudyController extends Controller
{
     public function index()
    {
        // Portfolio Categories                                
        $data['portfolio_categories'] = PortfolioCategory::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        // Portfolios                                
        $data['portfolios'] = Portfolio::where('status', '1')
            ->orderBy('id', 'desc')
            ->get();

        return view('web.case-studies', $data);
    }

    public function show($slug)
    {
        // Portfolio                                
        $data['portfolio'] = Portfolio::where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();

        return view('web.case-study', $data);
    }
}
