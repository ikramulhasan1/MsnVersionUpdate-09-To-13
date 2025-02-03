<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $setting->title }}</title>


<!-- CSS Reset : BEGIN -->
<style>

/* What it does: Remove spaces around the email design added by some email clients. */
/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
html,
body {
    margin:0px !important;
    padding: 0 !important;
    height: 100% !important;
    width: 100% !important;
    mso-line-height-rule: exactly; 
    background: #f8f8f8; 
}
/* What it does: Stops email clients resizing small text. */
* {
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
}

/* What it does: Centers email on Android 4.4 */
div[style*="margin: 16px 0"] {
    margin: 0 !important;
}

/* What it does: Stops Outlook from adding extra spacing to tables. */
table,
td {
    mso-table-lspace: 0pt !important;
    mso-table-rspace: 0pt !important;
}

/* What it does: Fixes webkit padding issue. */
table {
    width: 100% !important;
    border-spacing: 0 !important;
    border-collapse: collapse !important;
    table-layout: fixed !important;
    margin: 0 auto !important;
}

/* What it does: Uses a better rendering method when resizing images in IE. */
img {
    -ms-interpolation-mode:bicubic;
}

/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
a {
    text-decoration: none;
}

/* What it does: A work-around for email clients meddling in triggered links. */
*[x-apple-data-detectors],  /* iOS */
.unstyle-auto-detected-links *,
.aBn {
    border-bottom: 0 !important;
    cursor: default !important;
    color: inherit !important;
    text-decoration: none !important;
    font-size: inherit !important;
    font-family: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
}

/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
.a6S {
    display: none !important;
    opacity: 0.01 !important;
}

/* What it does: Prevents Gmail from changing the text color in conversation threads. */
.im {
    color: inherit !important;
}

/* If the above doesn't work, add a .g-img class to any image in question. */
img.g-img + div {
    display: none !important;
}

/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
/* Create one of these media queries for each additional viewport size you'd like to fix */
.invoice-container {
    margin: 0 auto !important;
    max-width: 800px !important; 
}

/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
    .invoice-container {
        min-width: 320px !important;
    }
}
/* iPhone 6, 6S, 7, 8, and X */
@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
    .invoice-container {
        min-width: 375px !important;
    }
}
/* iPhone 6+, 7+, and 8+ */
@media only screen and (min-device-width: 414px) {
    .invoice-container {
        min-width: 414px !important;
    }
}
</style>

<!-- CSS Reset : END -->


<!-- Invoice CSS : Start -->
<style>
.invoice-container {
    background: #f8f8f8; 
    padding: 0px 0px; 
    font-family:arial; 
    line-height:28px; 
    color: #514d6a;
}
.inner-container {
    max-width: 700px; 
    padding:50px 0;  
    margin: 0px auto; 
    font-size: 14px
}

p {
    font-family: 'arial'; 
    font-size: 14px; 
    line-height: 1.5;
    margin-top: 0px;
    margin-bottom: 0px;
    width: 100%;
}

.header-top {
    vertical-align: top; 
    padding-bottom:30px;
    margin-bottom: 20px
    width: 100%; 
}
.header-top img {
    border:none; 
    max-width: 120px; 
    max-height: 80px; 
    margin: auto; 
    overflow: hidden;
}
.header-bottom {
    background:#36bea6; 
    padding:20px; 
    color:#fff; 
    text-align:center;
}
.invoice-body {
    padding: 40px; 
    background: #fff;
}
.full-width {
    padding-top: 25px;
    width: 100%;
}
.invoice-body-bottom {
    padding:20px 0; 
    border-top:1px solid #f6f6f6;
}
.invoice-body-bottom .total td {
    font-family: 'arial'; 
    font-size: 14px; 
    vertical-align: middle; 
    border-top-width: 1px; 
    border-top-color: #f6f6f6; 
    border-top-style: solid; 
    margin: 0; 
    padding: 0px 0; 
    font-weight:bold;
}
.payment-btn {
    display: inline-block; 
    padding: 11px 30px; 
    margin: 20px 0px 30px; 
    font-size: 15px; 
    color: #fff; 
    background: #2962FF; 
    border-radius: 60px; 
    text-decoration:none;
}
.attach-btn {
    display: inline-block; 
    padding: 11px 30px; 
    margin: 20px 0px 30px; 
    font-size: 15px; 
    color: #fff; 
    background: #230548; 
    border-radius: 60px; 
    text-decoration:none;
}
</style>
<!-- Invoice CSS : End -->
</head>

