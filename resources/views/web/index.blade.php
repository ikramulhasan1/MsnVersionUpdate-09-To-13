@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('home');
@endphp
@if (isset($header))

    @section('title', $header->meta_title)

    @section('top_meta_tags')
        @if (isset($header->meta_description))
            <meta name="description" content="{!! str_limit(strip_tags($header->meta_description), 160, ' ...') !!}">
        @else
            <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
        @endif

        @if (isset($header->meta_keywords))
            <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
        @else
            <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
        @endif
    @endsection

@endif

@section('social_meta_tags')
    @if (isset($setting))
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

    {{-- Bootstrap 5, Font Awesome 6, and Google Fonts are already loaded once in
       web.layouts.master (bootstrap 5.3.8 via jsDelivr, font-awesome 6.7.2 + bootstrap-icons
       via cdnjs, Plus Jakarta Sans via Google Fonts). Re-declaring a second copy of each here
       (a different CDN/version) was doubling render-blocking CSS requests on every page load —
       removed. --}}
@endsection

@section('content')
    <style>
        /* ===== IDX HERO — background matched to About Us page style (dark red radial fade to black) ===== */
        .idx-hero-section {
            position: relative;
            background-color: #0d0405;
            background-image:
                radial-gradient(circle at 12% 15%, rgba(178, 24, 24, 0.75) 0%, rgba(120, 16, 16, 0.35) 30%, rgba(13, 4, 5, 0) 60%),
                linear-gradient(135deg, #2a0a0a 0%, #150607 45%, #0d0405 100%);
            background-repeat: no-repeat;
            background-size: cover;
            color: #fff;
            overflow: hidden;
        }

        .idx-hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.078) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.068) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .idx-hero-section .container {
            position: relative;
            z-index: 1;
        }

        .techImg {
            width: 50px;
            height: 50px;
        }

        .tch-stage {
            display: flex;
            flex-wrap: wrap;
            /* জায়গা শেষ হলে নিচে চলে যাবে */
            gap: 20px;
            justify-content: center;
        }

        .tch-card {
            width: 100px;
            height: 100px;
            flex: 0 0 auto;
        }


        .svc-service-list {
            display: flex;
            flex-wrap: wrap;
            /* নতুন লাইনে চলে যাবে */
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .svc-service-list li {
            border: 1px solid #fed7d7;
            border-radius: 20px;
            padding: 6px 10px;
            max-width: 100%;
            overflow-wrap: break-word;
        }

        /* ===== TCH SECTION — service categories (left) + tech logos (right) ===== */
        .tch-layout {
            display: flex;
            align-items: flex-start;
            gap: 32px;
            margin-top: 30px;
        }

        .tch-categories {
            flex: 0 0 280px;
            max-width: 280px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tch-cat-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }

        .tch-cat-btn i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .tch-cat-btn:hover {
            border-color: var(--red-600, #d2241d);
        }

        .tch-cat-btn.active {
            background: var(--red-600, #d2241d);
            border-color: var(--red-600, #d2241d);
            color: #fff;
        }

        .tch-logos-wrap {
            flex: 1 1 0;
            min-width: 0;
        }

        .tch-logos-panel {
            display: none;
        }

        .tch-logos-panel.active {
            display: block;
        }

        @media (max-width: 991px) {
            .tch-layout {
                flex-direction: column;
            }

            .tch-categories {
                flex: 0 0 auto;
                max-width: 100%;
                flex-direction: row;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
            }

            .tch-cat-btn {
                width: auto;
                white-space: nowrap;
                flex: 0 0 auto;
            }
        }

        @media (max-width: 575px) {
            .tch-cat-btn {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .tch-stage {
                justify-content: flex-start;
            }
        }
    </style>
    {{-- ============ IDX SECTION (hero) — wired to $sliders ============ --}}
    <div class="idx-scope">

        @if (count($sliders) > 0)
            @foreach ($sliders as $slider)
                <section class="idx-hero-section text-center">
                    <div class="container">
                        <div class="idx-gloss"></div>
                        <div class="idx-particles">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                        </div>
                        <span class="idx-hero-badge">
                            <span class="idx-dot"></span>
                            A Top-Rated & Leading Digital Agency </span>

                        <h1 class="idx-hero-title">
                            {!! $slider->title !!}
                        </h1>

                        <p style="color: white" class="idx-hero-sub">
                            {!! $slider->description !!}
                        </p>
                        @php
                            $page_quote = \App\Models\PageSetup::page('get-quote');
                        @endphp
                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                            {{-- <a href="{{ route('get-quote') }}" wire:navigate class="idx-btn-primary-red">Talk to an Expert <i
                                    class="fa-solid fa-arrow-right"></i></a> --}}
                            <a href="//wa.me/{{ str_replace(' ', '', $social->whatsapp) }}"
                                class="cta-cta-btn cta-cta-btn-whatsapp">
                                <svg class="cta-wa-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.13-2.9-7C17.19 3.03 14.7 2 12.04 2Zm0 18.13h-.01c-1.48 0-2.94-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.25-4.36c0-4.53 3.69-8.22 8.23-8.22 2.2 0 4.26.86 5.82 2.41a8.15 8.15 0 0 1 2.41 5.81c0 4.53-3.69 8.22-8.21 8.22Zm4.51-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31s-.87.85-.87 2.08.89 2.41 1.02 2.58c.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z" />
                                </svg>
                                Chat on WhatsApp
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                            {{-- <a href="{{ route('services') }}" wire:navigate class="idx-btn-outline-dark-pill">Explore Our Work <i
                                    class="fa-solid fa-arrow-right"></i></a> --}}
                            <button type="button" class="cta-cta-btn idx-btn-primary-red"
                                style="background-color: #D2241D; color: white;"
                                onclick="document.getElementById('quotePopupModal').classList.add('is-open'); document.body.style.overflow='hidden';">
                                {{ $page_quote->title }}

                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>

                        {{-- <div class="d-flex flex-wrap justify-content-center gap-3">
                            @foreach ($services as $service)
                                <a href="{{ route('services') }}" style="text-decoration: none" class="idx-pill-feature">
                                    <span class="idx-pill-icon"><i class="fs-5 {{ $service->service_icon }}"></i></span>
                                    {{ $service->short_title }}
                                </a>
                            @endforeach

                        </div> --}}
                    </div>
                </section>
            @endforeach
        @endif

        {{-- ================= TRUST STATEMENT (no matching Laravel section — kept as demo design) ================= --}}
        <section class="idx-trust-section">
            <div class="container">
                <p class="idx-trust-text mb-0">
                    <span class="idx-highlight">Helping Businesses Build, Launch & Scale Worldwide.</span>

                </p>

                {{-- ===== STAT COUNTER ROW (added — update numbers/labels as needed) ===== --}}
                <style>
                    .trust-stat-row {
                        display: flex;
                        flex-wrap: wrap;
                        background: #ffffff;
                        border-radius: 4px;
                        overflow: hidden;
                        margin: 40px 0 44px;
                        border: 1px solid #eee;
                        box-shadow: 0 0px 0px -1px rgba(45, 41, 41, 0.12), 0 2px 8px rgba(0, 0, 0, 0.05);
                    }

                    .trust-stat-item {
                        flex: 1 1 0;
                        min-width: 140px;
                        padding: 28px 32px;
                        border-right: 1px solid #e7e2e2;
                    }

                    .trust-stat-item:last-child {
                        border-right: none;
                    }

                    .trust-stat-number {
                        /* font-family: 'Courier New', ui-monospace, monospace; */
                        font-weight: 800;
                        font-size: clamp(1.8rem, 3vw, 2.6rem);
                        line-height: 1;
                        color: var(--red-600, #d2241d);
                        margin-bottom: 10px;
                    }

                    .trust-stat-label {
                        /* font-family: 'Courier New', ui-monospace, monospace; */
                        /* font-size: 0.78rem; */
                        letter-spacing: 0.04em;
                        text-transform: uppercase;
                        font-weight: 700;
                        /* color: #7a7370; */
                    }

                    @media (max-width: 767px) {
                        .trust-stat-row {
                            flex-direction: column;
                        }

                        .trust-stat-item {
                            border-right: none;
                            border-bottom: 1px solid #e7e2e2;
                        }

                        .trust-stat-item:last-child {
                            border-bottom: none;
                        }
                    }
                </style>

                <div class="trust-stat-row">
                    @foreach ($counters as $counter)
                        <div class="trust-stat-item">
                            <div class="trust-stat-number">{{ $counter->value }}</div>
                            <div class="trust-stat-label">{{ $counter->title }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- ============ SVC SECTION (services) — wired to $services / $section_services ============ --}}
    <div class="svc-scope">
        @php
            $section_services = \App\Models\Section::section('services');

        @endphp
        <style>
            .svc-service-desc {
                color: #000000;
            }

            .svc-service-desc ul {
                list-style: none;
                padding-left: 10px;
                /* পুরো লিস্ট (bullet + content) ১০px right এ shift হবে */
                margin: 0;
                color: #000000;
            }

            .svc-service-desc ul li {
                position: relative;
                padding-left: 18px;
                /* bullet-এর জন্য জায়গা, ul এর padding-left এর উপরে যোগ হবে */
                margin-bottom: 4px;
                font-size: 14px;
            }

            .svc-service-desc ul li::before {
                content: "•";
                position: absolute;
                left: 0;
                color: #ff0000;
                font-weight: bold;
            }
        </style>
        @if (count($services) > 0 && isset($section_services))
            <section class="svc-services-section">
                <div class="container">
                    <h2 class="svc-section-title">{{ $section_services->title }}</h2>

                    <div class="row g-4">
                        @foreach ($services as $key => $service)
                            <div class="col-12 col-md-6 col-lg-4 disabled-link">
                                <a href="{{ route('service.single', $service->slug) }}" wire:navigate
                                    class="svc-service-card text-decoration-none d-block text-black">
                                    <div class="svc-service-icon"><i class="{{ $service->service_icon }}"></i></div>
                                    <h3 class="svc-service-title">{{ $service->short_title }}</h3>

                                    @if (isset($service->short_description) && trim(strip_tags($service->short_description)) !== '')
                                        <div class="svc-service-desc">
                                            {!! $service->short_description !!}
                                        </div>
                                    @endif

                                    @if (isset($service->tags) && trim($service->tags) !== '')
                                        <ul class="svc-service-list">
                                            @foreach (array_filter(array_map('trim', explode(',', $service->tags))) as $tag)
                                                <li>{{ $tag }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <h4 style="text-align: center; margin-top: 50px;"><a wire:navigate=""
                            class="cta-cta-btn idx-btn-primary-red"
                            style="background-color: #D2241D; color: white; text-decoration: none;"
                            href="{{ route('services') }}" target="_blank" rel="noopener noreferrer">View All
                            Services</a></h4>
                </div>
            </section>
        @endif
    </div>

    {{-- ============ TCH SECTION (service categories left, matching tech logos right) ============ --}}
    <div class="tch-scope">
        <section class="tch-section container">
            <div class="container-fluid px-3">
                <p class="tch-eyebrow">Technologies We Work With</p>
                <h2 class="tch-heading">A Full Stack of Modern Tools</h2>

                <div class="tch-layout" id="tchLayout">
                    {{-- LEFT: service categories --}}
                    <div class="tch-categories" role="tablist" aria-label="Service categories">
                        @foreach ($services as $key => $service)
                            @if ($service->technologies->count() > 0)
                                <button type="button" class="tch-cat-btn {{ $loop->first ? 'active' : '' }}"
                                    data-tch-target="tchPanel{{ $key }}" role="tab"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    @if ($service->service_icon)
                                        <i class="{{ $service->service_icon }}"></i>
                                    @endif
                                    <span>{{ $service->short_title }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- RIGHT: tech logos for the selected category --}}
                    <div class="tch-logos-wrap">
                        @foreach ($services as $key => $service)
                            @if ($service->technologies->count() > 0)
                                <div class="tch-logos-panel {{ $loop->first ? 'active' : '' }}"
                                    id="tchPanel{{ $key }}" role="tabpanel">
                                    <div class="tch-stage" aria-label="{{ $service->short_title }} technology logos">
                                        @foreach ($service->technologies as $technology)
                                            <div class="tch-card" title="{{ $technology->short_title }}">
                                                <span class="tch-tooltip">{{ $technology->short_title }}</span>

                                                <div class="tch-float-inner"
                                                    style="--tch-dur:4.47s; --tch-delay:1.61s; --tch-amp:-7.2px;">
                                                    <img class="techImg"
                                                        src="{{ asset('uploads/technology/' . $technology->logo_path) }}"
                                                        width="50" height="50"
                                                        alt="{{ $technology->short_title }}" loading="lazy">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <script>
            function initTchTabs() {
                const wrap = document.getElementById('tchLayout');
                if (!wrap || wrap.dataset.tchInitialized === '1') return;
                wrap.dataset.tchInitialized = '1';

                wrap.addEventListener('click', function(e) {
                    const btn = e.target.closest('.tch-cat-btn');
                    if (!btn || !wrap.contains(btn)) return;

                    wrap.querySelectorAll('.tch-cat-btn').forEach((b) => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');

                    const targetId = btn.getAttribute('data-tch-target');
                    wrap.querySelectorAll('.tch-logos-panel').forEach((panel) => {
                        panel.classList.toggle('active', panel.id === targetId);
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', initTchTabs);
            document.addEventListener('livewire:navigated', initTchTabs);
        </script>
    </div>

    {{-- ============ PTN SECTION (technology partners — no matching Laravel section, kept as demo design) ============ --}}
    {{-- ============ PTN SECTION (technology partners — no matching Laravel section, kept as demo design) ============ --}}
    <div class="ptn-scope">
        <section class="ptn-partners-section">
            <div class="container">
                <p class="ptn-partners-label">Technology Partners</p>

                <div class="ptn-partners-panel">
                    <div class="ptn-partners-row">

                        <!-- AWS -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-aws-icon">
                                <svg viewBox="0 0 304 182" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#252F3E"
                                        d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5C39.4 1 44.6.4 50.1.4c11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4l1.8.9zM45.8 81.6c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.1 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l16.8 66.2 15.6-66.2c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4 1 .8.6 1.5 2 1.9 3.9l15.8 67 17.3-67c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-24.1 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4-1-.8-.7-1.5-2-1.9-4L156 23.7l-15.4 64.7c-.5 2-1.1 3.3-1.9 4-.8.7-2.2 1-4 1h-8.8zm128.2 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2l-14.5-4.5c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2 1-13.3 2.9-3.1 1.9-4.7 4.8-4.7 8.9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.7 11 5.4l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.7 3.1 4.2 4.6 9 4.6 14.2 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z" />
                                    <path fill="#FF9900"
                                        d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" />
                                    <path fill="#FF9900"
                                        d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 4-9.9 12.9-32.2 8.7-37.5z" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">AWS<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                        <!-- Cloudflare -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-cloudflare-icon">
                                <svg viewBox="0 0 200 130" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#F4811F"
                                        d="M155.2 84.6c1.4-4.8.9-9.2-1.4-12.4-2.1-3-5.6-4.7-9.9-5l-80.5-1c-.5 0-1-.3-1.3-.7-.3-.4-.4-1-.2-1.5.3-.8 1.1-1.4 2-1.5l81.2-1c9.6-.4 20.1-8.3 23.8-17.9l4.6-12.1c.2-.5.3-1 .1-1.5C165 15.6 141.5 0 114.4 0 89.5 0 68.2 13 56.6 32.5c-7.9-5.9-18-8.9-28.7-7.9C8.4 26.5-6.9 42.3-8.6 61.9c-.4 4.9-.1 9.8.9 14.5C-24.2 79.6-33 89.3-33 101c0 1.3.1 2.6.2 3.9.1.9.9 1.6 1.8 1.6h169.4c1 0 1.9-.7 2.1-1.7l1-3.5z" />
                                    <path fill="#FBAD41"
                                        d="M170.9 41.9c-.7 0-1.5 0-2.2.1-.5 0-1 .4-1.2.9l-3.1 10.9c-1.4 4.8-.9 9.2 1.4 12.4 2.1 3 5.6 4.7 9.9 5l17.1 1c.5 0 1 .3 1.3.7.3.4.4 1 .2 1.5-.3.8-1.1 1.4-2 1.5l-17.9 1c-9.6.4-20.1 8.3-23.8 17.9l-1.3 3.3c-.3.6.2 1.2.8 1.2h61.6c1 0 1.8-.6 2.1-1.6.9-3.4 1.4-6.9 1.4-10.6.1-22.1-17.9-40-40-40h-4.3z" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">Cloudflare<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                        <!-- PayPal -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-paypal-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#003087"
                                        d="M8.4 21.5H5.2c-.6 0-1-.5-.9-1L7.6 2.2c.1-.5.5-.8 1-.8h6.7c3.2 0 5.4 1.9 5 4.9-.5 4.3-3.4 6.4-7.3 6.4H10c-.5 0-.9.3-1 .8l-.6 5.7c0 .3-.3.3-.3.3z" />
                                    <path fill="#009cde"
                                        d="M18.3 6.3c-.5 3.9-3.4 6.4-7.3 6.4H8.9c-.5 0-.9.3-1 .8l-1.3 8.2c-.1.5.3 1 .9 1h3c.4 0 .8-.3.9-.7l.7-4.7c.1-.5.5-.8.9-.8h1.4c3.9 0 6.8-2.1 7.4-6.3.3-2.1-.1-3.6-1.1-4.5-.1.1-.2.4-.4.6z" />
                                    <path fill="#012169"
                                        d="M17.4 5.9c.2-.2.3-.4.4-.6-1-.9-2.6-1.4-4.6-1.4H7.5c-.5 0-.9.3-1 .8L4.3 20.5c-.1.5.3 1 .9 1h3l1.1-6.9c.1-.5.5-.8 1-.8h2.1c3.9 0 6.8-2.5 7.3-6.4.1-.5.1-1 0-1.5z" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">PayPal<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                        <!-- Google Cloud -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-gcloud-icon">
                                <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M32.6 18.3l1.6-6.2a1 1 0 00-.3-1A15.9 15.9 0 0024 6c-6 0-11.2 3.2-14 8l6.7 5.4c1.4-4 5-6.4 9-6.4 2.5 0 4.7.9 6.4 2.3.4.3.9.4 1.3.0z"
                                        fill="#EA4335" />
                                    <path
                                        d="M8 32c-1.4-2.4-2.2-5.1-2.2-8 0-2.7.7-5.3 1.9-7.6l6.7 5.4c-.5 1-.8 2-.8 3.2 0 1.1.3 2.2.8 3.1z"
                                        fill="#FBBC05" />
                                    <path
                                        d="M24 44c5.2 0 9.7-1.9 12.9-5.1l-6.3-5.2c-1.7 1.2-3.9 2-6.6 2-4 0-7.4-2.4-8.9-6.4l-6.7 5.4C11 40.6 17 44 24 44z"
                                        fill="#34A853" />
                                    <path
                                        d="M42.7 24.3c0-1.4-.1-2.7-.4-4H24v8h10.6c-.4 2.2-1.7 4-3.5 5.3l6.3 5.2C40.9 35.6 42.7 30.4 42.7 24.3z"
                                        fill="#4285F4" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">Google Cloud<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                        <!-- Shopify -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-shopify-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#95BF47"
                                        d="M18.7 3.9s-.1-.1-.2-.1c0 0-1.7-.1-1.7-.1s-1.4-1.4-1.5-1.5c-.1-.1-.4-.1-.5 0l-.7.2c-.1-.4-.4-.9-.7-1.3-.5-.6-1.2-.9-2-.9h-.1c0-.1-.1-.1-.1-.1-.3-.4-.7-.5-1-.5C7.5 0 6.1 1.8 5.6 4l-1.5.5c-.5.1-.5.2-.6.6L1.9 20.8 15.5 24l6.9-1.5s-3.5-18.4-3.7-18.6zm-4.5-1.1l-1.1.3v-.2c0-.6-.1-1.1-.3-1.5.7.1 1.1.7 1.4 1.4zm-2-1.2c.2.4.3.9.3 1.6v.1L10.8 3c.3-1 .8-1.5 1.4-1.7-.1 0 0 .3 0 .3zM11 1.5c.1 0 .2 0 .3.1-.7.3-1.4 1.1-1.7 2.7l-1.6.5C8.4 3 9.5 1.5 11 1.5z" />
                                    <path fill="#5E8E3E"
                                        d="M18.5 3.8s-1.7-.1-1.7-.1-1.4-1.4-1.5-1.5c-.1-.1-.1-.1-.2-.1L15.5 24l6.9-1.5s-3.5-18.4-3.7-18.6c0-.1-.1-.1-.2-.1z" />
                                    <path fill="#fff"
                                        d="M12.5 8.6l-.8 2.5s-.7-.4-1.6-.4c-1.3 0-1.3.8-1.3 1 0 1.1 2.9 1.5 2.9 4.1 0 2.1-1.3 3.4-3.1 3.4-2.2 0-3.3-1.4-3.3-1.4l.6-1.9s1.1 1 2.1 1c.6 0 .9-.5.9-.9 0-1.5-2.4-1.6-2.4-4 0-2 1.5-4 4.4-4 1.2 0 1.6.3 1.6.3z" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">Shopify<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ============ TST SECTION (testimonials) — wired to $testimonials / $section_testimonials ============ --}}
    <div class="tst-scope">
        @php
            $section_testimonials = \App\Models\Section::section('testimonials');
        @endphp

        @if (count($testimonials) > 0 && isset($section_testimonials))
            <section class="tst-testimonial-section">
                <div class="container">

                    <div class="tst-carousel-viewport">
                        <div class="tst-carousel-track" id="tstCarouselTrack">

                            @foreach ($testimonials as $testimonial)
                                <div class="tst-testi-card">
                                    <div class="tst-testi-stars">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                            class="fa-solid fa-star"></i>
                                    </div>
                                    <p class="tst-testi-quote">{!! $testimonial->description !!}</p>
                                    <div class="tst-testi-footer">
                                        <div class="tst-testi-avatar">
                                            @if (isset($testimonial->image_path))
                                                <img src="{{ asset('uploads/testimonial/' . $testimonial->image_path) }}"
                                                    alt="{{ $testimonial->title }}" loading="lazy"
                                                    style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                                            @else
                                                {{ strtoupper(substr($testimonial->title, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="tst-testi-name">{{ $testimonial->title }}</div>
                                            <div class="tst-testi-role">{{ $testimonial->designation }}@if (isset($testimonial->organization))
                                                    , {{ $testimonial->organization }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="tst-carousel-nav">
                        <button class="tst-nav-btn" id="tstPrevBtn" aria-label="Previous"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button class="tst-nav-btn" id="tstNextBtn" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                    </div>

                </div>
            </section>

            <script>
                function initTstCarousel() {
                    const track = document.getElementById('tstCarouselTrack');
                    if (!track) return;

                    const cards = Array.from(track.children);
                    const tstPrevBtn = document.getElementById('tstPrevBtn');
                    const tstNextBtn = document.getElementById('tstNextBtn');
                    if (!tstPrevBtn || !tstNextBtn || !cards.length) return;

                    let currentIndex = 0;

                    function getVisibleCount() {
                        const w = window.innerWidth;
                        if (w <= 575.98) return 1;
                        if (w <= 991.98) return 2;
                        return 3;
                    }

                    function getGap() {
                        return parseFloat(getComputedStyle(track).gap) || 24;
                    }

                    function update() {
                        // পেজ থেকে সরে গেলে (wire:navigate দিয়ে) এই track আর DOM-এ থাকবে না,
                        // তখন এই loop/handler কে চুপচাপ থেমে যেতে হবে
                        if (!document.body.contains(track)) return;

                        const visible = getVisibleCount();
                        const maxIndex = Math.max(0, cards.length - visible);
                        currentIndex = Math.min(currentIndex, maxIndex);

                        const cardWidth = cards[0].getBoundingClientRect().width;
                        const gap = getGap();
                        const offset = currentIndex * (cardWidth + gap);

                        track.style.transform = `translateX(-${offset}px)`;

                        tstPrevBtn.disabled = currentIndex === 0;
                        tstNextBtn.disabled = currentIndex >= maxIndex;
                        tstNextBtn.classList.toggle('tst-active-next', currentIndex < maxIndex);
                    }

                    tstPrevBtn.addEventListener('click', () => {
                        currentIndex = Math.max(0, currentIndex - 1);
                        update();
                    });

                    tstNextBtn.addEventListener('click', () => {
                        const visible = getVisibleCount();
                        const maxIndex = Math.max(0, cards.length - visible);
                        currentIndex = Math.min(maxIndex, currentIndex + 1);
                        update();
                    });

                    window.addEventListener('resize', update);
                    update();
                }

                // wire:navigate দিয়ে হোমপেজে বারবার এলেও carousel প্রতিবার re-init হবে,
                // আর পুরনো instance-এর event listener/element নতুন করে declare হওয়ার
                // সমস্যা (Identifier already declared) হবে না, কারণ সবকিছু function-scope-এ বন্দি
                document.addEventListener('DOMContentLoaded', initTstCarousel);
                document.addEventListener('livewire:navigated', initTstCarousel);
            </script>
        @endif
    </div>

    {{-- ============ CLI SECTION (client logos — no matching Laravel section, kept as demo design) ============ --}}
    @if (count($clients) > 0)
        <div class="cli-scope">
            <section class="cli-section">
                <div class="container-fluid px-0">
                    <p class="cli-eyebrow">Valued by Clients Worldwide</p>

                    <div class="cli-track-wrap" id="cliWrap">
                        <div class="cli-track" id="cliTrack">

                            @foreach ($clients as $client)
                                <div class="cli-card">
                                    <img src="{{ asset('uploads/client/' . $client->image_path) }}"
                                        alt="{{ $client->title }}" loading="lazy">
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </section>

            <script>
                function initCliCarousel() {
                    const cliWrap = document.getElementById("cliWrap");
                    const cliTrack = document.getElementById("cliTrack");
                    if (!cliWrap || !cliTrack) return;

                    // আগে থেকেই init করা থাকলে (একই DOM node-এ, hover/drag listener সহ)
                    // দ্বিতীয়বার বসাবো না, নাহলে ইভেন্ট লিসেনার আর rAF loop ডুপ্লিকেট হয়ে যাবে
                    if (cliWrap.dataset.cliInitialized === '1') return;
                    cliWrap.dataset.cliInitialized = '1';

                    // If you swap the markup for a single set of real <img> logos, this will
                    // auto-duplicate it once so the loop stays seamless.
                    if (!cliTrack.querySelector('[aria-hidden="true"]')) {
                        const clone = cliTrack.innerHTML;
                        cliTrack.innerHTML += clone;
                        [...cliTrack.children]
                        .slice(cliTrack.children.length / 2)
                            .forEach((el) => el.setAttribute("aria-hidden", "true"));
                    }

                    let cliSingleSetWidth = 0;

                    function cliMeasure() {
                        // width of ONE set = half of total scrollWidth (since content is duplicated)
                        cliSingleSetWidth = cliTrack.scrollWidth / 2;
                    }
                    cliMeasure();
                    window.addEventListener("resize", cliMeasure);

                    const CLI_SPEED = 0.6; // px per frame auto-scroll speed
                    let cliOffset = 0; // current translateX distance (positive = scrolled left)
                    let cliIsDragging = false;
                    let cliDragStartX = 0;
                    let cliDragStartOffset = 0;
                    let cliIsPaused = false; // paused on hover/pointer-down, resumes on release
                    let cliRafId = null;

                    function cliApplyTransform() {
                        // wrap offset into [0, singleSetWidth) for a seamless infinite loop
                        let wrapped = cliOffset % cliSingleSetWidth;
                        if (wrapped < 0) wrapped += cliSingleSetWidth;
                        cliTrack.style.transform = `translateX(${-wrapped}px)`;
                    }

                    function cliTick() {
                        // wire:navigate দিয়ে এই পেজ থেকে সরে গেলে এই node আর DOM-এ থাকবে না —
                        // তখন loop নিজে থেকে থেমে যাবে, নাহলে ব্যাকগ্রাউন্ডে চলতেই থাকবে
                        if (!document.body.contains(cliTrack)) return;

                        if (!cliIsDragging && !cliIsPaused) {
                            cliOffset += CLI_SPEED;
                            cliApplyTransform();
                        }
                        cliRafId = requestAnimationFrame(cliTick);
                    }
                    cliRafId = requestAnimationFrame(cliTick);

                    // Pause auto-scroll while the pointer is hovering (keeps things calm to inspect/drag)
                    cliWrap.addEventListener("mouseenter", () => {
                        cliIsPaused = true;
                    });
                    cliWrap.addEventListener("mouseleave", () => {
                        if (!cliIsDragging) cliIsPaused = false;
                    });

                    function cliStartDrag(clientX) {
                        cliIsDragging = true;
                        cliIsPaused = true;
                        cliDragStartX = clientX;
                        cliDragStartOffset = cliOffset;
                        cliWrap.classList.add("cli-is-dragging");
                    }

                    function cliMoveDrag(clientX) {
                        if (!cliIsDragging) return;
                        const delta = clientX - cliDragStartX;
                        cliOffset = cliDragStartOffset - delta; // drag right -> content moves right -> offset decreases
                        cliApplyTransform();
                    }

                    function cliEndDrag() {
                        if (!cliIsDragging) return;
                        cliIsDragging = false;
                        cliWrap.classList.remove("cli-is-dragging");
                        // resume auto-scroll shortly after release
                        setTimeout(() => {
                            cliIsPaused = false;
                        }, 300);
                    }

                    // Mouse events
                    cliWrap.addEventListener("mousedown", (e) => {
                        cliStartDrag(e.clientX);
                        e.preventDefault();
                    });
                    window.addEventListener("mousemove", (e) => cliMoveDrag(e.clientX));
                    window.addEventListener("mouseup", cliEndDrag);

                    // Touch events
                    cliWrap.addEventListener(
                        "touchstart",
                        (e) => cliStartDrag(e.touches[0].clientX), {
                            passive: true
                        },
                    );
                    cliWrap.addEventListener(
                        "touchmove",
                        (e) => cliMoveDrag(e.touches[0].clientX), {
                            passive: true
                        },
                    );
                    cliWrap.addEventListener("touchend", cliEndDrag);
                    cliWrap.addEventListener("touchcancel", cliEndDrag);

                    // Prevent native image/element drag ghost
                    cliTrack.addEventListener("dragstart", (e) => e.preventDefault());
                }

                document.addEventListener('DOMContentLoaded', initCliCarousel);
                document.addEventListener('livewire:navigated', initCliCarousel);
            </script>
        </div>
    @endif
    @include('web.inc.whymsn')
    {{-- ============ CTA SECTION (Unico Difference + closing CTA — no matching Laravel section, kept as demo design) ============ --}}
    {{-- Bootstrap's JS bundle (Popper + Bootstrap) was loaded 3x on this page (~65 KiB + parse/exec
       cost each time) but nothing on the page actually uses a Bootstrap JS component
       (no data-bs-* attributes anywhere in this view). Removed entirely; if a future
       section needs it (modal, dropdown, carousel via data-bs-toggle), add ONE copy
       with `defer` near the closing </body>, not inline mid-page. --}}

@endsection
