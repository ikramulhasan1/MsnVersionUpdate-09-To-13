@extends('web.layouts.master')
@section('title', $page->title)

@section('top_meta_tags')
@if(isset($page->description))
<meta name="description" content="{!! str_limit(strip_tags($page->description), 160, ' ...') !!}">
@else
<meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
@endif
@endsection

@section('social_meta_tags')
@if(isset($setting))
<meta property="og:type" content="website">
<meta property='og:site_name' content="{{ $setting->title }}" />
<meta property='og:title' content="{{ $page->title }}" />
<meta property='og:description' content="{!! str_limit(strip_tags($page->description), 160, ' ...') !!}" />
<meta property='og:url' content="{{ route('page.single', $page->slug) }}" />
<meta property='og:image' content="{{ asset('uploads/page/'.$page->image_path) }}" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
<meta name="twitter:creator" content="@HiTechParks" />
<meta name="twitter:url" content="{{ route('page.single', $page->slug) }}" />
<meta name="twitter:title" content="{{ $page->title }}" />
<meta name="twitter:description" content="{!! str_limit(strip_tags($page->description), 160, ' ...') !!}" />
<meta name="twitter:image" content="{{ asset('uploads/page/'.$page->image_path) }}" />
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
        /* list-style: decimal; */
        margin-left: 30px !important;
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

    .sidebar-page-container {
        padding-top: 50px !important;
    }

    .about-hero-section {
      /* background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('//images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover; */
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
</style>
@if(isset($page))
<!--Page Title-->
{{-- <section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ $page->title }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ $page->title }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section> --}}

  <section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>{{ $page->title }}</h1>
      <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
    </div>
  </section>
  <section class="page-title p-0" style="background-color: black;">
    <div class="container d-flex" style="height: 40px; align-items: center; justify-content: flex-end;">
        <div class="inner-container clearfix">
            {{-- <div class="title-box">
                <h1>{{ $page->title }}</h1>
            </div> --}}
            <div class="bread-crumb">
                <ul class="p-0">
                    <li>{{ $page->title }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!--End Page Title-->
@endif

@if(isset($page))
<!-- Sidebar Page Container -->
<div class="sidebar-page-container">
    <div class="container">
        <div class="row clearfix">
            <!--Content Side-->
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="blog-detail">
                    <!-- News Block -->
                    <div class="news-block">
                        <div class="inner-box">
                            @if(is_file('uploads/page/'.$page->image_path))
                            <div class="image-box">
                                <figure class="image"><img src="{{ asset('uploads/page/'.$page->image_path) }}" alt="{{ $page->title }}"></figure>
                            </div>
                            @endif
                            <div class="caption-box border-0 p-0 mt-5">
                                <div class="inner">
                                    <h2 class="font-weight-bold "><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a></h2>
                                    <br />
                                    <div class="description">
                                        {!! $page->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- End Sidebar Container -->
@endif

@endsection