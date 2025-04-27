@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('portfolio');
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
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

@endif

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







        /* Hero Section */
        .about-hero-section {
      background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('//images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center/cover;
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





/* portfolio-section */
.portfolio-section {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: #1d1d1d;
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
      background: linear-gradient(to right, #ff5a00, #ff9500);
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
      background: #ff5a00;
      color: #fff;
      border-color: #ff5a00;
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
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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
      background: rgba(0,0,0,0.5);
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

    /* .view-more-btn {
      background: #1d1d1d;
      color: #fff;
      padding: 14px 40px;
      font-size: 18px;
      border-radius: 50px;
      text-decoration: none;
      transition: background 0.3s ease;
    } */

    /* .view-more-btn:hover {
      background: #ff5a00;
    } */

</style>

<!--Page Title-->

<section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>{{ __('navbar.portfolios') }}</h1>
      <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
    </div>
  </section>
  <section class="page-title p-0" style="background-color: black;">
    <div class="container d-flex" style="height: 40px; align-items: center; justify-content: flex-end;">
        <div class="inner-container clearfix">
            {{-- <div class="title-box">
                <h1>{{ __('navbar.about') }}</h1>
            </div> --}}
            <div class="bread-crumb">
                <ul class="p-0">
                    <li>{{ __('navbar.portfolios') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!--End Page Title-->


{{-- @php
$section_portfolio = \App\Models\Section::section('portfolio');
@endphp
@if(count($portfolios) > 0 && isset($section_portfolio))
<!--Gallery Section-->
<section class="gallery-section">
    <!--Sortable Masonry-->
    <div class="sortable-masonry">
        <div class="container">
            <div class="sec-title centered">
                <h2>{{ $section_portfolio->title }}</h2>
                <div class="text description">{!! $section_portfolio->description !!}</div>
                <div class="separater"></div>
            </div>
            <!--Filter-->
            <div class="filters row clearfix">

                <ul class="filter-tabs filter-btns clearfix">
                    <li class="active filter" data-role="button" data-filter=".all">{{ __('common.all') }}</li>
                    @foreach($portfolio_categories as $portfolio_category)
                    <li class="filter" data-role="button" data-filter=".{{ $portfolio_category->slug }}">{{ $portfolio_category->title }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="row clearfix items-container">

                @foreach($portfolios as $portfolio)
                <!--Default Portfolio Item-->
                <div class="default-portfolio-item mix masonry-item all 
                        @foreach($portfolio->categories as $category)
                            {{ $category->slug }} 
                        @endforeach
                     col-lg-4 col-md-6 col-sm-12">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}" alt="{{ $portfolio->title }}"></figure>
                        <!--Overlay Box-->
                        <div class="overlay-box">
                            <div class="overlay-inner">
                                <div class="content">
                                    <div class="content-inner">
                                        <div class="tags">
                                            @foreach($portfolio->categories as $category)
                                            > {{ $category->title }}
                                            @endforeach
                                        </div>
                                        <h3><a href="{{ route('portfolio.single', $portfolio->slug) }}">{{ $portfolio->title }}</a></h3>
                                    </div>
                                    <!-- <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="link-btn">{{ __('common.read_more') }}</a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
<!--End Gallery Section-->
@endif --}}
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
            <button class="portfolio-filter-btn" data-filter=".{{ $portfolio_category->slug }}">{{ $portfolio_category->title }}</button>
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
                        <img src="{{ asset('uploads/portfolio/'.$portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="img-fluid">
                        <div class="portfolio-overlay">
                            <h5><a class="text-white" href="{{ route('portfolio.single', $portfolio->slug) }}">{{ $portfolio->title }}</a></h5>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
        </div>
  
      @php
      $page_portfolio = \App\Models\PageSetup::page('portfolio');
      @endphp

      {{-- @if(isset($page_portfolio))
      <div class="text-center mt-5" data-aos="zoom-in" data-aos-delay="500">
        <a href="{{ route('portfolios') }}" class="text-white btn view-more-btn">{{ __('common.view_more') }}</a>
      </div>
      @endif --}}
    </div>
  </section>
  
<!--End Gallery Section-->
@endif
{{-- portfolio-section --}}
<script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    
    // AOS Animation
    AOS.init();
  
    // Isotope Initialization
    var grid = document.querySelector('.portfolio-grid');
    var iso = new Isotope( grid, {
      itemSelector: '.portfolio-item',
      layoutMode: 'fitRows'
    });
  
    // Filter buttons
    var filterButtons = document.querySelectorAll('.portfolio-filter-btn');
  
    filterButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        var filterValue = button.getAttribute('data-filter');
        iso.arrange({ filter: filterValue });
  
        // Active class switching
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
      });
    });
</script>
@endsection



