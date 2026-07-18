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

    {{-- New design (design.html) dependencies: Bootstrap 5, Font Awesome 6, Plus Jakarta Sans.
       NOTE: this design ships with no CSS of its own (the <style> block in design.html
       was empty) — per instruction we're shifting the markup/structure only. --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
@endsection

@section('content')
    <style>
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
    </style>
    {{-- ============ IDX SECTION (hero) — wired to $sliders ============ --}}
    <div class="idx-scope">

        @if (count($sliders) > 0)
            @foreach ($sliders as $slider)
                <section class="idx-hero-section text-center">
                    <div class="container">
                        <span class="idx-hero-badge">
                            <span class="idx-dot"></span>
                            From AI strategy to product in weeks
                        </span>

                        <h1 class="idx-hero-title">
                            {!! $slider->title !!}
                        </h1>

                        <p class="idx-hero-sub">
                            {!! $slider->description !!}
                        </p>

                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                            <a href="{{ route('get-quote') }}" class="idx-btn-primary-red">Talk to an Expert <i
                                    class="fa-solid fa-arrow-right"></i></a>
                            <a href="{{ route('services') }}" class="idx-btn-outline-dark-pill">Explore Our Work <i
                                    class="fa-solid fa-arrow-right"></i></a>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-robot"></i></span>
                                Agentic AI
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-code-branch"></i></span>
                                AI Integration &amp; Automation
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-window-maximize"></i></span>
                                Web Applications
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-mobile-screen"></i></span>
                                Mobile Applications
                            </span>
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

                <div class="idx-review-row">
                    <div class="idx-review-card">
                        <div>
                            <div class="idx-review-label">Reviewed on</div>
                            <div class="idx-review-brand">Clutch</div>
                        </div>
                        <div class="idx-stars-red">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i>
                            <div class="idx-review-count">52 Reviews</div>
                        </div>
                    </div>

                    <div class="idx-review-card idx-design-rush-badge">
                        <i class="fa-solid fa-award" style="color:var(--red-600); font-size:1.6rem;"></i>
                        <div>
                            <div class="idx-review-count">30 Reviews on DesignRush</div>
                            <div class="idx-stars-red">
                                <span class="idx-review-score" style="font-size:1rem;">4.9</span>
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="idx-review-card">
                        <i class="fa-solid fa-shield-halved" style="color:var(--red-600); font-size:1.6rem;"></i>
                        <div>
                            <div class="idx-review-count">Goodfirms &middot; 34 Reviews</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="idx-review-score">4.89</span>
                                <span class="idx-stars-red">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                        class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                        class="fa-solid fa-star"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    </div>

    {{-- ============ SVC SECTION (services) — wired to $services / $section_services ============ --}}
    <div class="svc-scope">
        @php
            $section_services = \App\Models\Section::section('services');

        @endphp
<style>
.svc-service-desc ul {
    list-style: none;
    padding-left: 0;
    margin: 0;
}

.svc-service-desc ul li {
    position: relative;
    padding-left: 18px;
    margin-bottom: 4px;
}

.svc-service-desc ul li::before {
    content: "•";
    position: absolute;
    left: 10;
    color: #ff0800;
    font-weight: bold;
}
</style>
        @if (count($services) > 0 && isset($section_services))
            <section class="svc-services-section">
                <div class="container">
                    <h2 class="svc-section-title">{{ $section_services->title }}</h2>

                    <div class="row g-4">
                        @foreach ($services as $key => $service)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('service.single', $service->slug) }}"
                                    class="svc-service-card text-decoration-none d-block">
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
                </div>
            </section>
        @endif

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    </div>

    {{-- ============ TCH SECTION (technology logos — no matching Laravel section, kept as demo design) ============ --}}
    <div class="tch-scope ">
        <section class="tch-section container">
            <div class="container-fluid px-3">
                <p class="tch-eyebrow">Technologies We Work With</p>
                <h2 class="tch-heading">A Full Stack of Modern Tools</h2>

                <div class="tch-stage" id="tchStage" aria-label="Technology stack logos">
                    @foreach ($technologies->shuffle() as $technology)
                        <div class="tch-card" title="{{ $technology->short_title }}">
                            <span class="tch-tooltip">{{ $technology->short_title }}</span>

                            <div class="tch-float-inner" style="--tch-dur:4.47s; --tch-delay:1.61s; --tch-amp:-7.2px;">
                                <img class="techImg" src="{{ asset('uploads/technology/' . $technology->logo_path) }}"
                                    loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

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
                const track = document.getElementById('tstCarouselTrack');
                const cards = Array.from(track.children);
                const tstPrevBtn = document.getElementById('tstPrevBtn');
                const tstNextBtn = document.getElementById('tstNextBtn');

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
                window.addEventListener('load', update);
                update();
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
                (function() {
                    const cliWrap = document.getElementById("cliWrap");
                    const cliTrack = document.getElementById("cliTrack");

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
                })();
            </script>
        </div>
    @endif
    @include('web.inc.whymsn')
    {{-- ============ CTA SECTION (Unico Difference + closing CTA — no matching Laravel section, kept as demo design) ============ --}}


    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

@endsection
