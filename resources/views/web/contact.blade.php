@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('contact-us');
@endphp
@if(isset($header))

    @section('title', $header->meta_title)

    @section('top_meta_tags')
    @if(isset($header->meta_description))
    <meta name="description" content="{!! str_limit(strip_tags($header->meta_description), 160, ' ...') !!}">
    @else
    <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
    @endif

    @if(isset($header->meta_keywords))
    <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
    @else
    <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
    @endif
    @endsection

    <style>
        /*  */
    /* client */
    .partner-section {
      padding: 60px 0;
      background-color: #F5F7F8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .partner-section h2 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
      color: #333333;
    }




        /* Hero Section */
        .about-hero-section {
      background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('https://images.unsplash.com/photo-1603791440384-56cd371ee9a7?q=80&w=2034&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
      height: 40vh;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .about-hero-section h1 {
      font-size: 60px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .about-hero-section p {
      font-size: 22px;
      max-width: 700px;
      margin: 0 auto;
      opacity: 0.9;
    }

    </style>
@endif

@section('content')

    <!--Page Title-->
    <!-- Hero Section -->
  <section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>Contact Us</h1>
      <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
    </div>
  </section>
    <section class="page-title p-0" style="background-color: black;">
        <div class="container" style="height: 40px; align-items: center; justify-content: flex-end;">
            <div class="inner-container clearfix">
                {{-- <div class="title-box">
                    <h1>{{ __('navbar.contact') }}</h1>
                </div> --}}
                <div class="bread-crumb">
                    <ul class="p-0">
                        <li>{{ __('navbar.contact') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    <!-- Contact Section -->
    {{-- <section class="contact-section">
        <div class="container">
            <div class="row">

                @php
                    $section_mail = \App\Models\Section::section('mail');
                @endphp
                @if(isset($section_mail))
                <!-- Form Column -->
                <div class="form-column col-lg-8 col-md-12 col-sm-12">
                     <div class="sec-title left description">
                        <h2>{{ $section_mail->title }}</h2>
                        <div class="text">{!! $section_mail->description !!}</div>
                        <div class="separater"></div>
                    </div>
                    <div class="inner-column">

                        <div class="text-center">
                            <!-- Message Display -->
                            @if(Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ Session::get('success') }}
                            </div>
                            @endif

                            <!-- Message Display -->
                            @if(Session::has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ Session::get('error') }}
                            </div>
                            @endif

                            <!-- Error Display -->
                            @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>

                        <!-- Contact Form -->
                        <div class="contact-form">
                            <form method="post" action="{{ route('contact.send') }}" accept-charset="utf-8">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="name" placeholder="{{ __('contact.your_name') }}" value="{{ old('name') }}" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="phone" placeholder="{{ __('contact.phone_no') }}" value="{{ old('phone') }}">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="email" name="email" placeholder="{{ __('contact.email_address') }}" value="{{ old('email') }}" required>
                                    </div>
                                    
                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="subject" placeholder="{{ __('contact.subject') }}" value="{{ old('subject') }}" required>
                                    </div>
                                    

                                    <div class="form-group col-lg-12 col-md-12">
                                        <textarea name="message" placeholder="{{ __('contact.your_massage') }}" required>{{ old('message') }}</textarea>
                                    </div>
                                    
                                    <div class="form-group col-lg-12 col-md-12">
                                        <button class="theme-btn btn-style-one" type="submit" name="submit-form">{{ __('contact.send') }}</button>
                                    </div> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @php
                    $section_contact = \App\Models\Section::section('contact');
                @endphp
                @if(isset($setting) && isset($section_contact))
                <!-- Info Column -->
                <div class="info-column col-lg-4 col-md-12 col-sm-12">
                    <div class="sec-title left">
                        <h2>{{ $section_contact->title }}</h2>
                        <div class="text description">{!! $section_contact->description !!}</div>
                        <div class="separater"></div>
                    </div>
                    <div class="inner-column">
                        <ul class="contact-info">
                            <li> <i class="icon flaticon-email"></i> <span>{{ __('contact.email') }}:</span> <br> {{ $setting->email_one }}@if(isset($setting->email_two)), @endif {{ $setting->email_two }}</li>
                            <li> <i class="icon flaticon-phone-call"></i>  <span>{{ __('contact.phone') }}:</span> <br> {{ $setting->phone_one }}@if(isset($setting->phone_two)), @endif {{ $setting->phone_two }}</li>
                            @if(isset($setting->office_hours))
                            <li><i class="icon flaticon-clock"></i> <span>{{ __('contact.office_time') }}:</span> <br> {!! strip_tags($setting->office_hours) !!}</li>
                            @endif
                            <li><i class="icon flaticon-placeholder"></i> <span>{{ __('contact.address') }}:</span> <br> {{ $setting->contact_address }}</li>
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section> --}}

    <section>
        @include('web.inc.contact')
    </section>
    <!--End Contact Section -->

    {{-- @if(isset($setting->google_map))
    <section class="map-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="embed-responsive embed-responsive-16by9">
                      {!! strip_tags($setting->google_map, '<iframe>') !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif --}}
    
    @if(count($clients) > 0)
    <section class="partner-section">
        <div class="container">
        <h2>Enterprises & Tech Companies Worldwide Trust Us</h2>
        <div class="row gap-2 justify-content-center text-center partner-logos align-items-center">
            @foreach($clients as $client)
            <div class="col-6 col-sm-4 col-md-2 col-lg-2-4 bg-white px-3 py-0 d-flex align-items-center justify-content-center m-1" style="height: 90px;">
            <img src="{{ asset('uploads/client/'.$client->image_path) }}" alt="{{ $client->title }}" class="img-fluid my-1"/>
            </div>
            @endforeach
        </div>
        </div>
    </section>
    @endif


@endsection