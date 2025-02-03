@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('services');
@endphp
@if(isset($header))

    @section('title', $service->title)

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

@section('social_meta_tags')
    @if(isset($setting))
    <meta property="og:type" content="website">
    <meta property='og:site_name' content="{{ $setting->title }}"/>
    <meta property='og:title' content="{{ $service->title }}"/>
    <meta property='og:description' content="{!! str_limit(strip_tags($service->description), 160, ' ...') !!}"/>
    <meta property='og:url' content="{{ route('service.single', $service->slug) }}"/>
    <meta property='og:image' content="{{ asset('uploads/service/'.$service->image_path) }}"/>


    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('service.single', $service->slug) }}" />
    <meta name="twitter:title" content="{{ $service->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($service->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('uploads/service/'.$service->image_path) }}" />
    @endif
@endsection

@section('content')

    <!--Page Title-->
    <section class="page-title">
        <div class="container">
            <div class="inner-container clearfix">
                <div class="title-box">
                    <h1>{{ $service->title }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.service-detail') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    @if(isset($service))
    <!--Sidebar Page Container-->
    <div class="sidebar-page-container">
        <div class="container">
            <div class="row clearfix">
                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                    <aside class="sidebar services-sidebar">
                    
                        <!--Service Category Widget-->
                        <div class="sidebar-widget sidebar-blog-category">
                            <ul class="blog-cat">
                                @foreach($service_lists as $service_list)
                                <li class="@if($service_list->id == $service->id) active @endif"><a href="{{ route('service.single', $service_list->slug) }}">{!! str_limit(strip_tags($service_list->title), 60, ' ...') !!}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    
                    </aside>
                </div>
                
                <!--Content Side-->
                <div class="content-side col-lg-8 col-md-12 col-sm-12">
                    <div class="service-detail">
                        <div class="inner-box">
                            <div class="image-box">
                                <div class="single-item-">
                                    <figure class="image"><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}" /></figure>
                                </div>
                            </div>
                            <h2>{{ $service->title }}</h2>
                            <div class="text">
                                {!! $service->description !!}
                            </div>
                        </div> 
                    </div>

                    @php
                        $page_quote = \App\Models\PageSetup::page('get-quote');
                        $page_contact = \App\Models\PageSetup::page('contact-us');
                    @endphp
                    @if(isset($page_quote))
                    <a href="{{ route('get-quote') }}" class="theme-btn btn-style-four mt-3">{{ __('navbar.get_quote') }}</a>
                    @elseif(isset($page_contact))
                    <a href="{{ route('contact') }}" class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection