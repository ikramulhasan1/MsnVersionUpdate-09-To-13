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
                        <h1>{{ $service->title }}</h1>
                        <p class="lead-msn">{!! $service->short_desc !!}</p>
                        <div class="msn-hero-ctas" style="margin-top: 20px">
                            {{-- <a href="#" class="btn-msn btn-msn-primary">Get a Free Quote →</a> --}}
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
                            @php
                                $page_quote = \App\Models\PageSetup::page('get-quote');
                            @endphp
                            {{-- <a href="#" class="btn-msn btn-msn-ghost">Book a Consultation</a> --}}
                            <button type="button" class="cta-cta-btn btn-msn btn-msn-primary"
                                onclick="document.getElementById('quotePopupModal').classList.add('is-open'); document.body.style.overflow='hidden';">
                                {{ $page_quote->title }} →
                            </button>
                        </div>

                    </div>
                    <div>
                        <div class="msn-hero-img-wrap msn-reveal">
                            <div class="msn-hero-img-glow"></div>

                            @if (!empty($service->image_path))
                                <div class="msn-hero-img-card">
                                    <img src="{{ asset('uploads/subservices/' . $service->image_path) }}"
                                        alt="WordPress website design service">
                                    <div class="msn-hero-img-shade"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ============ BADGES ============ -->
        <section class="msn-badges msn-section" style="padding:56px 0 64px;">
            <div class="msn-container">
                <div class="msn-badges-row">
                    @foreach ($industries as $item)
                        <div class="msn-badge">
                            <div class="ic">
                                <i class="{{ $item['icon_class'] ?? '' }}"></i>
                            </div>
                            <div><strong>{{ $item['title'] ?? '' }}</strong><span>{{ $item['description'] ?? '' }}</span>
                            </div>
                        </div>
                    @endforeach

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
                                <i class="{{ $item['icon_class'] ?? '' }}"></i>
                            </div>
                            <strong>{{ $item['title'] ?? '' }}</strong>
                            <span>{{ $item['bottom_text'] ?? '' }}</span>
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
                            <div class="ic"><i class="{{ $item['icon'] ?? '' }}"></i></div>
                            <strong>{{ $item['title'] ?? '' }}</strong>
                            <span>{{ $item['bottom_text'] ?? '' }}</span>
                        </div>
                    @endforeach

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
                                <div class="ic-badge"> <i class="{{ $item['icon'] ?? '' }}"></i> </div>
                                <span class="tag-line">{{ $item['title'] ?? '' }}</span>
                            </div>
                            <h3>{{ $item['designation'] ?? '' }}</h3>
                            <p>{{ $item['meassage'] ?? '' }}</p>
                        </div>
                    @endforeach

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
                                <i class="{{ $item['icon_class'] ?? '' }}"></i>
                            </div>
                            <strong>{{ $item['title'] ?? '' }}</strong>
                            <span>{{ $item['bottom_text'] ?? '' }}</span>
                        </div>
                    @endforeach

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
                            <div><strong>{{ $item['title'] ?? '' }}</strong><span>{{ $item['bottom_text'] ?? '' }}</span>
                            </div>
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
                        <div class="msn-ind-chip"><span class="dot"></span>{{ $item['bottom_text'] ?? '' }}</div>
                    @endforeach

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
                            <p class="msg">{{ $item['meassage'] ?? '' }}</p>
                            <div class="who">
                                {{-- <div class="avatar">JD</div> --}}
                                <div><b>{{ $item['title'] ?? '' }}</b><small>{{ $item['designation'] ?? '' }}</small>
                                    <div class="stars">{{ $item['rating'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- ============ Achievements STATS ============ -->
        <section class="msn-section">
            <div class="msn-container">
                <div class="msn-stats-grid msn-reveal">
                    @foreach ($achievements as $item)
                        <div class="msn-stat"><b
                                data-count="{{ $item['count_number'] ?? '' }}">0</b><span>{{ $item['title'] ?? '' }}</span>
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
                                    <h4>{{ $item['question'] ?? '' }}</h4><span class="plus"></span>
                                </button>
                                <div class="msn-faq-a">
                                    <p>{!! $item['answer'] ?? '' !!}</p>
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
