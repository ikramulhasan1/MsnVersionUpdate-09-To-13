<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Models\WorkProcess;
use App\Models\Testimonial;
use App\Models\Subscriber;
use App\Mail\Subscription;
use App\Models\Portfolio;
use App\Models\Article;
use App\Models\Service;
use App\Models\Counter;
use App\Models\Setting;
use App\Models\Member;
use App\Models\Slider;
use App\Models\Client;
use App\Models\About;
use App\Models\Page;
use Mail;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Sliders
        $data['sliders'] = Slider::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->get();

        // About
        $data['about'] = About::where('status', '1')
                            ->first();

        // Counters                                
        $data['counters'] = Counter::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Services                                
        $data['services'] = Service::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Portfolio Categories                                
        $data['portfolio_categories'] = PortfolioCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Portfolios                                
        $data['portfolios'] = Portfolio::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->take(9)
                            ->get();

        // Members                                
        $data['members'] = Member::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Testimonials                                
        $data['testimonials'] = Testimonial::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->get();

        // Articles                                
        $data['articles'] = Article::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->take(3)
                            ->get();

        // Processes
        $data['processes'] = WorkProcess::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Clients
        $data['clients'] = Client::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->get();

        return view('web.index', $data);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function subscribe(Request $request)
    {
        // Field Validation
        $request->validate([
            'email' => 'required|email',
        ]);

        $subscriber = Subscriber::where('email', $request->email)->first();

        if(!isset($subscriber)){
            Subscriber::create($request->all());             
        }

        // Notify to User
        $template = EmailTemplate::where('slug', 'subscription')->first();
        $setting = Setting::where('status', '1')->first();

        if(isset($template) && isset($setting)){

            // Passing data to email template
            $data['email'] = $request->email;

            // Mail Information
            $data['subject'] = $template->title;
            $data['from'] = $setting->contact_mail;
            $data['sender'] = $setting->title;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new Subscription($data));

        }
        
        return redirect()->back();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function page($slug)
    {
        // Page
        $data['page'] = Page::where('slug', $slug)->where('status', 1)->firstOrFail();

        return view('web.page-single', $data);
    }
}
