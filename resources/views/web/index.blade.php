@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('home');
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

@section('social_meta_tags')
  @if(isset($setting))
    <meta property="og:type" content="website">
    <meta property='og:site_name' content="{{ $setting->title }}" />
    <meta property='og:title' content="{{ $setting->title }}" />
    <meta property='og:description' content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
    <meta property='og:url' content="{{ route('home') }}" />
    <meta property='og:image' content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />


    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('home') }}" />
    <meta name="twitter:title" content="{{ $setting->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />
  @endif

  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

@endsection
<style>
  .carousel-wrap {
    max-height: 450px !important;
  }

  .carousel-wrap .item {
    position: relative;
    color: white;
    max-height: 450px;
    overflow: hidden;
  }

  .row-item-content {
    height: 370px !important;
    max-height: 450px !important;
    max-width: 70% !important;
    display: flex;
    align-items: baseline;
    justify-content: start;
  }

  .item-content {
    max-height: 340px !important;
    position: relative;
    z-index: 2;
  }

  .item-content p {
    color: white !important;
  }

  .item-content h1 {
    font-weight: 700
  }

  .video-embed iframe {
    pointer-events: none;
  }


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

  /* Custom 5-column layout for large screens */
  @media (min-width: 992px) {
    .col-lg-2-4 {
      flex: 0 0 auto;
      width: 19%;
    }
  }



  /* model */
  .model-section {
    background-color: #052C58;
    font-family: 'Segoe UI', sans-serif !important;
    color: #ffffff;
    padding-bottom: 30px;
    padding-top: 30px;
  }

  .model-section h1 {
    font-size: 42px !important;
  }

  h1.title {
    font-size: 51px;
    font-weight: 900;
    text-align: center;
  }

  .subtitle {
    color: #ffffff;
    text-align: center;
    font-size: 25px;
    font-weight: 600;
  }

  .card-box {
    background: #fff;
    border-radius: 0px;
    padding: 30px 20px;

    height: 100%;
    position: relative;
  }

  .card-box img {
    width: 80px;
    height: 80px;
    margin-bottom: 15px;
  }

  .card-box h5 {
    font-weight: 700;
    color: #333333;
    font-size: 28px;
    text-align: center;
    margin-bottom: 10px;
  }

  .card-box p {
    color: #333333;
    font-size: 15px;
    font-weight: 500 !important;
    margin-bottom: 0px;
  }

  .card-box .btn {
    background-color: #052C58;
    color: white;
    font-weight: 600;
    border: none;
    padding: 10px 20px;
    font-size: 0.9rem;
    border-radius: 6px;
    margin-top: 15px;

  }

  .card-box .btn:hover {
    background-color: #052C58;
  }

  .card-box ul {
    list-style: none;
    /* remove default bullets */
    padding-left: 0;
    /* remove default padding */
  }

  .card-box ul li {
    position: relative;
    padding-left: 20px;
    /* space for custom bullet */
    margin-bottom: 0px;
    /* optional: add spacing between li */
    font-size: 16px;
    /* adjust font size as needed */
    color: #333333;
    /* text color */
  }

  .card-box ul li::before {
    content: '●';
    /* your custom bullet */
    position: absolute;
    left: 0;
    top: 0px;
    font-size: 18px;
    color: #00c853;
    /* green bullet color */
  }

  .border-success-bottom {
    border-bottom: 7px solid #3CC065;
  }

  .border-success-bottom2 {
    border-bottom: 7px solid #4492DC;
  }

  .fieldset-div {
    padding-top: 70px;
  }

  fieldset {
    border: 1px solid #3CC065 !important;
    border-radius: 10px;
    padding: 20px 30px 30px !important;
    margin-top: 50px;
    text-align: center;
    position: relative;
  }

  fieldset legend {
    width: auto !important;
    margin: 0 auto 0px !important;
    font-size: 25px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    position: absolute !important;
    top: -15px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background-color: #052C58 !important;
    padding: 0 15px !important;
    line-height: 1 !important;
  }

  fieldset p {
    color: #ffffff;
    margin-bottom: 30px;
    padding-top: 0px;
    font-size: 20px;
    font-weight: 500 !important;
  }

  .radio-options {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
  }

  .radio-options label {
    background-color: transparent;
    color: #00c48c;
    border: 1px solid #00c48c;
    border-radius: 6px;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    transition: 0.3s;
  }

  .radio-options input[type="radio"] {
    display: none;
  }

  .radio-options input[type="radio"]:checked+label {
    background-color: #00c48c;
    color: #fff;
  }

  .explore-btn {
    background-color: #3CC065;
    color: #fff;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 17px;
    border: none;
    margin-top: 10px;
  }

  .compare-link {
    display: block;
    margin-top: 15px;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: underline;
  }

  .compare-link:hover {
    color: #00B54A;
  }

  .our-mission-section {
    padding-bottom: 50px !important;
    padding-top: 70px !important;
    margin-bottom: 0px !important;
  }

  /* counter-column */
  .counter-column {
    padding-top: 40px !important;
  }


  /* tech section */
  .tech-section {
    background-color: #FAFAFA;
    font-family: 'Poppins', sans-serif;
    padding-top: 20px;
    padding-bottom: 30px;
  }

  .section-title {
    font-weight: 900;
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 10px;
    color: #212121;
  }

  .section-subtitle {
    text-align: center;
    color: #34A853;
    font-weight: 600;
    margin-bottom: 40px;
    font-size: 1.5rem;
  }

  .tech-card {
    background: #fff;
    border: 1px solid #E0E0E0;
    border-radius: 12px;
    padding: 15px 30px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
    text-decoration: none;
    margin: 5 !important;

  }

  .tech-card:hover {
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #3CC065;
  }

  .tech-card img {
    height: 35px;
    width: 35px;
    object-fit: contain;
  }

  .tech-title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0;
  }

  .tech-buttons {
    margin-top: 40px;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .btn-custom-orange {
    background-color: #052C58;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: background-color 0.3s;
  }

  .btn-custom-orange:hover {
    background-color: #052C58;
  }

  .btn-custom-outline {
    background-color: transparent;
    border: 2px solid #052C58;
    color: #052C58;
    border-radius: 12px;
    padding: 14px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
  }

  .btn-custom-outline:hover {
    background-color: #052C58;
    color: #fff;
  }


  /* blog */
  .latest-blogs {
    background-color: #f9f9f9;
    font-family: 'Poppins', sans-serif;
  }

  .blog-section-title {
    font-size: 44px;
    font-weight: 700;
    color: #052C58;
    margin-bottom: 10px;
  }

  .blog-section-subtitle {
    color: #052C58;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 30px;
  }

  .blog-card {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    height: 100%;
  }

  .blog-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 8px;
  }

  .blog-title {
    font-size: 20px;
    font-weight: 700;
    color: #1d1d1d;
    margin-bottom: 8px;
  }

  .blog-meta {
    color: #6c757d;
    font-size: 14px;
    margin-top: 8px;
  }

  .blog-btn-orange {
    background-color: #052C58;
    color: #ffffff;
    padding: 14px 36px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    text-transform: none;
    transition: background-color 0.3s ease;
  }

  .blog-btn-orange:hover {
    background-color: #052C58;
  }



  /* process section */
  /* process */
  .process-section {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f9fafc;
    padding: 80px 15px;
    /* padding-top: 50px !important;
      padding-bottom: 50px !important; */
  }

  .process-section-title {
    text-align: left;
    margin-bottom: 40px;
  }

  .process-section-title h2 {
    font-weight: 900;
    color: #333333;
  }

  .process-step-box {
    background-color: #fff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    padding: 30px 20px;
    height: 100%;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
  }

  .process-step-box:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  }

  .process-step-number {
    width: 35px;
    height: 35px;
    background-color: #052C58;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    position: absolute;
    top: -20px;
    left: 20px;
  }

  .process-step-icon {
    width: 30px;
    height: 30px;
    margin-right: 10px;
  }

  .process-step-heading {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 10px;
  }

  .process-step-heading img {
    margin-left: 0px;
  }

  .process-step-arrow {
    position: absolute;
    top: 50%;
    right: -40px;
    width: 40px;
    height: 2px;
    background: repeating-linear-gradient(to right,
        #999,
        #999 4px,
        transparent 4px,
        transparent 8px);
    animation: moveArrow 1s linear infinite;
  }

  .process-step-arrow::after {
    content: '';
    position: absolute;
    right: -6px;
    top: -4px;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-left: 6px solid #999;
  }

  @keyframes moveArrow {
    0% {
      background-position: 0;
    }

    100% {
      background-position: 8px;
    }
  }

  .process-btn-orange {
    background-color: #052C58;
    color: white;
    padding: 12px 26px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: uppercase;
    border: none;
  }

  .process-btn-orange:hover {
    background-color: #052C58;
    color: white
  }

  @media (max-width: 991px) {
    .process-step-arrow {
      display: none;
    }
  }

  .process-step-arrow.arrow-hidden {
    display: none !important;
  }

  .arrow-down {
    /* Customize this arrow to look like a vertical one */
    transform: rotate(90deg);
    /* or use a different SVG for down */
    /* Add margin or position tweaks as needed */
  }

  .process-description p {
    font-size: 16px !important;
    color: #333333 !important;

  }

  /* portfolio-section */
  .portfolio-section {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    color: #052C58;
  }

  .portfolio-section-title {
    font-size: 48px;
    font-weight: 700;
    position: relative;
    display: inline-block;
  }

  .portfolio-section-title::after {
    content: "";
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, #052C58, #07356b);
    margin: 12px auto 0;
    border-radius: 3px;
  }

  /* FILTER BUTTONS - HORIZONTAL SCROLL */
  .portfolio-filters-wrapper {
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }

  .portfolio-filters {
    display: inline-flex;
    gap: 12px;
    padding: 10px 0;
  }

  .portfolio-filter-btn {
    flex: 0 0 auto;
    background: #fff;
    border: 1px solid #dee2e6;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
    white-space: nowrap;
  }

  .portfolio-filter-btn.active,
  .portfolio-filter-btn:hover {
    background: #052C58;
    color: #fff;
    border-color: #052C58;
  }

  /* Hide ugly scrollbar (optional) */
  .portfolio-filters-wrapper::-webkit-scrollbar {
    height: 6px;
  }

  .portfolio-filters-wrapper::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
  }

  .portfolio-filters-wrapper::-webkit-scrollbar-track {
    background: transparent;
  }

  .portfolio-grid {
    margin-top: 30px;
  }

  .portfolio-card {
    overflow: hidden;
    position: relative;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    background: #fff;
    margin-bottom: 30px;
  }

  .portfolio-card img {
    width: 100%;
    height: auto;
    transition: transform 0.5s ease;
  }

  .portfolio-card:hover img {
    transform: scale(1.1);
  }

  .portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    transition: all 0.5s ease;
  }

  .portfolio-card:hover .portfolio-overlay {
    opacity: 1;
  }

  .portfolio-overlay h5 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 600;
  }

  .view-more-btn {
    background: #052C58;
    color: #fff;
    padding: 14px 40px;
    font-size: 18px;
    border-radius: 50px;
    text-decoration: none;
    transition: background 0.3s ease;
  }

  .view-more-btn:hover {
    background: #052C58;
  }



  /* case study */
  .case-studies-section {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background: #fff;
  }

  .case-studies-carousel-wrap {
    width: 100%;
    height: 500px;
    position: relative;
    overflow: hidden;
  }


  .owl-carousel .case-studies-item {
    width: 100%;
    height: 500px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    padding: 0 5%;
    color: #fff;
    position: relative;
  }

  /* Black overlay */
  .case-studies-item::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.646);
    z-index: 1;
  }

  .case-studies-item .case-studies-content {
    position: relative;
    z-index: 2;
    max-width: 700px;
  }

  .case-studies-item .case-studies-content h1 {
    font-size: 50px;
    font-weight: 900;
    line-height: 1.2;
    margin: 0 0 20px;
    color: #fff;
  }

  /* .case-studies-item .case-studies-content .btn {
      display: inline-block;
      background: #00c3ff;
      color: #000;
      padding: 10px 25px;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
    } */

  .case-studies-tech-tags {
    margin-bottom: 25px;
  }

  .case-studies-tech-tags span {
    display: inline-block;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 20px;
    margin: 5px;
    border: 1px solid #fff;
    border-radius: 50px;
    font-size: 16px;
  }

  /* Fixed Case Studies Badge */
  .case-studies-badge {
    position: absolute;
    top: 30px;
    left: 65px;
    background: #fff;
    color: #000;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 25px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
    border: 2px solid #3CC065;
    z-index: 20;
  }

  /* Move Dots to Left */
  .owl-dots {
    position: absolute !important;
    bottom: 30px !important;
    left: 30px !important;
    z-index: 15 !important;
    text-align: left !important;
  }

  .owl-dots .owl-dot {
    display: inline-block !important;
    margin: 0 5px !important;
  }

  .owl-dots .owl-dot span {
    width: 12px !important;
    height: 12px !important;
    background: rgba(255, 255, 255, 0.5) !important;
    display: block !important;
    border-radius: 50% !important;
    transition: all 0.3s ease !important;
  }

  .owl-dots .owl-dot.active span {
    background: #00c3ff !important;
    transform: scale(1.3) !important;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .case-studies-item .case-studies-content h1 {
      font-size: 26px;
    }

    .case-studies-badge {
      font-size: 14px;
      padding: 8px 16px;
    }

    .case-studies-item .case-studies-content .btn {
      padding: 8px 20px;
      font-size: 14px;
    }
  }

  .read-more-btn {
    background: #052C58;
    border: none;
    color: #fff;
    padding: 17px 30px;
    font-size: 17px;
    border-radius: 5px;
    transition: background 0.3s ease;
    text-decoration: none;
  }

  .read-more-btn:hover {
    background: #052C58;
  }

  .owl-prev,
  .owl-next {
    display: none !important;
  }

  .top-banner-img {
    justify-content: space-start;
  }

  @media (max-width: 575.98px) {
    .top-banner-img {
      justify-content: center;
    }

    .legend-p {
      margin-top: 50px;
    }

    .slider-img-title {
      font-size: 24px !important;
    }

    .row-item-content {
      height: 370px !important;
      max-height: 450px !important;
      max-width: 100% !important;
      display: flex;
      align-items: baseline;
      justify-content: center;
    }

  }



  /*  */
  .hero-section {
    /* background: linear-gradient(rgba(5, 44, 88, 0.85), rgba(5, 44, 88, 0.85)),
                  url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1920&q=80')
                  center/cover no-repeat; */
    color: #fff;
    padding: 130px 0;
    position: relative;
  }

  .hero-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(5, 44, 88, 0.3);
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero-section h1 {
    font-size: 3rem;
    line-height: 1.3;
    font-weight: 700;
  }

  .hero-section h1 span {
    color: #0d6efd;
  }

  .hero-section p {
    font-size: 1.1rem;
    color: #e2e2e2;
    margin-bottom: 30px;
  }

  .hero-section .btn {
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    border-radius: 50px;
  }

  .hero-section .btn:hover {
    transform: translateY(-3px);
  }

  .hero-image {
    max-width: 90%;
    animation: float 4s ease-in-out infinite;
    z-index: 2;
    position: relative;
  }

  /* Floating animation for the right image */
  @keyframes float {
    0% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-10px);
    }

    100% {
      transform: translateY(0);
    }
  }

  /* ===== Responsive ===== */
  @media (max-width: 991px) {
    .hero-section {
      text-align: center;
      padding: 100px 20px;
    }

    .hero-section h1 {
      font-size: 2.2rem;
    }

    .hero-section .btn {
      margin-bottom: 10px;
    }
  }
