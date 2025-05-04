<?php

namespace App\Http\Controllers\Web;

use App\Models\CaseStudy;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Models\PortfolioCategory;
use App\Http\Controllers\Controller;

class CaseController extends Controller
{
    public function index()
    {
        // Portfolio Categories                                
        // $data['portfolio_categories'] = PortfolioCategory::where('status', '1')
        //     ->orderBy('id', 'asc')
        //     ->get();

        // Portfolios                                
        $data['case_studies'] = CaseStudy::where('status', '1')
            ->orderBy('id', 'desc')
            ->get();

        return view('web.case-studies', $data);
    }

    public function show($slug)
    {
        // Portfolio                                
        $data['case_study'] = CaseStudy::where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();

        return view('web.case-study', $data);
    }
}
