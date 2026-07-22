@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('faqs');
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* ===================================================
                   FAQ PAGE — premium redesign
                   Tokens
                =================================================== */
        .faq-page {
            --navy: #052c58;
            --navy-deep: #031b38;
            --ink: #0a0e14;
            --red: #e42328;
            --red-deep: #b91419;
            --bg-soft: #f6f8fb;
            --text: #1c2430;
            --muted: #667085;
            --border: #e6e9ef;
            --font-head: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        .faq-page,
        .faq-page * {
            box-sizing: border-box;
            font-family: var(--font-body);
        }

        .faq-page h1,
        .faq-page h2,
        .faq-page h3,
        .faq-page h4 {
            font-family: var(--font-head);
        }

        /* ---------- Hero ---------- */
        .faq-hero {
            position: relative;
            background:
                radial-gradient(60% 90% at 85% 0%, rgba(228, 35, 40, 0.18), transparent 60%),
                linear-gradient(180deg, var(--navy) 0%, var(--navy-deep) 100%);
            padding: 108px 0 78px;
            overflow: hidden;
            isolation: isolate;
        }

        .faq-hero::before {
            content: "?";
            position: absolute;
            right: -40px;
            top: -70px;
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 340px;
            line-height: 1;
            color: rgba(255, 255, 255, 0.035);
            z-index: -1;
            pointer-events: none;
        }

        .faq-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #ffd7d8;
            background: rgba(228, 35, 40, 0.16);
            border: 1px solid rgba(228, 35, 40, 0.35);
            padding: 7px 16px;
            border-radius: 999px;
        }

        .faq-hero-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--red);
            display: inline-block;
        }

        .faq-hero h1 {
            color: #fff;
            font-weight: 800;
            font-size: clamp(34px, 5vw, 54px);
            margin: 20px 0 14px;
            letter-spacing: -0.02em;
        }

        .faq-hero-sub {
            color: rgba(255, 255, 255, 0.72);
            font-size: 17px;
            max-width: 560px;
            margin: 0 0 26px;
            line-height: 1.6;
        }

        .faq-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .faq-breadcrumb a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color .2s ease;
        }

        .faq-breadcrumb a:hover {
            color: #fff;
        }

        .faq-breadcrumb span.sep {
            color: rgba(255, 255, 255, 0.35);
        }

        .faq-breadcrumb span.current {
            color: var(--red);
        }

        /* ---------- Section title (CMS block) ---------- */
        .faq-section-title {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 52px;
        }

        .faq-section-title h2 {
            font-weight: 800;
            font-size: clamp(26px, 3vw, 36px);
            color: var(--text);
            margin-bottom: 14px;
        }

        .faq-section-title .description {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        /* ---------- Main layout ---------- */
        .faq-main {
            background: var(--bg-soft);
            padding: 70px 0 100px;
        }

        /* Sidebar */
        .faq-sidebar {
            position: sticky;
            top: 24px;
        }

        .faq-search {
            position: relative;
            margin-bottom: 26px;
        }

        .faq-search input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
            font-size: 14.5px;
            color: var(--text);
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .faq-search input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 4px rgba(228, 35, 40, 0.1);
        }

        .faq-search svg {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--muted);
        }

        .faq-sidebar-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .faq-cat-list {
            list-style: none;
            margin: 0 0 28px;
            padding: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .faq-cat-list li+li {
            border-top: 1px solid var(--border);
        }

        .faq-cat-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 15px 18px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background .2s ease, color .2s ease, border-color .2s ease, padding-left .2s ease;
        }

        .faq-cat-list a .arrow {
            width: 15px;
            height: 15px;
            flex: none;
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity .2s ease, transform .2s ease;
        }

        .faq-cat-list li:hover a {
            background: #fff6f6;
            color: var(--red-deep);
            padding-left: 22px;
        }

        .faq-cat-list li:hover a .arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .faq-cat-list li.active a {
            background: #fff1f1;
            color: var(--red-deep);
            border-left-color: var(--red);
            font-weight: 700;
        }

        .faq-cat-list li.active a .arrow {
            opacity: 1;
            transform: translateX(0);
            color: var(--red);
        }

        .faq-help-card {
            background: linear-gradient(155deg, var(--navy) 0%, var(--navy-deep) 100%);
            border-radius: 16px;
            padding: 26px 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .faq-help-card::after {
            content: "";
            position: absolute;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(228, 35, 40, 0.35), transparent 70%);
            right: -40px;
            bottom: -50px;
        }

        .faq-help-card h4 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .faq-help-card p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .faq-help-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--red);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 11px 18px;
            border-radius: 10px;
            text-decoration: none;
            position: relative;
            z-index: 1;
            transition: background .2s ease, transform .2s ease;
        }

        .faq-help-btn:hover {
            background: var(--red-deep);
            transform: translateY(-2px);
            color: #fff;
        }

        /* Accordion */
        .faq-accordion {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .faq-item {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 3px solid transparent;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.03);
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .faq-item.active {
            border-left-color: var(--red);
            box-shadow: 0 12px 28px -14px rgba(5, 44, 88, 0.25);
        }

        .faq-item-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            background: none;
            border: none;
            text-align: left;
            padding: 20px 22px;
            cursor: pointer;
        }

        .faq-q {
            font-size: 16.5px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.5;
        }

        .faq-item.active .faq-q {
            color: var(--navy);
        }

        .faq-icon {
            position: relative;
            flex: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--bg-soft);
            transition: background .25s ease;
        }

        .faq-item.active .faq-icon {
            background: var(--red);
        }

        .faq-icon .line {
            position: absolute;
            background: var(--navy);
            border-radius: 2px;
            top: 50%;
            left: 50%;
            transition: transform .25s ease, background .25s ease;
        }

        .faq-icon .line1 {
            width: 12px;
            height: 2px;
            transform: translate(-50%, -50%);
        }

        .faq-icon .line2 {
            width: 2px;
            height: 12px;
            transform: translate(-50%, -50%);
        }

        .faq-item.active .faq-icon .line {
            background: #fff;
        }

        .faq-item.active .faq-icon .line2 {
            transform: translate(-50%, -50%) rotate(90deg) scaleY(0);
        }

        .faq-item-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
        }

        .faq-item-inner {
            padding: 0 22px 22px 22px;
            color: var(--muted);
            font-size: 15.5px;
            line-height: 1.75;
        }

        .faq-no-results {
            text-align: center;
            color: var(--muted);
            background: #fff;
            border: 1px dashed var(--border);
            border-radius: 14px;
            padding: 30px;
            font-size: 15px;
        }

        @media (max-width: 991.98px) {
            .faq-sidebar {
                position: static;
                margin-bottom: 36px;
            }
        }

        @media (max-width: 575.98px) {
            .faq-hero {
                padding: 88px 0 60px;
            }

            .faq-item-btn {
                padding: 17px 16px;
                gap: 12px;
            }

            .faq-q {
                font-size: 15.5px;
            }

            .faq-item-inner {
                padding: 0 16px 18px 16px;
                font-size: 14.5px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .faq-item-body,
            .faq-icon .line,
            .faq-cat-list a,
            .faq-help-btn {
                transition: none !important;
            }
        }

        /* ---------- Rich-text CMS content (categories / answers) ---------- */
        .description>ul>li,
        .description>ol>li {
            margin-left: 22px !important;
            font-size: inherit !important;
        }

        .description>ul>li {
            list-style: initial;
        }

        .description>ol>li {
            all: revert;
            margin-left: 22px !important;
        }

        .description>ul>li>ul>li,
        .description>ol>li>ol>li,
        .description>ol>li>ul>li,
        .description>ul>li>ol>li {
            margin-left: 15px !important;
        }

        .description p {
            margin-bottom: 10px;
        }

        .description a {
            color: var(--red-deep);
            font-weight: 600;
            text-decoration: underline;
        }
    </style>

    <!-- ============== Hero ============== -->
    <section class="faq-page faq-hero" data-aos="fade">
        <div class="container">
            <span class="faq-hero-badge"><span class="dot"></span> Support Center</span>
            <h1>{{ __('navbar.faqs') }}</h1>
            <p class="faq-hero-sub">Quick, straight answers about how we work, what we deliver, and what it's like to
                build with MSN SoftTech.</p>
            <div class="faq-breadcrumb">
                <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
                <span class="sep">/</span>
                <span class="current">{{ __('navbar.faqs') }}</span>
            </div>
        </div>
    </section>

    @php
        $section_faqs = \App\Models\Section::section('faqs');
    @endphp
    @if (isset($section_faqs))
        <!-- ============== FAQs ============== -->
        <section class="faq-page faq-main">
            <div class="container">

                <div class="faq-section-title">
                    <h2>{{ $section_faqs->title }}</h2>
                    <div class="description">{!! $section_faqs->description !!}</div>
                </div>

                <div class="row g-4 g-lg-5">
                    <div class="col-lg-4 col-md-12">
                        <div class="faq-sidebar">

                            <div class="faq-search">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="faqSearchInput" placeholder="Search your question…">
                            </div>

                            <p class="faq-sidebar-label">Browse by category</p>
                            <ul class="faq-cat-list" id="faqCatList">
                                @foreach ($faq_categories as $faq_category)
                                    <li class="@if (isset($current_category) && $current_category->id == $faq_category->id) active @endif">
                                        <a href="{{ route('faqs.category', $faq_category->slug) }}" data-faq-cat-link>
                                            <span>{{ $faq_category->title }}</span>
                                            <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="faq-help-card">
                                <h4>Still stuck?</h4>
                                <p>Can't find what you're looking for? Our team usually replies within a few hours.
                                </p>
                                <a class="faq-help-btn" href="https://wa.me/8801325359909" target="_blank" rel="noopener">
                                    Chat on WhatsApp
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-12">
                        <div class="faq-accordion" id="faqAccordion">
                            @foreach ($faqs as $key => $faq)
                                <div class="faq-item @if ($key == 0) active @endif" data-faq-item>
                                    <button type="button" class="faq-item-btn" data-faq-trigger>
                                        <span class="faq-q">{{ $faq->title }}</span>
                                        <span class="faq-icon"><span class="line line1"></span><span
                                                class="line line2"></span></span>
                                    </button>
                                    <div class="faq-item-body" data-faq-body>
                                        <div class="faq-item-inner description">{!! $faq->description !!}</div>
                                    </div>
                                </div>
                            @endforeach

                            @if (count($faqs) == 0)
                                <p class="faq-no-results" id="faqEmptyState">No questions found in this category yet.</p>
                            @endif

                            <p class="faq-no-results d-none" id="faqNoResults">No matching questions found — try a
                                different keyword or browse a category on the left.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function() {

                // ---------- Accordion + search (re-run after every AJAX swap) ----------
                function initFaqAccordionAndSearch() {
                    var items = document.querySelectorAll('[data-faq-item]');

                    function setBodyHeight(item, open) {
                        var body = item.querySelector('[data-faq-body]');
                        if (open) {
                            body.style.maxHeight = body.scrollHeight + 'px';
                        } else {
                            body.style.maxHeight = 0;
                        }
                    }

                    items.forEach(function(item) {
                        var trigger = item.querySelector('[data-faq-trigger]');
                        if (item.classList.contains('active')) {
                            setBodyHeight(item, true);
                        }
                        trigger.addEventListener('click', function() {
                            var isActive = item.classList.contains('active');
                            items.forEach(function(other) {
                                other.classList.remove('active');
                                setBodyHeight(other, false);
                            });
                            if (!isActive) {
                                item.classList.add('active');
                                setBodyHeight(item, true);
                            }
                        });
                    });

                    var searchInput = document.getElementById('faqSearchInput');
                    var noResults = document.getElementById('faqNoResults');
                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            var term = this.value.trim().toLowerCase();
                            var visibleCount = 0;
                            items.forEach(function(item) {
                                var q = item.querySelector('.faq-q').textContent.toLowerCase();
                                var a = item.querySelector('.faq-item-inner').textContent.toLowerCase();
                                var match = q.indexOf(term) !== -1 || a.indexOf(term) !== -1;
                                item.style.display = match ? '' : 'none';
                                if (match) visibleCount++;
                            });
                            noResults.classList.toggle('d-none', visibleCount !== 0);
                        });
                    }
                }

                window.addEventListener('resize', function() {
                    document.querySelectorAll('[data-faq-item].active').forEach(function(item) {
                        var body = item.querySelector('[data-faq-body]');
                        body.style.maxHeight = body.scrollHeight + 'px';
                    });
                });

                initFaqAccordionAndSearch();

                // ---------- AJAX category switching (no page reload) ----------
                var accordionWrap = document.getElementById('faqAccordion').parentNode; // .col-lg-8
                var catList = document.getElementById('faqCatList');

                function bindCategoryLinks() {
                    catList.querySelectorAll('[data-faq-cat-link]').forEach(function(link) {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            loadCategory(this.getAttribute('href'), true);
                        });
                    });
                }

                function loadCategory(url, pushState) {
                    if (catList.dataset.loading === '1') return;
                    catList.dataset.loading = '1';
                    catList.style.opacity = '0.5';
                    accordionWrap.style.opacity = '0.5';

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(function(res) {
                            return res.text();
                        })
                        .then(function(html) {
                            var doc = new DOMParser().parseFromString(html, 'text/html');
                            var newCatList = doc.getElementById('faqCatList');
                            var newAccordion = doc.getElementById('faqAccordion');

                            if (newCatList) {
                                catList.innerHTML = newCatList.innerHTML;
                                bindCategoryLinks();
                            }
                            if (newAccordion) {
                                accordionWrap.replaceChild(newAccordion, document.getElementById(
                                    'faqAccordion'));
                            }

                            initFaqAccordionAndSearch();

                            if (pushState) {
                                history.pushState({
                                    faqCategoryUrl: url
                                }, '', url);
                            }

                            var section = document.querySelector('.faq-main');
                            if (section) {
                                section.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        })
                        .catch(function() {
                            // fall back to a normal navigation if the AJAX request fails
                            window.location.href = url;
                        })
                        .finally(function() {
                            catList.dataset.loading = '0';
                            catList.style.opacity = '1';
                            accordionWrap.style.opacity = '1';
                        });
                }

                bindCategoryLinks();

                window.addEventListener('popstate', function(e) {
                    if (e.state && e.state.faqCategoryUrl) {
                        loadCategory(e.state.faqCategoryUrl, false);
                    }
                });
            })();
        </script>
        <!--End FAQs Section-->
    @endif

@endsection
