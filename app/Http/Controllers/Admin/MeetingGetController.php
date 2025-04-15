<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Toastr;
class MeetingGetController extends Controller
{
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.meetings', 1);
        $this->route = 'admin.meetinggets';
        $this->view = 'admin.meeting';
        $this->path = 'meeting';
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
        
        $data['rows'] = Meeting::orderBy('date', 'asc')->limit(500)->get();
        return view($this->view.'.index', $data);
    }

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
        $meeting = Meeting::findOrFail($request->id);
        $meeting->status = $request->status;
        $meeting->save();
    
        return response()->json(['success' => true, 'status' => $meeting->status]);
    }
   
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $meeting;

        return view($this->view.'.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
       // Delete Data
       $meeting->delete();

       Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

       return redirect()->back();
    }
    
    
}