<body>
    <div class="invoice-container">
        <div class="inner-container">
            @if(isset($setting->logo_path))
            <table border="0" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td class="header-top" align="center"><a href="{{ route('home') }}" target="_blank"><img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="{{ $setting->title }}">
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
            <table border="0" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td class="header-bottom"> {{ $data['subject'] }} </td>
                    </tr>
                </tbody>
            </table>
            <div class="invoice-body">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td>
                                <b>{{ __('email.name') }}: {{ $data['name'] }}</b><br/>
                                <p>{{ __('email.email') }}: {{ $data['email'] }}</p>
                                @if(isset($data['invoice']->phone))
                                <p>{{ __('email.phone') }}: {{ $data['invoice']->phone }}</p>
                                @endif
                                @if(isset($data['invoice']->company))
                                <p>{{ __('email.company') }}: {{ $data['invoice']->company }}</p>
                                @endif
                                @if(isset($data['invoice']->address))
                                <p>{{ __('email.address') }}: {{ $data['invoice']->address }}</p>
                                @endif
                                @if(isset($data['invoice']->city))
                                <p>{{ __('email.city') }}: {{ $data['invoice']->city }}</p>
                                @endif
                                @if(isset($data['invoice']->reference))
                                <p>{{ __('email.reference') }}: {{ $data['invoice']->reference }}</p>
                                @endif
                                <p>{{ __('email.invoice') }} #{{ $data['invoice']->id }}</p>
                            </td>
                            <td align="right" width="40%">
                                <b>{{ __('email.invoice_type') }} :  
                                  @if( $data['invoice']->invoice_type == 0 )
                                    {{ __('email.estimate') }}
                                  @elseif( $data['invoice']->invoice_type == 1 )
                                    {{ __('email.advance') }}
                                  @elseif( $data['invoice']->invoice_type == 2 )
                                    {{ __('email.interval') }}
                                  @elseif( $data['invoice']->invoice_type == 3 )
                                    {{ __('email.milestone') }}
                                  @elseif( $data['invoice']->invoice_type == 4 )
                                    {{ __('email.final') }}
                                  @elseif( $data['invoice']->invoice_type == 5 )
                                    {{ __('email.full') }}
                                  @endif
                                </b>
                                <p>{{ __('email.invoice_date') }}: {{ date('d M Y', strtotime($data['invoice']->invoice_date)) }}</p>
                                @if(isset($data['invoice']->due_date))
                                <p>{{ __('email.due_date') }}: {{ date('d M Y', strtotime($data['invoice']->due_date)) }}</p>
                                @endif
                                @if(isset($data['invoice']->quote_id))
                                <p>{{ __('email.quote') }} #{{ $data['invoice']->quote_id }}</p>
                                @endif
                            </td>
                        </tr>
                        @if(count($data['invoice']->services) > 0)
                        <tr>
                            <td class="full-width">
                                <span>{{ __('email.services') }}: </span>
                                @foreach($data['invoice']->services as $service)
                                     {{ $service->title }} | 
                                @endforeach
                            </td>
                        </tr>
                        @endif
                        @if(isset($data['message']))

                        @php
                        $services_array = array();
                        foreach($data['invoice']->services as $service){
                            array_push($services_array, $service->title);
                        }
                        $services_list = implode(',', $services_array);

                        $search = array('[name]', '[email]', '[phone]', '[address]', '[city]', '[company]', '[services]');

                        $replace = array('<b>'.$data['invoice']->name.'</b>', '<b>'.$data['invoice']->email.'</b>', '<b>'.$data['invoice']->phone.'</b>', '<b>'.$data['invoice']->address.'</b>', '<b>'.$data['invoice']->city.'</b>', '<b>'.$data['invoice']->company.'</b>', '<b>'.$services_list.'</b>');

                        $message_body = str_replace($search, $replace, $data['message']);
                        @endphp
                
                        <tr>
                            <td class="full-width">
                                {!! strip_tags($message_body, '<p><a><b><i><u><strong><br><table><tr><th><td><ul><ol><li><h1><h2><h3><h4><h5><h6><del><ins><sup><sub><pre>') !!}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" class="invoice-body-bottom">
                                <div>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            @if(isset($data['invoice']->service_charge))
                                            <tr class="total">
                                                <td width="80%">{{ __('email.service_charge') }}</td>
                                                <td align="right"> {{ $data['invoice']->service_charge }} {{ __('common.currency') }}</td>
                                            </tr>
                                            @endif
                                            @if(isset($data['invoice']->tax))
                                            <tr class="total">
                                                <td width="80%">{{ __('email.tax') }}</td>
                                                <td align="right"> {{ $data['invoice']->tax }} {{ __('common.currency') }}</td>
                                            </tr>
                                            @endif
                                            @if(isset($data['invoice']->shipping))
                                            <tr class="total">
                                                <td width="80%">{{ __('email.shipping') }}</td>
                                                <td align="right"> {{ $data['invoice']->shipping }} {{ __('common.currency') }}</td>
                                            </tr>
                                            @endif
                                            <tr class="total">
                                                <td width="80%">{{ __('email.total_amount') }}</td>
                                                <td align="right"> {{ $data['invoice']->total_amount }} {{ __('common.currency') }}</td>
                                            </tr>
                                            @if(isset($data['invoice']->discount_amount))
                                            <tr class="total">
                                                <td width="80%">{{ __('email.discount_amount') }}</td>
                                                <td align="right"> - {{ $data['invoice']->discount_amount }} {{ __('common.currency') }}</td>
                                            </tr>
                                            @endif
                                            <tr class="total">
                                                <td width="80%">{{ __('email.payable_amount') }}</td>
                                                <td align="right"> = {{ $data['invoice']->invoice_amount }} {{ __('common.currency') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <center>
                                    @if(is_file('uploads/invoice/'.$data['invoice']->attach))
                                    <a href="{{ asset('uploads/invoice/'.$data['invoice']->attach) }}" class="attach-btn" target="_blank" download>{{ __('email.attach_btn') }}</a>
                                    @endif
                                    <a href="{{ route('make.payment', $data['invoice']->id) }}" class="payment-btn">{{ __('email.pay_btn') }}</a>
                                </center>
                                <b>- {{ __('email.thanks') }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>