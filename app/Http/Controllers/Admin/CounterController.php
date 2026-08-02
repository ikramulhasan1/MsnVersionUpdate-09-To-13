<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Toastr;

class CounterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.counter', 1);
        $this->route = 'admin.counter';
        $this->view = 'admin.counter';
        $this->path = 'counter';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = Counter::orderBy('id', 'asc')->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:counters,title',
            'value' => 'required',
        ]);

        // Insert Data
        $counter = new Counter;
        $counter->title = $request->title;
        $counter->slug = Str::slug($request->title, '-');
        $counter->description = $request->description;
        $counter->icon = $request->icon;
        $counter->value = $request->value;
        $counter->save();

        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Counter $counter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Counter $counter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Counter $counter)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:counters,title,'.$counter->id,
            'value' => 'required',
        ]);

        // Update Data
        $counter->title = $request->title;
        $counter->slug = Str::slug($request->title, '-');
        $counter->description = $request->description;
        $counter->icon = $request->icon;
        $counter->value = $request->value;
        $counter->status = $request->status;
        $counter->save();

        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Counter $counter)
    {
        // Delete Data
        $counter->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
