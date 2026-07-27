<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{--
    <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('web/css/contact.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/single-service.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/new-msn-theme.css') }}">
    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css"
        integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if (isset($setting))
        <!-- App Title -->
        <title>@yield('title') | {{ $setting->title }}</title>
        <!-- App favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/uploads/setting/' . $setting->favicon_path) }}"
            type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('/uploads/setting/' . $setting->favicon_path) }}" type="image/x-icon">
        {{-- google search console --}}
        <meta name="google-site-verification" content="40_D4AP8vh4ObjZrTZwcJvieoEEvUaOw4pmPTVX0t74" />
        @yield('top_meta_tags')
    @endif

    @yield('schema_markup')

    @if (empty($setting))
        <title>@yield('title')</title>
    @endif
    <!-- Social Meta Tags -->
    <link rel="canonical" href="{{ request()->url() }}">
    @yield('social_meta_tags')
    @if ($livechat->status == 1)
        <link href="{{ asset('web/css/floating-wpp.min.css') }}" rel="stylesheet">
    @endif

    {{-- devicon.min.css removed: not used anywhere. config/technologies.php references
       devicon SVG files directly by URL (devicons/devicon/icons/.../....svg as plain <img>
       sources), it never uses the devicon-* CSS icon-font classes this stylesheet provides.
       This was a fully wasted render-blocking request on every single page. --}}
    <link rel="preconnect" href="//fonts.googleapis.com" />
    <link href="//fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <script src="//www.google.com/recaptcha/api.js" async defer></script>



    <script src="//code.jquery.com/jquery-3.6.0.min.js" defer></script>
    {{-- floating-wpp.min.js defines jQuery.fn.floatingWhatsApp, used below in initFloatingWhatsapp().
       The CSS for this widget was linked but the JS file itself was missing from this layout,
       causing "jQuery(...).floatingWhatsApp is not a function" in the console on every page load.
       Must stay AFTER jquery and keep `defer` so browsers run both in document order, before
       DOMContentLoaded (which is when initFloatingWhatsapp() actually calls it). --}}
    <script src="{{ asset('web/js/floating-wpp.min.js') }}" defer></script>


    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- google analytics --}}
    <!-- Google tag (gtag.js) -->
    <script async src="//www.googletagmanager.com/gtag/js?id=G-FQTTGFBMBE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-FQTTGFBMBE');
    </script>
    {{-- <link href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    @livewireStyles
</head>

<body>
    <div class="page-wrapper">
        <!-- Preloader -->
        <div class="preloader"></div>

        <!-- Main Header-->
        <div class="special_overlay" id="overlay"></div>

        <div class="special_navbar-wrap special_at-top" id="navbarWrap">
            <nav class="special_navbar">

                @if (isset($setting))
                    <div class="special_logo">
                        <a href="{{ route('home') }}" wire:navigate><img
                                src="{{ asset('/uploads/setting/' . $setting->logo_path) }}"
                                alt="{{ $setting->title ?? 'Logo' }}"></a>
                    </div>
                @endif

                <ul class="special_nav-links" id="navLinks">

                    @php
                        $page_home = \App\Models\PageSetup::page('home');
                    @endphp
                    @if (isset($page_home))
                        <li><a class="{{ Request::path() == '/' ? 'special_current' : '' }}"
                                href="{{ route('home') }}" wire:navigate>{{ $page_home->title }}</a></li>
                    @endif

                    @php
                        $page_about = \App\Models\PageSetup::page('about-us');
                        $page_faqs = \App\Models\PageSetup::page('faqs');
                        $page_contact = \App\Models\PageSetup::page('contact-us');
                    @endphp
                    @if (isset($page_about) || isset($page_faqs) || isset($page_contact))
                        <li class="special_dropdown">
                            <a class="{{ Request::is('about*') || Request::is('faqs*') || Request::is('contact*') ? 'special_current' : '' }}"
                                href="{{ isset($page_about) ? route('about') : '#' }}">About Us <span
                                    class="special_chevron"></span></a>
                            <ul class="special_submenu">
                                @if (isset($page_about))
                                    <li><a href="{{ route('about') }}" wire:navigate>{{ $page_about->title }}</a></li>
                                @endif
                                @if (isset($page_faqs))
                                    <li><a href="{{ route('faqs') }}" wire:navigate>{{ $page_faqs->title }}</a></li>
                                @endif
                                {{-- @if (isset($page_contact))
                                    <li><a href="{{ route('contact') }}" wire:navigate>{{ $page_contact->title }}</a></li>
                                @endif --}}
                            </ul>
                        </li>
                    @endif

                    @php
                        $page_services = \App\Models\PageSetup::page('services');
                    @endphp
                    @if (isset($page_services))
                        <li class="special_dropdown special_has-mega">
                            <a class="{{ Request::is('service*') || Request::is('related-service*') ? 'special_current' : '' }} disabled-link"
                                href="{{ route('services') }}" wire:navigate>{{ $page_services->title }}
                                <span class="special_chevron"></span></a>

                            {{-- Mobile / tablet: simple accordion list (unchanged) --}}
                            <ul class="special_submenu">
                                @foreach ($service_subnavs as $service_subnav)
                                    @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                        <li>
                                            <a class="disabled-link"
                                                href="{{ route('service.single', $service_subnav->slug) }}"
                                                wire:navigate>{{ $service_subnav->short_title }}</a>
                                            @if ($service_subnav->subservices->count() > 0)
                                                <ul class="special_submenu-nested">
                                                    @foreach ($service_subnav->subservices as $sub)
                                                        <li><a href="{{ route('service.related-single', $sub->slug) }}"
                                                                wire:navigate>{{ $sub->short_title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                            {{-- Desktop / laptop: mega menu — left main list, right 2-column submenu --}}
                            <div class="special_megamenu" id="servicesMegaMenu">
                                <div class="special_megamenu-left">
                                    <ul>
                                        @foreach ($service_subnavs as $service_subnav)
                                            @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                                <li class="special_megamenu-item {{ $loop->first ? 'special_active' : '' }}"
                                                    data-panel="mega-panel-{{ $service_subnav->id }}">
                                                    <a class="disabled-link"
                                                        href="{{ route('service.single', $service_subnav->slug) }}"
                                                        wire:navigate>{{ $service_subnav->short_title }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="special_megamenu-right">
                                    @foreach ($service_subnavs as $service_subnav)
                                        @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                            <div class="special_megamenu-panel {{ $loop->first ? 'special_active' : '' }}"
                                                id="mega-panel-{{ $service_subnav->id }}">
                                                @if ($service_subnav->subservices->count() > 0)
                                                    <div class="special_megamenu-panel-title">
                                                        {{ $service_subnav->short_title }}
                                                    </div>
                                                    <div class="special_megamenu-columns">
                                                        @php
                                                            $subChunks = $service_subnav->subservices->chunk(
                                                                (int) ceil($service_subnav->subservices->count() / 2),
                                                            );
                                                        @endphp
                                                        @foreach ($subChunks as $chunk)
                                                            <ul class="special_megamenu-col">
                                                                @foreach ($chunk as $sub)
                                                                    <li><a style="font-weight: 400"
                                                                            href="{{ route('service.related-single', $sub->slug) }}"
                                                                            wire:navigate>
                                                                            <div
                                                                                style="display: flex; align-items: center">
                                                                                <i
                                                                                    class="fs-4 me-2 {{ $sub->sub_service_icon ?? '' }}"></i>{{ $sub->short_title }}
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                                <li><a style="font-weight: 700; font-size: 16px;"
                                                                        href="{{ route('services') }}" wire:navigate>
                                                                        <div style="display: flex; align-items: center">
                                                                            View All Service
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="special_megamenu-empty">{{ __('No sub services') }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endif
                    @php
                        $page_portfolio = \App\Models\PageSetup::page('portfolio');
                    @endphp
                    @if (isset($page_portfolio))
                        <li><a class="{{ Request::is('portfolio*') ? 'special_current' : '' }}"
                                href="{{ route('portfolios') }}" wire:navigate>{{ $page_portfolio->title }}</a></li>
                    @endif
                    @php
                        $all_pages = \App\Models\Page::where('type', 'casestudy')->get();
                        $isCurrentCasestudy = $all_pages->contains('slug', request()->segment(2));
                    @endphp
                    @if ($all_pages->count())
                        <li class="special_dropdown">
                            <a class="{{ $isCurrentCasestudy ? 'special_current' : '' }}"
                                href="#">{{ __('Case Study') }}
                                <span class="special_chevron"></span></a>
                            <ul class="special_submenu">
                                @foreach ($all_pages as $page)
                                    <li><a href="{{ route('page.single', $page->slug) }}"
                                            wire:navigate>{{ $page->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @php
                        $re_page = \App\Models\Page::where('type', 'resources')->get();
                        $isCurrentResource = $re_page->contains('slug', request()->segment(2));
                    @endphp
                    @if ($re_page->count())
                        <li class="special_dropdown">
                            <a class="{{ $isCurrentResource ? 'special_current' : '' }}"
                                href="#">{{ __('Resources') }}
                                <span class="special_chevron"></span></a>
                            <ul class="special_submenu">
                                @foreach ($re_page as $page)
                                    <li><a href="{{ route('page.single', $page->slug) }}"
                                            wire:navigate>{{ $page->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
                @php
                    $page_quote = \App\Models\PageSetup::page('get-quote');
                @endphp
                @if (isset($page_quote))
                    <a href="{{ route('get-quote') }}" wire:navigate style="background-color: #D2241D"
                        class="special_contact-btn">{{ $page_quote->title }} →</a>
                @endif

                <button class="special_hamburger" id="hamburger" aria-label="Toggle navigation">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
        <script>
            (function() {
                const navbarWrap = document.getElementById('navbarWrap');
                const hamburger = document.getElementById('hamburger');
                const navLinks = document.getElementById('navLinks');
                const overlay = document.getElementById('overlay');
                const SCROLL_THRESHOLD = 40;

                function onScroll() {
                    if (window.scrollY > SCROLL_THRESHOLD) {
                        navbarWrap.classList.remove('special_at-top');
                    } else {
                        navbarWrap.classList.add('special_at-top');
                    }
                }
                window.addEventListener('scroll', onScroll, {
                    passive: true
                });
                onScroll();

                function toggleMenu() {
                    hamburger.classList.toggle('special_open');
                    navLinks.classList.toggle('special_open');
                    overlay.classList.toggle('special_open');
                    document.body.style.overflow = navLinks.classList.contains('special_open') ? 'hidden' : '';
                }
                hamburger.addEventListener('click', toggleMenu);
                overlay.addEventListener('click', toggleMenu);
                navLinks.querySelectorAll('a').forEach(function(a) {
                    a.addEventListener('click', function(e) {
                        const parentDropdown = a.closest('.special_dropdown');
                        const isTopLevelDropdownToggle =
                            parentDropdown &&
                            a.parentElement === parentDropdown &&
                            window.innerWidth <= 860;

                        if (isTopLevelDropdownToggle) {
                            e.preventDefault();
                            const alreadyOpen = parentDropdown.classList.contains('special_hover-open');

                            navLinks.querySelectorAll('.special_dropdown.special_hover-open').forEach(
                                function(d) {
                                    if (d !== parentDropdown) d.classList.remove('special_hover-open');
                                });

                            parentDropdown.classList.toggle('special_hover-open', !alreadyOpen);
                            return;
                        }

                        if (navLinks.classList.contains('special_open')) toggleMenu();
                    });
                });

                navLinks.querySelectorAll('.special_dropdown').forEach(function(dropdown) {
                    let closeTimer;

                    dropdown.addEventListener('mouseenter', function() {
                        if (window.innerWidth <= 860) return;
                        clearTimeout(closeTimer);
                        dropdown.classList.add('special_hover-open');
                    });

                    dropdown.addEventListener('mouseleave', function() {
                        if (window.innerWidth <= 860) return;
                        closeTimer = setTimeout(function() {
                            dropdown.classList.remove('special_hover-open');
                        }, 300);
                    });
                });

                var megaMenu = document.getElementById('servicesMegaMenu');
                if (megaMenu) {
                    var items = megaMenu.querySelectorAll('.special_megamenu-item');
                    items.forEach(function(item) {
                        item.addEventListener('mouseenter', function() {
                            var targetId = item.getAttribute('data-panel');
                            items.forEach(function(i) {
                                i.classList.remove('special_active');
                            });
                            item.classList.add('special_active');
                            megaMenu.querySelectorAll('.special_megamenu-panel').forEach(function(panel) {
                                panel.classList.toggle('special_active', panel.id === targetId);
                            });
                        });
                    });

                    // megamenu-র left edge ও width, navbar container-এর (website-এর) left ও
                    // right edge বরাবর মিলিয়ে বসানো হচ্ছে — যাতে menu টা website-এর
                    // left সাইড থেকে শুরু হয়ে right সাইড পর্যন্ত বিস্তৃত থাকে
                    var navbarEl = document.querySelector('.special_navbar');

                    function positionMegaMenu() {
                        var rect = navbarEl.getBoundingClientRect();
                        var newWidth = rect.width * 0.65; // was: rect.width / 2 — ekhon 75% width
                        var newLeft = rect.left + (rect.width - newWidth) / 2;

                        megaMenu.style.left = newLeft + 'px';
                        megaMenu.style.width = newWidth + 'px';
                        megaMenu.style.top = (rect.bottom + 12) + 'px';
                    }

                    var megaMenuParent = megaMenu.closest('.special_dropdown');
                    if (megaMenuParent) {
                        megaMenuParent.addEventListener('mouseenter', positionMegaMenu);
                    }
                    window.addEventListener('scroll', positionMegaMenu, {
                        passive: true
                    });
                    window.addEventListener('resize', positionMegaMenu);
                    positionMegaMenu();
                }
            })();
        </script>
        <!--End Main Header -->
        <!-- Content Start -->
        <main>
            @yield('content')
        </main>
        <!-- Content End -->
        <!-- Main custom-Footer -->
        @include('web.inc.cta')
        <footer class="custom-footer">
            <div class="container">

                <div class="custom-footer-grid">

                    {{-- Column 1: Logo + Address --}}
                    <div class="custom-footer-col custom-footer-brand">
                        @if (isset($setting))
                            <a href="{{ route('home') }}" wire:navigate class="footer-logo d-inline-block">
                                <img src="{{ asset('/uploads/setting/' . $setting->logo_path) }}"
                                    alt="{{ $setting->title ?? 'Logo' }}">
                            </a>
                        @endif

                        @if (!empty($setting->contact_address))
                            <p class="footer-address">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $setting->contact_address }}</span>
                            </p>
                        @endif

                        @if (!empty($setting->email_one))
                            <p class="footer-contact-item">
                                <i class="bi bi-envelope"></i>
                                <span>{{ $setting->email_one }}</span>
                            </p>
                        @endif

                        @if (!empty($setting->phone_one))
                            <p class="footer-contact-item">
                                <i class="bi bi-telephone"></i>
                                <span>{{ $setting->phone_one }}</span>
                            </p>
                        @endif
                        {{-- Social Media Icons --}}
                        <div class="footer-social-icons">
                            @if (!empty($social->facebook))
                                <a href="{{ $social->facebook }}" target="_blank" rel="noopener noreferrer"
                                    class="social-icon facebook" aria-label="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                            @endif

                            @if (!empty($social->instagram))
                                <a href="{{ $social->instagram }}" target="_blank" rel="noopener noreferrer"
                                    class="social-icon instagram" aria-label="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            @endif

                            @if (!empty($social->linkedin))
                                <a href="{{ $social->linkedin }}" target="_blank" rel="noopener noreferrer"
                                    class="social-icon linkedin" aria-label="LinkedIn">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    @php
                        $activeServices = $service_subnavs
                            ->filter(function ($s) {
                                return isset($s->manu) && $s->manu == 1;
                            })
                            ->values();

                        $col1 = collect();
                        $col2 = collect();
                        $col1Weight = 0;
                        $col2Weight = 0;

                        foreach ($activeServices as $service) {
                            $weight = 1 + $service->subservices->count();
                            if ($col1Weight <= $col2Weight) {
                                $col1->push($service);
                                $col1Weight += $weight;
                            } else {
                                $col2->push($service);
                                $col2Weight += $weight;
                            }
                        }

                        $serviceChunks = collect([$col1, $col2])->filter(function ($c) {
                            return $c->count() > 0;
                        });
                    @endphp

                    @foreach ($serviceChunks as $chunk)
                        <div class="custom-footer-col custom-footer-services">
                            @if ($loop->first)
                                <h5>Services</h5>
                            @else
                                <h5>&nbsp;</h5>
                            @endif
                            <ul class="custom-footer-service-list">
                                @foreach ($chunk as $service)
                                    <li>
                                        <a class="footer-main-service disabled-link"
                                            href="{{ route('service.single', $service->slug) }}" wire:navigate>
                                            {{ $service->short_title }}
                                        </a>
                                        @if ($service->subservices->count() > 0)
                                            <ul class="footer-sub-service-list">
                                                @foreach ($service->subservices as $sub)
                                                    <li>
                                                        <a href="{{ route('service.related-single', $sub->slug) }}"
                                                            wire:navigate>
                                                            {{ $sub->short_title }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    {{-- Column 4: Quick Links --}}
                    <div class="custom-footer-col custom-footer-quicklinks">
                        <h5>Quick Links</h5>
                        <ul>
                            <li><a href="{{ route('home') }}" wire:navigate>Home</a></li>
                            <li><a href="{{ route('about') }}" wire:navigate>About Us</a></li>
                            <li><a href="{{ route('services') }}" wire:navigate>Services</a></li>
                            <li><a href="{{ route('portfolios') }}" wire:navigate>Portfolio</a></li>
                            <li><a href="{{ route('blogs') }}" wire:navigate>Blog</a></li>
                            {{-- <li><a href="{{ route('case') }}" wire:navigate>Case Studies</a></li> --}}
                            <li><a href="{{ route('get-quote') }}" wire:navigate>Contact Us</a></li>
                        </ul>
                    </div>

                </div>

            </div>

            <div class="custom-footer-bottom-bar">
                <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
                    @if (isset($setting))
                        <div class="custom-footer-bottom">
                            <p>Copyright &copy; 2023 –
                                {!! strip_tags($setting->footer_text, '<p><a><b><i><u><strong>') !!}
                            </p>
                        </div>
                    @endif
                    {{-- <div class="text-center">
                        <div class="custom-footer-social-icons">
                            @if (isset($social->facebook))
                                <a class="facebook d-flex justify-content-center align-items-center"
                                    href="{{ $social->facebook }}" target="_blank"><i
                                        class="bi bi-facebook"></i></a>
                            @endif
                            @if (isset($social->twitter))
                                <a class="twitter d-flex justify-content-center align-items-center"
                                    href="{{ $social->twitter }}" target="_blank"><i
                                        class="bi bi-twitter-x"></i></a>
                            @endif
                            @if (isset($social->instagram))
                                <a class="instagram d-flex justify-content-center align-items-center"
                                    href="{{ $social->instagram }}" target="_blank"><i
                                        class="bi bi-instagram"></i></a>
                            @endif
                            @if (isset($social->linkedin))
                                <a class="linkedin d-flex justify-content-center align-items-center"
                                    href="{{ $social->linkedin }}" target="_blank"><i
                                        class="bi bi-linkedin"></i></a>
                            @endif
                            @if (isset($social->pinterest))
                                <a class="pinterest d-flex justify-content-center align-items-center"
                                    href="{{ $social->pinterest }}" target="_blank"><i
                                        class="bi bi-pinterest"></i></a>
                            @endif
                            @if (isset($social->youtube))
                                <a class="youtube d-flex justify-content-center align-items-center"
                                    href="{{ $social->youtube }}" target="_blank"><i class="bi bi-youtube"></i></a>
                            @endif
                            @if (isset($social->skype))
                                <a href="skype:{{ $social->skype }}?chat" target="_blank"><i
                                        class="bi bi-skype"></i></a>
                            @endif
                            @if (isset($social->whatsapp))
                                <a rel="noopener noreferrer"
                                    class="whatsapp d-flex justify-content-center align-items-center"
                                    href="//wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" target="_blank"><i
                                        class="bi bi-whatsapp"></i></a>
                            @endif
                        </div>
                    </div> --}}
                </div>
            </div>
        </footer>
    </div>

    @if ($livechat->status == 1)
        <!--Div where the WhatsApp will be rendered-->
        <div id="whatspp_live"></div>

        <script type="text/javascript">
            function initFloatingWhatsapp() {
                // duplicate init আটকাতে guard (livewire:navigated বারবার ফায়ার হলেও সমস্যা হবে না)
                if (window._floatingWaInit) return;
                window._floatingWaInit = true;

                jQuery('#whatspp_live').floatingWhatsApp({
                    phone: '{{ $livechat->whatsapp_no }}', //WhatsApp Business phone number International format
                    headerTitle: '{{ $livechat->whatsapp_title }}', //Popup Title
                    popupMessage: '{{ $livechat->whatsapp_greeting }}', //Popup Message
                    showPopup: true, //Enables popup display
                    buttonImage: '<img src="{{ asset('web/images/social/whatsapp.png') }}">', //Button Image
                    headerColor: '{{ $livechat->whatsapp_color }}', //headerColor: 'crimson', //Custom header color
                    backgroundColor: 'transparent', //backgroundColor: 'crimson', //Custom background button color
                    position: "right"
                });
            }
            document.addEventListener('DOMContentLoaded', initFloatingWhatsapp);
            document.addEventListener('livewire:navigated', initFloatingWhatsapp);
        </script>
    @endif

    @if ($livechat->status == 0)
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <script type="text/javascript">
            function initFacebookChat() {
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
                    js.src = '//connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            }
            document.addEventListener('DOMContentLoaded', initFacebookChat);
            document.addEventListener('livewire:navigated', initFacebookChat);
        </script>

        <!-- Your Chat Plugin code -->
        <div class="fb-customerchat" attribution=setup_tool page_id="{{ $livechat->facebook_id }}"
            theme_color="{{ $livechat->facebook_color }}" logged_in_greeting="{{ $livechat->facebook_greeting_in }}"
            logged_out_greeting="{{ $livechat->facebook_greeting_out }}">
        </div>
    @endif

    <script>
        // wire:navigate ব্যবহার করলে পুরো পেজ reload হয় না, তাই DOMContentLoaded শুধু প্রথমবার fire হয়।
        // livewire:navigated ইভেন্টটা প্রতিটা SPA navigation-এর পরেও fire হয় (প্রথম লোডেও fire হয়)।
        // তাই নিচের সব init function দুটো ইভেন্টেই বাঁধা হয়েছে, প্রতিটা idempotent (বারবার চালালেও সমস্যা হবে না)।

        function refreshCsrfToken() {
            fetch("{{ route('csrf.refresh') }}", {
                    credentials: 'same-origin'
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (!data.token) return;
                    var meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', data.token);
                    document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                        input.value = data.token;
                    });
                })
                .catch(function() {});
        }

        document.addEventListener('DOMContentLoaded', refreshCsrfToken);
        document.addEventListener('livewire:navigated', refreshCsrfToken);

        function initWhatsappFab() {
            if (document.querySelector('.wa-fab')) return;

            const waWrap = document.createElement("div");
            waWrap.innerHTML = `
        <a rel="noopener noreferrer" href="//wa.link/lnuvjw" target="_blank" class="wa-fab" aria-label="Live chat with agent on WhatsApp">
            <span class="wa-fab-label">Live chat with agent</span>
            <span class="wa-fab-icon">
                <span class="wa-fab-ring"></span>
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.13-2.9-7C17.19 3.03 14.7 2 12.04 2Zm0 18.13h-.01c-1.48 0-2.94-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.25-4.36c0-4.53 3.69-8.22 8.23-8.22 2.2 0 4.26.86 5.82 2.41a8.15 8.15 0 0 1 2.41 5.81c0 4.53-3.69 8.22-8.21 8.22Zm4.51-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31s-.87.85-.87 2.08.89 2.41 1.02 2.58c.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/>
                </svg>
            </span>
        </a>
    `;
            document.body.appendChild(waWrap);
        }

        function hideEmptyLinks() {
            document.querySelectorAll('a').forEach(link => {
                if (!link.hasAttribute('href') && link.innerHTML.trim() === '') {
                    link.style.display = 'none';
                }
            });
        }

        document.addEventListener("DOMContentLoaded", initWhatsappFab);
        document.addEventListener("livewire:navigated", initWhatsappFab);

        document.addEventListener("DOMContentLoaded", hideEmptyLinks);
        document.addEventListener("livewire:navigated", hideEmptyLinks);
    </script>

    <script>
        function initGqHandlers() {
            if (window._gqHandlersBound) return;
            window._gqHandlersBound = true;

            // Toggle subservice visibility when main service label is clicked
            jQuery(document).on('click', '.gq-service-label', function(e) {
                e.preventDefault();

                let parent = jQuery(this).closest('.gq-service');
                let checkbox = parent.find('.gq-service-input');
                let subDiv = parent.find('.gq-subservices');

                if (subDiv.length > 0) {
                    if (!checkbox.is(':checked')) {
                        checkbox.prop('checked', true);
                    }
                    if (subDiv.is(':visible')) {
                        subDiv.stop(true, true).slideUp(300);
                    } else {
                        subDiv.stop(true, true).slideDown(300);
                    }
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });

            jQuery(document).on('change', '.gq-subservice input[type="checkbox"]', function() {
                let parentService = jQuery(this).closest('.gq-service');
                let parentCheckbox = parentService.find('.gq-service-input');
                let subDiv = parentService.find('.gq-subservices');

                if (parentService.find('.gq-subservice input:checked').length > 0) {
                    parentCheckbox.prop('checked', true);
                } else {
                    parentCheckbox.prop('checked', false);
                    subDiv.stop(true, true).slideUp(300);
                }
            });

            jQuery(document).on('click', function(e) {
                if (!jQuery(e.target).closest('.gq-service').length) {
                    jQuery('.gq-subservices').slideUp(200);
                }
            });
        }

        // jQuery is loaded with `defer`, so it isn't available yet when the browser parses
        // this inline script — calling jQuery(...) here directly threw "jQuery is not defined".
        // DOMContentLoaded fires only after all deferred scripts (incl. jquery) have run, so
        // binding there guarantees jQuery exists. livewire:navigated re-binds after wire:navigate
        // swaps the page without a full reload (guard above still prevents double-binding).
        document.addEventListener('DOMContentLoaded', initGqHandlers);
        document.addEventListener('livewire:navigated', initGqHandlers);
    </script>
    @yield('scriptjs')

    @livewireScripts
</body>

</html>
