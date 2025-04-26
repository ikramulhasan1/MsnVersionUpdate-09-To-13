@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('about-us');
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
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

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

    .stats-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: pulseEffect 3s infinite alternate;
}

/* Continuous Animation */
@keyframes pulseEffect {
    0% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgb(255, 255, 255);
    }
    50% {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); /* Soft red glow */
    }
    100% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgb(255, 255, 255);
    }
}



/* process */
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
      transition: all 0.5s ease;
    }

    .process-step-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .process-step-number {
      width: 35px;
      height: 35px;
      background-color: #0d6efd;
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
      background: repeating-linear-gradient(
        to right,
        #999,
        #999 4px,
        transparent 4px,
        transparent 8px
      );
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
      0% { background-position: 0; }
      100% { background-position: 8px; }
    }

    .process-btn-orange {
      background-color: #ff6a00;
      color: white;
      padding: 12px 26px;
      border-radius: 5px;
      font-weight: 600;
      text-transform: uppercase;
      border: none;
    }

    .process-btn-orange:hover {
      background-color: #e55c00;
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
    transform: rotate(90deg); /* or use a different SVG for down */
    /* Add margin or position tweaks as needed */
}

.process-description p{
    font-size: 16px !important; color: #333333 !important;

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
    


/*  */
body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      color: #333;
      /* margin: 0; */
    }

    /* Hero Section */
    .about-hero-section {
      background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
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

    .about-section-title {
      text-align: center;
      margin-bottom: 60px;
    }

    .about-section-title h2 {
      font-size: 42px;
      font-weight: 700;
      color: #222;
    }

    /* About Section */
    .about-page {
      padding: 80px 0;
    }

    .about-glass-card {
      background: rgba(255, 255, 255, 0.7);
      border-radius: 8px;
      backdrop-filter: blur(15px);
      /* box-shadow: 0 0px 5px rgba(31, 38, 135, 0.1); */
      padding: 40px;
      /* transition: 0.4s; */
    }

    /* .about-glass-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 40px rgba(31, 38, 135, 0.2);
    } */

    .about-glass-card h3 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #2575fc;
    }

    .about-glass-card p {
      font-size: 18px;
      line-height: 1.7;
      color: #555;
    }

    /* Features */
    .about-feature-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .about-feature-list li {
      font-size: 18px;
      padding-left: 30px;
      margin-bottom: 15px;
      position: relative;
      color: #333;
    }

    .about-feature-list li::before {
      content: '✔';
      position: absolute;
      left: 0;
      color: #2575fc;
      font-size: 20px;
    }

    /* Footer */
    footer {
      background: #111;
      padding: 30px 0;
      text-align: center;
      color: #aaa;
      font-size: 15px;
      margin-top: 50px;
    }




     /* About Section */
     .about-page {
      padding: 80px 0;
      background-color: #fff;
    }

    .about-page .card {
      background: #ffffff;
      border: none;
      border-radius: 8px;
      box-shadow: 0 0px 5px rgba(0, 0, 0, 0.1);
      /* transition: 0.3s; */
    }

    /* .about-page .card:hover {
      transform: translateY(-10px);
    } */

    .about-page .icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      color: white;
      font-size: 30px;
    }

</style>

