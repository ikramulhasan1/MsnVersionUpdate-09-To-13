<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\Portfolio;
use App\Models\Article;
use App\Models\Service;
use App\Models\Contact;
use App\Models\Member;
use App\Models\Client;
use App\Models\Faq;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.dashboard', 1);
        $this->route = 'admin.dashboard';
        $this->view = 'admin';
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

        $data['articles'] = Article::where('status', 1)->count();
        $data['portfolios'] = Portfolio::where('status', 1)->count();
        $data['services'] = Service::where('status', 1)->count();
        $data['faqs'] = Faq::where('status', 1)->count();
        $data['members'] = Member::where('status', 1)->count();
        $data['clients'] = Client::where('status', 1)->count();
        $data['contacts'] = Contact::where('status', 1)->count();
        $data['subscribers'] = Subscriber::where('status', 1)->count();

        return view($this->view.'.index', $data);
    }
}
