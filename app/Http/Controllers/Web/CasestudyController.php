<?php

namespace App\Http\Controllers\Web;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CasestudyController extends Controller
{
    public function index()
    {
        // Portfolio Categories                                
        // $data['portfolio_categories'] = PortfolioCategory::where('status', '1')
        //     ->orderBy('id', 'asc')
        //     ->get();

        // // Portfolios                                
        // $data['portfolios'] = Portfolio::where('status', '1')
        //     ->orderBy('id', 'desc')
        //     ->get();

        return view('web.portfolios');
    }

    public function show($slug)
    {
        // Portfolio                                ['portfolio'] = Portfolio::where('slug', $slug)
        //     ->where('status', '1')
        //     ->firstOrFail();

        return view('web.portfolio-single');
    }
}
