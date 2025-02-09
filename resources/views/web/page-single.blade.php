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
</style>
@if(isset($page))
<!--Page Title-->
<section class="page-title">
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
</section>
<!--End Page Title-->
@endif

@if(isset($page))
<!-- Sidebar Page Container -->
<div class="sidebar-page-container">
    <div class="container">
        <div class="row clearfix">
            <!--Content Side-->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">
                <div class="blog-detail">
                    <!-- News Block -->
                    <div class="news-block">
                        <div class="inner-box">
                            @if(is_file('uploads/page/'.$page->image_path))
                            <div class="image-box">
                                <figure class="image"><img src="{{ asset('uploads/page/'.$page->image_path) }}" alt="{{ $page->title }}"></figure>
                            </div>
                            @endif
                            <div class="caption-box">
                                <div class="inner">
                                    <h3><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a></h3>
                                    <br />
                                    <div>
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