@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('services');
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
                    <h1>{{ __('navbar.services') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.services') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->


    @php
        $section_services = \App\Models\Section::section('services');
    @endphp
    @if(count($services) > 0 && isset($section_services))
    <!-- Services Section -->
    <section class="services-section style-four" style="background-image: url({{ asset('web/images/background/services-bg.png') }});">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title centered">
                        <h2>{{ $section_services->title }}</h2>
                        <div class="text">{!! $section_services->description !!}</div>
                        <div class="separater"></div>
                    </div> 
                </div>
            </div>   
            <div class="row clearfix">

                @foreach($services as $service)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <!-- Service Block -->
                    <div class="service-block wow fadeInDown">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}"/></figure>
                                <div class="overlay-box"><a href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more') }}</a></div>
                            </div>
                            <div class="lower-content">
                                <h3><a href="{{ route('service.single', $service->slug) }}">{{ $service->title }}</a></h3>
                                <div class="text">{!! strip_tags($service->short_desc) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>
        </div>
    </section>
    <!--End Services Section -->
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

@endsection