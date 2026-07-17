<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/new-msn-theme.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css"
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/devicon/2.15.1/devicon.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <style>
       /* ============ New Navbar (special_) — FULL CORRECTED STYLE ============ */
:root {
    --special_blue: #D2241D;
    --special_blue-dark: #D2241D;
    --special_text-dark: #0b0b0f;
}

.special_navbar-wrap {
    position: sticky;
    top: 0;
    z-index: 1000;
    display: flex;
    justify-content: center;
    padding: 0;
    background: #fff;
    transition: padding 0.35s ease, background 0.35s ease;
}

.special_navbar-wrap.special_at-top {
    padding: 16px 20px 0 20px;
    background: transparent;
}

.special_navbar-wrap:not(.special_at-top) {
    padding: 0;
    background: #fff;
}

.special_navbar {
    width: 100%;
    max-width: 1400px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 0;
    box-shadow: 0 2px 12px rgba(20, 30, 60, 0.08);
    padding: 14px 40px;
    transition: padding 0.35s ease, box-shadow 0.35s ease, border-radius 0.35s ease, max-width 0.35s ease;
    position: relative;
}

.special_navbar-wrap.special_at-top .special_navbar {
    max-width: 1400px;
    border-radius: 100px;
    padding: 10px 32px;
    box-shadow: 0 6px 24px rgba(20, 30, 60, 0.12);
}

.special_logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 19px;
    color: var(--special_text-dark);
    white-space: nowrap;
}

.special_logo img {
    display: block;
    height: 30px;
    width: auto;
    transition: height 0.35s ease;
}

.special_navbar-wrap.special_at-top .special_logo img {
    height: 36px;
}

.special_nav-links {
    display: flex;
    align-items: center;
    gap: 26px;
    list-style: none;
}

.special_nav-links>li {
    position: relative;
}

.special_nav-links a {
    text-decoration: none;
    color: var(--special_text-dark);
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: color 0.2s ease;
}

.special_nav-links a:hover {
    color: var(--special_blue);
}

.special_nav-links a.special_current {
    color: var(--special_blue);
}

.special_chevron {
    width: 8px;
    height: 8px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(45deg);
    margin-top: -3px;
    flex-shrink: 0;
}

/* ---- Hover-gap fix: extend the dropdown's own hover box down into the gap ---- */
.special_dropdown {
    padding-bottom: 12px;
    margin-bottom: -12px;
}

/* Dropdown submenus (About Us / Services / Case Study / Resources) */
.special_dropdown>.special_submenu {
    display: none;
    position: absolute;
    top: calc(100% + 12px);
    left: 0;
    margin-top: 0;
    min-width: 240px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(20, 30, 60, 0.14);
    padding: 10px;
    list-style: none;
    z-index: 120;
}

.special_dropdown:hover>.special_submenu {
    display: block;
}

/* JS-controlled hover state (fixes fast-close on the gap) */
.special_dropdown.special_hover-open>.special_submenu {
    display: block !important;
}

.special_submenu li a {
    display: block;
    padding: 9px 12px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    color: var(--special_text-dark);
}

.special_submenu li a:hover {
    background: #FDECEB;
    color: var(--special_blue);
}

.special_submenu-nested {
    list-style: none;
    padding-left: 14px;
    margin: 2px 0 6px;
}

/* ============ Mega Menu (Services / What We Offer) — desktop only ============ */
.special_megamenu {
    display: none;
}

