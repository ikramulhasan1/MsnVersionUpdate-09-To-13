<?php

namespace App\Http\Controllers\Web;

use File;
use Mail;
use Session;
use App\Models\Service;
use App\Models\Setting;
use App\Models\GetQuote;
use App\Mail\NotifyAdmin;
use App\Models\WorkProcess;
use App\Mail\NotifyCustomer;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class GetQuoteController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Retrieve the selected service from session
        $data['work_model'] = $request->session()->get('work_model');
        $data['work_scope'] = $request->session()->get('work_scope');

        // Services                                
        $data['services'] = Service::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();

        // Processes
        $data['processes'] = WorkProcess::where('status', '1')
            ->orderBy('id', 'asc')
            ->get();


        return view('web.get-quote', $data);
    }

    public function storeSelection(Request $request)
    {
        // Save radio value to session
        $request->session()->put('work_model', $request->work_model);
        $request->session()->put('work_scope', $request->work_scope);
        return redirect()->route('get-quote');
    }

    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
           
            'message' => 'required',
            'g-recaptcha-response' => 'required', // ✅ NEW
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,pdf,doc,docx,txt,zip,rar,csv,xls,xlsx,ppt,pptx,mp3,avi,mp4,mpeg,3gp|max:50000',
        ]);

        // ✅ Verify Google reCAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $recaptcha = $response->json();

        if (empty($recaptcha['success']) || $recaptcha['success'] !== true) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        // file upload, fit and store inside public folder 
        if ($request->hasFile('file_path')) {
            //Upload New Image
            $filenameWithExt = $request->file('file_path')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file_path')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            //Crete Folder Location
            $path = public_path('uploads/quote/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Move File inside public/uploads/ folder
            $file = $request->file('file_path')->move($path, $fileNameToStore);
        } else {
            $fileNameToStore = NULL;
        }


        // Insert Quote
        $quote = new GetQuote();
        $quote->name = $request->name;
        $quote->email = $request->email;
        $quote->phone = $request->phone;
        $quote->address = $request->address;
        $quote->city = $request->city;
        $quote->company = $request->company;
        $quote->website = $request->website;
        $quote->prefer_contact = $request->prefer_contact;
        $quote->quantity = $request->quantity;
        $quote->message = $request->message;
        $quote->work_model = $request->work_model;
        $quote->work_scope = $request->work_scope;
        $quote->file_path = $fileNameToStore;
        $quote->pre_delivery_time = $request->pre_delivery_time;
        $quote->where_find = $request->where_find;
        $quote->save();

        // Clear session
        $request->session()->forget('work_model');
        $request->session()->forget('work_scope');
        // Polymorphic Services Store
        if (is_array($request->services) == 1) {
            foreach ($request->services as $service_id) {

                $quote->services()->attach([$service_id]);
            }
        }

        $template = EmailTemplate::where('slug', 'quote-placed')->first();
        $setting = Setting::where('status', '1')->first();

        if (isset($template) && isset($setting)) {

            // Passing data to email template
            $data['row'] = $quote;
            $data['id_type'] = __('email.quote_id');
            $data['order_id'] = $quote->id;

            // Mail Information
            $data['subject'] = $template->title;
            $data['email'] = $quote->email;
            $data['from'] = $setting->contact_mail;
            $data['sender'] = $setting->title;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyCustomer($data));
        }

        if (isset($template) && isset($setting)) {

            // Passing data to email template
            $data['row'] = $quote;
            $data['id_type'] = __('email.quote_id');
            $data['order_id'] = $quote->id;

            // Mail Information
            $data['subject'] = __('email.new_quote_request');
            $data['email'] = $setting->contact_mail;
            $data['from'] = 'support@msnsofttech.com';
            $data['sender'] = $quote->name;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyAdmin($data));
        }

        Session::flash('success', __('email.quote_submitted'));

        return redirect()->back();
    }
}
