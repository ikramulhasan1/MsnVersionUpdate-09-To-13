@extends('web.layouts.master')
@php
    $header = \App\Models\PageSetup::page('related-service');
@endphp
@if (isset($header))

    @section('title', content: $service->title)

    @section('top_meta_tags')
        @if (isset($service->short_desc))
            <meta name="description" content="{!! str_limit(strip_tags($service->short_desc), 200, ' ...') !!}">
        @else
            <meta name="description" content="{!! str_limit(strip_tags($service->short_desc), 200, ' ...') !!}">
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
        <meta property='og:title' content="{{ $service->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($service->short_desc), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('service.related-single', $service->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/service/' . $service->image_path) }}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@MSNSOFTTECH" />
        <meta name="twitter:url" content="{{ route('service.related-single', $service->slug) }}" />
        <meta name="twitter:title" content="{{ $service->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($service->short_desc), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/service/' . $service->image_path) }}" />
    @endif
@endsection

{{-- schema section --}}

@section('content')

    <!-- HERO -->
    @php
        $banners = json_decode($service->banner_steps ?? '[]', true);
        $features = json_decode($service->features_steps ?? '[]', true);
        $process = json_decode($service->process_steps ?? '[]', true);
        $why_we = json_decode($service->why_we_steps ?? '[]', true);
        $industries = json_decode($service->industries_steps ?? '[]', true);
        $achievements = json_decode($service->achievements_steps ?? '[]', true);
        $success_stories = json_decode($service->success_stories_steps ?? '[]', true);
        $clients_say = json_decode($service->clients_say_steps ?? '[]', true);
        $guaranteeSteps = json_decode($service->guaranteeSteps ?? '[]', true);
        $how_we_work = json_decode($service->how_we_work ?? '[]', true);
        $faq = json_decode($service->faq_steps ?? '[]', true);
        $our_promise = json_decode($service->our_promise ?? '[]', true);
        $cta = json_decode($service->cta_steps ?? '[]', true);
    @endphp

    <div class="msn-page">

        <!-- ============ HERO ============ -->
        <section class="msn-hero">
            <div class="msn-hero-blob b1"></div>
            <div class="msn-hero-blob b2"></div>
            <div class="msn-container">
                <div class="msn-hero-grid">
                    <div>
                        {{-- <div class="msn-hero-badge-top">
                                <div class="avatars"><span></span><span></span><span></span></div>
                                <span class="stars-mini">★★★★★</span> 4.9/5 from 900+ clients
                            </div> --}}
                        {{-- <span class="eyebrow">WordPress Website Development</span> --}}

                        <h1>{{ $service->title }}</h1>
                        <p class="lead-msn">{!! $service->short_desc !!}</p>
                        <div class="msn-hero-ctas">
                            <a href="#" class="btn-msn btn-msn-primary">Get a Free Quote →</a>
                            <a href="#" class="btn-msn btn-msn-ghost">Book a Consultation</a>
                        </div>
                        {{-- <div class="msn-hero-trust">
                                <div class="item">
                                    <div><strong>900+</strong><span>Projects Delivered</span></div>
                                </div>
                                <div class="item">
                                    <div><strong>13+ yrs</strong><span>In Business</span></div>
                                </div>
                                <div class="item">
                                    <div><strong>98%</strong><span>Client Satisfaction</span></div>
                                </div>
                            </div> --}}
                    </div>
                    <div>
                        <div class="msn-hero-img-wrap msn-reveal">
                            <div class="msn-hero-img-glow"></div>
                            {{-- <div class="msn-float-chip c1">
                                    <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" />
                                        </svg></div>
                                    <div><b>98/100</b><span>Speed Score</span></div>
                                </div> --}}
                            {{-- <div class="msn-float-chip c2">
                                    <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" />
                                        </svg></div>
                                    <div><b>SSL Secured</b><span>Fully protected</span></div>
                                </div> --}}
                            @if (!empty($service->image_path))
                                <div class="msn-hero-img-card">
                                    <img src="{{ asset('uploads/subservices/' . $service->image_path) }}"
                                        alt="WordPress website design service">
                                    <div class="msn-hero-img-shade"></div>
                                    {{-- <div class="msn-hero-img-badge">
                                                <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                        stroke="#fff" stroke-width="3">
                                                        <path d="M20 6 9 17l-5-5" />
                                                    </svg></div>
                                                <div><b>Live in 3–5 Weeks</b><span>Avg. project turnaround</span></div>
                                            </div> --}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- <svg class="msn-wave" viewBox="0 0 1440 46" preserveAspectRatio="none">
                <path d="M0,46 C360,0 1080,0 1440,46 L1440,46 L0,46 Z" fill="#FCEAE7" />
            </svg> --}}
        <!-- ============ BADGES ============ -->
        <section class="msn-badges msn-section" style="padding:56px 0 64px;">
            <div class="msn-container">
                <div class="msn-badges-row">
                    @foreach ($industries as $item)
                        <div class="msn-badge">
                            <div class="ic">
                                <i class="{{ $item['icon_class'] }}"></i>
                            </div>
                            <div><strong>{{ $item['title'] ?? '' }}</strong><span>{{ $item['description'] ?? '' }}</span>
                            </div>
                        </div>
                    @endforeach
                    {{-- <div class="msn-badge">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M3 17l6-6 4 4 8-8" />
                                <path d="M17 7h4v4" />
                            </svg></div>
                        <div><strong>Built to Scale</strong><span>Grows with your business</span></div>
                    </div>
                    <div class="msn-badge">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" />
                            </svg></div>
                        <div><strong>Secure &amp; Reliable</strong><span>Protected at every layer</span></div>
                    </div>
                    <div class="msn-badge">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M9 12l2 2 4-4" />
                            </svg></div>
                        <div><strong>Easy to Manage</strong><span>Simple client dashboard</span></div>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ WHO IS THIS SERVICE FOR ============ -->
        <section class="msn-audience msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Who Is This For</span>
                    <h2>Built for teams who need a website that actually performs</h2>
                    <p>Whether you're launching your first site or replacing one that's holding you back, this service fits
                        businesses at every stage.</p>
                </div>
                <div class="msn-audience-grid">
                    @foreach ($why_we as $item)
                        <div class="msn-audience-card msn-reveal">
                            <div class="ic">
                                <i class="{{ $item['icon_class'] }}"></i>
                            </div>
                            <strong>{{ $item['title'] }}</strong>
                            <span>{{ $item['bottom_text'] }}</span>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- ============ SERVICES INCLUDED ============ -->
        <section class="msn-included msn-section" style="background:var(--red-soft);">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">What's Included</span>
                    <h2>Everything needed to go from idea to a live website</h2>
                    <p>No hidden extras — every WordPress build includes the following as standard.</p>
                </div>
                <div class="msn-included-grid">
                    @foreach ($success_stories as $item)
                        <div class="msn-included-item msn-reveal">
                            <div class="ic"><i class="{{ $item['icon'] }}"></i></div>
                            <strong>{{ $item['title'] }}</strong>
                            <span>{{ $item['bottom_text'] }}</span>
                        </div>
                    @endforeach
                    {{-- <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="M2 9h20" />
                            </svg></div>
                        <strong>Responsive Development</strong>
                        <span>Every page built and tested across mobile, tablet and desktop.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m21 21-4.3-4.3" />
                            </svg></div>
                        <strong>On-Page SEO Setup</strong>
                        <span>Clean URLs, meta tags, schema markup and sitemap configuration.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" />
                            </svg></div>
                        <strong>Speed Optimization</strong>
                        <span>Image compression, caching and code minification for fast load times.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" />
                            </svg></div>
                        <strong>Security Hardening</strong>
                        <span>SSL setup, firewall rules and login protection configured at launch.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 8a13 13 0 01-8.5 12A13 13 0 014 8V4l8.5-2L21 4z" />
                            </svg></div>
                        <strong>Content Migration</strong>
                        <span>Existing pages, images and copy carried over cleanly to the new build.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16M4 4v10a8 8 0 0016 0V4" />
                            </svg></div>
                        <strong>Plugin &amp; Integration Setup</strong>
                        <span>Forms, analytics, booking or e-commerce plugins installed and configured.</span>
                    </div>
                    <div class="msn-included-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 3" />
                            </svg></div>
                        <strong>Training &amp; Handover</strong>
                        <span>A walkthrough video and admin guide so your team can manage it independently.</span>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ STACK ============ -->
        <section class="msn-stack msn-section">
            <div class="msn-container">
                <div class="msn-stack-head">
                    <span class="eyebrow" style="justify-content:center;">The Stack</span>
                    <h2 style="font-size:clamp(24px,3vw,34px); margin-top:14px; font-weight:800;">Built on tools that scale
                        with
                        you</h2>
                </div>
            </div>
            <div class="msn-marquee">
                <div class="msn-marquee-track" id="msnMarquee"></div>
            </div>
        </section>

        <!-- ============ PROCESS ============ -->
        <section class="msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">How We Work</span>
                    <h2>Six stages, one clear plan</h2>
                    <p>Every project moves through the same disciplined pipeline — nothing skipped, nothing rushed.</p>
                </div>
                <div class="msn-proc-grid">
                    @foreach ($how_we_work as $key => $item)
                        <div class="msn-proc-card msn-reveal">
                            <span class="ghost-num">{{ $key }}+1</span>
                            <div class="top-row">
                                <div class="ic-badge"> <i class="{{ $item['icon'] }}"></i> </div>
                                <span class="tag-line">{{ $item['title'] }}</span>
                            </div>
                            <h3>{{ $item['designation'] }}</h3>
                            <p>{{ $item['meassage'] }}</p>
                        </div>
                    @endforeach
                    {{-- <div class="msn-proc-card msn-reveal">
                        <span class="ghost-num">02</span>
                        <div class="top-row">
                            <div class="ic-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M3 15V6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg></div><span class="tag-line">Blueprint</span>
                        </div>
                        <h3>Planning &amp; Sitemap</h3>
                        <p>Page structure, content plan and tech stack get locked in before a single pixel is placed.</p>
                    </div>
                    <div class="msn-proc-card msn-reveal">
                        <span class="ghost-num">03</span>
                        <div class="top-row">
                            <div class="ic-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z" />
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z" />
                                </svg></div><span class="tag-line">Visual</span>
                        </div>
                        <h3>Design</h3>
                        <p>Custom layouts in your brand voice — reviewed and refined with you before development starts.</p>
                    </div>
                    <div class="msn-proc-card msn-reveal">
                        <span class="ghost-num">04</span>
                        <div class="top-row">
                            <div class="ic-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="m18 16 4-4-4-4M6 8l-4 4 4 4M14.5 4l-5 16" />
                                </svg></div><span class="tag-line">Build</span>
                        </div>
                        <h3>Development</h3>
                        <p>Clean, documented WordPress code — theme setup, plugins, and any custom functionality.</p>
                    </div>
                    <div class="msn-proc-card msn-reveal">
                        <span class="ghost-num">05</span>
                        <div class="top-row">
                            <div class="ic-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M9 12l2 2 4-4" />
                                </svg></div><span class="tag-line">QA</span>
                        </div>
                        <h3>Testing</h3>
                        <p>Cross-browser, cross-device and speed testing, plus a full security sweep before launch.</p>
                    </div>
                    <div class="msn-proc-card msn-reveal">
                        <span class="ghost-num">06</span>
                        <div class="top-row">
                            <div class="ic-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" />
                                </svg></div><span class="tag-line">Go live</span>
                        </div>
                        <h3>Launch &amp; Support</h3>
                        <p>We publish the site and stay on for 30 days of support to catch anything that comes up.</p>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ CORE FEATURES ============ -->
        <section class="msn-features msn-section" style="background:var(--red-soft);">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Core Features</span>
                    <h2>What makes every build stand out</h2>
                    <p>These aren't add-ons — they're the foundation of how we build every WordPress site.</p>
                </div>
                <div class="msn-features-grid">
                    @foreach ($features as $item)
                        <div class="msn-feature-card msn-reveal">
                            <div class="ic">
                                <i class="{{ $item['icon_class'] }}"></i>
                            </div>
                            <strong>{{ $item['title'] }}</strong>
                            <span>{{ $item['bottom_text'] }}</span>
                        </div>
                    @endforeach
                    {{-- <div class="msn-feature-card msn-reveal">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" />
                            </svg></div>
                        <strong>Lightning-Fast Performance</strong>
                        <span>Optimized code and assets keep load times low, even on slower connections.</span>
                    </div>
                    <div class="msn-feature-card msn-reveal">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m21 21-4.3-4.3" />
                            </svg></div>
                        <strong>SEO-Ready Structure</strong>
                        <span>Search-friendly markup and site architecture built in from the first page.</span>
                    </div>
                    <div class="msn-feature-card msn-reveal">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" />
                            </svg></div>
                        <strong>Secure Architecture</strong>
                        <span>Hardened login, regular updates and firewall rules to keep threats out.</span>
                    </div>
                    <div class="msn-feature-card msn-reveal">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M9 12l2 2 4-4" />
                            </svg></div>
                        <strong>Easy Content Management</strong>
                        <span>A clean WordPress admin so your team can update pages without touching code.</span>
                    </div>
                    <div class="msn-feature-card msn-reveal">
                        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="m18 16 4-4-4-4M6 8l-4 4 4 4M14.5 4l-5 16" />
                            </svg></div>
                        <strong>Scalable &amp; Future-Proof</strong>
                        <span>Built to handle new pages, plugins and traffic growth without a rebuild.</span>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ PORTFOLIO ============ -->
        @if (!empty($service->portfolios) && count($service->portfolios) > 0)
            <section class="msn-port msn-section">
                <div class="msn-container">
                    <div class="msn-between msn-reveal">
                        <div class="msn-section-head">
                            <span class="eyebrow">Recent work</span>
                            <h2>Sites we've shipped recently</h2>
                        </div>
                        <a href="{{ route('portfolios') }}" class="btn-msn btn-msn-ghost"
                            style="border-color:rgba(255,255,255,.5); color:#fff; background:transparent;">View All
                            Projects</a>
                    </div>
                    {{-- <div class="msn-port-filters msn-reveal" id="msnPortFilters">
                        <span class="msn-port-filter active" data-f="all">All</span>
                        <span class="msn-port-filter" data-f="healthcare">Healthcare</span>
                        <span class="msn-port-filter" data-f="ecommerce">E-Commerce</span>
                        <span class="msn-port-filter" data-f="realestate">Real Estate</span>
                        <span class="msn-port-filter" data-f="education">Education</span>
                        <span class="msn-port-filter" data-f="hospitality">Hospitality</span>
                    </div> --}}
                    <div class="msn-port-grid2" id="msnPortGrid">
                        @foreach ($service->portfolios as $portfolio)
                            <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="text-decoration-none ">
                                <div class="msn-port-card2 msn-reveal" data-cat="healthcare">
                                    <div class="msn-port-chrome"><span></span><span></span><span></span></div>
                                    <div class="msn-port-shot"
                                        style="background:linear-gradient(135deg,#2C355E,#E23A2E);">
                                        <img src="{{ asset('uploads/overview_image/' . $portfolio->overview_image) }}"
                                            class="img-fluid" alt="{{ $portfolio->title }}">
                                    </div>
                                    <div class="msn-port-body">
                                        {{-- <span class="cat">Healthcare</span> --}}
                                        <b>{{ $portfolio->title }}</b><span class="msn-port-view">View Project →</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                    </div>
                </div>
            </section>
        @endif
        <!-- ============ COMPARE ============ -->
        <section class="msn-compare msn-section" style="background:var(--red-soft);">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Why MSN Softtech</span>
                    <h2>What you get that a typical freelancer won't offer</h2>
                </div>
                <div class="msn-compare-cards msn-reveal">
                    <div class="msn-cc muted">
                        <div class="msn-cc-head">
                            <h3>Typical Freelancer</h3><span class="msn-cc-badge">Basic</span>
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Custom Strategy &amp; Design — templates only
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Dedicated Project Team — one person</div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Performance Optimization — rarely tested
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Security &amp; Data Protection — not managed
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Training &amp; Documentation — limited</div>
                        <div class="msn-cc-row"><span class="dot-ic">✗</span>Ongoing Support — none</div>
                    </div>
                    <div class="msn-cc feat">
                        <div class="msn-cc-head">
                            <h3>MSN Softtech</h3><span class="msn-cc-badge">Recommended</span>
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Custom Strategy &amp; Design — built for you
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Dedicated Project Team — full team on call
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Performance Optimization — benchmarked &amp;
                            tuned
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Security &amp; Data Protection — actively
                            managed</div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Training &amp; Documentation — video guide
                            included
                        </div>
                        <div class="msn-cc-row"><span class="dot-ic">✓</span>Ongoing Support — 30 days free</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ DELIVERABLES ============ -->
        <section class="msn-deliverables msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Deliverables</span>
                    <h2>Exactly what you'll walk away with</h2>
                    <p>A clear, itemized handover — no ambiguity about what's yours at the end of the project.</p>
                </div>
                <div class="msn-deliverables-grid">
                    @foreach ($process as $key => $item)
                        <div class="msn-deliverable-row msn-reveal">
                            <span class="num">0{{ $key + 1 }}</span>
                            <div><strong>{{ $item['title'] }}</strong><span>{{ $item['bottom_text'] }}</span></div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- ============ GUARANTEE ============ -->
        <section class="msn-guarantee msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Our Guarantee</span>
                    <h2>You're covered, start to finish</h2>
                    <p>Straightforward terms — no fine print, no surprise invoices.</p>
                </div>
                <div class="msn-g-grid">
                    @foreach ($success_stories as $item)
                        <div class="msn-included-item msn-reveal">
                            <div class="ic">
                                <i class="{{ $item['icon'] ?? '' }}"></i>
                            </div>

                            <strong>{{ $item['title'] ?? '' }}</strong>

                            <span>{{ $item['bottom_text'] ?? '' }}</span>
                        </div>
                    @endforeach
                    {{-- <div class="msn-g-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16M4 4v10a8 8 0 0016 0V4" />
                            </svg></div><strong>Quality Assured</strong><span>Every deliverable reviewed before
                            handoff.</span>
                    </div>
                    <div class="msn-g-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M3 12h4l3 8 4-16 3 8h4" />
                            </svg></div><strong>Transparent Pricing</strong><span>One quote, no hidden line items.</span>
                    </div>
                    <div class="msn-g-item msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <path d="M8 2v4M16 2v4M3 10h18" />
                            </svg></div><strong>On-Time Delivery</strong><span>Milestones you can plan around.</span>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ INDUSTRIES ============ -->
        <section class="msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Industries</span>
                    <h2>Sites we build across sectors</h2>
                </div>
                <div class="msn-ind-wrap msn-reveal">
                    @foreach ($our_promise as $item)
                        <div class="msn-ind-chip"><span class="dot"></span>{{ $item['bottom_text'] }}</div>
                    @endforeach
                    {{-- <div class="msn-ind-chip"><span class="dot"></span>Education</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Real Estate</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Travel &amp; Hospitality</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Retail &amp; E-commerce</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Legal Services</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Non-Profit</div>
                    <div class="msn-ind-chip"><span class="dot"></span>Finance</div> --}}
                </div>
            </div>
        </section>

        <!-- ============ TESTIMONIALS ============ -->
        <section class="msn-test msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Client Voices</span>
                    <h2>What clients say after launch</h2>
                </div>
                <div class="msn-test-track msn-reveal">
                    @foreach ($clients_say as $item)
                        <div class="msn-test-card">
                            <div class="quote">"</div>
                            <p class="msg">{{ $item['meassage'] }}</p>
                            <div class="who">
                                <div class="avatar">JD</div>
                                <div><b>{{ $item['title'] }}</b><small>{{ $item['designation'] }}</small>
                                    <div class="stars">{{ $item['rating'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{-- <div class="msn-test-card">
                        <div class="quote">"</div>
                        <p class="msg">Very responsive and easy to communicate with — always felt like we were their
                            only
                            client,
                            even during rush deadlines.</p>
                        <div class="who">
                            <div class="avatar">SH</div>
                            <div><b>Sarah H.</b><small>Retail</small>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    <div class="msn-test-card">
                        <div class="quote">"</div>
                        <p class="msg">Our new site loads instantly and the admin panel is so easy that our whole team
                            can
                            update it
                            without calling for help.</p>
                        <div class="who">
                            <div class="avatar">MA</div>
                            <div><b>Marcus A.</b><small>Education</small>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    <div class="msn-test-card">
                        <div class="quote">"</div>
                        <p class="msg">Traffic and leads both jumped within the first quarter after launch — the SEO
                            groundwork made
                            a real difference.</p>
                        <div class="who">
                            <div class="avatar">RK</div>
                            <div><b>Renee K.</b><small>Real Estate</small>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </section>

        <!-- ============ Achievements STATS ============ -->
        <section class="msn-section">
            <div class="msn-container">
                <div class="msn-stats-grid msn-reveal">
                    @foreach ($achievements as $item)
                        <div class="msn-stat"><b
                                data-count="{{ $item['count_number'] }}">0</b><span>{{ $item['title'] }}</span>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- ============ FAQ ============ -->
        @if (!empty($faq))
            <section class="msn-section" style="background:var(--red-soft);">
                <div class="msn-container" style="max-width:820px;">
                    <div class="msn-section-head msn-reveal" style="margin:0 auto 30px;">
                        <span class="eyebrow">FAQ</span>
                        <h2>Common questions</h2>
                    </div>
                    <div class="msn-faq msn-reveal">
                        @foreach ($faq as $key => $item)
                            <div class="msn-faq-item  {{ $key == 0 ? 'open' : '' }}">
                                <button class="msn-faq-q">
                                    <h4>{{ $item['question'] }}</h4><span class="plus"></span>
                                </button>
                                <div class="msn-faq-a">
                                    <p>{!! $item['answer'] !!}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!-- ============ RELATED SERVICES ============ -->
        <section class="msn-related msn-section">
            <div class="msn-container">
                <div class="msn-section-head msn-reveal">
                    <span class="eyebrow">Related Services</span>
                    <h2>Pair this with our other services</h2>
                    <p>Often ordered alongside your WordPress build to get more out of your new site.</p>
                </div>
                <div class="msn-related-grid">
                    <a href="#" class="msn-related-card msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1.5" />
                                <circle cx="19" cy="21" r="1.5" />
                                <path d="M2.5 3h3l3 12.5h9.5l3-8H6.2" />
                            </svg></div>
                        <strong>WooCommerce Development</strong>
                        <span class="desc">Full online store setup with payments, shipping and inventory.</span>
                        <span class="go">Learn more →</span>
                    </a>
                    <a href="#" class="msn-related-card msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m21 21-4.3-4.3" />
                            </svg></div>
                        <strong>SEO Optimization</strong>
                        <span class="desc">Ongoing keyword, content and technical SEO to grow organic traffic.</span>
                        <span class="go">Learn more →</span>
                    </a>
                    <a href="#" class="msn-related-card msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 3" />
                            </svg></div>
                        <strong>Website Maintenance</strong>
                        <span class="desc">Monthly updates, backups and monitoring so your site stays healthy.</span>
                        <span class="go">Learn more →</span>
                    </a>
                    <a href="#" class="msn-related-card msn-reveal">
                        <div class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 19l7-7 3 3-7 7-3-3z" />
                                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z" />
                            </svg></div>
                        <strong>UI/UX Design</strong>
                        <span class="desc">Custom branding and interface design before development begins.</span>
                        <span class="go">Learn more →</span>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function() {
            var els = document.querySelectorAll('.msn-reveal');
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('in');
                        io.unobserve(e.target);
                    }
                });
            }, {
                threshold: .15
            });
            els.forEach(function(el) {
                io.observe(el);
            });

            // dashboard stat counters
            function animateStat(el, target, suffix) {
                var cur = 0;
                var step = Math.max(1, Math.round(target / 30));
                var iv = setInterval(function() {
                    cur += step;
                    if (cur >= target) {
                        cur = target;
                        clearInterval(iv);
                    }
                    el.textContent = cur + suffix;
                }, 35);
            }
            // portfolio filters
            var filters = document.querySelectorAll('.msn-port-filter');
            var portCards = document.querySelectorAll('.msn-port-card2');
            filters.forEach(function(f) {
                f.addEventListener('click', function() {
                    filters.forEach(function(o) {
                        o.classList.remove('active');
                    });
                    f.classList.add('active');
                    var cat = f.getAttribute('data-f');
                    portCards.forEach(function(c) {
                        c.style.display = (cat === 'all' || c.getAttribute('data-cat') ===
                            cat) ? '' : 'none';
                    });
                });
            });
            // Marquee content
            var techs = @json($service->technologies->pluck('short_title')->values());

            var track = document.getElementById('msnMarquee');
            var html = '';

            for (var r = 0; r < 2; r++) {
                techs.forEach(function(t) {
                    html += '<div class="msn-tech-pill"><span class="dot"></span>' + t + '</div>';
                });
            }

            track.innerHTML = html;

            // faq accordion
            document.querySelectorAll('.msn-faq-item').forEach(function(item) {
                var q = item.querySelector('.msn-faq-q');
                var a = item.querySelector('.msn-faq-a');
                if (item.classList.contains('open')) {
                    a.style.maxHeight = a.scrollHeight + 'px';
                }
                q.addEventListener('click', function() {
                    var isOpen = item.classList.contains('open');
                    document.querySelectorAll('.msn-faq-item').forEach(function(other) {
                        other.classList.remove('open');
                        other.querySelector('.msn-faq-a').style.maxHeight = 0;
                    });
                    if (!isOpen) {
                        item.classList.add('open');
                        a.style.maxHeight = a.scrollHeight + 'px';
                    }
                });
            });

            // stats counter
            var counted = false;
            var statSection = document.querySelector('.msn-stats-grid');
            var io2 = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting && !counted) {
                        counted = true;
                        document.querySelectorAll('.msn-stat b[data-count]').forEach(function(b) {
                            var target = parseInt(b.getAttribute('data-count'), 10);
                            var cur = 0;
                            var step = Math.max(1, Math.round(target / 40));
                            var iv = setInterval(function() {
                                cur += step;
                                if (cur >= target) {
                                    cur = target;
                                    clearInterval(iv);
                                }
                                b.textContent = cur;
                            }, 30);
                        });
                    }
                });
            }, {
                threshold: .4
            });
            if (statSection) {
                io2.observe(statSection);
            }
        })();
    </script>
@endsection
