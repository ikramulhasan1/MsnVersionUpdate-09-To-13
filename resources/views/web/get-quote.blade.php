@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('get-quote');
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
                    <h1>{{ __('navbar.get_quote') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.get_quote') }}</li>
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
                    $section_getquote = \App\Models\Section::section('get-quote');
                @endphp
                @if(isset($section_getquote))
                <!-- Form Column -->
                <div class="form-column col-lg-12 col-md-12 col-sm-12">
                     <div class="sec-title left">
                        <h2>{{ $section_getquote->title }}</h2>
                        <div class="text">{!! $section_getquote->description !!}</div>
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
                            <form method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data" accept-charset="utf-8">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="name" placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="email" name="email" placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="phone" placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="company" placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
                                    </div>
                                    
                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="address" placeholder="{{ __('form.address') }}" value="{{ old('address') }}" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                        <input type="text" name="city" placeholder="{{ __('form.city') }}" value="{{ old('city') }}" required>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="form-element margin-top-20">
                                            <label for="prefer_contact">{{ __('form.prefer_contact') }} 
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <div class="custom-control custom-radio margin-bottom-30">
                                            <input class="custom-control-input" type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact') == '1') checked @else checked @endif required>

                                            <label class="custom-control-label" for="pre_email">
                                                {{ __('form.phone') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <div class="custom-control custom-radio margin-bottom-30">
                                            <input class="custom-control-input" type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact') == '2') checked @endif required>

                                            <label class="custom-control-label" for="pre_phone">
                                                {{ __('form.email') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <label for="services">{{ __('form.services') }}
                                        </label>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12">
                                      <div class="row">
                                        @foreach($services as $service)
                                        <div class="col-lg-6 col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="services[]" class="custom-control-input" value="{{ $service->id }}" @if(old('services') == $service->id) checked @endif id="service-{{ $service->id }}">
                                                <label class="custom-control-label" for="service-{{ $service->id }}">{{ $service->title }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                      </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-12 col-md-12">
                                        <textarea name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12">
                                      <div class="custom-file">
                                        <input type="file" name="file_path" class="custom-file-input" value="{{ old('file_path') }}" id="file_path">
                                        <label class="custom-file-label" for="file_path">{{ __('form.upload_file') }}</label>
                                      </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-6 col-md-12 text-right">
                                        <button class="theme-btn btn-style-one" type="submit" name="submit-form">{{ __('form.submit') }}</button>
                                    </div> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </section>
    <!--End Contact Section -->

    
    @php
        $section_process = \App\Models\Section::section('process');
    @endphp
    @if(count($processes) > 0 && isset($section_process))
    <!--Feautred Section -->
    <section class="feautred-section style-two" style="background-image: url({{ asset('web/images/background/process-bg.png') }});">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title left">
                        <h2>{{ $section_process->title }}</h2>
                        <div class="text">{!! $section_process->description !!}</div>
                        <div class="separater"></div>
                    </div>
                </div>
            </div>
            <div class="featured-box row clearfix">
                @foreach($processes as $key => $process)
                <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="{{ ($key + 1) * 200 }}ms">
                    <div class="inner-box">
                        <div class="title-box">
                            <h4><span class="numbe-post">{{ $key + 1 }}</span>{{ $process->title }}</h4>
                        </div>
                        <div class="lower-content">
                            <div class="text">{!! $process->description !!}</div> 
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!--End Feautred Section -->
    @endif

@endsection