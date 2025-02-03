<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Pricing;
use Toastr;

class PricingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.pricing', 1);
        $this->route = 'admin.pricing';
        $this->view = 'admin.pricing';
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

        $data['rows'] = Pricing::orderBy('id', 'asc')->get();

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
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        return view($this->view.'.create', $data);
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
            'title' => 'required|max:191|unique:pricings,title',
            'price' => 'numeric|required',
            'old_price' => 'numeric|nullable',
            'duration' => 'required',
            'features' => 'required',
        ]);

        // Insert Data
        $pricing = new Pricing;
        $pricing->title = $request->title;
        $pricing->slug = Str::slug($request->title, '-');
        $pricing->price = $request->price;
        $pricing->old_price = $request->old_price;
        $pricing->duration = $request->duration;
        $pricing->description = json_encode($request->features, JSON_UNESCAPED_UNICODE);
        $pricing->save();


        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pricing $pricing)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['row'] = $pricing;

        return view($this->view.'.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pricing $pricing)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['row'] = $pricing;

        return view($this->view.'.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pricing $pricing)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:pricings,title,'.$pricing->id,
            'price' => 'numeric|required',
            'old_price' => 'numeric|nullable',
            'duration' => 'required',
            'features' => 'required',
        ]);


        // Update Data
        $pricing->title = $request->title;
        $pricing->slug = Str::slug($request->title, '-');
        $pricing->price = $request->price;
        $pricing->old_price = $request->old_price;
        $pricing->duration = $request->duration;
        $pricing->description = json_encode($request->features, JSON_UNESCAPED_UNICODE);
        $pricing->status = $request->status;
        $pricing->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pricing $pricing)
    {
        // Delete Data
        $pricing->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
