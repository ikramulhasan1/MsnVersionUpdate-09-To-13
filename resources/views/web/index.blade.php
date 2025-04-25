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
<meta property='og:image' content="{{ asset('/uploads/setting/'.$setting->logo_path) }}" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
<meta name="twitter:creator" content="@HiTechParks" />
<meta name="twitter:url" content="{{ route('home') }}" />
<meta name="twitter:title" content="{{ $setting->title }}" />
<meta name="twitter:description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
<meta name="twitter:image" content="{{ asset('/uploads/setting/'.$setting->logo_path) }}" />
@endif
@endsection
<style>
    .carousel-wrap .item {
  position: relative;
  color: white;
  min-height: 85vh;
  overflow: hidden;
}

.item-content {
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
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    .partner-section h2 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
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
      background-color: #F5F7F8;
      font-family: 'Segoe UI', sans-serif;
      color: #333333;
    }

    h1.title {
      font-size: 51px;
      font-weight: 800;
      text-align: center;
    }

    .subtitle {
      color: #4492DC;
      text-align: center;
      font-size: 25px;
      font-weight: 600;
    }

    .card-box {
      background: #fff;
      border-radius: 0px;
      padding: 30px 20px;
      text-align: center;
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
      font-size: 31px;
    }

    .card-box p {
      color: #333333;
      font-size: 15px;
      font-weight: 500 !important;
    }

    .card-box .btn {
      background-color: #ff6f2c;
      color: white;
      font-weight: 600;
      border: none;
      padding: 10px 20px;
      font-size: 0.9rem;
      border-radius: 6px;
      margin-top: 15px;
    }

    .card-box .btn:hover {
      background-color: #e55d1b;
    }

    .border-success-bottom {
      border-bottom: 7px solid #3CC065;
    }

    .border-success-bottom2 {
      border-bottom: 7px solid #4492DC;
    }

    .fieldset-div{
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
        width: auto;
        margin: 0 auto 0px;
        font-size: 25px;
        font-weight: 700;
        color: #333333;
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #F5F7F8;
        padding: 0 15px;
        line-height: 1;
    }

    fieldset p {
      color: #333333;
      margin-bottom: 20px;
      padding-top: 20px;
      font-size: 20px;
      font-weight: 600;
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

    .radio-options input[type="radio"]:checked + label {
      background-color: #00c48c;
      color: #fff;
    }

    .explore-btn {
      background-color: #ff6f2c;
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
      text-decoration: none;
      color: #2138b9c6;
      font-size: 13px;
      font-weight: 700;
      text-decoration: underline;
    }

    .our-mission-section{
        padding-bottom: 40px !important;
        padding-top: 50px !important;
    }
</style>
{{-- schema  --}}
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
            "url": "{{ asset('/uploads/setting/'.$setting->logo_path) }}"
        }
    },

    "mainEntity": {
        "@type": "LocalBusiness",
        "name": "MSN Softtech",
        "url": "{{ route('home') }}",
        "logo": "{{ asset('/uploads/setting/'.$setting->logo_path) }}",
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
<!-- Bnner Section -->
<section class="banner-section">
    {{-- <div class="carousel-wrap">
        <div class="owl-carousel owl-theme">
        @foreach($sliders as $slider)
          <div class="item" style="justify-content: space-around; background-image: url({{ asset('uploads/slider/'.$slider->image_path) }});">
            <div class="row w-100">
              <div class="col-md-8 item-content">
                <div class="">
                  <h1 class="">{{ $slider->title }}</h1>
                  <p>{!! $slider->description !!}</p>

                  @php
                  $page_contact = \App\Models\PageSetup::page('contact-us');
                  @endphp
                  @if(isset($page_contact))
                  <a style="margin-top: 10px; position: relative; top: 150px; " href="{{ route('contact') }}" class="btn">{{ __('common.contact_us') }}</a>
                  @endif

                  @if(isset($slider->link))
                  <a style="margin-top: 10px; position: relative; top: 150px;" href="{{ $slider->link }}" target="_blank" class="btn">{{ __('common.services') }}</a>
                  @endif
    
                </div>
              </div>
               <div class="col-md-4 d-flex align-items-center justify-content-center short-item">
                <button class="btn">Discover</button>
              </div> 
            </div>
          </div>
        @endforeach
        </div>
    </div> --}}

    
    {{-- <div class="carousel-wrap">
        <div class="owl-carousel owl-theme">
          @foreach($sliders as $slider)
            <div class="item p-0" style="position: relative;">
              
              @if($slider->video_url)
                <div class="video-slide" style="position: relative; padding-top: 56.25%; height: 0; overflow: hidden;">
                    <iframe 
                        src="https://www.youtube.com/embed/{{ $slider->video_id }}?autoplay=1&mute=1&controls=1&rel=0" 
                        frameborder="0" 
                        allow="autoplay; encrypted-media" 
                        allowfullscreen 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                    </iframe>
                </div>
              @else
                <div class="image-slide" style="
                    background-image: url('{{ asset('uploads/slider/'.$slider->image_path) }}');
                    background-size: cover;
                    background-position: center;
                    height: 600px;
                    width: 100%;
                  ">
                </div>
              @endif
      
              <div class="slide-overlay-content" style="
                  position: absolute;
                  top: 0%;
                  left: 0%;
                  height: 600px;
                  z-index: 10;
                  color: white;
                  background: rgba(0, 0, 0, 0.4);
                  padding: 20px;
                  border-radius: 0px;
                ">
                
                    <h1>{{ $slider->title }}</h1>
                    <p style="color: white !important" >{!! $slider->description !!}</p>
        
                    @php $page_contact = \App\Models\PageSetup::page('contact-us'); @endphp
                    @if(isset($page_contact))
                    <a href="{{ route('contact') }}" class="btn btn-light mt-3">{{ __('common.contact_us') }}</a>
                    @endif
        
                    @if(isset($slider->link))
                    <a href="{{ $slider->link }}" target="_blank" class="btn btn-outline-light mt-2">{{ __('common.services') }}</a>
                    @endif
                
              </div>
              
            </div>
          @endforeach
        </div>
      </div> --}}
      
      <div class="carousel-wrap">
        <div class="owl-carousel owl-theme">
          @foreach($sliders as $slider)
            @php
              $style = '';
              if ($slider->media_type === 'image' && $slider->image_path) {
                $style = "background-image: url('" . asset('uploads/slider/' . $slider->image_path) . "'); background-size: cover; background-position: center;";
              }
            @endphp
      
            <div class="item"
                 style="justify-content: space-around; position: relative; min-height: 90vh; {{ $style }}"
                 @if($slider->media_type === 'video' && $slider->video_id)
                   data-video-id="{{ $slider->video_id }}"
                 @endif>
      
              {{-- Background YouTube Video --}}
              @if($slider->media_type === 'video' && $slider->video_id)
                <div class="video-embed" style="position: absolute; inset: 0; z-index: 0; overflow: hidden;">
                    <iframe
                    width="100%" height="100%"
                    src="https://www.youtube.com/embed/{{ $slider->video_id }}?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&playlist={{ $slider->video_id }}&modestbranding=1&rel=0"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; z-index: 0;">
                  </iframe>
                  
                </div>
              @endif
      
              {{-- Foreground Content --}}
              <div class="row w-100 position-relative" style="z-index: 2;">
                <div class="col-md-12 item-content">
                  <div>
                    <h1>{{ $slider->title }}</h1>
                    <p>{!! $slider->description !!}</p>
      
                    @php
                      $page_contact = \App\Models\PageSetup::page('contact-us');
                    @endphp
      
                    {{-- @if(isset($page_contact))
                      <a href="{{ route('contact') }}" class="btn" style="margin-top: 10px; position: relative; top: 150px;">
                        {{ __('common.contact_us') }}
                      </a>
                    @endif --}}
                    <button id="open-modal" class=" googleMeetBtn" style="position: relative; top: 150px;">Discuss Your Requirements →</button>

                    {{-- @if(isset($slider->link))
                      <a href="{{ $slider->link }}" class="btn" target="_blank" style="margin-top: 10px; position: relative; top: 150px;">
                        {{ __('common.services') }}
                      </a>
                    @endif --}}
                  </div>
                </div>
      
                {{-- <div class="col-md-4 d-flex align-items-center justify-content-center short-item">
                  <button class="btn">Discover</button>
                </div> --}}
              </div>
            </div>
          @endforeach
        </div>
      </div>
      
</section>
<!-- End Bnner Section -->
@endif


@if(isset($about) || count($counters) > 0)
<!-- About Section -->
<section style="background-color: #F5F7F8" class="our-mission-section">
    <div class="container">
        {{-- @if(isset($about))
        <div class="sec-title left">
            <h2>{{ $about->title }}</h2>
            <div class="separater"></div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 wow fadeInRight animated">
                <div class="inner-box ">
                    <div class="text description">{!! $about->description !!} <br /></div>
                    <br />
                    @php
                    $page_about = \App\Models\PageSetup::page('about-us');
                    @endphp
                    @if(isset($page_about))
                    <div class="link-box"><a href="{{ route('about') }}" class="theme-btn btn-style-three">{{ __('common.read_more') }}</a></div>
                    @endif
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                @if(isset($about->mission_title))
                <div class="innner-box wow fadeInLeft">
                    <div class="info-box">
                        <h4>{{ $about->mission_title }}</h4>
                        <div class="text">{!! $about->mission_desc !!}</div>
                    </div>
                </div>
                @endif
                @if(isset($about->vision_title))
                <div class="innner-box wow fadeInLeft">
                    <div class="info-box">
                        <h4>{{ $about->vision_title }}</h4>
                        <div class="text">{!! $about->vision_desc !!}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif --}}

        @if(count($counters) > 0)
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 clearfix fun-fact-section">
                <div class="fact-counter">
                    <div class="row ">
                        @foreach($counters as $counter)
                        <!--Column-->
                        <div class="counter-column col-lg-3 col-md-6 col-sm-12 wow fadeInUp ">
                            <div class="count-box border border-1 p-3 bg-white stats-card">
                                <div style="color: #1EC000" class="count">
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

        {{-- model --}}
        
 <section class="model-section">
    <div class="container py-5">
      <h1 class="title">Flexible Engagement Models<br>to Suit Your Needs</h1>
      <p id="compare" class="subtitle mt-3">
        Find the Perfect Solution for Your Project, Whether You Need a<br>
        Fully Managed Team, Staff Augmentation, or a Fixed-Price Approach.
      </p>
  
      <!-- Engagement Model Cards -->
      <div class="row mt-5 g-4">
        <!-- Managed Team -->
        <div class="col-md-4">
          <div class="card-box border-success-bottom2">
            <img src="https://img.icons8.com/ios/100/000000/developer.png" alt="Managed Team Icon">
            <h5>Managed Team</h5>
            <p>Your product, our dedicated team. From concept to completion, we handle it all.</p>
            <button class="btn">Contact Us For Details →</button>
          </div>
        </div>
  
        <!-- Staff Augmentation -->
        <div class="col-md-4">
          <div class="card-box border-success-bottom">
            <img src="https://img.icons8.com/ios/100/000000/teamwork.png" alt="Staff Augmentation Icon">
            <h5>Staff Augmentation</h5>
            <p>Need extra hands? Our experts seamlessly join your team, providing the skills you need, when you need them.</p>
            <button class="btn">Contact Us For Details →</button>
          </div>
        </div>
  
        <!-- Fixed Cost -->
        <div class="col-md-4">
          <div class="card-box border-success-bottom2">
            <img src="https://img.icons8.com/ios/100/000000/receipt-approved.png" alt="Fixed Cost Icon">
            <h5>Fixed Cost</h5>
            <p>Upfront price, guaranteed delivery. Your project completed on time and within budget.</p>
            <button class="btn">Share Your Requirements →</button>
          </div>
        </div>
      </div>
  
      <div class="fieldset-div">
        <!-- Options Section -->
      <fieldset>
        <legend>Need a Different Approach?</legend>
        <p>Explore More Ways We Can Help.</p>
  
        <div class="radio-options">
          <div>
            <input type="radio" id="option1" name="engagement-option">
            <label for="option1">Scope My Requirements</label>
          </div>
          <div>
            <input type="radio" id="option2" name="engagement-option" checked>
            <label for="option2">I Have an RFI/RFP</label>
          </div>
          <div>
            <input type="radio" id="option3" name="engagement-option">
            <label for="option3">Existing Project Takeover</label>
          </div>
          <div>
            <input type="radio" id="option4" name="engagement-option">
            <label for="option4">Get Help With a Task</label>
          </div>
        </div>
  
        <button class="explore-btn">Explore Your Options →</button>
        <a href="#compare" class="compare-link">Compare All Engagement Models</a>
      </fieldset>
      </div>
    </div>
   </section>
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
                            <figure><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}" /></figure>
                            <div class="overlay-box"><a href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more') }}</a></div>
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
@php
$section_services = \App\Models\Section::section('services');
@endphp
@if(count($services) > 0 && isset($section_services))
<!-- Services Section -->
<section class="my-5">
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
                            <figure><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}" /></figure>
                            <div class="overlay-box"><a href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more') }}</a></div>
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
    </div>
