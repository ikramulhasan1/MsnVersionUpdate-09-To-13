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
        $data['services'] = Service::with('subservices')->where('status', '1')
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
    public function upload(Request $request)
{
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/quote'), $fileName);
        return response()->json(['file_name' => $fileName]);
    }
    return response()->json(['error' => 'No file uploaded'], 400);
}



    public function store(Request $request)
    { dd($request->all());
        // ✅ 1. Validate form fields
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required',
            'uploaded_files' => 'nullable|array',
            'uploaded_files.*' => 'string',
        ]);

        // ✅ 2. Verify Google reCAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $recaptcha = $response->json();
        if (empty($recaptcha['success']) || $recaptcha['success'] !== true) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        // ✅ 3. Create new Quote record
        $quote = new GetQuote();
        $quote->name = $request->name;
        $quote->email = $request->email;
        $quote->phone = $request->phone;
        $quote->address = $request->address;
        $quote->city = $request->city;
        $quote->company = $request->company;
        $quote->prefer_contact = $request->prefer_contact;
        $quote->message = $request->message;
        $quote->work_model = $request->work_model;
        $quote->work_scope = $request->work_scope;
        $quote->pre_delivery_time = $request->pre_delivery_time;
        $quote->where_find = $request->where_find;
        $quote->quantity = $request->quantity ?? null;
        $quote->website = $request->website ?? null;

        // ✅ 4. Handle Subservices
        if (!empty($request->sub_service)) {
            $quote->sub_service = implode(',', $request->sub_service);
        }

        // ✅ 5. Handle multiple uploaded files from Dropzone
        // These are filenames sent from Dropzone via hidden inputs
$uploadedFiles = $request->uploaded_files ?? []; // default to empty array if none
$quote->file_path = !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null;

        // ✅ 6. Save quote
        $quote->save();

        // ✅ 7. Attach selected services (Many-to-Many)
        if (is_array($request->services) && count($request->services) > 0) {
            $quote->services()->attach($request->services);
        }

        // ✅ 8. Send emails (Customer + Admin)
        $template = EmailTemplate::where('slug', 'quote-placed')->first();
        $setting = Setting::where('status', '1')->first();

        if ($template && $setting) {
            $data = [
                'row' => $quote,
                'id_type' => __('email.quote_id'),
                'order_id' => $quote->id,
                'subject' => $template->title,
                'email' => $quote->email,
                'from' => $setting->contact_mail,
                'sender' => $setting->title,
                'message' => $template->description,
            ];
            Mail::to($data['email'])->send(new NotifyCustomer($data));
        }

        if ($template && $setting) {
            $data = [
                'row' => $quote,
                'id_type' => __('email.quote_id'),
                'order_id' => $quote->id,
                'subject' => __('email.new_quote_request'),
                'email' => $setting->contact_mail,
                'from' => 'support@msnsofttech.com',
                'sender' => $quote->name,
                'message' => $template->description,
            ];
            Mail::to($data['email'])->send(new NotifyAdmin($data));
        }

        // ✅ 9. Clear session
        $request->session()->forget(['work_model', 'work_scope']);

        // ✅ 10. Success message
        Session::flash('success', __('email.quote_submitted'));
        return redirect()->back();
    }


}
