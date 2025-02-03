@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('pricing');
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
                    <h1>{{ __('navbar.pricing') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.pricing') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    @php
        $section_pricing = \App\Models\Section::section('pricing');
    @endphp
    @if(count($pricings) > 0 && isset($section_pricing))
    <!--Pricing Section-->
    <section class="price-section" style="background-image: url({{ asset('web/images/background/pricetable-bg.jpg') }});">
        <div class="container">
            <div class="sec-title centered">
                <h2>{{ $section_pricing->title }}</h2>
                <div class="text">{!! $section_pricing->description !!}</div>
                <div class="separater"></div>
            </div>
            <div class="outer-container pricing-tabs">
                <div class="clearfix">
                          
                    <!--Price Column-->
                    <div class="price-column">
                        <div class="inner-column">
                            
                            <div class="content">
                                <div class="row clearfix">
                                    @foreach($pricings as $pricing)  
                                    <!-- Price Block -->
                                    <div class="price-block col-lg-4 col-md-6 col-sm-12">
                                        <div class="inner-box">
                                            <div class="upper-box">
                                                <h3>{{ $pricing->title }}</h3>
                                            </div>
                                            <div class="price">
                                                <sub>{{ __('common.currency') }}</sub>{{ $pricing->price }}
                                                @if(isset($pricing->old_price))
                                                <del>{{ $pricing->old_price }}</del>
                                                @endif
                                               <span>/ {{ $pricing->duration }}</span>
                                            </div>
                                            <div class="middle-box">
                                                <ul>
                                                    @php
                                                        $features = json_decode($pricing->description, true);
                                                    @endphp

                                                    @if(isset($features))
                                                    @foreach($features as $key => $feature)
                                                    <li>{{ $feature }}</li>
                                                    @endforeach
                                                    @endif
                                                </ul>
                                            </div>

                                            @php
                                                $page_quote = \App\Models\PageSetup::page('get-quote');
                                                $page_contact = \App\Models\PageSetup::page('contact-us');
                                            @endphp
                                            @if(isset($page_quote))
                                            <a href="{{ route('get-quote') }}" class="purchased btn-style-three">{{ __('common.get_start') }}</a>
                                            @elseif(isset($page_contact))
                                            <a href="{{ route('contact') }}" class="purchased btn-style-three">{{ __('common.get_start') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                      
                </div>
            </div>
        </div>
    </section>
    <!--End Pricing Section-->
    @endif

    @php
        $section_clients = \App\Models\Section::section('clients');
    @endphp
    @if(count($clients) > 0 && isset($section_clients))
    <!--Clients Section-->
    <section class="clients-section style-two">
        <div class="container">
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