@media (min-width: 861px) {
    .special_dropdown.special_has-mega:hover>.special_submenu {
        display: none !important;
    }

    .special_dropdown.special_has-mega:hover>.special_megamenu {
        display: flex;
    }

    /* JS-controlled hover state (fixes fast-close on the gap) */
    .special_dropdown.special_has-mega.special_hover-open>.special_submenu {
        display: none !important;
    }

    .special_dropdown.special_has-mega.special_hover-open>.special_megamenu {
        display: flex !important;
    }

    .special_megamenu {
        position: absolute;
        top: calc(100% + 12px);
        left: 0;
        margin-top: 0;
        width: max-content;
        min-width: 640px;
        max-width: 900px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(20, 30, 60, 0.14);
        overflow: hidden;
        z-index: 120;
    }

    .special_megamenu-left {
        width: max-content;
        min-width: 240px;
        max-width: 320px;
        flex-shrink: 0;
        background: #f7f9fc;
        padding: 10px;
        max-height: 420px;
        overflow-y: auto;
    }

    .special_megamenu-left ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .special_megamenu-item {
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    .special_megamenu-item>a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 14px;
        font-size: 14.5px;
        font-weight: 600;
        color: var(--special_text-dark);
        border-radius: 10px;
        white-space: normal;
        word-break: break-word;
    }

    .special_megamenu-item>a::after {
        content: '';
        width: 6px;
        height: 6px;
        border-right: 2px solid currentColor;
        border-bottom: 2px solid currentColor;
        transform: rotate(-45deg);
        opacity: 0.45;
        flex-shrink: 0;
    }

    .special_megamenu-item:hover,
    .special_megamenu-item.special_active {
        background: #fff;
    }

    .special_megamenu-item.special_active>a,
    .special_megamenu-item:hover>a {
        color: var(--special_blue);
        background: #FDECEB;
    }

    .special_megamenu-right {
        flex: 1;
        padding: 18px 20px;
        max-height: 420px;
        overflow-y: auto;
    }

    .special_megamenu-panel {
        display: none;
    }

    .special_megamenu-panel.special_active {
        display: block;
    }

    .special_megamenu-panel-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #8a90a2;
        margin-bottom: 12px;
    }

    .special_megamenu-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        column-gap: 20px;
    }

    .special_megamenu-col {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .special_megamenu-col li a {
        display: block;
        padding: 8px 10px;
        font-size: 14px;
        font-weight: 500;
        color: var(--special_text-dark);
        border-radius: 8px;
        white-space: normal;
        word-break: break-word;
    }

    .special_megamenu-col li a:hover {
        background: #FDECEB;
        color: var(--special_blue);
    }

    .special_megamenu-empty {
        font-size: 14px;
        color: #8a90a2;
    }
}

.special_contact-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--special_blue);
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 100px;
    white-space: nowrap;
    transition: padding 0.35s ease, background 0.2s ease;
}

.special_navbar-wrap.special_at-top .special_contact-btn {
    padding: 14px 22px;
    font-size: 15px;
}

.special_contact-btn:hover {
    background: var(--special_blue-dark);
    color: #fff;
}

.special_contact-btn-mobile {
    display: none;
}

/* Hamburger (mobile) */
.special_hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 30px;
    height: 30px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.special_hamburger span {
    display: block;
    height: 2px;
    width: 100%;
    background: var(--special_text-dark);
    border-radius: 2px;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.special_hamburger.special_open span:nth-child(1) {
    transform: translateY(7px) rotate(45deg);
}

.special_hamburger.special_open span:nth-child(2) {
    opacity: 0;
}

.special_hamburger.special_open span:nth-child(3) {
    transform: translateY(-7px) rotate(-45deg);
}

.special_overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 12, 20, 0.4);
    z-index: 140;
}

.special_overlay.special_open {
    display: block;
}

@media (max-width: 1100px) {
    .special_nav-links {
        gap: 16px;
    }

    .special_nav-links>li>a {
        font-size: 14px;
    }
}

