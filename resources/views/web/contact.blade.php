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

@endif

@section('content')

    <!--Page Title-->
    <section class="page-title">
        <div class="container">
            <div class="inner-container clearfix">
                <div class="title-box">
                    <h1>{{ __('navbar.contact') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.contact') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">

                @php
                    $section_mail = \App\Models\Section::section('mail');
                @endphp
                @if(isset($section_mail))
                <!-- Form Column -->
                <div class="form-column col-lg-8 col-md-12 col-sm-12">
                     <div class="sec-title left">
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
                        <div class="text">{!! $section_contact->description !!}</div>
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
    </section>
    <!--End Contact Section -->

    @if(isset($setting->google_map))
    <!-- map-column Section -->
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
    <!-- End map-column Section -->
    @endif

@endsection