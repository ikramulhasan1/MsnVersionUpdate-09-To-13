<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class PortfolioCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.portfolio_category', 1);
        $this->route = 'admin.portfolio-category';
        $this->view = 'admin.portfolio-category';
        $this->path = 'portfolio-category';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        
        $data['rows'] = PortfolioCategory::orderBy('id', 'asc')->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:portfolio_categories,title',
        ]);

        // Insert Data
        $portfolioCategory = new PortfolioCategory;
        $portfolioCategory->title = $request->title;
        $portfolioCategory->slug = Str::slug($request->title, '-');
        $portfolioCategory->description = $request->description;
        $portfolioCategory->save();


        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PortfolioCategory $portfolioCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PortfolioCategory $portfolioCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PortfolioCategory $portfolioCategory)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:portfolio_categories,title,'.$portfolioCategory->id,
        ]);

        // Update Data
        $portfolioCategory->title = $request->title;
        $portfolioCategory->slug = Str::slug($request->title, '-');
        $portfolioCategory->description = $request->description;
        $portfolioCategory->status = $request->status;
        $portfolioCategory->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PortfolioCategory $portfolioCategory)
    {
        // Delete Data
        $portfolioCategory->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