@media (max-width: 860px) {
    .special_nav-links {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        width: min(80vw, 320px);
        background: #fff;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 4px;
        padding: 90px 24px 28px;
        box-shadow: -8px 0 30px rgba(20, 30, 60, 0.15);
        transform: translateX(100%);
        transition: transform 0.35s ease;
        z-index: 150;
        overflow-y: auto;
    }

    .special_nav-links.special_open {
        transform: translateX(0);
    }

    .special_nav-links>li {
        width: 100%;
    }

    .special_nav-links>li>a {
        font-size: 16px;
        padding: 12px 0;
        width: 100%;
        border-bottom: 1px solid #f0f0f2;
    }

    .special_dropdown {
        position: static;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .special_dropdown>.special_submenu {
        display: block;
        position: static;
        box-shadow: none;
        margin-top: 0;
        padding-left: 12px;
    }

    .special_contact-btn-mobile {
        display: flex;
        margin-top: 14px;
    }

    .special_hamburger {
        display: flex;
    }

    .special_navbar>.special_contact-btn {
        display: none;
    }

    .special_navbar {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .special_logo {
        font-size: 16px;
    }

    .special_logo img {
        height: 26px !important;
    }
}

@media (max-width: 640px) {
    .special_navbar {
        padding-left: 16px;
        padding-right: 16px;
    }

    .special_navbar-wrap.special_at-top {
        padding: 10px 10px 0 10px;
    }

    .special_navbar-wrap.special_at-top .special_navbar {
        border-radius: 24px;
        padding: 12px 18px;
    }
}
    </style>
    <!-- Custom Style -->
    @if (isset($setting->custom_css))
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
            background-color: #D2241D;
            transform: scale(1.1);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-button:hover img {
            transform: rotate(10deg);
        }

        /* Bounce Animation */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-25px);
            }
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

        /* footer section */
        .custom-footer {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #0B2447;
            color: #ffffff !important;
        }

        .custom-footer {
            background: radial-gradient(circle at top left, #000000, #000000);
            padding: 60px 0 30px;
        }

        .custom-footer-section h5 {
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 25px;
            position: relative;
            color: #ffffff !important;
        }

        .custom-footer-section h5::after {
            content: '';
            width: 40px;
            height: 3px;
            background: #D2241D;
            position: absolute;
            bottom: -10px;
            left: 0;
        }

        .custom-footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .custom-footer-section ul li {
            margin-bottom: 15px;
        }

        .custom-footer-section ul li a {
            text-decoration: none;
            color: #e0e0e0;
            font-size: 16px;
            transition: 0.3s;
        }

        .custom-footer-section ul li a:hover {
            color: #D2241D;
        }

        .custom-footer-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 50px 0 20px;
        }

        .custom-footer-bottom {
            text-align: left;
            color: #aaa;
            font-size: 14px;
            line-height: 1.6;
        }

        .custom-footer-bottom p a {
            color: #ffffff
        }

        .custom-footer-social-icons {
            margin-top: 20px;
        }

        .custom-footer-social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            margin: 0 5px;
            text-align: center;
            border-radius: 50%;
            font-size: 20px;
            background: #fff;
            color: #000;
            transition: 0.3s;
        }

        .custom-footer-social-icons a:hover {
            transform: scale(1.1);
        }

        .custom-footer-social-icons a.whatsapp {
            background: #D2241D;
            color: #fff;
        }

        .custom-footer-social-icons a.facebook {
            background: #1877f2;
            color: #fff;
        }

        .custom-footer-social-icons a.twitter {
            background: #000;
            color: #fff;
        }

        .custom-footer-social-icons a.linkedin {
            background: #0a66c2;
            color: #fff;
        }

        .custom-footer-social-icons a.youtube {
            background: #ff0000;
            color: #fff;
        }

        .custom-footer-social-icons a.instagram {
            background: #E1306C;
            color: #fff;
        }

        .custom-footer-social-icons a.behance {
            background: #1769ff;
            color: #fff;
        }

        .custom-footer-social-icons a.pinterest {
            background: #e60023;
            color: #fff;
        }

       /* Hover gap fix — About Us / What We Offer dropdown-submenu মাঝের ফাঁকা জায়গা bridge করে */
/* JS দিয়ে নিয়ন্ত্রিত hover-open ক্লাস */
.special_dropdown.special_hover-open > .special_submenu {
    display: block !important;
}

