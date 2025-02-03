<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\Contact as ContactMail;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Setting;
use Session;
use Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('web.contact');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMail(Request $request)
    {
        $setting = Setting::where('status', '1')->first();

        if(isset($setting)){

            $sendTo = $setting->contact_mail;
            $appName = $setting->title;

            // Field Validation
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'subject' => 'required',
                'message' => 'required',
            ]);

            // Store Data
            Contact::create($request->all());
            

            // Passing data to email template
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone'] = $request->phone;

            // Mail Information
            $data['subject'] = $request->subject;
            $data['from'] = $request->email;
            $data['sender'] = $request->name;
            $data['message'] = $request->message;

            // Send Mail
            Mail::to($sendTo)->send(new ContactMail($data));

            
            Session::flash('success', __('email.send_successfully'));
        }
        else{
            Session::flash('error', __('email.receiver_not_found'));
        }


        return redirect()->back();
    }
}
