@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('services');
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
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .svc-scope {
            --svc-navy-deep: #1A0505;
            --svc-navy: #4A0A0A;
            --svc-navy-soft: #6E0F0F;
            --svc-teal: #E31E24;
            --svc-teal-dim: rgba(227, 30, 36, .12);
            --svc-amber: #E31E24;
            --svc-ink: #0B1220;
            --svc-slate: #5B6B7F;
            --svc-bg: #F7F9FC;
            --svc-line: #E3E9F1;
            --svc-card: #FFFFFF;
            --svc-radius: 14px;
            --svc-display: 'Space Grotesk', sans-serif;
            --svc-body: 'Inter', sans-serif;
            --svc-mono: 'JetBrains Mono', monospace;
            background: var(--svc-bg) !important;
            font-family: var(--svc-body) !important;
            color: var(--svc-ink) !important;
            overflow-x: hidden;
        }

        .svc-scope * {
            box-sizing: border-box;
        }

        .svc-scope h1,
        .svc-scope h2,
        .svc-scope h3,
        .svc-scope h4 {
            font-family: var(--svc-display) !important;
            margin: 0 !important;
            letter-spacing: -0.01em;
        }

        .svc-scope p {
            margin: 0 !important;
        }

        .svc-scope a {
            text-decoration: none !important;
        }

        .svc-scope img {
            max-width: 100%;
            display: block;
        }

        .svc-scope ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .svc-wrap {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .svc-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--svc-mono) !important;
            font-size: 12.5px;
            font-weight: 500;
            letter-spacing: .04em;
            color: var(--svc-teal) !important;
            background: var(--svc-teal-dim) !important;
            border: 1px solid rgba(227, 30, 36, .35) !important;
            padding: 6px 14px !important;
            border-radius: 100px !important;
        }

        .svc-eyebrow::before {
            content: '$';
            opacity: .75;
        }

        .svc-eyebrow.on-dark {
            color: #FF8A8D !important;
            background: rgba(255, 138, 141, .08) !important;
            border-color: rgba(255, 138, 141, .25) !important;
        }

        .svc-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--svc-body) !important;
            font-weight: 600 !important;
            font-size: 15px;
            padding: 14px 26px !important;
            border-radius: 9px !important;
            transition: transform .18s ease, box-shadow .18s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .svc-btn-primary {
            background: var(--svc-amber) !important;
            color: #fff !important;
        }

        .svc-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(227, 30, 36, .35);
            color: #fff !important;
        }

        .svc-btn-ghost {
            background: transparent !important;
            color: #fff !important;
            border-color: rgba(255, 255, 255, .28) !important;
        }

        .svc-btn-ghost:hover {
            background: rgba(255, 255, 255, .08) !important;
            color: #fff !important;
            transform: translateY(-2px);
        }

        @media (prefers-reduced-motion:no-preference) {
            .svc-cursor {
                animation: svcBlink 1.05s steps(1) infinite;
            }

            .svc-reveal {
                opacity: 0;
                transform: translateY(18px);
                animation: svcReveal .7s ease forwards;
            }
        }

        @keyframes svcBlink {
            50% {
                opacity: 0;
            }
        }

        @keyframes svcReveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* HERO */
        .svc-hero {
            position: relative;
            background: radial-gradient(120% 140% at 8% -10%, var(--svc-navy-soft) 0%, var(--svc-navy) 42%, var(--svc-navy-deep) 100%) !important;
            padding: 96px 0 64px;
            color: #fff !important;
            overflow: hidden;
        }

        .svc-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: .5;
            background-image: linear-gradient(rgba(255, 255, 255, .035) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .035) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(70% 60% at 25% 20%, #000 0%, transparent 75%);
        }

        .svc-hero-copy h1 {
            font-size: clamp(30px, 4.6vw, 54px);
            line-height: 1.08;
            font-weight: 700;
            color: #fff !important;
        }

        .svc-hero-copy h1 em {
            font-style: normal;
            color: var(--svc-teal) !important;
        }

        .svc-hero-copy p {
            margin-top: 20px !important;
            font-size: 17px;
            line-height: 1.65;
            color: rgba(255, 255, 255, .72) !important;
            max-width: 490px;
        }

        .svc-hero-actions {
            display: flex;
            gap: 14px;
            margin-top: 34px;
            flex-wrap: wrap;
        }

        .svc-hero-crumb {
            margin-top: 38px;
            font-family: var(--svc-mono) !important;
            font-size: 12.5px;
            color: rgba(255, 255, 255, .45) !important;
        }

        .svc-hero-crumb a {
            color: rgba(255, 255, 255, .65) !important;
        }

        .svc-hero-crumb a:hover {
            color: var(--svc-teal) !important;
        }

        .svc-term {
            background: #071A30 !important;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px !important;
            box-shadow: 0 30px 70px -20px rgba(0, 0, 0, .55);
            overflow: hidden;
        }

        .svc-term-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: #0A2038 !important;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }

        .svc-term-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .svc-term-dot:nth-child(1) {
            background: #FF5F57 !important;
        }

        .svc-term-dot:nth-child(2) {
            background: #FEBC2E !important;
        }

        .svc-term-dot:nth-child(3) {
            background: #28C840 !important;
        }

        .svc-term-tab {
            margin-left: 14px;
            font-family: var(--svc-mono) !important;
            font-size: 12px;
            color: rgba(255, 255, 255, .45) !important;
        }

        .svc-term-body {
            padding: 22px;
            font-family: var(--svc-mono) !important;
            font-size: 13.5px;
            line-height: 2;
            color: #B9C6D6 !important;
        }

        .svc-term-line {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .svc-term-tag {
            margin-left: auto;
            font-size: 11px;
            padding: 2px 9px;
            border-radius: 100px;
            border: 1px solid rgba(255, 138, 141, .3);
            color: #FF8A8D !important;
        }

        .svc-term-tag.beta {
            color: var(--svc-amber) !important;
            border-color: rgba(227, 30, 36, .35);
        }

        .svc-ticker {
            position: relative;
            z-index: 2;
            margin-top: 56px;
            background: #fff !important;
            border: 1px solid var(--svc-line);
            border-radius: 14px !important;
            box-shadow: 0 24px 50px -28px rgba(3, 24, 46, .35);
        }

        .svc-ticker-item {
            padding: 22px 20px;
            text-align: center;
            border-right: 1px solid var(--svc-line);
        }

        .svc-ticker-item:last-child {
            border-right: none;
        }

        .svc-ticker-num {
            font-family: var(--svc-mono) !important;
            font-size: 26px;
            font-weight: 600;
            color: var(--svc-navy) !important;
        }

        .svc-ticker-num span {
            color: var(--svc-teal) !important;
        }

        .svc-ticker-label {
            margin-top: 4px;
            font-size: 12.5px;
            color: var(--svc-slate) !important;
        }

        /* SECTION HEAD (Main services list, unchanged) */
        .svc-section {
            padding: 100px 0;
        }

        .svc-head {
            max-width: 640px;
            margin-bottom: 52px;
        }

        .svc-head .svc-eyebrow {
            margin-bottom: 16px;
        }

        .svc-head h2 {
            font-size: clamp(28px, 3.4vw, 38px);
            font-weight: 700;
            color: var(--svc-navy) !important;
            line-height: 1.18;
        }

        .svc-head p,
        .svc-head .desc {
            margin-top: 14px !important;
            font-size: 16px;
            color: var(--svc-slate) !important;
            line-height: 1.7;
        }

        .svc-card {
            position: relative;
            background: var(--svc-card) !important;
            border: 1px solid var(--svc-line) !important;
            border-radius: var(--svc-radius) !important;
            padding: 30px 28px 28px !important;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .svc-card:hover {
            transform: translateY(-6px);
            border-color: rgba(227, 30, 36, .5) !important;
            box-shadow: 0 26px 46px -22px rgba(74, 10, 10, .28);
        }

        .svc-card-file {
            font-family: var(--svc-mono) !important;
            font-size: 11.5px;
            color: var(--svc-slate) !important;
        }

        .svc-card-file span {
            color: var(--svc-teal) !important;
        }

        .svc-card-media {
            margin-top: 16px;
            width: 100%;
            aspect-ratio: 16/10;
            border-radius: 12px !important;
            overflow: hidden;
            background: var(--svc-navy) !important;
        }

        .svc-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .svc-card h3 {
            margin-top: 20px !important;
            font-size: 19px;
            font-weight: 600;
            color: var(--svc-navy) !important;
        }

        .svc-card h3 a {
            color: inherit !important;
        }

        .svc-card .desc {
            margin-top: 10px !important;
            font-size: 14.5px;
            line-height: 1.7;
            color: var(--svc-slate) !important;
            flex: 1;
        }

        .svc-card .desc * {
            font-size: inherit !important;
            margin: 0 !important;
        }

        .svc-card-more {
            margin-top: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--svc-mono) !important;
            font-size: 13px;
            font-weight: 500;
            color: var(--svc-navy) !important;
        }

        .svc-card-more::after {
            content: '→';
            transition: transform .2s ease;
        }

        .svc-card:hover .svc-card-more::after {
            transform: translateX(4px);
        }

        /* ================= FILTERABLE SUB_SERVICE GRID (portfolio-style) ================= */
        .sub_service-section {
            padding: 90px 0;
            background: #fff !important;
        }

        .sub_service-head {
            max-width: 680px;
            margin-bottom: 36px;
        }

        .sub_service-head h2 {
            font-size: clamp(26px, 3.2vw, 36px);
            font-weight: 700;
            color: var(--svc-navy) !important;
        }

        .sub_service-head p {
            margin-top: 12px !important;
            font-size: 15.5px;
            color: var(--svc-slate) !important;
            line-height: 1.7;
        }

        .sub_service-filterbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--svc-line);
        }

        .sub_service-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sub_service-tab {
            font-family: var(--svc-body) !important;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--svc-navy) !important;
            background: #F1F3F7 !important;
            border: 1px solid transparent;
            padding: 9px 18px !important;
            border-radius: 100px !important;
            cursor: pointer;
            transition: background .18s ease, color .18s ease;
        }

        .sub_service-tab:hover {
            background: #e7eaf0 !important;
        }

        .sub_service-tab.active {
            background: var(--svc-navy) !important;
            color: #fff !important;
        }

        .sub_service-shown-count {
            font-family: var(--svc-mono) !important;
            font-size: 12.5px;
            color: #9AA6B4 !important;
            white-space: nowrap;
        }

        .sub_service-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .sub_service-card {
            border: 1px solid var(--svc-line) !important;
            border-radius: 12px !important;
            overflow: hidden;
            background: #fff !important;
            transition: box-shadow .25s ease, transform .25s ease, opacity .25s ease;
            opacity: 0;
            animation: sub_serviceFadeIn .5s ease forwards;
        }

        .sub_service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -14px rgba(74, 10, 10, .22);
        }

        .sub_service-card.sub_service-hidden {
            display: none !important;
        }

        @keyframes sub_serviceFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sub_service-card-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 9px 12px;
            background: #fafafa !important;
            border-bottom: 1px solid #eee;
        }

        .sub_service-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ddd !important;
            display: inline-block;
        }

        .sub_service-url {
            margin-left: 8px;
            font-size: 10.5px;
            color: #999 !important;
            font-family: var(--svc-mono) !important;
            background: #fff !important;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 2px 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .sub_service-card-media {
            display: block;
            aspect-ratio: 16/10;
            overflow: hidden;
            background: #f5f5f5 !important;
        }

        .sub_service-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .sub_service-card:hover .sub_service-card-media img {
            transform: scale(1.04);
        }

        .sub_service-card-info {
            padding: 16px 18px 18px !important;
        }

        .sub_service-card-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .sub_service-card-title-row h4 {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.4;
            color: var(--svc-navy) !important;
        }

        .sub_service-card-title-row h4 a {
            color: inherit !important;
        }

        .sub_service-card-arrow {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #ddd;
            color: #333 !important;
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }

        .sub_service-card:hover .sub_service-card-arrow {
            background: var(--svc-teal) !important;
            border-color: var(--svc-teal) !important;
            color: #fff !important;
        }

        .sub_service-card-desc {
            font-size: 12.5px;
            color: #777 !important;
            line-height: 1.6;
            margin: 8px 0 12px !important;
        }

        .sub_service-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sub_service-tag {
            font-size: 11px;
            color: #555 !important;
            background: #f2f2f2 !important;
            border-radius: 20px;
            padding: 3px 10px;
        }

        .sub_service-tag-muted {
            background: transparent !important;
            border: 1px solid #eee;
            display: inline-flex;
            align-items: center;
        }

        .sub_service-empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 20px;
            font-family: var(--svc-mono) !important;
            font-size: 14px;
            color: #aaa !important;
        }

        @media (max-width:1200px) {
            .sub_service-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width:900px) {
            .sub_service-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width:560px) {
            .sub_service-grid {
                grid-template-columns: 1fr;
            }

            .sub_service-filterbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* CTA */
        .svc-cta {
            background: var(--svc-navy-deep) !important;
            padding: 90px 0;
            text-align: center;
        }

        .svc-cta h2 {
            color: #fff !important;
            font-size: clamp(26px, 3.4vw, 36px);
            font-weight: 700;
            max-width: 620px;
            margin: 0 auto !important;
        }

        .svc-cta p {
            margin-top: 16px !important;
            color: rgba(255, 255, 255, .6) !important;
            font-size: 15.5px;
            max-width: 520px;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .svc-cta .svc-btn {
            margin-top: 30px !important;
        }
    </style>

    <div class="svc-scope">

        {{-- ================= HERO ================= --}}
        <section class="svc-hero">
            <div class="svc-wrap">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-7 svc-hero-copy svc-reveal">
                        <span class="svc-eyebrow on-dark">./services --init</span>
                        <h1 class="mt-3">
                            One Team. Every Digital Solution You Need.
                        </h1>
                        <p>Whatever your business needs to grow online, we've probably already built it for someone else.
                            Explore everything we offer, all under one roof.</p>
                        <div class="svc-hero-actions">
                            <a href="#svc-services-list" class="svc-btn svc-btn-primary">View Services</a>
                            {{-- <a href="{{ route('contact') ?? '#' }}" class="svc-btn svc-btn-ghost">Start a Project</a> --}}
                            <button type="button" class="cta-cta-btn svc-btn svc-btn-ghost"
                                onclick="document.getElementById('quotePopupModal').classList.add('is-open'); document.body.style.overflow='hidden';">
                                Start a Project
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        </div>
                        <div class="svc-hero-crumb">
                            <a href="{{ route('home') }}">{{ __('navbar.home') }}</a> / {{ __('navbar.services') }}
                        </div>
                    </div>

                    <div class="col-lg-5 svc-reveal" style="animation-delay:.15s">
                        <div class="svc-term">
                            <div class="svc-term-bar">
                                <span class="svc-term-dot"></span><span class="svc-term-dot"></span><span
                                    class="svc-term-dot"></span>
                                <span class="svc-term-tab">services.json</span>
                            </div>
                            <div class="svc-term-body">
                                <div class="svc-term-line"><span style="color:#FF8A8D">msn@softtech</span>&nbsp;~&nbsp;$ ls
                                    ./services</div>
                                @if (isset($services) && count($services) > 0)
                                    @foreach ($services->take(4) as $tService)
                                        <div class="svc-term-line">
                                            &gt; {{ \Illuminate\Support\Str::slug($tService->short_title) }}
                                            <span class="svc-term-tag">active</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="svc-term-line">&gt; web-development <span class="svc-term-tag">active</span>
                                    </div>
                                    <div class="svc-term-line">&gt; mobile-app-dev <span class="svc-term-tag">active</span>
                                    </div>
                                    <div class="svc-term-line">&gt; ai-solutions <span class="svc-term-tag beta">beta</span>
                                    </div>
                                @endif
                                <div class="svc-term-line">msn@softtech ~ $ <span class="svc-cursor">▍</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="svc-ticker svc-reveal row g-0 mt-5" style="animation-delay:.25s">
                    @foreach ($counters as $counter)
                        <div class="col-6 col-md-3 svc-ticker-item">
                            <div class="svc-ticker-num">{{ $counter->value }}<span>+</span></div>
                            <div class="svc-ticker-label">{{ $counter->title }}</div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        @php
            $section_services = \App\Models\Section::section('services');
        @endphp

        {{-- ================= MAIN SERVICES GRID (unchanged) ================= --}}
        @if (isset($services) && count($services) > 0)
            <section class="svc-section" id="svc-services-list">
                <div class="svc-wrap">
                    <div class="svc-head">
                        <span class="svc-eyebrow">what_we_build.list()</span>
                        <h2 class="mt-3">{{ $section_services->title ?? 'Our Services' }}</h2>
                        <div class="desc">{!! $section_services->description ?? 'Smart Solutions for a Smarter Tomorrow.' !!}</div>
                    </div>

                    <div class="row g-4">
                        @foreach ($services as $key => $service)
                            <div class="col-md-6 col-lg-4">
                                <div class="svc-card svc-reveal" style="animation-delay:{{ ($key % 3) * 0.08 }}s">
                                    <div class="svc-card-file">
                                        //
                                        {{ sprintf('%02d', $key + 1) }}<span>/{{ \Illuminate\Support\Str::slug($service->short_title) }}</span>
                                    </div>
                                    <div class="svc-card-media">
                                        <img src="{{ asset('uploads/service/' . $service->image_path) }}"
                                            alt="{{ $service->title }}">
                                    </div>
                                    <h3><a
                                            href="{{ route('service.single', $service->slug) }}">{{ $service->short_title }}</a>
                                    </h3>
                                    <div class="desc">{!! strip_tags(\Illuminate\Support\Str::words($service->short_desc, 18)) !!}</div>
                                    {{-- <a href="{{ route('service.single', $service->slug) }}"
                                        class="svc-card-more">{{ __('common.read_more') }}</a> --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @php
            $allSubservices = collect();
            if (isset($services)) {
                foreach ($services as $service) {
                    foreach ($service->subservices as $sub) {
                        if (isset($sub->manu) && $sub->manu != 1) {
                            continue;
                        } // চাইলে বাদ দিন এই লাইন
                        $sub->parent_service = $service;
                        $allSubservices->push($sub);
                    }
                }
            }
        @endphp

        {{-- ================= FILTERABLE SUB_SERVICE PORTFOLIO GRID ================= --}}
        @if ($allSubservices->count() > 0)
            <section class="sub_service-section" id="sub_service-portfolio">
                <div class="svc-wrap">

                    <div class="sub_service-head">
                        <span class="svc-eyebrow">{{ __('dashboard.service_categories') }}.filter()</span>
                        <h2 class="mt-3">{{ __('dashboard.service_categories') ?? 'Explore Sub-Services' }}</h2>
                        <p>Filter by service category to find exactly what you need.</p>
                    </div>

                    <div class="sub_service-filterbar">
                        <div class="sub_service-tabs" id="sub_serviceTabs">
                            <button type="button" class="sub_service-tab active" data-filter="all">All</button>
                            @foreach ($services as $service)
                                @if ($service->subservices->count() > 0)
                                    <button type="button" class="sub_service-tab"
                                        data-filter="{{ \Illuminate\Support\Str::slug($service->short_title) }}">
                                        {{ $service->short_title }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        <div class="sub_service-shown-count" id="sub_serviceCount">{{ $allSubservices->count() }} items
                            shown</div>
                    </div>

                    <div class="sub_service-grid" id="sub_serviceGrid">
                        @foreach ($allSubservices as $key => $sub)
                            <div class="sub_service-card"
                                data-category="{{ \Illuminate\Support\Str::slug($sub->parent_service->short_title) }}"
                                style="animation-delay:{{ ($key % 4) * 0.06 }}s">

                                <div class="sub_service-card-bar">
                                    <span class="sub_service-dot"></span>
                                    <span class="sub_service-dot"></span>
                                    <span class="sub_service-dot"></span>
                                    <span
                                        class="sub_service-url">{{ \Illuminate\Support\Str::slug($sub->parent_service->short_title . '-' . $sub->short_title) }}</span>
                                </div>

                                <a href="{{ route('service.related-single', $sub->slug) }}"
                                    class="sub_service-card-media">
                                    <img src="{{ asset('uploads/subservice/' . $sub->image_path) }}"
                                        alt="{{ $sub->short_title }}" loading="lazy">
                                </a>

                                <div class="sub_service-card-info">
                                    <div class="sub_service-card-title-row">
                                        <h4><a
                                                href="{{ route('service.related-single', $sub->slug) }}">{{ $sub->short_title }}</a>
                                        </h4>
                                        <a href="{{ route('service.related-single', $sub->slug) }}"
                                            class="sub_service-card-arrow" aria-label="{{ $sub->short_title }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                                                <path d="M7 17L17 7M17 7H8M17 7V16" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    </div>

                                    @if (!empty($sub->short_desc))
                                        <p class="sub_service-card-desc">
                                            {{ \Illuminate\Support\Str::words(strip_tags($sub->short_desc), 10) }}</p>
                                    @endif

                                    <div class="sub_service-card-tags">
                                        <span class="sub_service-tag">{{ $sub->parent_service->short_title }}</span>
                                        @if (!empty($sub->sub_service_icon))
                                            <span class="sub_service-tag sub_service-tag-muted"><i
                                                    class="{{ $sub->sub_service_icon }}"></i></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabs = document.querySelectorAll('#sub_serviceTabs .sub_service-tab');
            var cards = document.querySelectorAll('#sub_serviceGrid .sub_service-card');
            var countEl = document.getElementById('sub_serviceCount');

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    tabs.forEach(function(t) {
                        t.classList.remove('active');
                    });
                    tab.classList.add('active');

                    var filter = tab.getAttribute('data-filter');
                    var shown = 0;

                    cards.forEach(function(card) {
                        var match = (filter === 'all' || card.getAttribute(
                            'data-category') === filter);
                        card.classList.toggle('sub_service-hidden', !match);
                        if (match) shown++;
                    });

                    countEl.textContent = shown + ' items shown';
                });
            });
        });
    </script>

@endsection
