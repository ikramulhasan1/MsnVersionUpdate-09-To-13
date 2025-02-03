@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('portfolio');
@endphp
@if(isset($header))

    @section('title', $portfolio->title)

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
    <meta property='og:title' content="{{ $portfolio->title }}"/>
    <meta property='og:description' content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}"/>
    <meta property='og:url' content="{{ route('portfolio.single', $portfolio->slug) }}"/>
    <meta property='og:image' content="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}"/>


    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('portfolio.single', $portfolio->slug) }}" />
    <meta name="twitter:title" content="{{ $portfolio->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}" />
    @endif
@endsection

@section('content')

    <!--Page Title-->
    <section class="page-title">
        <div class="container">
            <div class="inner-container clearfix">
                <div class="title-box">
                    <h1>{{ $portfolio->title }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.portfolio-detail') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    @if(isset($portfolio))
    <!--Portfolio Detail Section-->
    <section class="project-details-section">
        <div class="project-detail">
            <div class="container">
                <!-- Upper Box -->
                <div class="upper-box">
                    <div class="row project-tabs clearfix">
                        <div class="content-column col-lg-8 col-md-12 col-sm-12">
                            <figure class="image"><a href="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}" class="lightbox-image" data-fancybox="images"><img src="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}" alt="{{ $portfolio->title }}"></a></figure>
                        </div>
                    </div>
                </div>
                
                <!--Lower Content-->
                <div class="lower-content"> 
                    <div class="row clearfix">
                        
                        <!--Content Column-->
                        <div class="content-column col-lg-8 col-md-12 col-sm-12">
                            <div class="inner-column">
                                <h2>{{ $portfolio->title }}</h2>
                                
                                <div>
                                    {!! $portfolio->description !!}
                                </div>

                                @if(!empty($portfolio->video_id))
                                <div class="embed-responsive embed-responsive-16by9">
                                  <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $portfolio->video_id }}?rel=0" allowfullscreen></iframe>
                                </div>
                                @endif
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
        </div>
    </section>
    <!--End Portfolio Details-->
    @endif

@endsection