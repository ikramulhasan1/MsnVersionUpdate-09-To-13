@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('about-us');
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
                    <h1>{{ __('navbar.about') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.about') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    @if(isset($about) || count($counters) > 0)
    <!-- About Section -->    
    <section class="our-mission-section">
        <div class="container">
            @if(isset($about))
            <div class="sec-title left">
                <h2>{{ $about->title }}</h2>
                <div class="separater"></div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 wow fadeInRight animated">
                    <div class="inner-box">
                        <div class="text">{!! $about->description !!} <br/></div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    @if(isset($about->mission_title))
                    <div class="innner-box wow fadeInLeft">
                        <div class="info-box">
                            <h4>{{ $about->mission_title }}</h4>
                            <div class="text">{!! $about->mission_desc !!}</div>
                        </div>
                    </div>
                    @endif
                    @if(isset($about->vision_title))
                    <div class="innner-box wow fadeInLeft">
                        <div class="info-box">
                            <h4>{{ $about->vision_title }}</h4>
                            <div class="text">{!! $about->vision_desc !!}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(count($counters) > 0)
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 clearfix fun-fact-section">
                    <div class="fact-counter">
                        <div class="row">
                            @foreach($counters as $counter)
                            <!--Column-->
                            <div class="counter-column col-lg-3 col-md-6 col-sm-12 wow fadeInUp">
                                <div class="count-box">
                                    <div class="count"><span class="count-text" data-speed="5000" data-stop="{{ $counter->value }}">0</span></div>
                                    <div class="separater"></div>
                                    <h4 class="counter-title">{{ $counter->title }}</h4>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
    <!--End About Section --> 
    @endif

    @php
        $section_whyus = \App\Models\Section::section('why-us');
    @endphp
    @if(isset($section_whyus) || isset($about->video_id))
    <!--Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="container-fluid">
            <div class="row clearfix">
                @if(!empty($about->video_id))
                 <!--Image Column-->
                <div class="col-lg-6 col-md-12 col-sm-12 content-cloumn wow fadeInLeft animated">
                    
                    <div class="embed-responsive embed-responsive-16by9">
                      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $about->video_id }}?rel=0" allowfullscreen></iframe>
                    </div>
                    
                </div>
                @endif


                @if(count($chooses) > 0 && isset($section_whyus))
                <div class="col-lg-6 col-md-12 col-sm-12 content-cloumn">
                    <div class="inner-column">
                        <div class="sec-title left">
                            <h2>{{ $section_whyus->title }}</h2>
                            <div class="separater"></div>
                        </div>
                        <p>{!! $section_whyus->description !!}</p><br/>
                        <ul class="list-why-us">
                            @foreach($chooses as $choose)
                            <li>{{ $choose->title }}</li>
                            @endforeach
                        </ul>

                        @php
                            $page_quote = \App\Models\PageSetup::page('get-quote');
                            $page_contact = \App\Models\PageSetup::page('contact-us');
                        @endphp
                        @if(isset($page_quote))
                        <a href="{{ route('get-quote') }}" class="btn-theme btn-style-five">{{ __('navbar.get_quote') }}</a>
                        @elseif(isset($page_contact))
                        <a href="{{ route('contact') }}" class="btn-theme btn-style-five">{{ __('common.get_start') }}</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    <!--End Why Choose Us Section -->
    @endif


    @php
        $section_team = \App\Models\Section::section('team');
    @endphp
    @if(count($members) > 0 && isset($section_team))
    <!-- Team Section -->
    <section class="team-section style-two">
        <div class="container">
            <div class="sec-title left">
                <h2>{{ $section_team->title }}</h2>
                <div class="text">{!! $section_team->description !!}</div>
                <div class="separater"></div>
            </div>
            
            <div class="row clearfix">

                @foreach($members as $member)
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <!-- Team Block -->
                    <div class="team-block">
                        <div class="inner-box">
                            <div class="image-box">
                                <div class="image"><img src="{{ asset('uploads/member/'.$member->image_path) }}" alt="{{ $member->title }}"></div>
                                
                            </div>
                            <div class="info-box">
                                <h3 class="name"><a>{{ $member->title }}</a></h3>
                                <span class="designation">{{ $member->designation->title }}@if(isset($member->designation->department)), {{ $member->designation->department }}@endif</span>
                                @if(isset($member->email))
                                <span><i class="far fa-envelope"></i> {{ $member->email }}</span>
                                @endif
                                @if(isset($member->phone))
                                <span><i class="fas fa-phone-volume"></i> {{ $member->phone }}</span>
                                @endif
                            </div>
                            <ul class="social-links">
                                @if(isset($member->facebook))
                                <li><a href="{{ $member->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                @endif
                                @if(isset($member->twitter))
                                <li><a href="{{ $member->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                @endif
                                @if(isset($member->instagram))
                                <li><a href="{{ $member->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                @endif
                                @if(isset($member->linkedin))
                                <li><a href="{{ $member->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>
    <!--End Team Section -->
    @endif


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


    @php
        $section_clients = \App\Models\Section::section('clients');
    @endphp
    @if(count($clients) > 0 && isset($section_clients))
    <!--Clients Section-->
    <section class="clients-section style-two">
        <div class="container">
            <div class="sec-title centered">
                <h2>{{ $section_clients->title }}</h2>
                <div class="text">{!! $section_clients->description !!}</div>
                <div class="separater"></div>
            </div>
            <div class="sponsors-outer">
                <!--Sponsors Carousel-->
                <ul class="sponsors-carousel owl-carousel owl-theme">
                    @foreach($clients as $client)
                    <li class="slide-item"><figure class="image-box"><a href="{{ $client->link }}" target="_blank"><img src="{{ asset('uploads/client/'.$client->image_path) }}" alt="{{ $client->title }}"></a></figure></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <!--End Clients Section-->
    @endif

@endsection