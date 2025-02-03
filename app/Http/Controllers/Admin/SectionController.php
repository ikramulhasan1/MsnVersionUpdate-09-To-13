<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use Toastr;

class SectionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.section', 1);
        $this->route = 'admin.section';
        $this->view = 'admin.section';
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
        
        $data['rows'] = Section::orderBy('id', 'asc')->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:sections,title,'.$section->id,
        ]);

        if($request->status == 1){
            $status = 1;
        }
        else{
            $status = 0;
        }

        // Update Data
        $section->title = $request->title;
        $section->description = $request->description;
        $section->icon = $request->icon;
        $section->status = $status;
        $section->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
