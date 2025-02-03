@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('faqs');
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
                    <h1>{{ __('navbar.faqs') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.faqs') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->


    @php
        $section_faqs = \App\Models\Section::section('faqs');
    @endphp
    @if(isset($section_faqs))
    <!--FAQs Section-->
    <section class="faq-section-two" style="background-image: url({{ asset('web/images/background/pricetable-bg.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title left">
                        <h2>{{ $section_faqs->title }}</h2>
                        <div class="text">{!! $section_faqs->description !!}</div>
                        <div class="separater"></div>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <!--FAQs Category Widget-->
                <div class="faq-list-sidebar">
                    <ul class="faq-cat">
                        @foreach($faq_categories as $faq_category)
                        <li class="@if(isset($current_category)) @if($current_category->id == $faq_category->id) active @endif @endif">
                            <a href="{{ route('faqs.category', $faq_category->slug) }}">{{ $faq_category->title }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="accordion-column col-lg-8 col-md-12 col-sm-12">
                <ul class="accordion-box">

                    @foreach($faqs as $key => $faq)
                    <!--Accordion Block-->
                    <li class="accordion block">
                        <div class="acc-btn @if($key== 0) active @endif"><div class="icon-outer"><span class="icon icon_plus fas fa-plus"></span> <span class="icon icon_minus far fa-minus"></span> </div> {{ $faq->title }}</div> 
                        <div class="acc-content @if($key== 0) current @endif">
                            <div class="content">
                                <div class="text">{!! $faq->description !!}</div>
                            </div>
                        </div>
                    </li>
                    @endforeach

                </ul>
            </div>
            </div>
        </div>
    </section>
    <!--End FAQs Section-->
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