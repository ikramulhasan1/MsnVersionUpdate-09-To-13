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
                linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
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
                        <span class="idx-hero-badge">
                            <span class="idx-dot"></span>
                            A Top-Rated & Leading Digital Agency </span>

                        <h1 class="idx-hero-title">
                            {!! $slider->title !!}
                        </h1>

                        <p class="idx-hero-sub">
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

                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            @foreach ($services as $service)
                                <a href="{{ route('services') }}" style="text-decoration: none" class="idx-pill-feature">
                                    <span class="idx-pill-icon"><i class="fs-5 {{ $service->service_icon }}"></i></span>
                                    {{ $service->short_title }}
                                </a>
                            @endforeach

                        </div>
                    </div>
                </section>
            @endforeach
        @endif

        {{-- ================= TRUST STATEMENT (no matching Laravel section — kept as demo design) ================= --}}
        <section class="idx-trust-section">
            <div class="container">
                <p class="idx-trust-text mb-0">
                    <span class="idx-highlight">Trusted by Customers across 40+ countries to take products from concept
                        to scale.</span>

                </p>

                {{-- ===== STAT COUNTER ROW (added — update numbers/labels as needed) ===== --}}
                <style>
                    .trust-stat-row {
                        display: flex;
                        flex-wrap: wrap;
                        background: #ffffff;
                        border-radius: 10px;
                        overflow: hidden;
                        margin: 40px 0 44px;
                        border: 1px solid #eee;
                        box-shadow: 0 10px 30px -8px rgba(210, 36, 29, 0.12), 0 2px 8px rgba(0, 0, 0, 0.05);
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
                    <div class="trust-stat-item">
                        <div class="trust-stat-number">3700+</div>
                        <div class="trust-stat-label">Projects Completed</div>
                    </div>
                    <div class="trust-stat-item">
                        <div class="trust-stat-number">900+</div>
                        <div class="trust-stat-label">Happy Clients</div>
                    </div>
                    <div class="trust-stat-item">
                        <div class="trust-stat-number">56+</div>
                        <div class="trust-stat-label">Expert Developers</div>
                    </div>
                    <div class="trust-stat-item">
                        <div class="trust-stat-number">25+</div>
                        <div class="trust-stat-label">Countries Served</div>
                    </div>
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
                            href="http://msnversionupdate-09-to-13.test/services" target="_blank"
                            rel="noopener noreferrer">View All
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
    <div class="ptn-scope">
        <section class="ptn-partners-section">
            <div class="container">
                <p class="ptn-partners-label">Technology Partners</p>

                <div class="ptn-partners-panel">
                    <div class="ptn-partners-row">

                        <!-- Xano -->
                        <div class="ptn-partner-item">
                            <div class="ptn-xano-badge">
                                <div class="ptn-stripe"></div>
                                <div class="ptn-xano-body">
                                    <div class="ptn-xano-title">XANO</div>
                                    <div class="ptn-xano-sub">OFFICIAL <span>PARTNER</span></div>
                                </div>
                            </div>
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

                        <!-- weweb -->
                        <div class="ptn-partner-item">
                            <span class="ptn-weweb-word">weweb</span>
                        </div>

                        <!-- Google Workspace -->
                        <div class="ptn-partner-item">
                            <div>
                                <span class="ptn-gws-title"><b style="color:#4285F4;">G</b><b
                                        style="color:#EA4335;">o</b><b style="color:#FBBC05;">o</b><b
                                        style="color:#4285F4;">g</b><b style="color:#34A853;">l</b><b
                                        style="color:#EA4335;">e</b> Workspace</span>
                                <span class="ptn-gws-partner">Partner</span>
                            </div>
                        </div>

                        <!-- JioCloud -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-jio-icon">
                                <i class="fa-solid fa-cloud"></i>
                            </div>
                            <span class="ptn-jio-name">JioCloud</span>
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
