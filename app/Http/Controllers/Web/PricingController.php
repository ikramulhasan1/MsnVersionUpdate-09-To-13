<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pricing;
use App\Models\Client;

class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Pricings                                
        $data['pricings'] = Pricing::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Clients
        $data['clients'] = Client::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->get();

        return view('web.pricing', $data);
    }
}
