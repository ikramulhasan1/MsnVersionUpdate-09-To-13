<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkProcess;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Services                                
        $data['services'] = Service::where('status', '1')
                        ->orderBy('id', 'asc')
                        ->get();

        // Processes
        $data['processes'] = WorkProcess::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        return view('web.services', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Service                                
        $data['service'] = Service::where('slug', $slug)
                        ->where('status', '1')
                        ->firstOrFail();

        // Service Lists                                
        $data['service_lists'] = Service::where('status', '1')
                        ->orderBy('id', 'asc')
                        ->get();

        return view('web.service-single', $data);
    }
}