</style>
{{-- schema --}}
@section('schema_markup')
  <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebSite",
                "name": "{{ $setting->title }}",
                "url": "{{ route('home') }}",
                "description": "{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}",
                "publisher": {
                    "@type": "Organization",
                    "name": "MSN Softtech",
                    "logo": {
                        "@type": "ImageObject",
                        "url": "{{ asset('/uploads/setting/' . $setting->logo_path) }}"
                    }
                },

                "mainEntity": {
                    "@type": "LocalBusiness",
                    "name": "MSN Softtech",
                    "url": "{{ route('home') }}",
                    "logo": "{{ asset('/uploads/setting/' . $setting->logo_path) }}",
                    "description": "{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}",

                    "contactPoint": {
                        "@type": "ContactPoint",
                        "telephone": "{{ $setting->phone_two }}",
                        "contactType": "customer service"
                    },
                    "areaServed": {
                        "@type": "Country",
                        "name": "United States"
                    }
                }
            }
            </script>
@endsection


@section('content')
  <link rel="stylesheet" href="{{ asset('web/css/extra-index.css') }}">
  @if(count($sliders) > 0)

    @foreach($sliders as $slider)
    <section style="background: linear-gradient(rgba(5, 44, 88, 0.85), rgba(5, 44, 88, 0.85)),
                                  url('{{ asset('uploads/slider/' . $slider->image_path) }}')
                                  center/cover no-repeat;" class="hero-section d-flex align-items-center">
      <div class="container hero-content">
        <div class="row align-items-center">
          <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
            <h1 class="mb-3">{!! $slider->title !!}</h1>
            <p>
              {!! $slider->description !!}
            </p>
            <a href="{{ route('get-quote') }}" class="btn btn-primary px-4 py-2 me-2">Get Started</a>
            <a href="{{ route('services') }}" class="btn btn-outline-light px-4 py-2">WHAT WE OFFER</a>
          </div>
          <div class="col-lg-4 text-center text-lg-end"> 
            <!-- <img src="https://media.istockphoto.com/id/2193065392/photo/young-business-professionals-collaborating-in-a-modern-meeting-room.jpg?s=1024x1024&w=is&k=20&c=kEERak83iER3k1MUxHZyJKC_Vrdl7YSjh6Y80KWupbg="
                               alt="Digital Agency Illustration"
                               class="img-fluid hero-image"> -->
            
          </div>
        </div>
      </div>
    </section>
    @endforeach
    <!-- End Bnner Section -->



    {{-- <style>
      .hero-modern {
        padding-top: 100px;
        padding-bottom: 80px;
      }

      .hero-modern h1 span {
        color: #0d6efd;
      }

      .hero-modern .btn {
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
      }

      .hero-modern .btn-outline-secondary {
        color: #052C58;
        border-color: #052C58;
      }

      .hero-modern .btn-outline-secondary:hover {
        background-color: #052C58;
        color: #fff;
      }

      .hero-modern i {
        display: block;
      }

      .hero-modern .flex-fill {
        min-width: 120px;
      }

      @media(max-width: 992px) {
        .hero-modern .d-flex {
          justify-content: center;
        }
      }
    </style>
    @foreach($sliders as $slider)
      <section class="hero-modern py-5">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
              <h1 class="display-4 font-weight-bold" style="color: #052C58">{!! $slider->title !!}</h1>
              <p class="lead mt-3">{!! $slider->description !!}</p>
              <a href="{{ route('get-quote') }}" class="btn btn-primary mr-2 mt-3">Get Started</a>
              <a href="{{ route('services') }}" class="btn btn-outline-secondary mt-3">Our Services</a>

              <div class="d-flex flex-wrap mt-5">
                <div class="p-3 border rounded text-center flex-fill mx-2 mb-3">
                  <i class="bi bi-browser-chrome display-4 text-primary"></i>
                  <h6 class="mt-2">Web Development</h6>
                </div>
                <div class="p-3 border rounded text-center flex-fill mx-2 mb-3">
                  <i class="bi bi-phone display-4 text-primary"></i>
                  <h6 class="mt-2">Mobile Apps</h6>
                </div>
                <div class="p-3 border rounded text-center flex-fill mx-2 mb-3">
                  <i class="bi bi-bar-chart display-4 text-primary"></i>
                  <h6 class="mt-2">Digital Marketing</h6>
                </div>
              </div>
            </div>
            <div class="col-lg-6 text-center">
              <img src="{{ asset('uploads/slider/' . $slider->image_path) }}" class="img-fluid rounded" style="height: 400px"
                alt="Digital Agency Illustration">
            </div>
          </div>
        </div>
      </section>
    @endforeach --}}
  @endif

  @include('web.inc.client')
  @if(isset($about) || count($counters) > 0)
    <!-- About Section -->
    <section style="background-color: #ffffff" class="our-mission-section">
      <div class="container">

        @if(count($counters) > 0)
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 clearfix fun-fact-section">
              <h3 style="color: #052C58; font-weight: 700;">Our Impact in Numbers</h3>
              <div class="fact-counter">
                <div class="row ">
                  @foreach($counters as $counter)
                    <!--Column-->
                    <div class="counter-column col-lg-3 col-md-6 col-sm-12 wow fadeInUp ">
                      <div class="count-box border border-1 p-3 bg-white stats-card">
                        <div style="color: #052C58" class="count">
                          {{ $counter->value }}+
                          {{-- <span class="count-text" data-speed="5000" data-stop="{{ $counter->value }}">0</span> --}}
                        </div>
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
  @include('web.layouts.googlemeet')
  {{--
  @php
  $section_services = \App\Models\Section::section('services');
  @endphp
  @if(count($services) > 0 && isset($section_services))
  <section class="">
    <div class="container">
      <div class="sec-title centered">
        <h2>{{ $section_services->title }}</h2>
        <div class="text description">{!! $section_services->description !!}</div>
        <div class="separater"></div>
      </div>
      <div class="services-box row clearfix">
        <div class="services-carousel owl-carousel owl-theme">
          @foreach($services as $service)
          <div class="service-block wow fadeInDown">
            <div class="inner-box">
              <div class="image-box">
                <figure><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}" />
                </figure>
                <div class="overlay-box"><a href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more')
                    }}</a></div>
              </div>
              <div class="lower-content">
                <h3><a href="{{ route('service.single', $service->slug) }}">{{ $service->short_title }}</a></h3>
                <div class="text text-left ">{!! strip_tags(Str::words($service->short_desc, 20)) !!}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
  @endif --}}
  {{-- model --}}

  <section class="model-section">
    <div class="container py-5">
      <h1 class="title">Flexible Engagement Models</h1>
      <p id="compare" class="subtitle mt-3">
        Tailored ways to collaborate — pick the model that matches your goals and budget.
      </p>

      <!-- Engagement Model Cards -->
      <form id="modelForm" action="{{ route('goToQuotePage') }}" method="post" accept-charset="utf-8">
        @csrf
        <div class="row mt-5 g-4">
          <!-- Managed Team -->
          <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom2">
              <input type="radio" name="work_model" id="model-fixed-price" value="Fixed Price Model" hidden>
              <img
                src="https://thumbs.dreamstime.com/b/fixed-price-badge-sign-white-background-design-vector-366219601.jpg"
                alt="Managed Team Icon" loading="lazy">
              <h5>Fixed Price Model</h5>
              <p>Perfect for projects with a clearly defined scope. You pay a set price for guaranteed delivery.</p>
              {{-- <p>For clear, small projects with a fixed budget</p> --}}
              {{-- <ul>
                <li>Fixed cost & timeline</li>
                <li>No surprises</li>
                <li><strong>Best for:</strong> Landing pages, ecommerce, company websites</li>
              </ul> --}}

              <button class="btn" type="button" onclick="selectAndSubmit('fixed-price')">Contact Us For Details →</button>
            </div>
          </div>

          {{-- Milestone-Based --}}
          <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom">
              <input type="radio" name="work_model" id="model-milestone-based" value="Milestone-Based Model" hidden>
              <img
                src="https://img.freepik.com/free-vector/ambition-abstract-concept-vector-illustration-business-ambition-determination-setting-big-goal-making-fast-career-self-confident-getting-what-you-want-desire-success-abstract-metaphor_335657-2892.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Staff Augmentation Icon" loading="lazy">
              <h5>Milestone-Based Model</h5>
              <p>Break your project into achievable phases and pay only as each milestone is successfully completed.</p>
              {{-- <p>Break the project into parts. Pay as we deliver.</p> --}}
              {{-- <ul>
                <li>Track progress easily</li>
                <li>Pay after each milestone</li>
                <li><strong>Best for:</strong> Big projects, apps, platforms</li>
              </ul> --}}
              <button class="btn" type="button" onclick="selectAndSubmit('milestone-based')">Contact Us For Details
                →</button>
            </div>
          </div>
          <!-- Staff Augmentation -->
          {{-- <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom">
              <input type="radio" name="work_model" id="model-hourly" value="Hourly Model" hidden>
              <img
                src="https://img.freepik.com/free-vector/alarm-clock-concept-illustration_114360-12926.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Staff Augmentation Icon">
              <h5>Hourly Model</h5>
              <p>Pay only for what you need, when you need it</p>
              <ul>
                <li>Full flexibility</li>
                <li>You decide what to change or add</li>
                <li><strong>Best for:</strong> Features, updates, redesigns</li>
              </ul>
              <button class="btn" type="button" onclick="selectAndSubmit('hourly')">Contact Us For Details →</button>
            </div>
          </div> --}}

          <!-- Fixed Cost -->


          <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom2">
              <input type="radio" name="work_model" id="model-monthly-support" value="Monthly Support" hidden>
              <img
                src="https://img.freepik.com/free-vector/customer-support-flat-design-illustration_23-2148889374.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Fixed Cost Icon" loading="lazy">
              <h5>Monthly Support</h5>
              <p>Ongoing assistance to ensure your business operations run smoothly without interruptions.</p>
              {{-- <p>Keep us on standby for monthly help</p> --}}
              {{-- <ul>
                <li>Regular updates & maintenance</li>
                <li>SEO/Performance/Bug fixing</li>
                <li><strong>Best for:</strong> Running websites, ongoing services</li>
              </ul> --}}
              <button class="btn" type="button" onclick="selectAndSubmit('monthly-support')">Share Your Requirements
                →</button>
            </div>
          </div>

          <!-- Fixed Cost -->


          {{-- <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom2">
              <input type="radio" name="work_model" id="model-dedicated-developer" value="Dedicated Developer / Team"
                hidden>
              <img
                src="https://img.freepik.com/free-vector/programming-concept-illustration_114360-1351.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Fixed Cost Icon">
              <h5>Dedicated Developer / Team</h5>
              <p>Your own remote developer without hiring full-time</p>
              <ul>
                <li>Full focus on your project</li>
                <li>Control & collaboration</li>
                <li><strong>Best for:</strong> Large projects, long-term goals</li>
              </ul>
              <button class="btn" type="button" onclick="selectAndSubmit('dedicated-developer')">Share Your Requirements
                →</button>
            </div>
          </div> --}}

          <!-- Staff Augmentation -->

          {{-- <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom">
              <input type="radio" name="work_model" id="model-milestone-based" value="Milestone-Based Model" hidden>
              <img
                src="https://img.freepik.com/free-vector/ambition-abstract-concept-vector-illustration-business-ambition-determination-setting-big-goal-making-fast-career-self-confident-getting-what-you-want-desire-success-abstract-metaphor_335657-2892.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Staff Augmentation Icon">
              <h5>Milestone-Based Model</h5>
              <p>Break the project into parts. Pay as we deliver.</p>
              <ul>
                <li>Track progress easily</li>
                <li>Pay after each milestone</li>
                <li><strong>Best for:</strong> Big projects, apps, platforms</li>
              </ul>
              <button class="btn" type="button" onclick="selectAndSubmit('milestone-based')">Contact Us For Details
                →</button>
            </div>
          </div> --}}

          <!-- Fixed Cost -->


          {{-- <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom2">
              <input type="radio" name="work_model" id="model-pay-as-you-go" value="Pay-as-You-Go" hidden>
              <img
                src="https://img.freepik.com/free-vector/payment-information-concept-illustration_114360-2886.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Fixed Cost Icon">
              <h5>Pay-as-You-Go</h5>
              <p>One-time small tasks & urgent help</p>
              <ul>
                <li>Quick & affordable</li>
                <li>No contract needed</li>
                <li><strong>Best for:</strong> Fixes, performance tweaks</li>
              </ul>
              <button class="btn" type="button" onclick="selectAndSubmit('pay-as-you-go')">Share Your Requirements
                →</button>
            </div>
          </div> --}}

          <!-- Fixed Cost -->


          {{-- <div class="col-md-4 mb-4">
            <div class="card-box border-success-bottom2">
              <input type="radio" name="work_model" id="model-partnership" value="Partnership / Revenue Share" hidden>
              <img
                src="https://img.freepik.com/free-vector/handshake-concept-illustration_114360-22576.jpg?ga=GA1.1.976765849.1741899989&semt=ais_hybrid&w=740"
                alt="Fixed Cost Icon">
              <h5>Partnership / Revenue Share</h5>
              <p>We grow with your idea</p>
              <ul>
                <li>We reduce your upfront cost</li>
                <li>We share success</li>
                <li><strong>Best for:</strong> Startups with big ideas but small budgets</li>
              </ul>
              <button class="btn" type="button" onclick="selectAndSubmit('partnership')">Share Your Requirements
                →</button>
            </div>
          </div> --}}

        </div>
      </form>
      <div class="fieldset-div">
        <!-- Options Section -->
        <form action="{{ route('goToQuotePage') }}" method="post" accept-charset="utf-8">
          @csrf

          <fieldset>
            {{-- <div class=""> --}}
              <legend>Need a Different Approach?</legend>
              <p class="legend-p">Explore More Ways We Can Help.</p>
              {{--
            </div> --}}

            <div class="radio-options">
              <div>
                <input type="radio" id="option1" value="Define My Project Scope" name="work_scope">
                <label for="option1">Define My Project Scope</label>
              </div>

              <div>
                <input type="radio" id="option3" value="Take Over My Project" name="work_scope">
                <label for="option3">Take Over My Project</label>
              </div>
              <div>
                <input type="radio" id="option4" value="Assist Me With a Task" name="work_scope">
                <label for="option4">Assist Me With a Task</label>
              </div>
            </div>

            <button class="explore-btn">Explore Your Options →</button>
            <a class="compare-link">Compare All Engagement Models</a>
          </fieldset>
        </form>
      </div>

    </div>
  </section>
  @php
    $section_services = \App\Models\Section::section('services');
  @endphp
  @if(count($services) > 0 && isset($section_services))
    <!-- Services Section -->
    <section style="margin-bottom: 70px; margin-top: 70px;">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="sec-title centered">
              <h2>{{ $section_services->title }}</h2>
              <div class="text description">{!! $section_services->description !!}</div>
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
                    <figure><img src="{{ asset('uploads/service/' . $service->image_path) }}" alt="{{ $service->title }}"
                        loading="lazy" />
                    </figure>
                    <div class="overlay-box"><a
                        href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more') }}</a></div>
                  </div>
                  <div class="lower-content">
                    <h3><a href="{{ route('service.single', $service->slug) }}">{{ $service->short_title }}</a></h3>
                    {{-- <div class="text text-left">{!! strip_tags(Str::words($service->short_desc, 20)) !!}</div> --}}
                  </div>
                </div>
              </div>
            </div>
          @endforeach

        </div>
        <div class="d-flex justify-content-center">
          <button id="go-services" class="btn text-white fw-800" style="background-color: #052C58; font-weight: bold;">View
            All Services</button>
        </div>
      </div>
    </section>
    <!--End Services Section -->
  @endif

  <section class="tech-section">
    <div class="container py-5">
      <h1 class="section-title">Design. Develop. Maintain. Scale.<br>Your Full-Stack Development Partner</h1>
      <p class="section-subtitle">30+ Team of Experts Skilled in 10+ Cutting-Edge Technologies</p>

      <div class="d-flex flex-wrap justify-content-center">
        @foreach ($technologies as $technology)
          <a href="{{ route('service.technology', $technology->slug) }}" class="tech-card">
            <img src="{{ asset('uploads/technology/' . $technology->logo_path) }}" alt="{{ $technology->short_title }}"
              loading="lazy">
            <p class="tech-title">{{ $technology->short_title }}</p>
          </a>

        @endforeach

      </div>

      <div class="tech-buttons mt-5">
        <a href="{{ route('get-quote') }}" class="btn btn-custom-orange">Get A Quote →</a>
        <a href="{{ route('technologies') }}" class="btn btn-custom-outline">See All Technologies →</a>
      </div>
    </div>
  </section>
  @php
    $section_portfolio = \App\Models\Section::section('portfolio');
  @endphp

  @if(count($portfolios) > 0 && isset($section_portfolio))
    <section class="portfolio-section py-5">
      <div class="container text-center">
        <h2 class="portfolio-section-title" data-aos="fade-up">{{ $section_portfolio->title }}</h2>
        <div class="text description">{!! $section_portfolio->description !!}</div>

        <!-- FILTER BUTTONS SCROLLABLE WRAPPER -->
        <div class="portfolio-filters-wrapper" data-aos="fade-up" data-aos-delay="200">
          <div class="portfolio-filters">
            <button class="portfolio-filter-btn active" data-filter="*">All</button>
            @foreach($portfolio_categories as $portfolio_category)
              <button class="portfolio-filter-btn"
                data-filter=".{{ $portfolio_category->slug }}">{{ $portfolio_category->title }}</button>
            @endforeach

          </div>
        </div>

        <div class="row portfolio-grid" data-aos="fade-up" data-aos-delay="400">
          <!-- Portfolio Items -->
          @foreach($portfolios as $portfolio)
            <a href="{{ route('portfolio.single', $portfolio->slug) }}">
              <div class="col-lg-4 col-md-6 portfolio-item @foreach($portfolio->categories as $category)
                {{ $category->slug }} 
              @endforeach">
                <div class="portfolio-card">
                  <img src="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" alt="{{ $portfolio->title }}"
                    class="img-fluid" loading="lazy">
                  <div class="portfolio-overlay">
                    <h5><a class="text-white"
                        href="{{ route('portfolio.single', $portfolio->slug) }}">{{ $portfolio->title }}</a></h5>
                  </div>
                </div>
              </div>
            </a>
          @endforeach
        </div>

        @php
          $page_portfolio = \App\Models\PageSetup::page('portfolio');
        @endphp

        @if(isset($page_portfolio))
          <div class="text-center mt-5" data-aos="zoom-in" data-aos-delay="500">
            <a href="{{ route('portfolios') }}" class="text-white btn view-more-btn">{{ __('common.view_more') }}</a>
          </div>
        @endif
      </div>
    </section>

    <!--End Gallery Section-->
  @endif


  @php
    $section_team = \App\Models\Section::section('team');
  @endphp
  @if(count($members) > 0 && isset($section_team))
    <!-- Team Section -->
    <section class="team-section">
      <div class="container">
        <div class="sec-title left">
          <h2>{{ $section_team->title }}</h2>
          <div class="text description">{!! $section_team->description !!}</div>
          <div class="separater"></div>
        </div>

        <div class="outer-column clearfix">
          <div class="team-carousal">
            @foreach($members as $member)
              <!-- Team Block -->
              <div class="team-block">
                <div class="inner-box">
                  <div class="image-box">
                    <div class="image"><img src="{{ asset('uploads/member/' . $member->image_path) }}"
                        alt="{{ $member->title }}" loading="lazy"></div>

                  </div>
                  <div class="info-box">
                    <h3 class="name"><a>{{ $member->title }}</a></h3>
                    <span class="designation">{{ $member->designation->title }}@if(isset($member->designation->department)),
                    {{ $member->designation->department }}@endif</span>
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
            @endforeach
          </div>

        </div>
      </div>
    </section>
    <!--End Team Section -->
  @endif


  @php
    $section_testimonials = \App\Models\Section::section('testimonials');
  @endphp
  @if(count($testimonials) > 0 && isset($section_testimonials))
    <!-- Testimonial Section Two-->
    <section style="background-color: #F9FAFC" class="testimonial-section">
      <div class="container">
        <div class="sec-title centered">
          <h2>{{ $section_testimonials->title }}</h2>
          <div class="text description">{!! $section_testimonials->description !!}</div>
          <div class="separater"></div>
        </div>

        <div class="testimonial-carousel owl-carousel owl-theme">
          @foreach($testimonials as $testimonial)
            <!-- Testimonial block two -->
            <div class="testimonial-block">
              <div class="inner-box">
                <div class="image-box">
                  <div class="thumb"><img src="{{ asset('uploads/testimonial/' . $testimonial->image_path) }}"
                      alt="{{ $testimonial->title }}" loading="lazy"></div>
                </div>
                <div class="info-box">
                  <div class="text description">{!! $testimonial->description !!}</div>
                  <h5 class="name">{{ $testimonial->title }}</h5>
                  <div class="company-name">{{ $testimonial->designation }}@if(isset($testimonial->organization)),
                  {{ $testimonial->organization }}@endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    <!--End Testimonial Section Two-->
  @endif


  @php
    $section_blog = \App\Models\Section::section('blog');
  @endphp


  @php
    $section_process = \App\Models\Section::section('process');
  @endphp

  @if(count($processes) > 0 && isset($section_process))
    {{-- process-section --}}
    <section class="process-section">
      <div class="container">
        <div class="process-section-title">
          <h2 style="padding-bottom: 30px !important">{{ $section_process->title }}</h2>
          {{-- <p class="text-muted">From research to testing, we ensure your design is intuitive, user-focused, and aligned
            with your goals.</p> --}}
        </div>

        <!-- First Row -->
        <div class="row g-4 mb-4">
          @foreach($processes as $key => $process)
            {{-- @foreach ($service->processworks as $key => $process) --}}
            <div class="col-md-4 mb-4">
              <div class="process-step-box">
                <div class="process-step-number">{{ $key + 1 }}</div>
                <div class="process-step-heading" style="font-size: 20px; color: #333333;">
                  {{-- <img style="width: 50px; height: 50px;" src="{{ asset('uploads/process/' . $process->image_path) }}"
                    class="process-step-icon" alt=""> --}}
                  {{ $process->title }}
                </div>

                <div class="process-description">
                  {!! $process->description !!}
                </div>
                {{-- Show arrow after every item except the last one --}}
                @php
                  $totalSteps = count($processes);
                  $showArrow = ($key != $totalSteps - 1); // hide arrow for last step
                @endphp

                <div
                  class="process-step-arrow d-none d-md-block {{ $showArrow ? ($key == 2 ? 'arrow-down' : '') : 'arrow-hidden' }}">
                </div>
              </div>
            </div>
            {{-- @endforeach --}}
          @endforeach
        </div>

        <!-- CTA -->
        <div class="text-center mt-5">
          <a href="https://msnsofttech.com/get-quote" class="btn process-btn-orange">Get in Touch With Us →</a>
        </div>
      </div>
    </section>
  @endif


  {{-- case study --}}
  <section class="case-studies-section">

    <div class="case-studies-carousel-wrap">
      <!-- Fixed Badge -->
      <div class="case-studies-badge">Case Studies</div>

      <div id="case-owl-carousel" class="owl-carousel owl-theme">

        <!-- Slide 1 -->
        <div class="case-studies-item"
          style="background-image: url('https://www.websitesinaflash.com/wp-content/uploads/2022/06/Pro-Theme-Website-1200x799.jpg');">
          <div class="case-studies-content">
            <h1>From Vision to Launch: Custom Web Development for a Growing Startup</h1>
            <div class="case-studies-tech-tags">
              <span>Node.js</span>
              <span>Ionic</span>
              <span>iOS</span>
              <span>Android</span>
            </div>
            <a href="#" class="read-more-btn">View Case Study ➔</a>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="case-studies-item"
          style="background-image: url('https://media.licdn.com/dms/image/v2/D4E12AQEa4pDRMW7YxA/article-cover_image-shrink_720_1280/article-cover_image-shrink_720_1280/0/1721072399995?e=2147483647&v=beta&t=6U7MgUOmoSmUucaC4WAmZ7rtczUwnxqoUH6bOV_Wbak');">
          <div class="case-studies-content">
            <h1>Revolutionizing Online Shopping Experience with AI</h1>
            <div class="case-studies-tech-tags">
              <span>Node.js</span>
              <span>Ionic</span>
              <span>iOS</span>
              <span>Android</span>
            </div>
            <a href="#" class="read-more-btn">View Case Study ➔</a>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="case-studies-item"
          style="background-image: url('https://media.licdn.com/dms/image/v2/D5612AQG1CwDBj2sjsg/article-cover_image-shrink_600_2000/article-cover_image-shrink_600_2000/0/1697532231304?e=2147483647&v=beta&t=uGstB5a4NDkGaNmXEhRizNmLKfU5Eab36YAiIeYI1eo');">
          <div class="case-studies-content">
            <h1>Scalable & Secure: Building a Laravel-Powered Enterprise Platform</h1>
            <div class="case-studies-tech-tags">
              <span>Node.js</span>
              <span>Ionic</span>
              <span>iOS</span>
              <span>Android</span>
            </div>
            <a href="#" class="read-more-btn">View Case Study ➔</a>
          </div>
        </div>

      </div>
    </div>
  </section>



  {{-- @if(count($clients) > 0)
  <section class="partner-section">
    <div class="container">
      <h2>Enterprises & Tech Companies Worldwide Trust Us</h2>
      <div class="row gap-2 justify-content-center text-center partner-logos align-items-center">
        @foreach($clients as $client)
        <div
          class="col-6 col-sm-4 col-md-2 col-lg-2-4 bg-white px-3 py-0 d-flex align-items-center justify-content-center m-1"
          style="height: 90px;">
          <img src="{{ asset('uploads/client/' . $client->image_path) }}" alt="{{ $client->title }}"
            class="img-fluid my-1" />
        </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif --}}

  <section class="latest-blogs py-5">
    <div class="container">
      <h2 class="blog-section-title">{{ $section_blog->title }}</h2>
      <p class="blog-section-subtitle">Explore Featured Insights</p>

      <div class="row g-4 mt-4">
        <!-- Blog Card 1 -->
        @foreach($articles as $key => $article)
          <div class="col-md-4">
            <div class="blog-card p-3">
              <img src="{{ asset('uploads/article/' . $article->image_path) }}" class="img-fluid blog-img"
                alt="{{ $article->title }}" loading="lazy">
              <div class="blog-content pt-3">
                <div class="blog-title"><a href="{{ route('blog.single', $article->slug) }}">{{ $article->title }}</a></div>
                <p class="blog-meta">By <span class="fw-bold">MSN Softtech</span>, in Digital Transformation</p>
              </div>
            </div>
          </div>
        @endforeach

      </div>

      <div class="text-center mt-5">
        <a href="{{ route('blogs') }}" class="btn blog-btn-orange">Read More Blog Posts →</a>
      </div>
    </div>
  </section>

  @section('scriptjs')
    <script>
      function selectAndSubmit(model) {
        document.getElementById('model-' + model).checked = true; // Select radio
        document.getElementById('modelForm').submit(); // Submit form
      }
    </script>


    {{-- banner --}}
    <script>
      $(document).ready(function () {
        $('.owl-carousel').owlCarousel({
          items: 1,
          loop: true,
          autoplay: true,
          autoplayTimeout: 5000,
          smartSpeed: 3000,
          animateOut: 'fadeOut',
          nav: true,
          navText: [],
          dots: false
        });
      });
    </script>

    {{-- case study --}}
    <script>
      $(document).ready(function () {
        $("#case-owl-carousel").owlCarousel({
          loop: true,
          margin: 0,
          nav: true,
          navText: [],
          dots: true,
          autoplay: true,
          autoplayTimeout: 9000,
          autoplayHoverPause: true,
          items: 1,
          smartSpeed: 1500,
        });
      });

    </script>


    <!-- Include YouTube Iframe API -->
    <script src="https://www.youtube.com/iframe_api"></script>

    <script>
      let players = [];

      // YouTube Iframe API onReady function
      function onYouTubeIframeAPIReady() {
        document.querySelectorAll('.youtube-bg-video').forEach((el, index) => {
          const videoId = el.dataset.videoId;

          // Create the player for each video
          players[index] = new YT.Player(el.id, {
            videoId: videoId,
            playerVars: {
              autoplay: 1,
              controls: 0,
              showinfo: 0,
              modestbranding: 1,
              rel: 0,
              loop: 1,
              mute: 1,
              playlist: videoId,
              iv_load_policy: 3,  // Hide annotations
              fs: 0,  // Disable fullscreen button
            },
            events: {
              onReady: function (event) {
                event.target.mute();
                event.target.playVideo();
              }
            }
          });
        });
      }
    </script>

    <script>
      document.getElementById("go-services").addEventListener("click", function () {
        window.location.href = "{{ route('services') }}";
      });
    </script>
    {{-- portfolio-section --}}
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      // AOS Animation
      AOS.init();

      // Isotope Initialization
      var grid = document.querySelector('.portfolio-grid');
      var iso = new Isotope(grid, {
        itemSelector: '.portfolio-item',
        layoutMode: 'fitRows'
      });

      // Filter buttons
      var filterButtons = document.querySelectorAll('.portfolio-filter-btn');

      filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          var filterValue = button.getAttribute('data-filter');
          iso.arrange({ filter: filterValue });

          // Active class switching
          filterButtons.forEach(btn => btn.classList.remove('active'));
          button.classList.add('active');
        });
      });
    </script>

  @endsection
@endsection