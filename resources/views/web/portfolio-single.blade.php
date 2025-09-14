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
        <meta property='og:site_name' content="{{ $setting->title }}" />
        <meta property='og:title' content="{{ $portfolio->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@HiTechParks" />
        <meta name="twitter:url" content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta name="twitter:title" content="{{ $portfolio->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />
    @endif
@endsection

@section('content')
    <style>
        table {
            width: px;
        }

        table,
        table th,
        table td {
            border: solid;
        }

        table th,
        table td {
            border: solid;
        }

        table th>ol>li,
        table td>ul>li,
        table th>ul>li,
        table td>ol>li {
            list-style: initial !important;
            margin-left: 20px;

        }

        .marker {
            background-color: yellow;
        }

        /* </p><table border="1" cellpadding="1" cellspacing="1" style="width:500px">  */

        .description>ul>li {
            margin-left: 30px !important;
            list-style: initial;
            font-size: 16px !important;
        }

        .description>ol>li {
            margin-left: 30px !important;
            all: revert;
            font-size: 16px !important;
        }

        .description>ul>li>ul>li {
            margin-left: 10px !important;
            list-style: initial;
            font-size: 16px !important;
        }

        .description>ol>li>ol>li {
            margin-left: 10px !important;
            all: revert;
            font-size: 16px !important;
        }

        .description>ol>li>ul>li {
            margin-left: 10px !important;
            list-style: initial;
            font-size: 16px !important;
        }

        .description>ul>li>ol>li {
            margin-left: 10px !important;
            all: revert;
            font-size: 16px !important;
        }

        .description>p>a {
            color: blue;
            font-weight: bold;
            text-decoration: underline;
        }

        .description>p {
            font-size: 18px !important;
        }




        /* Hero Section */
        .about-hero-section {
            /* background: linear-gradient(135deg, rgba(106, 17, 203, 0.9), rgba(37, 117, 252, 0.9)), url('//images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center/cover; */
            background-color: #052C58;
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

        .portfolio-btn{
            background-color: #052C58 !important;
            color: white !important;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
    <!--Page Title-->
    {{-- <section class="page-title">
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
    </section> --}}

    <section class="about-hero-section" data-aos="fade">
        <div class="container">
            <h1>{{ $portfolio->title }}</h1>
            <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
        </div>
    </section>
    <section class="page-title p-0" style="background-color: black;">
        <div class="container d-flex" style="height: 40px; align-items: center; justify-content: flex-end;">
            <div class="inner-container clearfix">
                {{-- <div class="title-box">
                    <h1>{{ __('navbar.contact') }}</h1>
                </div> --}}
                <div class="bread-crumb">
                    <ul class="p-0">
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
                                <figure class="image"><a href="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}"
                                        class="lightbox-image" data-fancybox="images"><img
                                            src="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}"
                                            alt="{{ $portfolio->title }}"></a></figure>
                            </div>
                        </div>
                    </div>
                    <div class="my-4">
                        <div class="row d-flex justify-content-between content-column col-lg-8 col-md-12 col-sm-12">
                            @if (!empty($portfolio->link))
                                <div class="mb-3">
                                    <a class="portfolio-btn" target="_blank" href="{{ $portfolio->link }}">Web view
                                        {{-- <img
                                            style="width:100%; height: 100%; box-shadow: 10px 10px 8px #888888;"
                                            src="{{ asset('uploads/portfolio/view/Frontend.png') }}" alt="Frontend view"> --}}
                                        </a>
                                </div>
                            @endif
                            @if (!empty($portfolio->link2))
                                <div class="mb-3">
                                    <a class="portfolio-btn" target="_blank" href="{{ $portfolio->link2 }}">Admin dashboard
                                        {{-- <img
                                            style="width:100%; height: 100%; box-shadow: 10px 10px 8px #888888;"
                                            src="{{ asset('uploads/portfolio/view/Admin.png') }}" alt="Admin view"> --}}
                                        </a>
                                </div>
                            @endif
                            @if (!empty($portfolio->link3))
                                <div class="mb-3">
                                    <a class="portfolio-btn" target="_blank" href="{{ $portfolio->link3 }}">User dashboard
                                        {{-- <img
                                            style="width:100%; height: 100%; box-shadow: 10px 10px 8px #888888;"
                                            src="{{ asset('uploads/portfolio/view/User.png') }}" alt="Admin view"> --}}
                                        </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!--Lower Content-->
                    <div class="lower-content">
                        <div class="row clearfix">

                            <!--Content Column-->
                            <div class="content-column col-lg-8 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <h2>{{ $portfolio->title }}</h2>
                                    {{-- <div class="description">

                                        {!! $portfolio->description !!}
                                    </div> --}}
                                    @php
                                        $modifiedDescription = preg_replace_callback(
                                            '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>.*?<\/a>/i',
                                            function ($matches) {
                                                $url = $matches[1];
                                                return '<a href="' . $url . '" style="font-weight: 700; color: #052C58;" target="_blank" rel="noopener noreferrer">Visit Now</a>';
                                            },
                                            $portfolio->description
                                        );
                                    @endphp

                                    <div class="description">
                                        {!! $modifiedDescription !!}
                                    </div>



                                    @if(!empty($portfolio->video_id))
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item"
                                                src="https://www.youtube.com/embed/{{ $portfolio->video_id }}?rel=0"
                                                allowfullscreen></iframe>
                                        </div>
                                    @endif
                                </div>

                                @php
                                    $page_quote = \App\Models\PageSetup::page('get-quote');
                                    $page_contact = \App\Models\PageSetup::page('contact-us');
                                @endphp
                                @if(isset($page_quote))
                                    <a style="background-color: #052C58 !important;" href="{{ route('get-quote') }}"
                                        class="theme-btn btn-style-four mt-3">{{ __('navbar.get_quote') }}</a>
                                @elseif(isset($page_contact))
                                    <a style="background-color: #052C58 !important;" href="{{ route('contact') }}"
                                        class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
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