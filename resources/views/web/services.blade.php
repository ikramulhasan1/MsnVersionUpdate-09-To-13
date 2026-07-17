@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('services');
@endphp
@if(isset($header))

@section('title', $header->meta_title)

@section('top_meta_tags')
    @if(isset($header->meta_description))
        <meta name="description" content="{!! str_limit(strip_tags($header->meta_description), 160, ' ...') !!}">
    @else
        <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
    @endif

    @if(isset($header->meta_keywords))
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

    <style>
        /* ==========================================================================
                   SVC — Services Page Design System
                   Signature: developer-terminal motif (this is a software agency, so the
                   page borrows the vernacular of an editor/terminal: tab bars, file
                   eyebrows, commit-style process log, blinking cursor).
                   ========================================================================== */

        :root {
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
        }

        .svc-scope {
            background: var(--svc-bg);
            font-family: var(--svc-body);
            color: var(--svc-ink);
            overflow-x: hidden;
        }

        .svc-scope h1,
        .svc-scope h2,
        .svc-scope h3,
        .svc-scope h4 {
            font-family: var(--svc-display);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .svc-scope p {
            margin: 0;
        }

        .svc-scope a {
            text-decoration: none;
        }

        .svc-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .svc-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--svc-mono);
            font-size: 12.5px;
            font-weight: 500;
            letter-spacing: .04em;
            color: var(--svc-teal);
            text-transform: none;
            background: var(--svc-teal-dim);
            border: 1px solid rgba(227, 30, 36, .35);
            padding: 6px 14px;
            border-radius: 100px;
        }

        .svc-eyebrow::before {
            content: '$';
            opacity: .75;
        }

        .svc-eyebrow.on-dark {
            color: #FF8A8D;
            background: rgba(255, 138, 141, .08);
            border-color: rgba(255, 138, 141, .25);
        }

        .svc-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--svc-body);
            font-weight: 600;
            font-size: 15px;
            padding: 14px 26px;
            border-radius: 9px;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .svc-btn-primary {
            background: var(--svc-amber);
            color: #fff;
        }

        .svc-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(227, 30, 36, .35);
            color: #fff;
        }

        .svc-btn-ghost {
            background: transparent;
            color: #fff;
            border-color: rgba(255, 255, 255, .28);
        }

        .svc-btn-ghost:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
            transform: translateY(-2px);
        }

        .svc-btn-outline-dark {
            background: transparent;
            color: var(--svc-navy);
            border-color: var(--svc-line);
        }

        .svc-btn-outline-dark:hover {
            border-color: var(--svc-navy);
            color: var(--svc-navy);
            transform: translateY(-2px);
        }

        @media (prefers-reduced-motion: no-preference) {
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

        /* ---------------- HERO ---------------- */
        .svc-hero {
            position: relative;
            background: radial-gradient(120% 140% at 8% -10%, var(--svc-navy-soft) 0%, var(--svc-navy) 42%, var(--svc-navy-deep) 100%);
            padding: 96px 0 64px;
            color: #fff;
            overflow: hidden;
        }

        .svc-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: .5;
            background-image:
                linear-gradient(rgba(255, 255, 255, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .035) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(70% 60% at 25% 20%, #000 0%, transparent 75%);
        }

        .svc-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 56px;
            align-items: center;
        }

        .svc-hero-copy .svc-eyebrow {
            margin-bottom: 22px;
        }

        .svc-hero-copy h1 {
            font-size: clamp(34px, 4.6vw, 54px);
            line-height: 1.08;
            font-weight: 700;
        }

        .svc-hero-copy h1 em {
            font-style: normal;
            color: var(--svc-teal);
        }

        .svc-hero-copy p {
            margin-top: 20px;
            font-size: 17px;
            line-height: 1.65;
            color: rgba(255, 255, 255, .72);
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
            font-family: var(--svc-mono);
            font-size: 12.5px;
            color: rgba(255, 255, 255, .45);
        }

        .svc-hero-crumb a {
            color: rgba(255, 255, 255, .65);
        }

        .svc-hero-crumb a:hover {
            color: var(--svc-teal);
        }

        /* terminal window — signature element */
        .svc-term {
            background: #071A30;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px;
            box-shadow: 0 30px 70px -20px rgba(0, 0, 0, .55), 0 0 0 1px rgba(255, 255, 255, .02) inset;
            overflow: hidden;
        }

        .svc-term-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: #0A2038;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }

        .svc-term-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .svc-term-dot:nth-child(1) {
            background: #FF5F57;
        }

        .svc-term-dot:nth-child(2) {
            background: #FEBC2E;
        }

        .svc-term-dot:nth-child(3) {
            background: #28C840;
        }

        .svc-term-tab {
            margin-left: 14px;
            font-family: var(--svc-mono);
            font-size: 12px;
            color: rgba(255, 255, 255, .45);
        }

        .svc-term-body {
            padding: 22px 22px 26px;
            font-family: var(--svc-mono);
            font-size: 13.5px;
            line-height: 2;
            color: #B9C6D6;
        }

        .svc-term-body .path {
            color: #FF8A8D;
        }

        .svc-term-body .flag {
            color: var(--svc-amber);
        }

        .svc-term-line {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .svc-term-tag {
            margin-left: auto;
            font-size: 11px;
            padding: 2px 9px;
            border-radius: 100px;
            border: 1px solid rgba(255, 138, 141, .3);
            color: #FF8A8D;
        }

        .svc-term-tag.beta {
            color: var(--svc-amber);
            border-color: rgba(227, 30, 36, .35);
        }

        /* stat ticker strip */
        .svc-ticker {
            position: relative;
            z-index: 2;
            margin-top: 56px;
            background: #fff;
            border: 1px solid var(--svc-line);
            border-radius: 14px;
            box-shadow: 0 24px 50px -28px rgba(3, 24, 46, .35);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            font-family: var(--svc-mono);
            font-size: 26px;
            font-weight: 600;
            color: var(--svc-navy);
        }

        .svc-ticker-num span {
            color: var(--svc-teal);
        }

        .svc-ticker-label {
            margin-top: 4px;
            font-size: 12.5px;
            color: var(--svc-slate);
            letter-spacing: .02em;
        }

        /* ---------------- SECTION HEADS ---------------- */
        .svc-section {
            padding: 100px 0;
        }

        .svc-section.tight {
            padding-top: 40px;
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
            color: var(--svc-navy);
            line-height: 1.18;
        }

        .svc-head p {
            margin-top: 14px;
            font-size: 16px;
            color: var(--svc-slate);
            line-height: 1.7;
        }

        .svc-head p a {
            color: var(--svc-teal);
            font-weight: 600;
        }

        /* ---------------- SERVICES GRID ---------------- */
        .svc-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        .svc-card {
            position: relative;
            background: var(--svc-card);
            border: 1px solid var(--svc-line);
            border-radius: var(--svc-radius);
            padding: 30px 28px 28px;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .svc-card:hover {
            transform: translateY(-6px);
            border-color: rgba(227, 30, 36, .5);
            box-shadow: 0 26px 46px -22px rgba(74, 10, 10, .28);
        }

        .svc-card-file {
            font-family: var(--svc-mono);
            font-size: 11.5px;
            color: var(--svc-slate);
            letter-spacing: .02em;
        }

        .svc-card-file span {
            color: var(--svc-teal);
        }

        .svc-card-media {
            margin-top: 16px;
            width: 56px;
            height: 56px;
            border-radius: 12px;
            overflow: hidden;
            background: var(--svc-navy);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .svc-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .svc-card h3 {
            margin-top: 20px;
            font-size: 19px;
            font-weight: 600;
            color: var(--svc-navy);
        }

        .svc-card h3 a {
            color: inherit;
        }

        .svc-card .desc {
            margin-top: 10px;
            font-size: 14.5px;
            line-height: 1.7;
            color: var(--svc-slate);
            flex: 1;
        }

        .svc-card .desc * {
            font-size: inherit !important;
            margin: 0 !important;
        }

        .svc-card-more {
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--svc-mono);
            font-size: 13px;
            font-weight: 500;
            color: var(--svc-navy);
        }

        .svc-card-more::after {
            content: '→';
            transition: transform .2s ease;
        }

        .svc-card:hover .svc-card-more::after {
            transform: translateX(4px);
        }

        /* ---------------- VALUES STRIP ---------------- */
        .svc-values {
            background: #fff;
            border-top: 1px solid var(--svc-line);
            border-bottom: 1px solid var(--svc-line);
        }

        .svc-values-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .svc-value {
            padding: 44px 30px;
            border-right: 1px solid var(--svc-line);
        }

        .svc-value:last-child {
            border-right: none;
        }

        .svc-value .flag {
            font-family: var(--svc-mono);
            font-size: 12px;
            color: var(--svc-teal);
        }

        .svc-value h4 {
            margin-top: 10px;
            font-size: 17px;
            font-weight: 600;
            color: var(--svc-navy);
        }

        .svc-value p {
            margin-top: 8px;
            font-size: 14px;
            color: var(--svc-slate);
            line-height: 1.65;
        }

        /* ---------------- PROCESS — commit log timeline ---------------- */
        .svc-process {
            background: var(--svc-navy-deep);
            position: relative;
        }

        .svc-process .svc-head h2,
        .svc-process .svc-head p {
            color: #fff;
        }

        .svc-process .svc-head p {
            color: rgba(255, 255, 255, .6);
        }

        .svc-log {
            position: relative;
            padding-left: 8px;
        }

        .svc-log::before {
            content: '';
            position: absolute;
            left: 29px;
            top: 6px;
            bottom: 6px;
            width: 1px;
            background: rgba(255, 255, 255, .12);
        }

        .svc-log-item {
            position: relative;
            display: grid;
            grid-template-columns: 58px 1fr;
            gap: 22px;
            padding: 22px 0;
        }

        .svc-log-node {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #2A0808;
            border: 1px solid rgba(255, 138, 141, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--svc-mono);
            font-size: 13px;
            color: #FF8A8D;
            z-index: 1;
        }

        .svc-log-body {
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 12px;
            padding: 20px 24px;
        }

        .svc-log-body h4 {
            font-size: 17px;
            font-weight: 600;
            color: #fff;
        }

        .svc-log-body .desc {
            margin-top: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, .6);
            line-height: 1.7;
        }

        .svc-log-body .desc * {
            font-size: inherit !important;
            margin: 0 !important;
        }

        /* ---------------- ENGAGEMENT MODELS ---------------- */
        .svc-plans {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .svc-plan {
            border: 1px solid var(--svc-line);
            border-radius: var(--svc-radius);
            padding: 32px 28px;
            background: #fff;
            transition: border-color .2s ease, transform .2s ease;
        }

        .svc-plan:hover {
            transform: translateY(-4px);
            border-color: var(--svc-navy);
        }

        .svc-plan.featured {
            border-color: var(--svc-teal);
            position: relative;
        }

        .svc-plan.featured::before {
            content: 'RECOMMENDED';
            position: absolute;
            top: -12px;
            left: 28px;
            background: var(--svc-teal);
            color: #fff;
            font-family: var(--svc-mono);
            font-size: 10.5px;
            letter-spacing: .06em;
            padding: 4px 10px;
            border-radius: 100px;
        }

        .svc-plan .flag {
            font-family: var(--svc-mono);
            font-size: 12.5px;
            color: var(--svc-teal);
        }

        .svc-plan h4 {
            margin-top: 12px;
            font-size: 20px;
            font-weight: 600;
            color: var(--svc-navy);
        }

        .svc-plan p.desc {
            margin-top: 10px;
            font-size: 14px;
            color: var(--svc-slate);
            line-height: 1.7;
        }

        .svc-plan ul {
            list-style: none;
            margin: 20px 0 0;
            padding: 0;
        }

        .svc-plan ul li {
            display: flex;
            gap: 10px;
            font-size: 13.5px;
            color: var(--svc-ink);
            padding: 6px 0;
        }

        .svc-plan ul li::before {
            content: '✓';
            color: var(--svc-teal);
            font-weight: 700;
        }

        .svc-plan a {
            margin-top: 24px;
            display: inline-flex;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--svc-navy);
            font-family: var(--svc-mono);
        }

        .svc-plan a::after {
            content: ' →';
        }

        /* ---------------- TESTIMONIAL ---------------- */
        .svc-quote {
            background: var(--svc-navy);
            color: #fff;
            padding: 90px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .svc-quote-mark {
            font-family: var(--svc-mono);
            font-size: 46px;
            color: var(--svc-teal);
            opacity: .6;
        }

        .svc-quote blockquote {
            max-width: 760px;
            margin: 14px auto 0;
            font-family: var(--svc-display);
            font-size: clamp(22px, 3vw, 30px);
            font-weight: 600;
            line-height: 1.42;
        }

        .svc-quote cite {
            display: block;
            margin-top: 22px;
            font-style: normal;
            font-family: var(--svc-mono);
            font-size: 13px;
            color: rgba(255, 255, 255, .55);
        }

        /* ---------------- CTA ---------------- */
        .svc-cta {
            background: var(--svc-navy-deep);
            padding: 90px 0;
            text-align: center;
        }

        .svc-cta h2 {
            color: #fff;
            font-size: clamp(26px, 3.4vw, 36px);
            font-weight: 700;
            max-width: 620px;
            margin: 0 auto;
        }

        .svc-cta p {
            margin-top: 16px;
            color: rgba(255, 255, 255, .6);
            font-size: 15.5px;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        .svc-cta .svc-btn {
            margin-top: 30px;
        }

        /* ---------------- RESPONSIVE ---------------- */
        @media (max-width: 1024px) {
            .svc-hero-grid {
                grid-template-columns: 1fr;
            }

            .svc-term {
                max-width: 520px;
            }

            .svc-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .svc-values-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .svc-values-grid>div:nth-child(2) {
                border-right: none;
            }

            .svc-plans {
                grid-template-columns: 1fr;
                max-width: 460px;
                margin: 0 auto;
            }
        }

        @media (max-width: 720px) {
            .svc-hero {
                padding: 74px 0 44px;
            }

            .svc-hero-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .svc-hero-actions .svc-btn {
                justify-content: center;
            }

            .svc-ticker {
                grid-template-columns: repeat(2, 1fr);
            }

            .svc-ticker-item:nth-child(2) {
                border-right: none;
            }

            .svc-ticker-item:nth-child(3),
            .svc-ticker-item:nth-child(4) {
                border-top: 1px solid var(--svc-line);
            }

            .svc-section {
                padding: 64px 0;
            }

            .svc-grid {
                grid-template-columns: 1fr;
            }

            .svc-values-grid {
                grid-template-columns: 1fr;
            }

            .svc-value {
                border-right: none;
                border-bottom: 1px solid var(--svc-line);
            }

            .svc-value:last-child {
                border-bottom: none;
            }

            .svc-log-item {
                grid-template-columns: 44px 1fr;
                gap: 14px;
            }

            .svc-log-node {
                width: 44px;
                height: 44px;
                font-size: 11px;
            }

            .svc-log::before {
                left: 22px;
            }
        }
    </style>

    <div class="svc-scope">

        <!-- ================= HERO ================= -->
        <section class="svc-hero">
            <div class="svc-wrap">
                <div class="svc-hero-grid">
                    <div class="svc-hero-copy svc-reveal">
                        <span class="svc-eyebrow on-dark">./services --init</span>
                        <h1>{{ __('navbar.services') }}, built like <em>production code</em> — not a pitch deck.</h1>
                        <p>Every engagement starts with a clear spec, a real timeline, and a team that ships. Explore what
                            we build below, then tell us what you're working on.</p>
                        <div class="svc-hero-actions">
                            <a href="#svc-services-list" class="svc-btn svc-btn-primary">View Services</a>
                            <a href="{{ route('contact') ?? '#' }}" class="svc-btn svc-btn-ghost">Start a Project</a>
                        </div>
                        <div class="svc-hero-crumb">
                            <a href="{{ route('home') }}">{{ __('navbar.home') }}</a> / {{ __('navbar.services') }}
                        </div>
                    </div>

                    <div class="svc-hero-term svc-reveal" style="animation-delay:.15s">
                        <div class="svc-term">
                            <div class="svc-term-bar">
                                <span class="svc-term-dot"></span><span class="svc-term-dot"></span><span
                                    class="svc-term-dot"></span>
                                <span class="svc-term-tab">services.json</span>
                            </div>
                            <div class="svc-term-body">
                                <div class="svc-term-line"><span class="path">msn@softtech</span>&nbsp;<span>~</span>&nbsp;$
                                    ls ./services</div>
                                @if(isset($services) && count($services) > 0)
                                    @foreach($services->take(4) as $tService)
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

                <div class="svc-ticker svc-reveal" style="animation-delay:.25s">
                    <div class="svc-ticker-item">
                        <div class="svc-ticker-num">3700<span>+</span></div>
                        <div class="svc-ticker-label">Projects Completed</div>
                    </div>
                    <div class="svc-ticker-item">
                        <div class="svc-ticker-num">900<span>+</span></div>
                        <div class="svc-ticker-label">Happy Clients</div>
                    </div>
                    <div class="svc-ticker-item">
                        <div class="svc-ticker-num">56<span>+</span></div>
                        <div class="svc-ticker-label">Expert Developers</div>
                    </div>
                    <div class="svc-ticker-item">
                        <div class="svc-ticker-num">25<span>+</span></div>
                        <div class="svc-ticker-label">Countries Served</div>
                    </div>
                </div>
            </div>
        </section>

        @php
            $section_services = \App\Models\Section::section('services');
        @endphp
        @if(count($services) > 0 && isset($section_services))
            <!-- ================= SERVICES GRID ================= -->
            <section class="svc-section" id="svc-services-list">
                <div class="svc-wrap">
                    <div class="svc-head">
                        <span class="svc-eyebrow">what_we_build.list()</span>
                        <h2>{{ $section_services->title }}</h2>
                        <div class="desc" style="margin-top:14px; font-size:16px; color:var(--svc-slate); line-height:1.7;">
                            {!! $section_services->description !!}
                        </div>
                    </div>

                    <div class="svc-grid">
                        @foreach($services as $key => $service)
                            <div class="svc-card svc-reveal" style="animation-delay:{{ ($key % 3) * .08 }}s">
                                <div class="svc-card-file">//
                                    {{ sprintf('%02d', $key + 1) }}<span>/{{ \Illuminate\Support\Str::slug($service->short_title) }}</span>
                                </div>
                                <div class="svc-card-media">
                                    <img src="{{ asset('uploads/service/' . $service->image_path) }}" alt="{{ $service->title }}">
                                </div>
                                <h3><a href="{{ route('service.single', $service->slug) }}">{{ $service->short_title }}</a></h3>
                                <div class="desc">{!! strip_tags(Str::words($service->short_desc, 18)) !!}</div>
                                <a href="{{ route('service.single', $service->slug) }}"
                                    class="svc-card-more">{{ __('common.read_more') }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ================= VALUES ================= -->
        <section class="svc-values">
            <div class="svc-wrap">
                <div class="svc-values-grid">
                    <div class="svc-value">
                        <div class="flag">--transparent</div>
                        <h4>Clear Pricing</h4>
                        <p>Fixed scope or transparent hourly — no hidden line items, ever.</p>
                    </div>
                    <div class="svc-value">
                        <div class="flag">--tested</div>
                        <h4>QA on Every Build</h4>
                        <p>Nothing ships without a real testing pass across devices.</p>
                    </div>
                    <div class="svc-value">
                        <div class="flag">--maintained</div>
                        <h4>Long-Term Support</h4>
                        <p>We stay on after launch — patches, uptime, and updates.</p>
                    </div>
                    <div class="svc-value">
                        <div class="flag">--partnered</div>
                        <h4>Embedded Team</h4>
                        <p>We work inside your workflow, not as an outside vendor.</p>
                    </div>
                </div>
            </div>
        </section>

        @php
            $section_process = \App\Models\Section::section('process');
        @endphp
        @if(count($processes) > 0 && isset($section_process))
            <!-- ================= PROCESS — commit log ================= -->
            <section class="svc-section svc-process">
                <div class="svc-wrap">
                    <div class="svc-head">
                        <span class="svc-eyebrow on-dark">git log --oneline</span>
                        <h2>{{ $section_process->title }}</h2>
                        <p>{!! strip_tags($section_process->description) !!}</p>
                    </div>

                    <div class="svc-log">
                        @foreach($processes as $key => $process)
                            <div class="svc-log-item svc-reveal" style="animation-delay:{{ $key * .07 }}s">
                                <div class="svc-log-node">#{{ sprintf('%02d', $key + 1) }}</div>
                                <div class="svc-log-body">
                                    <h4>{{ $process->title }}</h4>
                                    <div class="desc">{!! $process->description !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ================= ENGAGEMENT MODELS ================= -->
        <section class="svc-section">
            <div class="svc-wrap">
                <div class="svc-head">
                    <span class="svc-eyebrow">choose_engagement.model</span>
                    <h2>Flexible ways to work together</h2>
                    <p>Pick the structure that fits your budget and how defined your scope already is.</p>
                </div>
                <div class="svc-plans">
                    <div class="svc-plan">
                        <div class="flag">--fixed-price</div>
                        <h4>Fixed Price</h4>
                        <p class="desc">A locked scope and quote for projects with a clear brief.</p>
                        <ul>
                            <li>Defined deliverables upfront</li>
                            <li>Single milestone payment plan</li>
                            <li>Best for short, well-scoped builds</li>
                        </ul>
                        <a href="{{ route('contact') ?? '#' }}">Contact us for details</a>
                    </div>
                    <div class="svc-plan featured">
                        <div class="flag">--milestone</div>
                        <h4>Milestone-Based</h4>
                        <p class="desc">Break the roadmap into phases and pay as each one ships.</p>
                        <ul>
                            <li>Pay only as milestones complete</li>
                            <li>Room to refine scope mid-project</li>
                            <li>Best for evolving product builds</li>
                        </ul>
                        <a href="{{ route('contact') ?? '#' }}">Contact us for details</a>
                    </div>
                    <div class="svc-plan">
                        <div class="flag">--retainer</div>
                        <h4>Monthly Support</h4>
                        <p class="desc">Ongoing capacity for ship-and-maintain product teams.</p>
                        <ul>
                            <li>Reserved hours every month</li>
                            <li>No re-hiring or ramp-up cost</li>
                            <li>Best for long-term partnerships</li>
                        </ul>
                        <a href="{{ route('contact') ?? '#' }}">Share your requirements</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= TESTIMONIAL ================= -->
        <section class="svc-quote">
            <div class="svc-wrap">
                <div class="svc-quote-mark">&gt;&gt;</div>
                <blockquote>We don't just ship projects. We build the software your business will still be proud of five
                    years from now.</blockquote>
                <cite>— MSN SoftTech</cite>
            </div>
        </section>


    </div>

@endsection