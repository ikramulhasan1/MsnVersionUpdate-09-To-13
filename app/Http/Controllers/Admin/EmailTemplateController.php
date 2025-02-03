<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Toastr;

class EmailTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.template', 1);
        $this->route = 'admin.template';
        $this->view = 'admin.template';
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
        
        $data['rows'] = EmailTemplate::orderBy('id', 'asc')->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:email_templates,title,'.$id,
        ]);

        if($request->status == 1){
            $status = 1;
        }
        else{
            $status = 0;
        }

        // Update Data
        $emailTemplate = EmailTemplate::findOrfail($id);
        $emailTemplate->title = $request->title;
        $emailTemplate->description = $request->description;
        $emailTemplate->status = $status;
        $emailTemplate->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
