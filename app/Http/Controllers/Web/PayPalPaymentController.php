<?php

namespace App\Http\Controllers\Web;

use Srmklive\PayPal\Services\ExpressCheckout;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Mail\NotifyCustomer;
use App\Mail\NotifyAdmin;
use App\Models\Setting;
use App\Models\Invoice;
use Mail;

class PayPalPaymentController extends Controller
{
    public function handlePayment($id)
    {
    	$invoice = Invoice::findOrFail($id);

    	if(isset($invoice)){

        $data = [];
        $data['items'] = [
            
        ];
  
        $data['invoice_id'] = $invoice->id;
        $data['invoice_description'] = __('email.service_bill');
        $data['return_url'] = route('success.payment', $invoice->id);
        $data['cancel_url'] = route('cancel.payment', $invoice->id);
        $data['total'] = $invoice->invoice_amount;
  
        $paypalModule = new ExpressCheckout;
  
        $res = $paypalModule->setExpressCheckout($data);
        $res = $paypalModule->setExpressCheckout($data, true);
  
        return redirect($res['paypal_link']);

        }

        return redirect()->back();
    }
   
    public function paymentCancel($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = 0;
        $invoice->save();


        $template = EmailTemplate::where('slug', 'invoice-cancelled')->first();
        $setting = Setting::where('status', '1')->first();

        if(isset($template) && isset($setting)){

            // Passing data to email template
            $data['row'] = $invoice;
            $data['id_type'] = __('email.invoice_id');
            $data['order_id'] = $invoice->id;
            $data['amount'] = $invoice->invoice_amount;

            // Mail Information
            $data['subject'] = $template->title;
            $data['email'] = $invoice->email;
            $data['from'] = $setting->contact_mail;
            $data['sender'] = $setting->title;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyCustomer($data));

        }

        if(isset($template) && isset($setting)){

            // Passing data to email template
            $data['row'] = $invoice;
            $data['id_type'] = __('email.invoice_id');
            $data['order_id'] = $invoice->id;
            $data['amount'] = $invoice->invoice_amount;

            // Mail Information
            $data['subject'] = __('email.payment_cancelled');
            $data['email'] = $setting->contact_mail;
            $data['from'] = $invoice->email;
            $data['sender'] = $invoice->name;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyAdmin($data));

        }


        return redirect()->route('payment.feedback')->with('error', __('email.payment_cancelled'));
    }
  
    public function paymentSuccess(Request $request, $id)
    {
        $paypalModule = new ExpressCheckout;
        $response = $paypalModule->getExpressCheckoutDetails($request->token);
  
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

            $invoice = Invoice::findOrFail($id);
            $invoice->status = 2;
            $invoice->save();


            $template = EmailTemplate::where('slug', 'invoice-paid')->first();
            $setting = Setting::where('status', '1')->first();

            if(isset($template) && isset($setting)){

            // Passing data to email template
            $data['row'] = $invoice;
            $data['id_type'] = __('email.invoice_id');
            $data['order_id'] = $invoice->id;
            $data['amount'] = $invoice->invoice_amount;

            // Mail Information
            $data['subject'] = $template->title;
            $data['email'] = $invoice->email;
            $data['from'] = $setting->contact_mail;
            $data['sender'] = $setting->title;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyCustomer($data));

            }

            if(isset($template) && isset($setting)){

            // Passing data to email template
            $data['row'] = $invoice;
            $data['id_type'] = __('email.invoice_id');
            $data['order_id'] = $invoice->id;
            $data['amount'] = $invoice->invoice_amount;

            // Mail Information
            $data['subject'] = __('email.got_new_payment');
            $data['email'] = $setting->contact_mail;
            $data['from'] = $invoice->email;
            $data['sender'] = $invoice->name;
            $data['message'] = $template->description;

            // Send Mail
            Mail::to($data['email'])->send(new NotifyAdmin($data));

            }


            return redirect()->route('payment.feedback')->with('success', __('email.payment_successfull'));
        }
        
        return redirect()->route('payment.feedback')->with('error', __('email.something_is_wrong'));
    }

    public function paymentFeedback(){

        return view('web.payment-feedback');
    }
}