.special_dropdown.special_has-mega.special_hover-open > .special_megamenu {
    display: flex !important;
}
    </style>
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
    <link href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
                        <a href="{{ route('home') }}"><img src="{{ asset('/uploads/setting/' . $setting->logo_path) }}"
                                alt="{{ $setting->title ?? 'Logo' }}"></a>
                    </div>
                @endif

                <ul class="special_nav-links" id="navLinks">

                    @php
                        $page_home = \App\Models\PageSetup::page('home');
                    @endphp
                    @if (isset($page_home))
                        <li><a class="{{ Request::path() == '/' ? 'special_current' : '' }}"
                                href="{{ route('home') }}">{{ $page_home->title }}</a></li>
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
                                    <li><a href="{{ route('about') }}">{{ $page_about->title }}</a></li>
                                @endif
                                @if (isset($page_faqs))
                                    <li><a href="{{ route('faqs') }}">{{ $page_faqs->title }}</a></li>
                                @endif
                                @if (isset($page_contact))
                                    <li><a href="{{ route('contact') }}">{{ $page_contact->title }}</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @php
                        $page_services = \App\Models\PageSetup::page('services');
                    @endphp
                    @if (isset($page_services))
                        <li class="special_dropdown special_has-mega">
                            <a class="{{ Request::is('service*') || Request::is('related-service*') ? 'special_current' : '' }}"
                                href="{{ route('services') }}">{{ $page_services->title }} <span
                                    class="special_chevron"></span></a>

                            {{-- Mobile / tablet: simple accordion list (unchanged) --}}
                            <ul class="special_submenu">
                                @foreach ($service_subnavs as $service_subnav)
                                    @if (isset($service_subnav->manu) && $service_subnav->manu == 1)
                                        <li>
                                            <a
                                                href="{{ route('service.single', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
                                            @if ($service_subnav->subservices->count() > 0)
                                                <ul class="special_submenu-nested">
                                                    @foreach ($service_subnav->subservices as $sub)
                                                        <li><a
                                                                href="{{ route('service.related-single', $sub->slug) }}">{{ $sub->short_title }}</a>
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
                                                    <a
                                                        href="{{ route('service.single', $service_subnav->slug) }}">{{ $service_subnav->short_title }}</a>
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
                                                        {{ $service_subnav->short_title }}</div>
                                                    <div class="special_megamenu-columns">
                                                        @php
                                                            $subChunks = $service_subnav->subservices->chunk(
                                                                (int) ceil($service_subnav->subservices->count() / 2),
                                                            );
                                                        @endphp
                                                        @foreach ($subChunks as $chunk)
                                                            <ul class="special_megamenu-col">
                                                                @foreach ($chunk as $sub)
                                                                    <li><a
                                                                            href="{{ route('service.related-single', $sub->slug) }}">{{ $sub->short_title }}</a>
                                                                    </li>
                                                                @endforeach
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

                    <li><a class="{{ Request::is('technologies*') ? 'special_current' : '' }}"
                            href="{{ route('technologies') }}">Technologies</a></li>

                    @php
                        $page_portfolio = \App\Models\PageSetup::page('portfolio');
                    @endphp
                    @if (isset($page_portfolio))
                        <li><a class="{{ Request::is('portfolio*') ? 'special_current' : '' }}"
                                href="{{ route('portfolios') }}">{{ $page_portfolio->title }}</a></li>
                    @endif

                    @php
                        $all_pages = \App\Models\Page::where('type', 'casestudy')->get();
                        $isCurrentCasestudy = $all_pages->contains('slug', request()->segment(2));
                    @endphp
                    @if ($all_pages->count())
                        <li class="special_dropdown">
                            <a class="{{ $isCurrentCasestudy ? 'special_current' : '' }}"
                                href="#">{{ __('Case Study') }} <span class="special_chevron"></span></a>
                            <ul class="special_submenu">
                                @foreach ($all_pages as $page)
                                    <li><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
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
                                href="#">{{ __('Resources') }} <span class="special_chevron"></span></a>
                            <ul class="special_submenu">
                                @foreach ($re_page as $page)
                                    <li><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    {{-- @php
                        $page_blog = \App\Models\PageSetup::page('blog');
                    @endphp
                    @if (isset($page_blog))
                        <li><a class="{{ Request::is('blogs*') ? 'special_current' : '' }}"
                                href="{{ route('blogs') }}">{{ $page_blog->title }}</a></li>
                    @endif --}}
                </ul>
                @php
                    $page_quote = \App\Models\PageSetup::page('get-quote');
                @endphp
                @if (isset($page_quote))
                    <a href="{{ route('get-quote') }}" style="background-color: #D2241D"
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
                    a.addEventListener('click', function() {
                        if (navLinks.classList.contains('special_open')) toggleMenu();
                    });
                });

                // Mega menu: hovering a left item shows its panel on the right
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
                }
            })();
        </script>
        <!--End Main Header -->

        <!--  -->
        <!-- Content Start -->
        @yield('content')
        <!-- Content End -->



        <!-- Main custom-Footer -->
        <footer class="custom-footer">
            <div class="container">
                <div class="row text-left">

                    <div class="col-md-3 custom-footer-section mb-4">
                        <h5>Company</h5>
                        <ul>
                            <li><a href="{{ route('about') }}">About Us</a></li>
                            {{-- <li><a href="#">Careers</a></li>
                            <li><a href="#">Giving Back</a></li>
                            <li><a href="#">Referral Program</a></li> --}}
                        </ul>
                    </div>

                    <div class="col-md-3 custom-footer-section mb-4">
                        <h5>Services</h5>
                        <ul>
                            <li><a href="{{ route('services') }}">Services</a></li>
                            <li><a href="{{ route('technologies') }}">Technologies</a></li>
                            {{-- <li><a href="#">How We Work</a></li> --}}
                        </ul>
                    </div>

                    <div class="col-md-3 custom-footer-section mb-4">
                        <h5>Insights</h5>
                        <ul>
                            <li><a href="{{ route('blogs') }}">Blog</a></li>
                            <li><a href="{{ route('case') }}">Case Studies</a></li>
                            {{-- <li><a href="#">Sitemap</a></li> --}}
                        </ul>
                    </div>

                    @if (count($pages) > 0)
                        <div class="col-md-3 custom-footer-section mb-4">
                            <h5>Policies</h5>
                            <ul>
                                @foreach ($pages as $key => $page)
                                    @if (isset($page->type) && $page->type == 'footer')
                                        <li><a href="{{ route('page.single', $page->slug) }}">{{ $page->title }}</a>
                                        </li>
                                    @endif
                                @endforeach
                                {{-- <li><a href="#">Privacy Policy</a></li> --}}
                                {{-- <li><a href="#">Cookie Policy</a></li>
                                <li><a href="#">Refund Policy</a></li> --}}
                                {{-- <li><a href="#">Disclaimer</a></li> --}}
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="custom-footer-divider"></div>

                <div class="d-flex justify-content-between align-items-center">
                    @if (isset($setting))
                        <div class="custom-footer-bottom mt-3">
                            <p style="color: #ffffff;">Copyright &copy; 2023 –
                                {!! strip_tags($setting->footer_text, '<p><a><b><i><u><strong>') !!}
                            </p>
                        </div>
                    @endif
                    <div class="text-center">
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
                    </div>
                </div>


            </div>
        </footer>



    </div>

    <script src="//code.jquery.com/jquery-3.6.0.min.js"></script>



    @if ($livechat->status == 1)
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


    @if ($livechat->status == 0)
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
                    js.src = '//connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));

            })(jQuery);
        </script>

        <!-- Your Chat Plugin code -->
        <div class="fb-customerchat" attribution=setup_tool page_id="{{ $livechat->facebook_id }}"
            theme_color="{{ $livechat->facebook_color }}" logged_in_greeting="{{ $livechat->facebook_greeting_in }}"
            logged_out_greeting="{{ $livechat->facebook_greeting_out }}">
        </div>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let waButton = document.createElement("div");
            waButton.innerHTML = `
            
                <a rel="noopener noreferrer" href="//wa.link/lnuvjw" target="_blank" class="whatsapp-button">
                    <img src="//upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
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










        // Dropdown/Mega menu hover with close-delay (fast-close সমস্যা সমাধান)
document.querySelectorAll('.special_dropdown').forEach(function(dropdown) {
    let closeTimer;

    dropdown.addEventListener('mouseenter', function() {
        clearTimeout(closeTimer);
        dropdown.classList.add('special_hover-open');
    });

    dropdown.addEventListener('mouseleave', function() {
        closeTimer = setTimeout(function() {
            dropdown.classList.remove('special_hover-open');
        }, 300); // 300ms delay — এই সময়ের মধ্যে মেনুতে ঢুকলে বন্ধ হবে না
    });
});
    </script>
    @yield('scriptjs')

</body>

</html>
