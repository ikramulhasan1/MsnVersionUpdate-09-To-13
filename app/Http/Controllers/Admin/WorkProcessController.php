<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\WorkProcess;
use Toastr;

class WorkProcessController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.work_process', 1);
        $this->route = 'admin.work-process';
        $this->view = 'admin.work-process';
        $this->path = 'work-process';
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
        
        $data['rows'] = WorkProcess::orderBy('id', 'asc')->get();

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
            'title' => 'required|max:191|unique:work_processes,title',
        ]);

        // Insert Data
        $workProcess = new WorkProcess;
        $workProcess->title = $request->title;
        $workProcess->slug = Str::slug($request->title, '-');
        $workProcess->description = $request->description;
        $workProcess->icon = $request->icon;
        $workProcess->save();


        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(WorkProcess $workProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkProcess $workProcess)
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
    public function update(Request $request, WorkProcess $workProcess)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:work_processes,title,'.$workProcess->id,
        ]);

        // Update Data
        $workProcess->title = $request->title;
        $workProcess->slug = Str::slug($request->title, '-');
        $workProcess->description = $request->description;
        $workProcess->icon = $request->icon;
        $workProcess->status = $request->status;
        $workProcess->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkProcess $workProcess)
    {
        // Delete Data
        $workProcess->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
