@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('portfolio');
@endphp
@if (isset($header))

    @section('title', $portfolio->title)

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
        <meta property='og:title' content="{{ $portfolio->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@HiTechParks" />
        <meta name="twitter:url" content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta name="twitter:title" content="{{ $portfolio->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />
    @endif
@endsection

@section('content')

    @php
        $screenshotImage = json_decode($portfolio->screenshot ?? '[]', true) ?? [];
        $results_steps = json_decode($portfolio->results_steps ?? '[]', true) ?? [];
        $initials = strtoupper(substr($portfolio->title, 0, 2));
    @endphp

    <style>
        :root {
            --ink: #0f0e0d;
            --paper: #ffffff;
            --white: #ffffff;
            --soft: #f7f5f3;
            --soft-2: #ececea;
            --line: #e2ded9;
            --muted: #5b564f;
            --faint: #9a948b;
            --accent: #e2231a;
            --accent-2: #a3150d;
            --green: #1F9D6B;
            --radius: 20px;
            --ease: cubic-bezier(.22, 1, .36, 1);
            --proj1: #95BF47;
            --proj2: #1F9D6B;
        }

        .pj-main * {
            box-sizing: border-box;
        }

        .pj-main {
            background: var(--paper);
            color: var(--ink);
            font-family: "Inter", system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        .pj-main a {
            color: inherit;
            text-decoration: none;
        }

        .pj-main button {
            font: inherit;
        }

        .pj-main .wrap {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
        }

        .pj-main h1,
        .pj-main h2,
        .pj-main h3,
        .pj-main h4 {
            font-family: "Space Grotesk", system-ui, sans-serif;
            line-height: 1.05;
        }

        .pj-main p {
            color: var(--muted);
            line-height: 1.75;
        }

        .pj-main .mono {
            font-family: "JetBrains Mono", monospace;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .pj-main .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px 8px 13px;
            border-radius: 999px;
            background: rgba(226, 35, 26, .08);
            color: var(--accent-2);
            font-size: 13px;
            font-weight: 700;
        }

        .pj-main .eyebrow::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            flex: none;
        }

        .pj-main .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 48px;
            padding: 13px 22px;
            border-radius: 999px;
            border: 1px solid var(--ink);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            background: none;
            transition: transform .28s var(--ease), background-color .28s var(--ease), color .28s var(--ease), border-color .28s var(--ease);
        }

        .pj-main .btn:hover {
            transform: translateY(-2px);
        }

        .pj-main .btn-dark {
            background: var(--accent);
            color: var(--white);
            border-color: var(--accent);
        }

        .pj-main .btn-dark:hover {
            background: var(--accent-2);
            border-color: var(--accent-2);
        }

        .pj-main .btn-light {
            background: var(--white);
            color: var(--ink);
            border-color: var(--line);
        }

        .pj-main .btn-light:hover {
            border-color: var(--ink);
        }

        .pj-main section {
            padding: clamp(64px, 8vw, 100px) 0;
        }

        .pj-main .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .7s var(--ease), transform .7s var(--ease);
        }

        .pj-main .reveal.in {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============ BREADCRUMB ============ */
        .crumb-bar {
            border-bottom: 1px solid var(--line);
            padding: 18px 0;
        }

        .crumb-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .crumb-links {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--faint);
        }

        .crumb-links a {
            color: var(--muted);
            font-weight: 600;
            transition: color .2s var(--ease);
        }

        .crumb-links a:hover {
            color: var(--accent);
        }

        .crumb-links span {
            color: var(--ink);
            font-weight: 600;
        }

        .crumb-nav-arrows {
            display: flex;
            gap: 8px;
        }

        .crumb-arrow {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: border-color .2s var(--ease), color .2s var(--ease), transform .2s var(--ease);
        }

        .crumb-arrow:hover {
            border-color: var(--ink);
            color: var(--ink);
            transform: translateY(-2px);
        }

        .crumb-arrow svg {
            width: 15px;
            height: 15px;
        }

        /* ============ HERO ============ */
        .pj-hero {
            border-bottom: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff, var(--soft));
        }

        .pj-hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(30px, 5vw, 64px);
            align-items: center;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-live {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 13px;
            border-radius: 999px;
            background: rgba(31, 157, 107, .12);
            color: var(--green);
            font-family: "JetBrains Mono", monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            position: relative;
        }

        .status-dot::after {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1.2px solid var(--green);
            animation: pulseRing 1.8s ease-out infinite;
        }

        @keyframes pulseRing {
            0% {
                transform: scale(.6);
                opacity: .9;
            }

            100% {
                transform: scale(2.1);
                opacity: 0;
            }
        }

        .pj-hero h1 {
            font-size: clamp(38px, 5.6vw, 64px);
            margin-bottom: 16px;
        }

        .pj-hero-sub {
            font-size: 17px;
            max-width: 520px;
            margin-bottom: 30px;
        }

        .pj-hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1px;
            background: var(--line);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            max-width: 520px;
        }

        .meta-cell {
            background: var(--white);
            padding: 16px 18px;
        }

        .meta-cell span {
            display: block;
            font-size: 11px;
            color: var(--faint);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }

        .meta-cell b {
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: 16px;
        }

        /* device mockup */
        .device-wrap {
            position: relative;
        }

        .browser-frame {
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--white);
            box-shadow: 0 40px 90px rgba(15, 14, 13, .14);
            overflow: hidden;
        }

        .browser-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: var(--soft);
            border-bottom: 1px solid var(--line);
        }

        .browser-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--line);
        }

        .browser-dot:nth-child(1) {
            background: #ff6058;
        }

        .browser-dot:nth-child(2) {
            background: #ffbd2e;
        }

        .browser-dot:nth-child(3) {
            background: #28c940;
        }

        .browser-url {
            flex: 1;
            margin-left: 10px;
            padding: 6px 12px;
            border-radius: 8px;
            background: var(--white);
            font-family: "JetBrains Mono", monospace;
            font-size: 11px;
            color: var(--faint);
            border: 1px solid var(--line);
        }

        .browser-screen {
            height: 320px;
            background: linear-gradient(135deg, var(--proj1), var(--proj2));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .device-float {
            position: absolute;
            bottom: -30px;
            right: -24px;
            width: 130px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: var(--white);
            box-shadow: 0 30px 60px rgba(15, 14, 13, .18);
            overflow: hidden;
            animation: floaty 5s ease-in-out infinite;
        }

        @keyframes floaty {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .device-float .mini-screen {
            height: 220px;
            background: linear-gradient(160deg, var(--proj2), var(--proj1));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .device-float .mini-screen span {
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
        }

        .device-float .mini-bar {
            height: 24px;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 0 9px;
            position: relative;
        }

        .mini-time {
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: 9px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .01em;
            line-height: 1;
        }

        .mini-status {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #fff;
            line-height: 1;
        }

        .mi-signal {
            width: 11px;
            height: 8px;
            display: block;
        }

        .mi-wifi {
            width: 11px;
            height: 8px;
            display: block;
        }

        .mi-battery {
            display: flex;
            align-items: center;
        }

        .mi-battery-body {
            position: relative;
            width: 17px;
            height: 8px;
            border: 1px solid #fff;
            border-radius: 2.5px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
        }

        .mi-battery-fill {
            position: absolute;
            left: 1.4px;
            top: 1.4px;
            bottom: 1.4px;
            width: 65%;
            background: var(--green);
            border-radius: 1px;
        }

        .mi-bolt {
            position: relative;
            width: 6px;
            height: 7px;
            margin: 0 auto;
            color: var(--ink);
            z-index: 1;
        }

        .mi-battery-tip {
            width: 1.3px;
            height: 3.4px;
            background: #fff;
            border-radius: 0 1px 1px 0;
            margin-left: 1px;
        }

        /* ============ STICKY SUBNAV ============ */
        .subnav-wrap {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }

        .subnav-row {
            display: flex;
            gap: 4px;
            overflow-x: auto;
            padding: 14px 0;
            scrollbar-width: none;
        }

        .subnav-row::-webkit-scrollbar {
            display: none;
        }

        .subnav-link {
            white-space: nowrap;
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--muted);
            transition: background-color .2s var(--ease), color .2s var(--ease);
            flex: none;
        }

        .subnav-link:hover {
            background: var(--soft);
            color: var(--ink);
        }

        .subnav-link.active {
            background: var(--ink);
            color: #fff;
        }

        /* ============ OVERVIEW ============ */
        .overview-section {
            border-bottom: 1px solid var(--line);
        }

        .overview-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: clamp(30px, 5vw, 64px);
            align-items: start;
        }

        .overview-grid h2 {
            font-size: clamp(28px, 3.6vw, 40px);
            margin-bottom: 18px;
        }

        .overview-grid p {
            font-size: 16px;
            margin-bottom: 16px;
        }

        .overview-content p {
            font-size: 16px !important;
        }

        .challenge-box {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--soft);
            padding: 26px;
        }

        .challenge-box h3 {
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .challenge-box h3 svg {
            width: 20px;
            height: 20px;
            color: var(--accent);
        }

        .challenge-box p {
            font-size: 14.5px;
            margin-bottom: 0;
        }

        .goal-list {
            list-style: none;
            display: grid;
            gap: 12px;
            margin-top: 22px;
        }

        .goal-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14.5px;
            color: var(--ink);
        }

        .goal-list svg {
            width: 18px;
            height: 18px;
            color: var(--green);
            flex: none;
            margin-top: 2px;
        }

        /* ============ PROCESS TIMELINE ============ */
        .process-section {
            background: var(--soft);
            border-bottom: 1px solid var(--line);
        }

        .process-section .section-head {
            margin-bottom: 44px;
        }

        .section-head h2 {
            font-size: clamp(28px, 3.6vw, 42px);
            margin-top: 14px;
        }

        .timeline {
            position: relative;
            padding-left: 36px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 11px;
            top: 6px;
            bottom: 6px;
            width: 1px;
            background: var(--line);
        }

        .tl-item {
            position: relative;
            padding-bottom: 38px;
        }

        .tl-item:last-child {
            padding-bottom: 0;
        }

        .tl-dot {
            position: absolute;
            left: -36px;
            top: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--white);
            border: 2px solid var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tl-dot span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
        }

        .tl-item h4 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .tl-item .tl-date {
            font-family: "JetBrains Mono", monospace;
            font-size: 11px;
            color: var(--faint);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 8px;
            display: block;
        }

        .tl-item p {
            font-size: 14.5px;
            max-width: 600px;
        }

        /* ============ GALLERY ============ */
        .gallery-section {
            border-bottom: 1px solid var(--line);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .gallery-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            background: var(--white);
            transition: transform .3s var(--ease), box-shadow .3s var(--ease);
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 50px rgba(15, 14, 13, .1);
        }

        .gallery-shot {
            height: 220px;
            position: relative;
            background-size: cover;
            background-position: center;
            background-color: var(--soft-2);
        }

        .gallery-label {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            border-top: 1px solid var(--line);
        }

        /* ============ RESULTS ============ */
        .results-section {
            background: var(--ink);
            color: #fff;
            border-bottom: 1px solid var(--line);
        }

        .results-section .section-head p {
            color: #cfcfca;
        }

        .results-section .eyebrow {
            background: rgba(255, 255, 255, .08);
            color: #e8e6e2;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border-top: 1px solid rgba(255, 255, 255, .2);
            border-left: 1px solid rgba(255, 255, 255, .2);
        }

        .result-cell {
            border-right: 1px solid rgba(255, 255, 255, .2);
            border-bottom: 1px solid rgba(255, 255, 255, .2);
            padding: 28px 22px;
        }

        .result-cell .result-icon {
            font-size: 1.8rem;
            color: #ff8f88;
            margin-bottom: 14px;
        }

        .result-cell b {
            display: block;
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: 19px;
        }

        .result-cell span {
            display: block;
            margin-top: 10px;
            font-size: 13px;
            color: #cfcfca;
            line-height: 1.6;
        }

        /* ============ TECH STACK ============ */
        .tech-section {
            border-bottom: 1px solid var(--line);
        }

        .tech-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tech-chip {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 11px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--white);
            font-size: 13.5px;
            font-weight: 600;
            transition: border-color .2s var(--ease), transform .2s var(--ease);
        }

        .tech-chip:hover {
            border-color: var(--ink);
            transform: translateY(-2px);
        }

        /* ============ TESTIMONIAL ============ */
        .testimonial-section {
            background: linear-gradient(180deg, var(--soft), #ffffff);
            border-bottom: 1px solid var(--line);
        }

        .testimonial-box {
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            padding: 0 20px;
        }

        .testimonial-box svg.quote-mark {
            width: 40px;
            height: 40px;
            color: rgba(226, 35, 26, .18);
            margin: 0 auto 18px;
        }

        .testimonial-box p {
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: clamp(20px, 2.6vw, 28px);
            color: var(--ink);
            line-height: 1.5;
            font-weight: 500;
            margin-bottom: 26px;
        }

        .testimonial-person {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .testimonial-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--proj1), var(--proj2));
            color: #fff;
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-weight: 700;
        }

        .testimonial-person div {
            text-align: left;
        }

        .testimonial-person b {
            display: block;
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: 15px;
        }

        .testimonial-person span {
            font-size: 12.5px;
            color: var(--faint);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 960px) {
            .pj-hero-grid {
                grid-template-columns: 1fr;
            }

            .pj-hero-grid .device-wrap {
                order: -1;
                max-width: 480px;
                margin: 0 auto;
            }

            .overview-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {

            .gallery-grid,
            .results-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .browser-screen {
                height: 260px;
            }

            .device-float {
                width: 110px;
                right: -14px;
                bottom: -20px;
            }

            .device-float .mini-screen {
                height: 185px;
            }
        }

        @media (max-width: 560px) {
            .meta-grid {
                grid-template-columns: 1fr;
                max-width: 100%;
            }

            .browser-screen {
                height: 220px;
            }

            .device-float {
                width: 88px;
                right: -8px;
                bottom: -14px;
                border-radius: 14px;
            }

            .device-float .mini-screen {
                height: 148px;
            }

            .device-float .mini-bar {
                height: 18px;
                padding: 0 6px;
            }

            .mini-time {
                font-size: 7.5px;
            }

            .mi-wifi {
                display: none;
            }

            .mi-signal {
                width: 9px;
                height: 7px;
            }

            .mi-battery-body {
                width: 14px;
                height: 7px;
            }

            .pj-hero-actions,
            .status-row {
                gap: 8px;
            }

            .btn {
                min-height: 44px;
                padding: 11px 18px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {

            .gallery-grid,
            .results-grid {
                grid-template-columns: 1fr;
            }

            .device-wrap {
                padding-right: 0;
            }

            .device-float {
                width: 74px;
                right: 4px;
                bottom: -10px;
            }

            .device-float .mini-screen {
                height: 124px;
            }

            .device-float .mini-bar {
                height: 16px;
                padding: 0 5px;
            }

            .mini-time {
                font-size: 6.5px;
            }

            .mi-signal {
                display: none;
            }

            .mi-battery-body {
                width: 12px;
                height: 6px;
            }

            .mi-bolt {
                width: 5px;
                height: 6px;
            }
        }
    </style>

    <div class="pj-main">
        <main>
            <!-- HERO -->
            <section class="pj-hero">
                <div class="wrap pj-hero-grid">
                    <div class="reveal">
                        <div class="status-row">
                            <span class="status-live"><span class="status-dot"></span>Live</span>
                            @if ($portfolio->technologies->count() || $portfolio->categories->count())
                                <span class="eyebrow mono">
                                    @if ($portfolio->technologies->count())
                                        {{-- {{ $portfolio->technologies->pluck('short_title')->join(', ') }} --}}
                                    @endif

                                    @if ($portfolio->categories->count())
                                        {{-- {{ $portfolio->categories->pluck('title')->join(', ') }} --}}
                                    @endif
                                </span>
                            @endif
                        </div>
                        <h1>{{ $portfolio->title }}</h1>
                        <p class="pj-hero-sub">{{ $portfolio->sub_title }}</p>
                        <div class="pj-hero-actions">
                            @if (!empty($portfolio->link))
                                <a href="{{ $portfolio->link }}" target="_blank" rel="noopener" class="btn btn-dark">Visit
                                    Live Site
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M7 17L17 7" />
                                        <path d="M7 7h10v10" />
                                    </svg>
                                </a>
                            @endif
                            <a href="{{ route('portfolios') }}" wire:navigate class="btn btn-light">Back to Our Work</a>
                        </div>
                        <div class="meta-grid">
                            <div class="meta-cell"><span>Client</span><b>{{ $portfolio->client }}</b></div>
                            <div class="meta-cell"><span>Date</span><b>{{ $portfolio->date }}</b></div>
                            @if ($portfolio->categories->count())
                                <div class="meta-cell">
                                    <span>Category</span><b>{{ $portfolio->categories->pluck('title')->join(', ') }}</b>
                                </div>
                            @endif
                            @if ($portfolio->technologies->count())
                                <div class="meta-cell">
                                    <span>Technologies</span><b>{{ $portfolio->technologies->pluck('short_title')->join(', ') }}</b>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="device-wrap reveal">
                        <div class="browser-frame">
                            <div class="browser-bar">
                                <span class="browser-dot"></span><span class="browser-dot"></span><span
                                    class="browser-dot"></span>
                                <span
                                    class="browser-url">{{ $portfolio->link ? parse_url($portfolio->link, PHP_URL_HOST) : $portfolio->title }}</span>
                            </div>
                            <div class="browser-screen"
                                style="background-image:url('{{ asset('uploads/portfolio/' . $portfolio->image_path) }}');">
                            </div>
                        </div>
                        <div class="device-float">
                            <div class="mini-bar">
                                <span class="mini-time">9:41</span>
                                <span class="mini-status">
                                    <svg class="mi-signal" viewBox="0 0 14 10" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect x="0" y="6" width="2.4" height="4" rx="0.5" />
                                        <rect x="3.8" y="4" width="2.4" height="6" rx="0.5" />
                                        <rect x="7.6" y="2" width="2.4" height="8" rx="0.5" />
                                        <rect x="11.4" y="0" width="2.4" height="10" rx="0.5" opacity=".4" />
                                    </svg>
                                    <svg class="mi-wifi" viewBox="0 0 14 10" fill="none" stroke="currentColor"
                                        stroke-width="1.3" stroke-linecap="round" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5 4.2a8 8 0 0 1 11 0" />
                                        <path d="M3.6 6.6a5 5 0 0 1 6.8 0" />
                                        <circle cx="7" cy="9" r=".9" fill="currentColor"
                                            stroke="none" />
                                    </svg>
                                    <span class="mi-battery">
                                        <span class="mi-battery-body">
                                            <span class="mi-battery-fill"></span>
                                            <svg class="mi-bolt" viewBox="0 0 8 10" fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.6 0 0 5.6h2.6L2 10l4.4-5.8H4z" />
                                            </svg>
                                        </span>
                                        <span class="mi-battery-tip"></span>
                                    </span>
                                </span>
                            </div>
                            <div class="mini-screen"
                                style="background-image:url('{{ asset('uploads/overview_image/' . $portfolio->overview_image) }}')">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- STICKY SUBNAV -->
            <div class="subnav-wrap">
                <div class="wrap subnav-row" id="subnavRow">
                    <a href="#overview" class="subnav-link active">Overview</a>
                    <a href="#process" class="subnav-link">Process</a>
                    @if (!empty($screenshotImage))
                        <a href="#gallery" class="subnav-link">Gallery</a>
                    @endif
                    @if (!empty($results_steps))
                        <a href="#results" class="subnav-link">Results</a>
                    @endif
                    @if ($portfolio->technologies->count())
                        <a href="#tech" class="subnav-link">Tech Stack</a>
                    @endif
                    <a href="#testimonial" class="subnav-link">Client Feedback</a>
                    {{-- <a href="#team" class="subnav-link">Team</a> --}}
                </div>
            </div>

            <!-- OVERVIEW -->
            <section class="overview-section" id="overview">
                <div class="wrap overview-grid">
                    <div class="reveal">
                        <span class="eyebrow mono">Project Overview</span>
                        <h2 style="margin-top:16px;">Project details</h2>
                        <div class="overview-content">
                            {!! $portfolio->description !!}
                        </div>
                    </div>
                    @if (!empty($portfolio->link2))
                        <div class="challenge-box reveal">
                            <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 9v4" />
                                    <path d="M12 17h.01" />
                                    <path
                                        d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" />
                                </svg>The Challenge</h3>
                            <p>{{ $portfolio->link2 }}</p>

                        </div>
                    @endif
                </div>
            </section>

            <!-- PROCESS TIMELINE (demo — no matching Laravel data) -->
            <section class="process-section" id="process">
                <div class="wrap">
                    <div class="section-head reveal">
                        <span class="eyebrow mono">How we got there</span>
                        <h2>Our process, step by step.</h2>
                    </div>
                    <div class="timeline reveal">
                        <div class="tl-item">
                            <span class="tl-dot"><span></span></span>
                            <span class="tl-date">Phase 1 · Discovery</span>
                            <h4>Demo step title</h4>
                            <p>Demo placeholder description for this phase.</p>
                        </div>
                        <div class="tl-item">
                            <span class="tl-dot"><span></span></span>
                            <span class="tl-date">Phase 2 · Design</span>
                            <h4>Demo step title</h4>
                            <p>Demo placeholder description for this phase.</p>
                        </div>
                        <div class="tl-item">
                            <span class="tl-dot"><span></span></span>
                            <span class="tl-date">Phase 3 · Build</span>
                            <h4>Demo step title</h4>
                            <p>Demo placeholder description for this phase.</p>
                        </div>
                        <div class="tl-item">
                            <span class="tl-dot"><span></span></span>
                            <span class="tl-date">Phase 4 · Launch</span>
                            <h4>Demo step title</h4>
                            <p>Demo placeholder description for this phase.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GALLERY -->
            @if (!empty($screenshotImage))
                <section class="gallery-section" id="gallery">
                    <div class="wrap">
                        <div class="section-head reveal">
                            <span class="eyebrow mono">A closer look</span>
                            <h2>Key screens from the project.</h2>
                        </div>
                        <div class="gallery-grid">
                            @foreach ($screenshotImage as $item)
                                <div class="gallery-card reveal">
                                    <div class="gallery-shot"
                                        style="background-image:url('{{ asset('uploads/screenshot/' . $item['screenshot_image']) }}');">
                                    </div>
                                    <div class="gallery-label">Project Screenshot</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- RESULTS -->
            @if (!empty($results_steps))
                <section class="results-section" id="results">
                    <div class="wrap">
                        <div class="section-head reveal">
                            <span class="eyebrow mono">The Outcome</span>
                            <h2>Results & Impact</h2>
                        </div>
                        <div class="results-grid">
                            @foreach ($results_steps as $item)
                                <div class="result-cell reveal">
                                    <div class="result-icon"><i class="{{ $item['icon_class'] }}"></i></div>
                                    <b>{{ $item['title'] }}</b>
                                    <span>{{ $item['description'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- TECH STACK -->
            @if ($portfolio->technologies->count())
                <section class="tech-section" id="tech">
                    <div class="wrap">
                        <div class="section-head reveal">
                            <span class="eyebrow mono">Built with</span>
                            <h2>The stack behind the build.</h2>
                        </div>
                        <div class="tech-chips reveal">
                            @foreach ($portfolio->technologies as $tech)
                                <span class="tech-chip">{{ $tech->short_title }}</span>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- TESTIMONIAL (demo — no matching Laravel data) -->
            <section class="testimonial-section" id="testimonial">
                <div class="wrap">
                    <div class="testimonial-box reveal">
                        <svg class="quote-mark" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M9.5 4C6 4 3 7 3 11.5S6 19 9.5 19c.3 0 .5-.2.5-.5V15c0-.3-.2-.5-.5-.5C7.6 14.5 6 12.9 6 11c0-.1 0-.2 0-.3C6.8 11 7.5 11.3 8.3 11.3 10 11.3 11.3 10 11.3 8.3S10 5.3 8.3 5.3M19.5 4C16 4 13 7 13 11.5S16 19 19.5 19c.3 0 .5-.2.5-.5V15c0-.3-.2-.5-.5-.5-1.9 0-3.5-1.6-3.5-3.5 0-.1 0-.2 0-.3.8.3 1.5.6 2.3.6 1.7 0 3-1.3 3-3S21.2 5.3 19.5 5.3" />
                        </svg>
                        <p>"Demo testimonial text — replace with a real client quote about this project."</p>
                        <div class="testimonial-person">
                            <span class="testimonial-avatar">{{ $portfolio->client }}</span>
                            <div><b>{{ $portfolio->client }}</b>
                                {{-- <span>Role, </span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </main>
    </div>

    <script>
        function initPortfolioSingle() {
            // পুরনো পেজের window scroll listener জমে থাকতে পারে, তাই আগে সরিয়ে নিচ্ছি
            if (window._pjSubnavScrollHandler) {
                window.removeEventListener("scroll", window._pjSubnavScrollHandler);
            }

            /* reveal on scroll */
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
            document.querySelectorAll(".pj-main .reveal:not(.in)").forEach(function(el, index) {
                el.style.transitionDelay = (index % 4 * 0.06) + "s";
                observer.observe(el);
            });

            /* scroll-spy subnav */
            var subnavLinks = document.querySelectorAll(".subnav-link");
            var trackedSections = Array.prototype.map.call(subnavLinks, function(link) {
                return document.getElementById(link.getAttribute("href").slice(1));
            }).filter(Boolean);

            if (!subnavLinks.length) return;

            function updateSubnav() {
                var currentId = trackedSections[0] ? trackedSections[0].id : null;
                trackedSections.forEach(function(sec) {
                    var rect = sec.getBoundingClientRect();
                    if (rect.top <= 120) currentId = sec.id;
                });
                subnavLinks.forEach(function(link) {
                    link.classList.toggle("active", link.getAttribute("href") === "#" + currentId);
                });
            }

            window._pjSubnavScrollHandler = updateSubnav;
            window.addEventListener("scroll", updateSubnav, {
                passive: true
            });
            updateSubnav();
        }

        // wire:navigate দিয়ে portfolio-single পেজে বারবার এলেও ঠিকভাবে re-init হবে
        document.addEventListener('DOMContentLoaded', initPortfolioSingle);
        document.addEventListener('livewire:navigated', initPortfolioSingle);
    </script>

@endsection
