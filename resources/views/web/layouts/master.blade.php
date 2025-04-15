<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    @if(isset($setting))
    <!-- App Title -->
    <title>@yield('title') | {{ $setting->title }}</title>

    <!-- App favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/uploads/setting/'.$setting->favicon_path) }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('/uploads/setting/'.$setting->favicon_path) }}" type="image/x-icon">

    {{-- google search console --}}
    <meta name="google-site-verification" content="40_D4AP8vh4ObjZrTZwcJvieoEEvUaOw4pmPTVX0t74" />
    @yield('top_meta_tags')
    @endif
    
    {{-- schema_markup --}}
    @yield('schema_markup')

    @if(empty($setting))
    <!-- App Title -->
    <title>@yield('title')</title>
    @endif


    <!-- Social Meta Tags -->
    <link rel="canonical" href="{{ request()->url() }}">

    @yield('social_meta_tags')


    <!-- Stylesheets -->
    <link href="{{ asset('web/css/bootstrap.css') }}" rel="stylesheet">
    @if($livechat->status == 1)
    <link href="{{ asset('web/css/floating-wpp.min.css') }}" rel="stylesheet">
    @endif
    <link href="{{ asset('web/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('web/css/responsive.css') }}" rel="stylesheet">

    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

    <!-- Optional: Slick Theme CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&display=swap" rel="stylesheet">
    
    <!-- ✅ Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        .poppins-regular {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: normal;
        }

        .poppins-medium {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-style: normal;
        }

        .poppins-semibold {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-style: normal;
        }

        .poppins-regular-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-style: italic;
        }

        .poppins-medium-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-style: italic;
        }

        .poppins-semibold-italic {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-style: italic;
        }

            
        *{
            font-family: 'Poppins';
        }




/* Base Mega Menu Styling */
.mega-menu {
    /* position: relative; */
}

.mega-menu-trigger {
    /* position: relative; */
    /* display: inline-block; */
}

.mega-menu-link {
    padding-top: 15px;
    display: inline-block;
    color: #222222;
    font-size: 16px;
    /* text-decoration: none; */
    font-weight: 500;
}
.mega-menu-link:hover{
    color: red !important;
}
.mega-menu-content {
    display: none;
    position: absolute;
    top: 100%;
    /* right: 50%; */
    left: -350%;
    width: 900px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.mega-menu-content2 {
    display: none;
    position: absolute;
    top: 100%;
    /* right: 50%; */
    right: -450%;
    width: 900px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

/* Show the menu on hover of the link */
.mega-menu-trigger:hover .mega-menu-content {
    display: grid;
}
.mega-menu-trigger:hover .mega-menu-content2 {
    display: grid;
}
.mega-links:hover{
    color: #ffffff !important;
    background-color: #000000;
    padding: 7px;
}
.mega-menu-column h4 {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #111;
}

.mega-menu-column ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.mega-menu-column ul li {
    margin-bottom: 8px;
}

.mega-menu-column ul li a {
    color: #555;
    text-decoration: none;
    font-size: 0.95rem;
}

.mega-menu-column ul li a:hover {
    color: #007BFF;
}


.mega-menu-column .service-item {
    display: flex;
    align-items: center;
}

.mega-menu-column .service-item img {
    margin-right: 10px; /* Space between image and title */
}

.mega-menu-column .mega-links {
    font-size: 14px; /* Adjust size as necessary */
    text-decoration: none;
}

    </style>
    <!-- Custom Style -->
    @if(isset($setting->custom_css))
    <style type="text/css">
        {
            ! ! strip_tags($setting->custom_css) ! !
        }

        .page-title .bread-crumb {
            background: black !important;
        }
    </style>
    @endif
  
    <style>
        /* Floating WhatsApp Button */
        .whatsapp-button {
            position: fixed;
            bottom: 15px;
            right: 15px;
            z-index: 1000;
            width: 50px;
            height: 50px;
            background-color: #25d366;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            animation: bounce 3s infinite;
        }
      
        .whatsapp-button img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            transition: transform 0.3s ease-in-out;
        }
      
        /* Hover Effects */
        .whatsapp-button:hover {
            background-color: #1ebe5d;
            transform: scale(1.1);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }
      
        .whatsapp-button:hover img {
            transform: rotate(10deg);
        }
      
        /* Bounce Animation */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-25px); }
        }
      
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .whatsapp-button {
                width: 45px;
                height: 45px;
                bottom: 10px;
                right: 10px;
            }
      
            .whatsapp-button img {
                width: 30px;
                height: 30px;
            }
        }
        
      </style>

      {{-- google analytics --}}
      <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FQTTGFBMBE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-FQTTGFBMBE');
    </script>
