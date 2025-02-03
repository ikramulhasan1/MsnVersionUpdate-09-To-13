<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveChat;
use Toastr;

class LiveChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.live_chat', 1);
        $this->route = 'admin.livechat';
        $this->view = 'admin.livechat';
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

        $data['row'] = LiveChat::first();

        return view($this->view.'.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->id;

        // Status
        if($request->status == null || $request->status != 1){
            $status = 0; 
        }
        else {
            $status = 1; 
        }

        $request->request->add(['status' => $status]); //add request

        // -1 means no data row found
        if($id == -1){
            // Insert Data
            $input = $request->all();
            $data = LiveChat::create($input);
        }
        else{
            // Update Data
            $data = LiveChat::find($id);

            $input = $request->all();
            $data->update($input);
        }


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }
}
