@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('portfolio');
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

@section('content')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <main id="works-page">

        <!-- HERO (title is dynamic, subtitle + stats are demo — no Laravel fields for these) -->
        <section class="works-hero" data-aos="fade">
            <div class="wrap works-hero-inner">
                <span class="eyebrow mono">{{ __('navbar.portfolios') }}</span>
                <h1>Real builds, <span>real numbers.</span></h1>
                <p>A running record of what we've shipped — live sites, apps, automations and the results each one produced
                    after
                    launch.</p>
                <div class="works-breadcrumb">
                    <ul>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                        <li>{{ __('navbar.portfolios') }}</li>
                    </ul>
                </div>
            </div>
            @if (count($counters) > 0)
                <div class="wrap works-hero-stats">
                    @foreach ($counters as $counter)
                        <div class="wstat" data-count="{{ (int) $counter->value }}"><b>{{ (int) $counter->value }}</b><span
                                class="mono">{{ $counter->title }}</span></div>
                    @endforeach
                </div>
            @endif
        </section>

        @php
            $section_portfolio = \App\Models\Section::section('portfolio');
        @endphp

        @if (count($portfolios) > 0 && isset($section_portfolio))

            <!-- FILTER (dynamic — $portfolio_categories) -->
            <div class="filter-bar">
                <div class="wrap filter-row" id="filterRow">
                    <button class="filter-btn active" data-filter="all">{{ __('common.all') }}</button>
                    @foreach ($portfolio_categories as $portfolio_category)
                        <button class="filter-btn"
                            data-filter="{{ $portfolio_category->slug }}">{{ $portfolio_category->title }}</button>
                    @endforeach
                    <span class="filter-count" id="filterCount"></span>
                </div>
            </div>

            <!-- FEATURED SPOTLIGHT — demo template, no matching Laravel section/data, left as-is -->
            {{-- <section class="spotlight-section">
        <div class="wrap">
          <div class="spotlight-card reveal">
            <div class="spotlight-visual">
              <div class="spotlight-browser">
                <div class="spotlight-bar">
                  <span class="spotlight-dot"></span><span class="spotlight-dot"></span><span class="spotlight-dot"></span>
                  <span class="spotlight-url">orendahome-demo.com</span>
                </div>
                <div class="spotlight-screen" style="background:linear-gradient(135deg,#95BF47,#1F9D6B);"><span>OH</span>
                </div>
              </div>
            </div>
            <div class="spotlight-body">
              <div class="spotlight-tag">
                <span class="spotlight-live"><span class="spotlight-dotpulse"></span>Live · Featured</span>
                <span class="mono" style="color:var(--faint);">Shopify · E-commerce</span>
              </div>
              <h3>Orenda Home</h3>
              <p>A full Shopify Plus rebuild for a D2C furniture retailer — faster browsing, clearer product pages, and a
                checkout that finally matched the brand's premium positioning.</p>
              <div class="spotlight-results">
                <div class="r"><b>+64%</b><span>Conversion</span></div>
                <div class="r"><b>-1.8s</b><span>Load time</span></div>
                <div class="r"><b>2.1×</b><span>Mobile orders</span></div>
              </div>
              <a href="#" class="btn btn-dark" style="width:fit-content;">View Case Study
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </section> --}}

            <!-- WORKS GRID (dynamic — $portfolios) -->
            <section class="works-grid-section">
                <div class="wrap">
                    {{-- <h2 class="mb-3" data-aos="fade-up">{{ $section_portfolio->title }}</h2> --}}
                    {{-- <div class="text description" data-aos="fade-up">{!! $section_portfolio->description !!}</div> --}}

                    <div class="works-grid mt-4" id="worksGrid">
                        @foreach ($portfolios as $portfolio)
                            <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="work-card reveal"
                                data-cat="all @foreach ($portfolio->categories as $category){{ $category->slug }} @endforeach">
                                <div class="wc-browser">
                                    <div class="wc-bar">
                                        <span class="wc-dot"></span><span class="wc-dot"></span><span class="wc-dot"></span>
                                        <span class="wc-url">{{ $portfolio->slug }}</span>
                                    </div>
                                    @if (!empty($portfolio->overview_image))
                                        <div class="wc-screen">
                                            <img src="{{ asset('uploads/overview_image/' . $portfolio->overview_image) }}"
                                                alt="{{ $portfolio->title }}">
                                        </div>
                                    @else
                                        <div class="wc-screen no-image"
                                            style="background:linear-gradient(135deg,#0f0e0d,#3a352f);">
                                            <span>{{ strtoupper(substr($portfolio->title, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="wc-info">
                                    <div class="wc-info-top">
                                        <h3>{{ $portfolio->title }}</h3>
                                        <span class="wc-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 17L17 7" />
                                                <path d="M7 7h10v10" />
                                            </svg></span>
                                    </div>
                                    <div class="wc-cat">
                                        @foreach ($portfolio->categories as $category)
                                            {{ $category->title }}{{ !$loop->last ? ' · ' : '' }}
                                        @endforeach
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="works-empty" id="worksEmpty">
                        {{ __('common.no_results') ?? 'No projects match this filter yet.' }}
                    </div>
                </div>
            </section>

            <!-- CTA — demo template, no matching Laravel data, left as-is -->
            {{-- <section class="works-cta">
        <div class="wrap reveal">
          <div class="works-cta-box">
            <span class="eyebrow">{{ $setting->title ?? config('app.name') }} • Start a project</span>
            <h2>Want to see your project on this page next?</h2>
            <p>Tell us what you're building and we'll show you the approach we'd take, based on the work above.</p>
            <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
              <a href="mailto:{{ $setting->email ?? '' }}" class="btn btn-light">Email the team</a>
              <a href="{{ route('home') }}#contact" class="btn btn-dark">Get a Free Consultation</a>
            </div>
          </div>
        </div>
      </section> --}}

        @endif

    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        (function() {
            AOS.init();

            var filterBtns = document.querySelectorAll("#works-page .filter-btn");
            var filterCount = document.getElementById("filterCount");
            var worksEmpty = document.getElementById("worksEmpty");

            function applyFilter(filter) {
                var visible = 0;
                document.querySelectorAll("#works-page .work-card").forEach(function(card) {
                    var cats = card.dataset.cat.trim().split(/\s+/);
                    var show = filter === "all" || cats.indexOf(filter) !== -1;
                    card.style.display = show ? "" : "none";
                    if (show) visible++;
                });
                if (filterCount) {
                    filterCount.textContent = visible + " project" + (visible === 1 ? "" : "s") + " shown";
                }
                if (worksEmpty) {
                    worksEmpty.classList.toggle("show", visible === 0);
                }
            }

            filterBtns.forEach(function(btn) {
                btn.addEventListener("click", function() {
                    filterBtns.forEach(function(b) {
                        b.classList.remove("active");
                    });
                    btn.classList.add("active");
                    applyFilter(btn.dataset.filter);
                });
            });

            if (filterBtns.length) {
                applyFilter("all");
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("in");
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: .1
            });
            document.querySelectorAll("#works-page .reveal:not(.in)").forEach(function(el, index) {
                el.style.transitionDelay = (index % 6 * 0.06) + "s";
                observer.observe(el);
            });
        })();
    </script>
@endsection