</head>

<body>

    <div class="page-wrapper">
        <!-- Preloader -->
        <div class="preloader"></div>

        <!-- Main Header-->
        <header class="main-header header-style-one">

            @if(isset($setting->contact_address) || isset($social))
            <!--Header Top-->
            <div class="header-top">
                <div class="container">
                    <div class="clearfix">
                        <!--Top Left-->
                        <div class="top-left clearfix">
                            <ul class="links clearfix">
                                @if(isset($setting->contact_address))
                                <li><span class="icon fa fa-map-marker-alt"></span>{{ $setting->contact_address }}</li>
                                @endif
                            </ul>
                        </div>

                        <!--Top Right-->
                        <div class="top-right pull-right">
                            <ul class="social-links clearfix">
                                @if(isset($social->facebook))
                                <li><a href="{{ $social->facebook }}" target="_blank"><span class="icon fab fa-facebook-f"></span></a></li>
                                @endif
                                @if(isset($social->twitter))
                                <li><a href="{{ $social->twitter }}" target="_blank"><span class="icon fab fa-twitter"></span></a></li>
                                @endif
                                @if(isset($social->instagram))
                                <li><a href="{{ $social->instagram }}" target="_blank"><span class="icon fab fa-instagram"></span></a></li>
                                @endif
                                @if(isset($social->linkedin))
                                <li><a href="{{ $social->linkedin }}" target="_blank"><span class="icon fab fa-linkedin-in"></span></a></li>
                                @endif
                                @if(isset($social->pinterest))
                                <li><a href="{{ $social->pinterest }}" target="_blank"><span class="icon fab fa-pinterest"></span></a></li>
                                @endif
                                @if(isset($social->youtube))
                                <li><a href="{{ $social->youtube }}" target="_blank"><span class="icon fab fa-youtube"></span></a></li>
                                @endif
                                @if(isset($social->skype))
                                <li><a href="skype:{{ $social->skype }}?chat" target="_blank"><span class="icon fab fa-skype"></span></a></li>
                                @endif
                                @if(isset($social->whatsapp))
                                <li><a rel="noopener noreferrer" href="https://wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" target="_blank"><span class="icon fab fa-whatsapp"></span></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!--Header-Upper-->
            <div class="header-upper">
                <div class="container">
                    <div class="clearfix">
                        <div class="nav-inner">
                            @if(isset($setting))
                            <div class="pull-left logo-box">
                                <div class="logo"><a href="{{ route('home') }}"><img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="Logo"></a></div>
                            </div>
                            @endif

                            <div class="pull-right upper-right clearfix">

                                <!--Info Box-->
                                @if(isset($setting->office_hours))
                                <div class="upper-column info-box">
                                    <div class="icon-box"><span class="flaticon-clock"></span></div>
                                    <ul>
                                        <li><strong>{{ __('contact.office_time') }}:</strong></li>
                                        <li>{!! strip_tags($setting->office_hours) !!}</li>
                                    </ul>
                                </div>
                                @endif

                                @if(isset($setting->phone_one))
                                <!--Info Box-->
                                <div class="upper-column info-box">
                                    <div class="icon-box"><span class="flaticon-phone-call"></span></div>
                                    <ul>
                                        <li><strong>{{ __('contact.phone') }}:</strong></li>
                                        <li>{{ $setting->phone_one }}</li>
                                    </ul>
                                </div>
                                @endif

                                @if(isset($setting->email_one))
                                <!--Info Box-->
                                <div class="upper-column info-box">
                                    <div class="icon-box"><span class="flaticon-email"></span></div>
                                    <ul>
                                        <li><strong>{{ __('contact.email') }}:</strong></li>
                                        <li>{{ $setting->email_one }}</li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Header Upper-->

            <!--Header Lower-->
            <div class="header-lower">

                <div class="container">
                    <div class="nav-outer clearfix">

                        <!-- Main Menu -->
                        <nav class="main-menu navbar-expand-md">
                            <div class="navbar-header">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>


                            <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                <ul class="navigation clearfix">
                                    @php
                                    $page_home = \App\Models\PageSetup::page('home');
                                    @endphp
                                    @if(isset($page_home))
                                    <li class="{{ Request::path() == '/' ? 'current' : '' }}"><a href="{{ route('home') }}">{{ $page_home->title }}</a></li>
                                    @endif

                                    @php
                                    $page_about = \App\Models\PageSetup::page('about-us');
                                    $page_faqs = \App\Models\PageSetup::page('faqs');
                                    $page_contact = \App\Models\PageSetup::page('contact-us');
                                    @endphp

                                    @if(isset($page_about) || isset($page_faqs) || isset($page_contact))
                                    <li class="dropdown 
                                    {{ Request::is('about*') ? 'current' : '' }}
                                    {{ Request::is('faqs*') ? 'current' : '' }}
                                    {{ Request::is('contact*') ? 'current' : '' }}
                                    "><a href="">Company</a>
                                        <ul>
                                            @if(isset($page_about))
                                            <li class="{{ Request::is('about*') ? 'current' : '' }}"><a href="{{ route('about') }}">{{ $page_about->title }}</a></li>
                                            @endif

                                            @if(isset($page_faqs))
                                            <li class="{{ Request::is('faqs*') ? 'current' : '' }}"><a href="{{ route('faqs') }}">{{ $page_faqs->title }}</a></li>
                                            @endif

                                            @if(isset($page_contact))
                                            <li class="{{ Request::path() == 'contact' ? 'current' : '' }}"><a href="{{ route('contact') }}">{{ $page_contact->title }}</a></li>
                                            @endif

                                        </ul>
                                    </li>
                                    @endif


                                @php
                                    $page_services = \App\Models\PageSetup::page('services');
                                    $related_services = \App\Models\PageSetup::page('related-service');
                                    $technology_services = \App\Models\PageSetup::page('technology');
                                @endphp
                                @if(isset($page_services))
                                <li class="dropdown mega-menu {{ Request::is('service*') ? 'current' : '' }} {{ Request::is('related-service*') ? 'current' : '' }} {{ Request::is('technology*') ? 'current' : '' }}">
                                    <div class="mega-menu-trigger">
                                        <a href="{{ route('services') }}" class="mega-menu-link">{{ strtoupper($page_services->title) }}</a>
                                        
                                        <div class="mega-menu-content">
                                            <div class="mega-menu-column">
                                                <h4>Our Services</h4>
                                                <ul>
                                                    @foreach($service_subnavs as $service_subnav)
                                                        @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                                            <li class="{{ Request::is('service/'.$service_subnav->slug) ? 'current' : '' }}">
                                                                <a class="mega-links" href="{{ route('service.single', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="mega-menu-column">
                                                <h4>Related Services</h4>
                                                <ul>
                                                    @foreach($related_service_subnavs as $service_subnav)
                                                        <li class="{{ Request::is('related-service/'.$service_subnav->slug) ? 'current' : '' }}">
                                                            <a class="mega-links" href="{{ route('service.related-single', $service_subnav->slug) }}">{{ $service_subnav->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="mega-menu-column">
                                                <h4>Technology Services</h4>
                                                <ul>
                                                    @foreach($technologies as $service_subnav)
                                                        <li class="{{ Request::is('technology/'.$service_subnav->slug) ? 'current' : '' }}">
                                                            <div class="service-item">
                                                                <img class="ml-0" width="30" src="{{ asset('uploads/service/'.$service_subnav->logo_path) }}" alt="{{ $service_subnav->title }}" srcset=""> <a class="mega-links" href="{{ route('service.technology', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </li>
                                @endif
                                

                                    @php
                                    $page_portfolio = \App\Models\PageSetup::page('portfolio');
                                    @endphp
                                    @if(isset($page_portfolio))
                                    <li class="{{ Request::is('portfolio*') ? 'current' : '' }}"><a href="{{ route('portfolios') }}">{{ $page_portfolio->title }}</a></li>
                                    @endif

                                    @php
                                    $page_pricing = \App\Models\PageSetup::page('pricing');
                                    @endphp
                                    @if(isset($page_pricing))
                                    <!-- <li class="{{ Request::is('pricing*') ? 'current' : '' }}"><a href="{{ route('pricing') }}">{{ $page_pricing->title }}</a></li> -->
                                    @endif



                                    <!-- route('page.single', $page->slug) -->
                                    @php
                                    // Fetch only pages with 'casestudy' type
                                    $all_pages = \App\Models\Page::where('type', 'casestudy')->get();

                                    // Check if the current page is a 'casestudy'
                                    $isCurrentCasestudy = $all_pages->contains('slug', request()->segment(2));
                                    @endphp

                                    @if($all_pages->count())
                                    <li class="dropdown {{ $isCurrentCasestudy ? 'current' : '' }}">
                                        <a href="">{{ __('Case Study') }}</a>
                                        <ul>
                                            @foreach($all_pages as $page)
                                            <li class="{{ Request::is('page/'.$page->slug) ? 'current' : '' }}">
                                                <a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif

                                    @php
                                    // Fetch only 'resources' type pages
                                    $re_page = \App\Models\Page::where('type', 'resources')->get();

                                    // Check if the current page belongs to 'resources'
                                    $isCurrentResource = $re_page->contains('slug', request()->segment(2));
                                    @endphp

                                    @if($re_page->count())
                                    <li class="dropdown {{ $isCurrentResource ? 'current' : '' }}">
                                        <a href="">{{ __('Resources') }}</a>
                                        <ul>
                                            @foreach($re_page as $page)
                                            <li class="{{ Request::is('page/'.$page->slug) ? 'current' : '' }}">
                                                <a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif



                                    @php
                                    $page_blog = \App\Models\PageSetup::page('blog');
                                    @endphp
                                    @if(isset($page_blog))
                                    <li class="dropdown {{ Request::is('blog*') ? 'current' : '' }}"><a href="{{ route('blogs') }}">{{ $page_blog->title }}</a>
                                        <ul>
                                            @foreach($article_subnavs as $article_subnav)
                                            <li class="{{ Request::is('blogs/'.$article_subnav->slug) ? 'current' : '' }}"><a href="{{ route('blog.category', $article_subnav->slug) }}">{{ $article_subnav->title }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif


                                </ul>
                            </div>
                        </nav>
                        <!-- Main Menu End-->

                        <div class="outer-box clearfix">
                            @php
                            $page_quote = \App\Models\PageSetup::page('get-quote');
                            @endphp
                            @if(isset($page_quote))
                            <div class="advisor-box {{ Request::is('get-quote*') ? 'current' : '' }}">
                                <a href="{{ route('get-quote') }}" class="theme-btn advisor-btn">{{ $page_quote->title }}</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--End Header Lower-->

            <!--Sticky Header-->
            <div style="height: 60px" class="sticky-header">
                <div class="container clearfix">
                    @if(isset($setting))
                    <!--Logo-->
                    <div class="logo pull-left">
                        <a href="{{ route('home') }}" class="img-responsive"><img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="Logo"></a>
                    </div>
                    @endif

                    <!--Right Col-->
                    <div class="right-col pull-right">
                        <!-- Main Menu -->
                        <nav class="main-menu  navbar-expand-md">
                            <div class="navbar-header">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>

                            <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent1">
                                <ul class="navigation clearfix">
                                    @php
                                    $page_home = \App\Models\PageSetup::page('home');
                                    @endphp
                                    @if(isset($page_home))
                                    <li class="{{ Request::path() == '/' ? 'current' : '' }}"><a href="{{ route('home') }}">{{ $page_home->title }}</a></li>
                                    @endif


                                    @php
                                    $page_about = \App\Models\PageSetup::page('about-us');
                                    $page_faqs = \App\Models\PageSetup::page('faqs');
                                    $page_contact = \App\Models\PageSetup::page('contact-us');

                                    @endphp

                                    @if(isset($page_about) || isset($page_faqs) ||isset($page_contact) )
                                    <li class="dropdown 
                                    {{ Request::is('about*') ? 'current' : '' }}
                                    {{ Request::is('faqs*') ? 'current' : '' }}
                                    {{ Request::is('contact*') ? 'current' : '' }}">
                                    <a href="">Company<a>
                                        <ul >
                                            @if(isset($page_about))
                                            <li class="{{ Request::is('about*') ? 'current' : '' }}"> <a href="{{ route('about') }}">{{ $page_about->title }}</a></li>
                                            @endif
                                            @if(isset($page_faqs))
                                            <li class="{{ Request::is('faqs*') ? 'current' : '' }}"><a href="{{ route('faqs') }}">{{ $page_faqs->title }}</a></li>
                                            @endif
                                            @if(isset($page_contact))
                                            <li class="{{ Request::is('contact') ? 'current' : '' }}"><a href="{{ route('contact') }}">{{ $page_contact->title }}</a></li>
                                            @endif
                                        </ul>
                                    </li>
                                    @endif
                                
                                @php
                                    $page_services = \App\Models\PageSetup::page('services');
                                    $related_services = \App\Models\PageSetup::page('related-service');
                                @endphp
                                
                                @if(isset($page_services))
                                <li class="dropdown mega-menu {{ Request::is('service*') ? 'current' : '' }} {{ Request::is('related-service*') ? 'current' : '' }}">
                                    <div class="mega-menu-trigger">
                                        <a href="{{ route('services') }}" class="mega-menu-link">{{ strtoupper($page_services->title) }}</a>
                                        
                                        <div class="mega-menu-content2">
                                            <div class="mega-menu-column">
                                                <h4>Our Services</h4>
                                                <ul>
                                                    @foreach($service_subnavs as $service_subnav)
                                                        @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                                            <li class="{{ Request::is('service/'.$service_subnav->slug) ? 'current' : '' }}">
                                                                <a class="mega-links" href="{{ route('service.single', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="mega-menu-column">
                                                <h4>Related Services</h4>
                                                <ul>
                                                    @foreach($related_service_subnavs as $service_subnav)
                                                        <li class="{{ Request::is('related-service/'.$service_subnav->slug) ? 'current' : '' }}">
                                                            <a class="mega-links" href="{{ route('service.related-single', $service_subnav->slug) }}">{{ $service_subnav->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="mega-menu-column">
                                                <h4>Technology Services</h4>
                                                <ul>
                                                    @foreach($technologies as $service_subnav)
                                                        <li class="{{ Request::is('technology/'.$service_subnav->slug) ? 'current' : '' }}">
                                                            <div class="service-item">
                                                                <img class="ml-0" width="30" src="{{ asset('uploads/service/'.$service_subnav->logo_path) }}" alt="{{ $service_subnav->title }}" srcset=""> <a class="mega-links" href="{{ route('service.technology', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                    @php
                                    $page_portfolio = \App\Models\PageSetup::page('portfolio');
                                    @endphp
                                    @if(isset($page_portfolio))
                                    <li class="{{ Request::is('portfolio*') ? 'current' : '' }}"><a href="{{ route('portfolios') }}">{{ $page_portfolio->title }}</a></li>
                                    @endif

                                    @php
                                    $page_pricing = \App\Models\PageSetup::page('pricing');
                                    @endphp
                                    @if(isset($page_pricing))
                                    <!-- <li class="{{ Request::is('pricing*') ? 'current' : '' }}"><a href="{{ route('pricing') }}">{{ $page_pricing->title }}</a></li> -->
                                    @endif

                                    <!-- route('page.single', $page->slug) -->
                                    @php
                                    // Fetch only pages with 'casestudy' type
                                    $all_pages = \App\Models\Page::where('type', 'casestudy')->get();

                                    // Check if the current page is a 'casestudy'
                                    $isCurrentCasestudy = $all_pages->contains('slug', request()->segment(2));
                                    @endphp

                                    @if($all_pages->count())
                                    <li class="dropdown {{ $isCurrentCasestudy ? 'current' : '' }}">
                                        <a href="">{{ __('Case Study') }}</a>
                                        <ul>
                                            @foreach($all_pages as $page)
                                            <li class="{{ Request::is('page/'.$page->slug) ? 'current' : '' }}">
                                                <a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif

                                    @php
                                    // Fetch only 'resources' type pages
                                    $re_page = \App\Models\Page::where('type', 'resources')->get();

                                    // Check if the current page belongs to 'resources'
                                    $isCurrentResource = $re_page->contains('slug', request()->segment(2));
                                    @endphp

                                    @if($re_page->count())
                                    <li class="dropdown {{ $isCurrentResource ? 'current' : '' }}">
                                        <a href="">{{ __('Resources') }}</a>
                                        <ul>
                                            @foreach($re_page as $page)
                                            <li class="{{ Request::is('page/'.$page->slug) ? 'current' : '' }}">
                                                <a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif

                                    @php
                                    $page_blog = \App\Models\PageSetup::page('blog');
                                    @endphp
                                    @if(isset($page_blog))
                                    <li class="dropdown {{ Request::is('blog*') ? 'current' : '' }}"><a href="{{ route('blogs') }}">{{ $page_blog->title }}</a>
                                        <ul>
                                            @foreach($article_subnavs as $article_subnav)
                                            <li class="{{ Request::is('blogs/'.$article_subnav->slug) ? 'current' : '' }}"><a href="{{ route('blog.category', $article_subnav->slug) }}">{{ $article_subnav->title }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endif


                                    @php
                                    $page_quote = \App\Models\PageSetup::page('get-quote');
                                    @endphp
                                    @if(isset($page_quote))
                                    <li class="advisor-box {{ Request::is('get-quote*') ? 'current' : '' }}">
                                        <a href="{{ route('get-quote') }}">{{ $page_quote->title }}</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </nav><!-- Main Menu End-->
                    </div>

                </div>
            </div>
            <!--End Sticky Header-->

        </header>
        <!--End Main Header -->

        <!--  -->
        <!-- Content Start -->
        @yield('content')
        <!-- Content End -->


        @php
        $section_subscribe = \App\Models\Section::section('subscribe');
        @endphp
        @if(isset($section_subscribe))
        <!--Subscribe Section-->
        <section class="subscribe-section">
            <div class="container">
                <div class="row clearfix">
                    <!--Form Column-->
                    <div class="title-column col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <h2>{{ $section_subscribe->title }}</h2>
                        <div class="text">{!! $section_subscribe->description !!}</div>
                        <div class="icon-box">
                            <span class="icon flaticon-mail"></span>
                        </div>
                    </div>
                    <!--Form Column-->
                    <div class="form-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <div class="subscribe-form">
                                <form method="post" action="{{ route('subscribe') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" name="email" value="" placeholder="{{ __('contact.email_address') }}" required>
                                        <button type="submit" class="theme-btn "><i class="fab fa-telegram-plane text-white fa-2x"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End Subscribe Section-->
        @endif

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="container">
                <!--Widgets Section-->
                <div class="widgets-section">
                    <div class="row clearfix">
                        <div class="big-column col-xl-8 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <!--Footer Column-->
                                <div class="footer-column col-lg-6 col-md-12 col-sm-12">
                                    <div class="footer-widget about-widget">
                                        @if(isset($setting))
                                        <div class="footer-logo"><a href="{{ route('home') }}"><img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="Logo"></a></div>

                                        <div class="widget-content">
                                            <ul class="info-box">
                                                <li><i class="far fa-map"></i><span>{{ __('contact.address') }}:</span> {{ $setting->contact_address }}</li>
                                                <li><i class="fa fa-phone-volume"></i> <span>{{ __('contact.phone') }}:</span> {{ $setting->phone_one }}@if(isset($setting->phone_two)), @endif {{ $setting->phone_two }} </li>
                                                <li><i class="fas fa-envelope"></i> <span>{{ __('contact.email') }}:</span> {{ $setting->email_one }}@if(isset($setting->email_two)), @endif {{ $setting->email_two }} </li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                @if(count($pages) > 0)
                                <!--Footer Column-->
                                <div class="footer-column col-lg-6 col-md-12 col-sm-12">
                                    <div class="footer-widget links-widget">
                                        <h2 class="widget-title">{{ __('common.footer_links') }}</h2>
                                        <div class="widget-content">
                                            <ul class="list">
                                                @foreach($pages as $key => $page)
                                                @if (isset($page->type) && $page->type == 'footer')
                                                <li><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a></li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if(count($recents) > 0)
                        <div class="big-column col-xl-4 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <!--Footer Column-->
                                <div class="footer-column col-lg-12 col-md-12 col-sm-12">
                                    <div class="footer-widget recent-posts">
                                        <h2 class="widget-title">{{ __('common.recent_posts') }}</h2>
                                        <!--Footer Column-->
                                        <div class="widget-content">
                                            <div class="item">
                                                @foreach($recents as $key => $recent)
                                                @if($key <= 1)
                                                    <div class="post">
                                                    <ul class="post-date">
                                                        <li>{{ date('F d Y', strtotime($recent->created_at)) }}</li>
                                                    </ul>
                                                    <div class="thumb"><a href="{{ route('blog.single', $recent->slug) }}"><img src="{{ asset('uploads/article/'.$recent->image_path) }}" alt="{{ $recent->title }}"></a></div>
                                                    <h4><a href="{{ route('blog.single', $recent->slug) }}">{!! str_limit(strip_tags($recent->title), 50, ' ...') !!}</a></h4>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
    </div>

    <!--Footer Bottom-->
    <div class="footer-bottom">
        <div class="container">
            <div class="inner-container clearfix">
                @if(isset($setting))
                <div class="text-white copyright-text">&copy; {!! strip_tags($setting->footer_text, '<p><a><b><i><u><strong>') !!}</div>
                @endif
                <div class="social-links">
                    <ul class="social-icon-two">
                        @if(isset($social->facebook))
                        <li><a href="{{ $social->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        @endif
                        @if(isset($social->twitter))
                        <li><a href="{{ $social->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        @endif
                        @if(isset($social->instagram))
                        <li><a href="{{ $social->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                        @endif
                        @if(isset($social->linkedin))
                        <li><a href="{{ $social->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                        @endif
                        @if(isset($social->pinterest))
                        <li><a href="{{ $social->pinterest }}" target="_blank"><i class="fab fa-pinterest"></i></a></li>
                        @endif
                        @if(isset($social->youtube))
                        <li><a href="{{ $social->youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                        @endif
                        @if(isset($social->skype))
                        <li><a href="skype:{{ $social->skype }}?chat" target="_blank"><i class="fab fa-skype"></i></a></li>
                        @endif
                        @if(isset($social->whatsapp))
                        <li><a rel="noopener noreferrer" href="https://wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </footer>
    <!-- End Main Footer -->



    </div>

    <!--Scroll to top-->
    <div style="background-color: #1ebe5d" class="scroll-to-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    
    <!-- ✅ Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="{{ asset('web/js/jquery.js') }}"></script>
    <script src="{{ asset('web/js/popper.min.js') }}"></script>
    <script src="{{ asset('web/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('web/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('web/js/owl.js') }}"></script>
    <script src="{{ asset('web/js/wow.js') }}"></script>
    <script src="{{ asset('web/js/appear.js') }}"></script>
    <script src="{{ asset('web/js/isotope.js') }}"></script>
    <script src="{{ asset('web/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('web/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('web/js/mixitup.js') }}"></script>
    @if($livechat->status == 1)
    <script src="{{ asset('web/js/floating-wpp.min.js') }}"></script>
    @endif
    <script src="{{ asset('web/js/script.js') }}"></script>


    @if($livechat->status == 1)
    <!--Div where the WhatsApp will be rendered-->
    <div id="whatspp_live"></div>

    <script type="text/javascript">
        (function($) {
            "use strict";
            $('#whatspp_live').floatingWhatsApp({
                phone: '{{ $livechat->whatsapp_no }}', //WhatsApp Business phone number International format
                headerTitle: '{{ $livechat->whatsapp_title }}', //Popup Title
                popupMessage: '{{ $livechat->whatsapp_greeting }}', //Popup Message
                showPopup: true, //Enables popup display
                buttonImage: '<img src="{{ asset('
                web / images / social / whatsapp.png ') }}">', //Button Image
                headerColor: '{{ $livechat->whatsapp_color }}', //headerColor: 'crimson', //Custom header color
                backgroundColor: 'transparent', //backgroundColor: 'crimson', //Custom background button color
                position: "right"
            });
        })(jQuery);
    </script>
    @endif


    @if($livechat->status == 0)
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script type="text/javascript">
        (function($) {
            "use strict";

            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v8.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

        })(jQuery);
    </script>

    <!-- Your Chat Plugin code -->
    <div class="fb-customerchat"
        attribution=setup_tool
        page_id="{{ $livechat->facebook_id }}"
        theme_color="{{ $livechat->facebook_color }}"
        logged_in_greeting="{{ $livechat->facebook_greeting_in }}"
        logged_out_greeting="{{ $livechat->facebook_greeting_out }}">
    </div>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let waButton = document.createElement("div");
            waButton.innerHTML = `
            
                <a rel="noopener noreferrer" href="https://wa.link/lnuvjw" target="_blank" class="whatsapp-button">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
                </a>
            `;
            document.body.appendChild(waButton);
        });
    </script>
    <script>
        document.querySelectorAll('a').forEach(link => {
            if (!link.hasAttribute('href') && link.innerHTML.trim() === '') {
                link.style.display = 'none';
            }
        });
    </script>
    @yield('scriptjs')
</body>

</html>