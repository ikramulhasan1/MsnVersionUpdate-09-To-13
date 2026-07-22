<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Counter;
use App\Models\Service;
use App\Models\Subservice;
use App\Models\Technology;
use App\Models\WorkProcess;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Services
        $data['services'] = Service::with('subservices')->where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        // Processes
        $data['processes'] = WorkProcess::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        $data['counters'] = Counter::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        return view('web.services', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($slug)
    {
        // Service
        $data['service'] = Service::where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();

        // Service Lists
        $data['industry'] = Service::with('industries')->where('status', '1')
            ->orderBy('id', 'asc')
            ->get();
        // Clients
        $data['clients'] = Client::where('status', '1')->orderBy('id', 'desc')->take(10)->get();
        // Counters
        $data['counters'] = Counter::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        return view('web.service-single', $data);
    }

    public function related($slug)
    {
        // Service
        $data['service'] = Subservice::with('portfolios', 'technologies')->where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();
        $data['all_service'] = Service::get();

        // Service Lists
        $data['service_lists'] = Subservice::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        // Counters
        $data['counters'] = Counter::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        return view('web.related-service-single', $data);
    }

    public function technology($slug)
    {
        // Service
        $data['technology'] = Technology::where('slug', $slug)
            ->where('status', '1')
            ->firstOrFail();

        // Service Lists
        $data['service_lists'] = Technology::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        return view('web.technology', $data);
    }
}