</section>
<!--End Services Section -->
@endif


@php
$section_portfolio = \App\Models\Section::section('portfolio');
@endphp
@if(count($portfolios) > 0 && isset($section_portfolio))
<!--Gallery Section-->
<section style="background-color: #F9FAFC" class="gallery-section">
    <!--Sortable Masonry-->
    <div class="sortable-masonry">
        <div class="container">
            <div class="sec-title centered description">
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
                                    {{-- <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="link-btn">{{ __('common.read_more') }}</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            @php
            $page_portfolio = \App\Models\PageSetup::page('portfolio');
            @endphp
            @if(isset($page_portfolio))
            <div class="load-more-btn text-center">
                <a href="{{ route('portfolios') }}" class="theme-btn btn-style-four">{{ __('common.view_more') }}</a>
            </div>
            @endif
        </div>
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
                            <div class="image"><img src="{{ asset('uploads/member/'.$member->image_path) }}" alt="{{ $member->title }}"></div>

                        </div>
                        <div class="info-box">
                            <h3 class="name"><a>{{ $member->title }}</a></h3>
                            <span class="designation">{{ $member->designation->title }}@if(isset($member->designation->department)), {{ $member->designation->department }}@endif</span>
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
                        <div class="thumb"><img src="{{ asset('uploads/testimonial/'.$testimonial->image_path) }}" alt="{{ $testimonial->title }}"></div>
                    </div>
                    <div class="info-box">
                        <div class="text description">{!! $testimonial->description !!}</div>
                        <h5 class="name">{{ $testimonial->title }}</h5>
                        <div class="company-name">{{ $testimonial->designation }}@if(isset($testimonial->organization)), {{ $testimonial->organization }}@endif</div>
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
{{-- @if(count($articles) > 0 && isset($section_blog))

<section class="">
    <div class="container">
        <div class="sec-title left text-left">
            <h2>{{ $section_blog->title }}</h2>
            <div class="text description">{!! $section_blog->description !!}</div>
            <div class="separater"></div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                @foreach($articles as $key => $article)
                @if($key == 0)
                <!-- News Block -->
                <div class="news-block">
                    <div class="inner-box">
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('uploads/article/'.$article->image_path) }}" alt="{{ $article->title }}"></figure>
                            <div class="overlay-box"><a href="{{ route('blog.single', $article->slug) }}" class="link-btn">{{ __('common.read_more') }}</a></div>

                        </div>
                        <div class="caption-box text-left">
                            <h3><a href="{{ route('blog.single', $article->slug) }}">{!! str_limit(strip_tags($article->title), 50, ' ...') !!}</a></h3>
                            <div class="text">{!! str_limit(strip_tags($article->description), 110, ' ...') !!}</div>
                            <ul class="post-meta">
                                <li><i class="far fa-calendar-check"></i> {{ date('d M, Y', strtotime($article->created_at)) }}</li>
                            </ul>
                        </div>

                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                <div class="news-block-two">
                    @foreach($articles as $key => $article)
                    @if($key > 0)
                    <div class="inner-box">
                        <div class="row clearfix">
                            <!--Image Column-->
                            <div class="image-box col-lg-6 col-md-6 col-sm-12">
                                <div class="image">
                                    <figure class="image"><img src="{{ asset('uploads/article/'.$article->image_path) }}" alt="{{ $article->title }}"></figure>
                                    <div class="overlay-box"><a href="{{ route('blog.single', $article->slug) }}" class="link-btn">{{ __('common.read_more') }}</a></div>
                                </div>
                            </div>
                            <!--Content Column-->
                            <div class="caption-box col-lg-6 col-md-6 col-sm-12 description">
                                <h3><a href="{{ route('blog.single', $article->slug) }}">{!! str_limit(strip_tags($article->title), 50, ' ...') !!}</a></h3>
                                <div class="text">{!! str_limit(strip_tags($article->description), 110, ' ...') !!}</div>
                                <ul class="post-meta">
                                    <li><i class="far fa-calendar-check"></i> {{ date('d M, Y', strtotime($article->created_at)) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
@endif
 --}}

@php
$section_process = \App\Models\Section::section('process');
@endphp
@if(count($processes) > 0 && isset($section_process))
<!--Feautred Section -->
<section style="background-color: " class="feautred-section style-two" >
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sec-title left description">
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
                    <div class="lower-content description">
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



@if(count($clients) > 0)
    <section class="partner-section">
        <div class="container">
        <h2>Enterprises & Tech Companies Worldwide Trust Us</h2>
        <div class="row gap-2 justify-content-center text-center partner-logos align-items-center">
            @foreach($clients as $client)
            <div class="col-6 col-sm-4 col-md-2 col-lg-2-4 bg-white px-3 py-0 d-flex align-items-center justify-content-center m-1" style="height: 90px;">
            <img src="{{ asset('uploads/client/'.$client->image_path) }}" alt="{{ $client->title }}" class="img-fluid my-1"/>
            </div>
            @endforeach
        </div>
        </div>
    </section>
@endif
@section('scriptjs')
<script>
    $(document).ready(function(){
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
@endsection
@endsection
