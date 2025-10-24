<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use App\Models\Portfolio;

class PortfolioController extends Controller
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

        return view('web.portfolios', $data);
    }

    public function show($slug)
    {
        // Portfolio                                
        $data['portfolio'] = Portfolio::with('categories','technologies')->where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();

        return view('web.portfolio-single', $data);
    }
}
