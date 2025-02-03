<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Invoice as InvoiceMail;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Mail\NotifyCustomer;
use App\Models\GetQuote;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Service;
use Carbon\Carbon;
use Toastr;
use File;
use Mail;

class InvoiceController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.invoice', 1);
        $this->route = 'admin.invoice';
        $this->view = 'admin.invoice';
        $this->path = 'invoice';
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
        
        $data['rows'] = Invoice::orderBy('id', 'desc')->limit(500)->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['services'] = Service::where('status', 1)->get();
        $data['template_send'] = EmailTemplate::where('slug', 'invoice-send')->first();

        return view($this->view.'.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'service_charge' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'invoice_amount' => 'required|numeric',
            'discount_amount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'due_date' => 'nullable|date|after_or_equal:today',
            'attach' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,pdf,doc,docx,txt,zip,rar,csv,xls,xlsx,ppt,pptx,mp3,avi,mp4,mpeg,3gp|max:50000',
        ]);


        // file upload, fit and store inside public folder 
        if($request->hasFile('attach')){
            //Upload New Image
            $filenameWithExt = $request->file('attach')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
            $extension = $request->file('attach')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            //Crete Folder Location
            $path = public_path('uploads/invoice/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Move File inside public/uploads/ folder
            $file = $request->file('attach')->move($path, $fileNameToStore);
        }
        else{
            $fileNameToStore = NULL;
        }


        // Insert Data
        $invoice = new Invoice;
        $invoice->quote_id = $request->quote_id;
        $invoice->name = $request->name;
        $invoice->email = $request->email;
        $invoice->address = $request->address;
        $invoice->city = $request->city;
        $invoice->company = $request->company;
        $invoice->service_charge = $request->service_charge;
        $invoice->tax = $request->tax;
        $invoice->shipping = $request->shipping;
        $invoice->total_amount = $request->total_amount;
        $invoice->discount_amount = $request->discount_amount;
        $invoice->invoice_amount = $request->invoice_amount;
        $invoice->invoice_date = Carbon::now();
        $invoice->due_date = $request->due_date;
        $invoice->message = $request->message;
        $invoice->terms_conditions = $request->terms_conditions;
        $invoice->reference = $request->reference;
        $invoice->attach = $fileNameToStore;
        $invoice->invoice_type = $request->invoice_type;
        $invoice->estimate_flag = 0;
        $invoice->save();


        // Polymorphic Services Store
        if(is_array($request->services) == 1){
            foreach($request->services as $service_id){

               $invoice->services()->attach([$service_id]);
            }
        }
        
        $setting = Setting::where('status', '1')->first();

        // Passing data to email template
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['invoice'] = $invoice;

        // Mail Information
        $data['subject'] = $request->subject;
        $data['from'] = $setting->contact_mail;
        $data['sender'] = $setting->title;
        $data['message'] = $request->message;

        // Send Mail
        Mail::to($data['email'])->send(new InvoiceMail($data));


        Toastr::success(__('dashboard.sent_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['row'] = $invoice;

        return view($this->view.'.show', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Update Data
        $invoice->status = $request->status;
        $invoice->save();

        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        // Delete Data
        $attach = public_path('uploads/'.$this->path.'/'.$invoice->attach);
        if(File::isFile($attach)){
            File::delete($attach);
        }

        $invoice->services()->detach();
        $invoice->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }
}
