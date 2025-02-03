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
    <meta property='og:site_name' content="{{ $setting->title }}"/>
    <meta property='og:title' content="{{ $setting->title }}"/>
    <meta property='og:description' content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}"/>
    <meta property='og:url' content="{{ route('home') }}"/>
    <meta property='og:image' content="{{ asset('/uploads/setting/'.$setting->logo_path) }}"/>


    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('home') }}" />
    <meta name="twitter:title" content="{{ $setting->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('/uploads/setting/'.$setting->logo_path) }}" />
    @endif
@endsection

@section('content')

    @if(count($sliders) > 0)
    <!-- Bnner Section -->
    <section class="banner-section">
        <div class="carousel-column">
            <div class="carousel-outer">
                <div class="banner-carousel owl-carousel owl-theme">
                    @foreach($sliders as $slider)
                    <!-- Slide Item -->
                    <div class="slide-item" style="background-image: url({{ asset('uploads/slider/'.$slider->image_path) }});">
                        <div class="container">
                            <div class="content-box">
                                <h1>{{ $slider->title }}</h1>
                                <div class="text">{!! $slider->description !!}</div>
                                <div class="link-box">
                                    @php
                                        $page_contact = \App\Models\PageSetup::page('contact-us');
                                    @endphp
                                    @if(isset($page_contact))
                                    <a href="{{ route('contact') }}" class="theme-btn btn-style-one">{{ __('common.contact_us') }}</a>
                                    @endif

                                    @if(isset($slider->link))
                                    <a href="{{ $slider->link }}" target="_blank" class="theme-btn btn-style-two">{{ __('common.read_more') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- End Bnner Section -->
    @endif


    @if(isset($about) || count($counters) > 0)
    <!-- About Section -->    
    <section class="our-mission-section">
        <div class="container">
            @if(isset($about))
            <div class="sec-title left">
                <h2>{{ $about->title }}</h2>
                <div class="separater"></div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 wow fadeInRight animated">
                    <div class="inner-box">
                        <div class="text">{!! $about->description !!} <br/></div>
                        <br/>
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
            @endif

            @if(count($counters) > 0)
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 clearfix fun-fact-section">
                    <div class="fact-counter">
                        <div class="row">
                            @foreach($counters as $counter)
                            <!--Column-->
                            <div class="counter-column col-lg-3 col-md-6 col-sm-12 wow fadeInUp">
                                <div class="count-box">
                                    <div class="count"><span class="count-text" data-speed="5000" data-stop="{{ $counter->value }}">0</span></div>
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
        $section_services = \App\Models\Section::section('services');
    @endphp
    @if(count($services) > 0 && isset($section_services))
    <!-- Services Section -->
    <section class="services-section">
        <div class="container">    
            <div class="sec-title centered">
                <h2>{{ $section_services->title }}</h2>
                <div class="text">{!! $section_services->description !!}</div>
                <div class="separater"></div>
            </div>
            <div class="services-box row clearfix">
                <div class="services-carousel owl-carousel owl-theme">
                    @foreach($services as $service)
                    <!-- Service Block -->
                    <div class="service-block wow fadeInDown">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure><img src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}"/></figure>
                                <div class="overlay-box"><a href="{{ route('service.single', $service->slug) }}">{{ __('common.read_more') }}</a></div>
                            </div>
                            <div class="lower-content">
                                <h3><a href="{{ route('service.single', $service->slug) }}">{{ $service->title }}</a></h3>
                                <div class="text">{!! strip_tags($service->short_desc) !!}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
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
    <section class="gallery-section">
        <!--Sortable Masonry-->
        <div class="sortable-masonry">
            <div class="container">
                <div class="sec-title centered">
                    <h2>{{ $section_portfolio->title }}</h2>
                    <div class="text">{!! $section_portfolio->description !!}</div>
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
                                        <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="link-btn">{{ __('common.read_more') }}</a>
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
                <div class="text">{!! $section_team->description !!}</div>
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
    <section class="testimonial-section">
        <div class="container">
            <div class="sec-title centered">
                <h2>{{ $section_testimonials->title }}</h2>
                <div class="text">{!! $section_testimonials->description !!}</div>
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
                            <div class="text">{!! $testimonial->description !!}</div>
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
    @if(count($articles) > 0 && isset($section_blog))
    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <div class="sec-title left">
                <h2>{{ $section_blog->title }}</h2>
                <div class="text">{!! $section_blog->description !!}</div>
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
                        <div class="caption-box">
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
                                <div class="caption-box col-lg-6 col-md-6 col-sm-12">
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
    <!--End News Section -->
    @endif


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
                        <div class="lower-content">
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


    @php
        $section_clients = \App\Models\Section::section('clients');
    @endphp
    @if(count($clients) > 0 && isset($section_clients))
    <!--Clients Section-->
    <section class="clients-section style-two">
        <div class="container">
            <div class="sponsors-outer">
                <!--Sponsors Carousel-->
                <ul class="sponsors-carousel owl-carousel owl-theme">
                    @foreach($clients as $client)
                    <li class="slide-item"><figure class="image-box"><a href="{{ $client->link }}" target="_blank"><img src="{{ asset('uploads/client/'.$client->image_path) }}" alt="{{ $client->title }}"></a></figure></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <!--End Clients Section-->
    @endif

@endsection