<!--Page Title-->
{{-- <section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ __('navbar.about') }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.about') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section> --}}
<!--End Page Title-->
<!-- Hero Section -->
<section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>About Us</h1>
      <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
    </div>
  </section>
  
  <!-- About Section -->
  <section class="about-page">
    <div class="container">
      <div class="about-section-title" data-aos="fade-up">
        <h2>Who We Are</h2>
      </div>
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4" data-aos="fade-right">
          <div class="about-glass-card shadow-sm">
            <h3>Our Journey</h3>
            <p>MSN SoftTech is a global leader in <strong>software development, web design, mobile app development</strong> and <strong>SEO services</strong>. With over <strong>10 years</strong> of excellence, we deliver powerful IT solutions locally and internationally, helping businesses grow, innovate, and lead in their industries.</p>
            <ul class="about-feature-list mt-4">
              <li>Over <strong>3,500+</strong> satisfied clients worldwide</li>
              <li>Custom software & digital marketing expertise</li>
              <li>Cross-industry technology leadership</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1350&q=80" alt="About Us Image" class="img-fluid rounded-4 shadow">
        </div>
      </div>
    </div>
  </section>
  
  <!-- Mission and Vision -->
  <section class="about-page" style="background: #eef2f7;">
    <div class="container">
      <div class="row g-5">
        <div class="col-md-6" data-aos="zoom-in">
          <div class="about-glass-card shadow-sm text-center">
            <h3>Our Mission</h3>
            <p>To empower businesses with next-gen technology solutions that enhance operational efficiency, stimulate innovation, and drive exponential growth in the digital era.</p>
          </div>
        </div>
        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="150">
          <div class="about-glass-card shadow-sm text-center">
            <h3>Our Vision</h3>
            <p>To be a global benchmark in delivering transformative IT services and creating a future where every business thrives through technology-driven success and sustainable growth.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- About-page Us Section -->
  <section class="about-page">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <h2 class="fw-bold">We Are Provide</h2>
          <p class="lead">10+ years of excellence in delivering top-notch IT services globally.</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-4" data-aos="fade-right">
            <div class="card p-4 text-center">
              <div class="icon mx-auto mb-3">
                <i class="bi bi-laptop"></i>
              </div>
              <h5 class="fw-bold">Software Development</h5>
              <p>Custom solutions to help you grow faster, smarter, and stronger in the digital world.</p>
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up">
            <div class="card p-4 text-center">
              <div class="icon mx-auto mb-3">
                <i class="bi bi-phone"></i>
              </div>
              <h5 class="fw-bold">Mobile App Development</h5>
              <p>Crafting seamless and powerful mobile experiences to captivate your audience globally.</p>
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-left">
            <div class="card p-4 text-center">
              <div class="icon mx-auto mb-3">
                <i class="bi bi-bar-chart-line"></i>
              </div>
              <h5 class="fw-bold">SEO & Marketing</h5>
              <p>Driving visibility, engagement, and success through intelligent SEO and marketing strategies.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    
@if(isset($about) || count($counters) > 0)
<!-- About Section -->
<section class="">
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
                    <div class="row">
                        @foreach($counters as $counter)
                        <!--Column-->
                        <div class="counter-column col-lg-3 col-md-6 col-sm-12 wow fadeInUp ">
                            <div class="count-box border border-1 p-3 bg-white stats-card">
                                <div style="color: #1EC000" class="count">
                                    {{ $counter->value }}
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

@php
$section_whyus = \App\Models\Section::section('why-us');
@endphp
@if(isset($section_whyus) || isset($about->video_id))
<!--Why Choose Us Section -->
{{-- <section class="why-choose-us">
    <div class="container-fluid">
        <div class="row clearfix">
            @if(!empty($about->video_id))
            <!--Image Column-->
            <div class="col-lg-6 col-md-12 col-sm-12 content-cloumn wow fadeInLeft animated">

                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $about->video_id }}?rel=0" allowfullscreen></iframe>
                </div>

            </div>
            @endif


            @if(count($chooses) > 0 && isset($section_whyus))
            <div class="col-lg-6 col-md-12 col-sm-12 content-cloumn">
                <div class="inner-column">
                    <div class="sec-title left description">
                        <h2>{{ $section_whyus->title }}</h2>
                        <div class="separater"></div>
                    </div>
                    <p class="description" >{!! $section_whyus->description !!}</p><br />
                    <ul class="list-why-us">
                        @foreach($chooses as $choose)
                        <li>{{ $choose->title }}</li>
                        @endforeach
                    </ul>

                    @php
                    $page_quote = \App\Models\PageSetup::page('get-quote');
                    $page_contact = \App\Models\PageSetup::page('contact-us');
                    @endphp
                    @if(isset($page_quote))
                    <a href="{{ route('get-quote') }}" class="btn-theme btn-style-five">{{ __('navbar.get_quote') }}</a>
                    @elseif(isset($page_contact))
                    <a href="{{ route('contact') }}" class="btn-theme btn-style-five">{{ __('common.get_start') }}</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section> --}}
<!--End Why Choose Us Section -->
@endif


@php
$section_team = \App\Models\Section::section('team');
@endphp
@if(count($members) > 0 && isset($section_team))
<!-- Team Section -->
<section class="team-section style-two">
    <div class="container">
        <div class="sec-title left">
            <h2>{{ $section_team->title }}</h2>
            <div class="text description">{!! $section_team->description !!}</div>
            <div class="separater"></div>
        </div>

        <div class="row clearfix">

            @foreach($members as $member)
            <div class="col-lg-3 col-md-6 col-sm-12">
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
            </div>
            @endforeach

        </div>
    </div>
</section>
<!--End Team Section -->
@endif

{{-- 
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
                    <div class="text description">{!! $section_process->description !!}</div>
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
                        <div class="text description">{!! $process->description !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!--End Feautred Section -->
@endif
 --}}
 @php
 $section_process = \App\Models\Section::section('process');
 @endphp
 
 @if(count($processes) > 0 && isset($section_process))
 {{-- process-section --}}
 <section class="process-section px-5">
   <div class="container">
     <div class="process-section-title">
       <h2 style="padding-bottom: 30px !important">{{ $section_process->title }}</h2>
       {{-- <p class="text-muted">From research to testing, we ensure your design is intuitive, user-focused, and aligned with your goals.</p> --}}
     </div>
 
     <!-- First Row -->
     <div class="row g-4 mb-4">
         @foreach($processes as $key => $process)
             {{-- @foreach ($service->processworks as $key => $process) --}}
             <div class="col-md-4 mb-4">
                 <div class="process-step-box">
                     <div class="process-step-number">{{ $key + 1 }}</div>
                     <div class="process-step-heading" style="font-size: 20px; color: #333333;">
                         {{-- <img style="width: 50px; height: 50px;" src="{{ asset('uploads/process/' . $process->image_path) }}" class="process-step-icon" alt=""> --}}
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
 
                     <div class="process-step-arrow d-none d-md-block 
                         {{ $showArrow ? ($key == 2 ? 'arrow-down' : '') : 'arrow-hidden' }}">
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
 </section >
 @endif

 

{{-- @php
$section_clients = \App\Models\Section::section('clients');
@endphp --}}
{{-- @if(count($clients) > 0 && isset($section_clients))
<!--Clients Section-->
<section class="clients-section style-two">
    <div class="container">
        <div class="sec-title centered">
            <h2>{{ $section_clients->title }}</h2>
            <div class="text description">{!! $section_clients->description !!}</div>
            <div class="separater"></div>
        </div>
        <div class="sponsors-outer">
            <!--Sponsors Carousel-->
            <ul class="sponsors-carousel owl-carousel owl-theme">
                @foreach($clients as $client)
                <li class="slide-item">
                    <figure class="image-box"><a href="{{ $client->link }}" target="_blank"><img src="{{ asset('uploads/client/'.$client->image_path) }}" alt="{{ $client->title }}"></a></figure>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
<!--End Clients Section-->
@endif --}}

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
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@endsection