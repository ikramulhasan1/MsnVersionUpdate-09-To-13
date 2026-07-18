@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('contact-us');
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

<style>
    /*  */
    /* client */
    .partner-section {
        padding: 60px 0;
        background-color: #F5F7F8;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .partner-section h2 {
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        color: #333333;
    }




    /* Hero Section */
    .about-hero-section {
        /* background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('//images.unsplash.com/photo-1603791440384-56cd371ee9a7?q=80&w=2034&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover; */
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
@endif

@section('content')

    <!--Page Title-->
    <!-- Hero Section -->
    <section class="about-hero-section" data-aos="fade">
        <div class="container">
            <h1>{{ __('navbar.contact') }}</h1>
            <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
        </div>
    </section>
    <section class="page-title p-0" style="background-color: black;">
        <div class="container d-flex" style="height: 40px; align-items: center; justify-content: flex-end;">
            <div class="inner-container clearfix">

                <div class="bread-crumb">
                    <ul class="p-0">
                        <li>{{ __('navbar.contact') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->


    <section>
        @include('web.inc.contact')
    </section>
    <!--End Contact Section -->

@endsection