<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    public function index() {
        $techData = config('technologies');
        return view('web.technologies', compact('techData'));
    }
}
