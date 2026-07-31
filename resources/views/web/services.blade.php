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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .svc-scope {
            --svc-navy-deep: #1A0505;
            --svc-navy: #1A0505;
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
            /* overflow-x: hidden; */
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
            color: white !important;
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
            position: sticky;
            top: var(--svc-nav-offset, 0px);
            z-index: 40;
            background: #fff !important;
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

        /* ---- two-pane explorer: category tabs (top) -> sub-service list (left) -> detail (right) ---- */
        .sub_service-panels {
            display: flex;
            align-items: stretch;
            background: #fff !important;
            border: 1px solid var(--svc-line) !important;
            border-radius: var(--svc-radius) !important;
            overflow: hidden;
            box-shadow: 0 24px 50px -34px rgba(3, 24, 46, .35);
        }

        .sub_service-list-pane {
            display: none;
            width: 300px;
            flex-shrink: 0;
            background: #FAFBFD !important;
            border-right: 1px solid var(--svc-line) !important;
            max-height: 560px;
            overflow-y: auto;
        }

        .sub_service-list-pane.active {
            display: block;
            animation: sub_serviceFadeIn .35s ease forwards;
        }

        .sub_service-list {
            padding: 12px;
        }

        .sub_service-list-item {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            text-align: left;
            background: transparent !important;
            border: none;
            padding: 14px 16px !important;
            border-radius: 9px !important;
            margin-bottom: 4px;
            font-family: var(--svc-body) !important;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--svc-ink) !important;
            cursor: pointer;
            transition: background .18s ease, color .18s ease;
        }

        .sub_service-list-item:hover {
            background: #EFF2F7 !important;
        }

        .sub_service-list-item.active {
            background: var(--svc-navy) !important;
            color: #fff !important;
        }

        .sub_service-list-item svg {
            flex-shrink: 0;
            opacity: .55;
            transition: transform .18s ease, opacity .18s ease;
        }

        .sub_service-list-item.active svg,
        .sub_service-list-item:hover svg {
            opacity: 1;
        }

        .sub_service-list-item.active svg {
            transform: translateX(2px);
        }

        @keyframes sub_serviceFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sub_service-detail {
            flex: 1;
            min-width: 0;
            position: relative;
        }

        .sub_service-detail-pane {
            display: none;
            padding: 40px;
        }

        .sub_service-detail-pane.active {
            display: block;
            animation: sub_serviceFadeIn .4s ease forwards;
        }

        .sub_service-detail-row {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .sub_service-detail-text {
            flex: 1;
            min-width: 0;
        }

        .sub_service-detail-tag {
            display: inline-block;
            font-family: var(--svc-mono) !important;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--svc-teal) !important;
            background: var(--svc-teal-dim) !important;
            border: 1px solid rgba(227, 30, 36, .3);
            padding: 4px 12px !important;
            border-radius: 100px !important;
        }

        .sub_service-detail-text h3 {
            margin-top: 14px !important;
            font-size: clamp(19px, 2.4vw, 24px);
            font-weight: 700;
            line-height: 1.3;
            color: var(--svc-navy) !important;
        }

        .sub_service-detail-text p {
            margin-top: 12px !important;
            font-size: 14.5px;
            line-height: 1.75;
            color: var(--svc-slate) !important;
        }

        .sub_service-detail-link {
            margin-top: 22px !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--svc-teal) !important;
        }

        .sub_service-detail-link svg {
            transition: transform .18s ease;
        }

        .sub_service-detail-link:hover svg {
            transform: translateX(3px);
        }

        .sub_service-detail-media {
            width: 190px;
            flex-shrink: 0;
            aspect-ratio: 4/3;
            border-radius: 10px !important;
            overflow: hidden;
            background: #f5f5f5 !important;
            border: 1px solid var(--svc-line);
        }

        .sub_service-detail-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sub_service-empty-state {
            text-align: center;
            padding: 60px 20px;
            font-family: var(--svc-mono) !important;
            font-size: 14px;
            color: #aaa !important;
        }

        @media (max-width:900px) {
            .sub_service-panels {
                flex-direction: column;
            }

            .sub_service-list-pane {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--svc-line) !important;
                max-height: 260px;
            }

            .sub_service-detail-pane {
                padding: 28px;
            }

            .sub_service-filterbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width:560px) {
            .sub_service-detail-pane {
                padding: 22px;
            }

            .sub_service-detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }

            .sub_service-detail-media {
                width: 140px;
            }
        }

        /* ---- Industries We Serve + Our Commitment (below desc/image) ---- */
        .sub_service-extra {
            margin-top: 30px;
            padding-top: 26px;
            border-top: 1px dashed var(--svc-line);
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .sub_service-extra-block {
            width: 100%;
        }

        .sub_service-extra-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-family: var(--svc-mono) !important;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--svc-teal) !important;
        }

        .sub_service-extra-eyebrow::before {
            content: '';
            width: 14px;
            height: 1px;
            background: var(--svc-teal);
            display: inline-block;
        }

        .sub_service-extra-block h4 {
            margin-top: 8px !important;
            font-size: 16.5px;
            font-weight: 700;
            color: var(--svc-navy) !important;
        }

        /* Industries: outline pills */
        .sub_service-industries-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .sub_service-industry-pill {
            font-family: var(--svc-body) !important;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--svc-navy) !important;
            background: transparent !important;
            border: 1px solid var(--svc-line);
            padding: 7px 15px !important;
            border-radius: 100px !important;
            white-space: nowrap;
            transition: border-color .18s ease, color .18s ease, background .18s ease;
        }

        .sub_service-industry-pill:hover {
            border-color: var(--svc-teal);
            color: var(--svc-teal) !important;
            background: var(--svc-teal-dim) !important;
        }

        /* Our commitment */
        .sub_service-commitment-sub {
            margin-top: 10px !important;
            font-size: 14px !important;
            line-height: 1.75;
            color: var(--svc-slate) !important;
            padding-left: 16px;
            border-left: 2px solid var(--svc-teal);
        }

        @media (max-width:560px) {
            .sub_service-extra {
                gap: 24px;
                margin-top: 24px;
                padding-top: 20px;
            }

            .sub_service-industries-tags {
                gap: 7px;
            }

            .sub_service-industry-pill {
                font-size: 12px;
                padding: 6px 13px !important;
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

        /* ================= HOW WE WORK ================= */
        .svc-process {
            padding: 90px 0;
            background: var(--svc-bg) !important;
        }

        .svc-process-head {
            max-width: 680px;
            margin: 0 auto 50px;
            text-align: center;
        }

        .svc-process-head h2 {
            margin-top: 14px !important;
            font-size: clamp(26px, 3.2vw, 36px);
            font-weight: 700;
            color: var(--svc-navy) !important;
        }

        .svc-process-head p {
            margin-top: 12px !important;
            font-size: 15.5px;
            color: var(--svc-slate) !important;
            line-height: 1.7;
        }

        .svc-process-track {
            position: relative;
            display: flex;
            gap: 22px;
        }

        .svc-process-track::before {
            content: '';
            position: absolute;
            top: 26px;
            left: 4%;
            right: 4%;
            height: 2px;
            background: repeating-linear-gradient(90deg,
                    var(--svc-line) 0,
                    var(--svc-line) 8px,
                    transparent 8px,
                    transparent 16px);
        }

        .svc-process-step {
            position: relative;
            flex: 1;
            min-width: 0;
            background: var(--svc-card) !important;
            border: 1px solid var(--svc-line);
            border-radius: var(--svc-radius) !important;
            padding: 26px 20px;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .svc-process-step:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 46px -28px rgba(3, 24, 46, .35);
            border-color: rgba(227, 30, 36, .3);
        }

        .svc-process-num {
            position: relative;
            z-index: 2;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--svc-mono) !important;
            font-size: 17px;
            font-weight: 700;
            color: var(--svc-navy) !important;
            background: #fff !important;
            border: 2px solid var(--svc-navy);
            border-radius: 50%;
        }

        .svc-process-step:hover .svc-process-num {
            color: #fff !important;
            background: var(--svc-teal) !important;
            border-color: var(--svc-teal);
        }

        .svc-process-step h4 {
            margin-top: 20px !important;
            font-size: 16.5px;
            font-weight: 700;
            color: var(--svc-navy) !important;
        }

        .svc-process-step p {
            margin-top: 10px !important;
            font-size: 13.5px;
            line-height: 1.65;
            color: var(--svc-slate) !important;
        }

        @media (max-width:991px) {
            .svc-process-track {
                flex-wrap: wrap;
            }

            .svc-process-track::before {
                display: none;
            }

            .svc-process-step {
                flex: 1 1 calc(50% - 11px);
            }
        }

        @media (max-width:560px) {
            .svc-process {
                padding: 60px 0;
            }

            .svc-process-step {
                flex: 1 1 100%;
            }
        }

        /* ================= WHY US / COMPARISON ================= */
        .svc-compare {
            padding: 90px 0;
            background: #fff !important;
        }

        .svc-compare-head {
            max-width: 620px;
            margin-bottom: 48px;
        }

        .svc-compare-head h2 {
            margin-top: 14px !important;
            font-size: clamp(28px, 3.4vw, 40px);
            font-weight: 700;
            line-height: 1.15;
            color: var(--svc-ink) !important;
        }

        .svc-compare-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 26px;
            align-items: stretch;
        }

        .svc-compare-card {
            border-radius: 18px !important;
            padding: 34px 30px;
        }

        .svc-compare-card.is-basic {
            background: #fff !important;
            border: 1px solid var(--svc-line);
        }

        .svc-compare-card.is-us {
            position: relative;
            background: var(--svc-ink) !important;
            box-shadow: 0 30px 60px -30px rgba(11, 18, 32, .5);
        }

        .svc-compare-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .svc-compare-card-head h3 {
            font-size: 20px;
            font-weight: 700;
        }

        .svc-compare-card.is-basic .svc-compare-card-head h3 {
            color: var(--svc-ink) !important;
        }

        .svc-compare-card.is-us .svc-compare-card-head h3 {
            color: #fff !important;
        }

        .svc-compare-badge {
            font-family: var(--svc-mono) !important;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .04em;
            padding: 6px 14px !important;
            border-radius: 100px !important;
            white-space: nowrap;
        }

        .svc-compare-badge.is-basic {
            color: var(--svc-slate) !important;
            background: var(--svc-bg) !important;
            border: 1px solid var(--svc-line);
        }

        .svc-compare-badge.is-us {
            color: #fff !important;
            background: var(--svc-teal) !important;
        }

        .svc-compare-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 15px 0;
            border-bottom: 1px solid var(--svc-line);
            font-size: 15px;
            line-height: 1.55;
        }

        .svc-compare-list li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .svc-compare-card.is-us .svc-compare-list li {
            border-bottom-color: rgba(255, 255, 255, .1);
        }

        .svc-compare-mark {
            flex-shrink: 0;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            margin-top: 1px;
        }

        .svc-compare-card.is-basic .svc-compare-mark {
            color: var(--svc-slate) !important;
            background: var(--svc-bg) !important;
            border: 1px solid var(--svc-line);
        }

        .svc-compare-card.is-us .svc-compare-mark {
            color: #fff !important;
            background: var(--svc-teal) !important;
        }

        .svc-compare-card.is-basic .svc-compare-list li span {
            color: var(--svc-slate) !important;
        }

        .svc-compare-card.is-basic .svc-compare-list li strong {
            color: var(--svc-ink) !important;
            font-weight: 600;
        }

        .svc-compare-card.is-us .svc-compare-list li strong {
            color: #fff !important;
            font-weight: 700;
        }

        .svc-compare-card.is-us .svc-compare-list li span {
            color: rgba(255, 255, 255, .55) !important;
        }

        @media (max-width:860px) {
            .svc-compare-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:560px) {
            .svc-compare {
                padding: 60px 0;
            }

            .svc-compare-card {
                padding: 26px 22px;
            }
        }

        /* ================= FAQ ================= */
        .svc-faq {
            padding: 90px 0;
            background: #fff !important;
        }

        .svc-faq-head {
            max-width: 680px;
            margin: 0 auto 44px;
            text-align: center;
        }

        .svc-faq-head h2 {
            margin-top: 14px !important;
            font-size: clamp(26px, 3.2vw, 36px);
            font-weight: 700;
            color: var(--svc-navy) !important;
        }

        .svc-faq-head p {
            margin-top: 12px !important;
            font-size: 15.5px;
            color: var(--svc-slate) !important;
            line-height: 1.7;
        }

        .svc-faq-list {
            max-width: 820px;
            margin: 0 auto;
        }

        .svc-faq-item {
            border: 1px solid var(--svc-line);
            border-radius: 12px !important;
            margin-bottom: 14px;
            overflow: hidden;
            background: var(--svc-card) !important;
            transition: border-color .2s ease;
        }

        .svc-faq-item.active {
            border-color: rgba(227, 30, 36, .35);
        }

        .svc-faq-q {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: transparent !important;
            border: none;
            text-align: left;
            padding: 20px 22px !important;
            cursor: pointer;
            font-family: var(--svc-body) !important;
            font-size: 15.5px;
            font-weight: 600;
            color: var(--svc-navy) !important;
        }

        .svc-faq-icon {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid var(--svc-line);
            position: relative;
            transition: background .2s ease, border-color .2s ease, transform .2s ease;
        }

        .svc-faq-icon::before,
        .svc-faq-icon::after {
            content: '';
            position: absolute;
            background: var(--svc-navy);
            transition: background .2s ease, transform .2s ease, opacity .2s ease;
        }

        .svc-faq-icon::before {
            width: 12px;
            height: 2px;
        }

        .svc-faq-icon::after {
            width: 2px;
            height: 12px;
        }

        .svc-faq-item.active .svc-faq-icon {
            background: var(--svc-teal) !important;
            border-color: var(--svc-teal);
        }

        .svc-faq-item.active .svc-faq-icon::before,
        .svc-faq-item.active .svc-faq-icon::after {
            background: #fff !important;
        }

        .svc-faq-item.active .svc-faq-icon::after {
            transform: rotate(90deg);
            opacity: 0;
        }

        .svc-faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
        }

        .svc-faq-a-inner {
            padding: 0 22px 20px;
            font-size: 14.5px;
            line-height: 1.75;
            color: var(--svc-slate) !important;
        }

        @media (max-width:560px) {
            .svc-faq {
                padding: 60px 0;
            }

            .svc-faq-q {
                font-size: 14.5px;
                padding: 16px 18px !important;
            }

            .svc-faq-a-inner {
                padding: 0 18px 16px;
                font-size: 14px;
            }
        }
    </style>

    <div class="svc-scope">

        {{-- ================= HERO ================= --}}
        <section class="svc-hero">
            <div class="svc-wrap">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-7 svc-hero-copy svc-reveal">
                        {{-- <span class="svc-eyebrow on-dark">./services --init</span> --}}
                        <h1 class="mt-3">
                            One Team. Every Digital Solution You Need.
                        </h1>
                        <p>Whatever your business needs to grow online, we've probably already built it for someone else.
                            Explore everything we offer, all under one roof.</p>
                        <div class="svc-hero-actions">
                            {{-- <a href="#svc-services-list" class="svc-btn svc-btn-primary">View Services</a> --}}
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
                            <a href="{{ route('home') }}" wire:navigate>{{ __('navbar.home') }}</a> /
                            {{ __('navbar.services') }}
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
            $allSubservices = collect();
            if (isset($services)) {
                foreach ($services as $service) {
                    foreach ($service->subservices as $sub) {
                        if (isset($sub->status) && $sub->status != 1) {
                            continue;
                        } // চাইলে বাদ দিন এই লাইন
                        $sub->parent_service = $service;
                        $allSubservices->push($sub);
                    }
                }
            }
        @endphp

        {{-- ================= SERVICE CATEGORIES -> SUB-SERVICE EXPLORER ================= --}}
        @if ($allSubservices->count() > 0)
            <section class="sub_service-section" id="sub_service-portfolio">
                <div class="svc-wrap">

                    <div class="sub_service-head">
                        {{-- <span class="svc-eyebrow">{{ __('dashboard.service_categories') }}.explore()</span> --}}
                        <h2 class="mt-3">Services At a Glance</h2>
                        <p>Pick a service category, then select a sub-service to see its details.</p>
                    </div>

                    {{-- Service Categories (top) --}}
                    <div class="sub_service-filterbar">
                        <div class="sub_service-tabs" id="sub_serviceTabs">
                            @foreach ($services as $service)
                                @if ($service->subservices->count() > 0)
                                    <button type="button" class="sub_service-tab {{ $loop->first ? 'active' : '' }}"
                                        data-category="{{ \Illuminate\Support\Str::slug($service->short_title) }}">
                                        {{ $service->short_title }}<i style="color: red" class="ms-2 fa-solid fa-down-long"></i>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Left: sub-services of the selected category | Right: detail (short desc + image) --}}
                    <div class="sub_service-panels" id="sub_servicePanels">

                        @foreach ($services as $service)
                            @if ($service->subservices->count() > 0)
                                <div class="sub_service-list-pane {{ $loop->first ? 'active' : '' }}"
                                    data-category-list="{{ \Illuminate\Support\Str::slug($service->short_title) }}">
                                    <ul class="sub_service-list">
                                        @foreach ($service->subservices as $sub)
                                            @if (!isset($sub->status) || $sub->status == 1)
                                                <li>
                                                    <button type="button"
                                                        class="sub_service-list-item {{ $loop->first ? 'active' : '' }}"
                                                        data-target="sub_service-detail-{{ $sub->id }}">
                                                        <span>{{ $sub->short_title }}</span>
                                                        <svg width="14" height="14" viewBox="0 0 24 24"
                                                            fill="none">
                                                            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </button>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach

                        <div class="sub_service-detail" id="sub_serviceDetail">
                            @foreach ($allSubservices as $key => $sub)
                                <div class="sub_service-detail-pane {{ $key == 0 ? 'active' : '' }}"
                                    id="sub_service-detail-{{ $sub->id }}">

                                    <div class="sub_service-detail-row">
                                        <div class="sub_service-detail-text">
                                            <span
                                                class="sub_service-detail-tag">{{ $sub->parent_service->short_title }}</span>
                                            <h3>{{ $sub->short_title }}</h3>
                                            @if (!empty($sub->short_desc))
                                                <p>{{ \Illuminate\Support\Str::words(strip_tags($sub->short_desc), 36) }}
                                                </p>
                                            @endif
                                            <a href="{{ route('service.related-single', $sub->slug) }}" wire:navigate
                                                class="sub_service-detail-link">
                                                Learn More
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        </div>

                                        <a href="{{ route('service.related-single', $sub->slug) }}" wire:navigate
                                            class="sub_service-detail-media">
                                            <img src="{{ asset('uploads/subservices/' . $sub->image_path) }}"
                                                alt="{{ $sub->short_title }}" loading="lazy">
                                        </a>
                                    </div>

                                    {{-- Industries We Serve + Our Commitment --}}
                                    <div class="sub_service-extra">
                                        <div class="sub_service-extra-block">
                                            {{-- <span class="sub_service-extra-eyebrow">Where We Work</span> --}}
                                            <h4>Industries We Serve</h4>
                                            <div class="sub_service-industries-tags">
                                                <span class="sub_service-industry-pill">E-commerce</span>
                                                <span class="sub_service-industry-pill">Healthcare</span>
                                                <span class="sub_service-industry-pill">Education</span>
                                                <span class="sub_service-industry-pill">Real Estate</span>
                                                <span class="sub_service-industry-pill">Finance &amp; Banking</span>
                                                <span class="sub_service-industry-pill">Retail</span>
                                                <span class="sub_service-industry-pill">Logistics</span>
                                                <span class="sub_service-industry-pill">Hospitality</span>
                                                <span class="sub_service-industry-pill">Manufacturing</span>
                                                <span class="sub_service-industry-pill">Technology</span>
                                                <span class="sub_service-industry-pill">Travel &amp; Tourism</span>
                                                <span class="sub_service-industry-pill">Non-Profit</span>
                                            </div>
                                        </div>

                                        <div class="sub_service-extra-block">
                                            <h4>Our Commitment</h4>
                                            <p class="sub_service-commitment-sub">Quality-first delivery, transparent
                                                communication, and long-term support — for every project, big or
                                                small.</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </section>
        @endif




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

        {{-- ================= HOW WE WORK ================= --}}
        <section class="svc-process">
            <div class="svc-wrap">
                <div class="svc-process-head">
                    {{-- <span class="svc-eyebrow">./how-we-work --run()</span> --}}
                    <h2>How We Work</h2>
                    <p>A clear, proven process that takes your idea from first conversation to a fully supported,
                        live product.</p>
                </div>

                <div class="svc-process-track">
                    <div class="svc-process-step">
                        <div class="svc-process-num">01</div>
                        <h4>Discover</h4>
                        <p>We learn about your business, audience, and goals to define exactly what success looks
                            like.</p>
                    </div>

                    <div class="svc-process-step">
                        <div class="svc-process-num">02</div>
                        <h4>Planning &amp; Design</h4>
                        <p>We map the strategy and craft the UI/UX so every screen has a clear purpose.</p>
                    </div>

                    <div class="svc-process-step">
                        <div class="svc-process-num">03</div>
                        <h4>Development</h4>
                        <p>Our engineers build your solution with clean, scalable, well-documented code.</p>
                    </div>

                    <div class="svc-process-step">
                        <div class="svc-process-num">04</div>
                        <h4>Testing &amp; Launch</h4>
                        <p>Rigorous QA across devices, then a smooth, monitored release to production.</p>
                    </div>

                    <div class="svc-process-step">
                        <div class="svc-process-num">05</div>
                        <h4>Support</h4>
                        <p>We stay on after launch with maintenance, updates, and ongoing improvements.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= WHY US / COMPARISON ================= --}}
        <section class="svc-compare">
            <div class="svc-wrap">
                <div class="svc-compare-head">
                    {{-- <span class="svc-eyebrow">./why --msn-softtech</span> --}}
                    <h2>What you get that a typical<br class="d-none d-md-block"> freelancer won't offer</h2>
                </div>

                <div class="svc-compare-grid">
                    <div class="svc-compare-card is-basic">
                        <div class="svc-compare-card-head">
                            <h3>Typical Freelancer</h3>
                            <span class="svc-compare-badge is-basic">BASIC</span>
                        </div>
                        <ul class="svc-compare-list">
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Custom Strategy &amp; Design</strong> — templates only</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Dedicated Project Team</strong> — one person</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Performance Optimization</strong> — rarely tested</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Security &amp; Data Protection</strong> — not managed</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Training &amp; Documentation</strong> — limited</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✕</span>
                                <span><strong>Ongoing Support</strong> — none</span>
                            </li>
                        </ul>
                    </div>

                    <div class="svc-compare-card is-us">
                        <div class="svc-compare-card-head">
                            <h3>MSN Softtech</h3>
                            <span class="svc-compare-badge is-us">RECOMMENDED</span>
                        </div>
                        <ul class="svc-compare-list">
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Custom Strategy &amp; Design</strong> — built for you</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Dedicated Project Team</strong> — full team on call</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Performance Optimization</strong> — benchmarked &amp; tuned</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Security &amp; Data Protection</strong> — actively managed</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Training &amp; Documentation</strong> — video guide included</span>
                            </li>
                            <li>
                                <span class="svc-compare-mark">✓</span>
                                <span><strong>Ongoing Support</strong> — 30 days free</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FAQ ================= --}}
        <section class="svc-faq" id="svc-faq">
            <div class="svc-wrap">
                <div class="svc-faq-head">
                    {{-- <span class="svc-eyebrow">./faqs --open()</span> --}}
                    <h2>Frequently Asked Questions</h2>
                    <p>Answers to the questions we hear most from clients before starting a project.</p>
                </div>

                <div class="svc-faq-list" id="svcFaqList">
                    <div class="svc-faq-item active">
                        <button type="button" class="svc-faq-q">
                            <span>How long does a typical project take?</span>
                            <span class="svc-faq-icon"></span>
                        </button>
                        <div class="svc-faq-a">
                            <div class="svc-faq-a-inner">Timelines depend on scope, but most projects move from
                                discovery to launch within 4-10 weeks. We'll give you a clear estimate after our
                                first discovery call.</div>
                        </div>
                    </div>

                    <div class="svc-faq-item">
                        <button type="button" class="svc-faq-q">
                            <span>How much does it cost to work with you?</span>
                            <span class="svc-faq-icon"></span>
                        </button>
                        <div class="svc-faq-a">
                            <div class="svc-faq-a-inner">Every project is scoped individually based on features,
                                complexity, and timeline. Share your requirements with us and we'll provide a
                                transparent, detailed quote.</div>
                        </div>
                    </div>

                    <div class="svc-faq-item">
                        <button type="button" class="svc-faq-q">
                            <span>Do you provide support after launch?</span>
                            <span class="svc-faq-icon"></span>
                        </button>
                        <div class="svc-faq-a">
                            <div class="svc-faq-a-inner">Yes. Every engagement includes a support period after
                                launch, and we offer ongoing maintenance plans for ongoing updates and improvements.
                            </div>
                        </div>
                    </div>

                    <div class="svc-faq-item">
                        <button type="button" class="svc-faq-q">
                            <span>Can you work with our existing codebase or design?</span>
                            <span class="svc-faq-icon"></span>
                        </button>
                        <div class="svc-faq-a">
                            <div class="svc-faq-a-inner">Absolutely. We regularly join projects mid-way, whether
                                that means extending an existing codebase or continuing from an established design
                                system.</div>
                        </div>
                    </div>

                    <div class="svc-faq-item">
                        <button type="button" class="svc-faq-q">
                            <span>How do we communicate during the project?</span>
                            <span class="svc-faq-icon"></span>
                        </button>
                        <div class="svc-faq-a">
                            <div class="svc-faq-a-inner">You'll have a dedicated point of contact and regular
                                progress updates through email, calls, or your preferred project management tool.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script>
        function initSubServiceExplorer() {
            var catTabs = document.querySelectorAll('#sub_serviceTabs .sub_service-tab');
            var listPanes = document.querySelectorAll('#sub_servicePanels .sub_service-list-pane');
            var detailPanes = document.querySelectorAll('#sub_serviceDetail .sub_service-detail-pane');

            if (!catTabs.length || !listPanes.length) return;

            function showDetail(targetId) {
                detailPanes.forEach(function(pane) {
                    pane.classList.toggle('active', pane.id === targetId);
                });
            }

            function bindListItems(listPane) {
                var items = listPane.querySelectorAll('.sub_service-list-item');
                items.forEach(function(item) {
                    item.addEventListener('click', function() {
                        items.forEach(function(it) {
                            it.classList.remove('active');
                        });
                        item.classList.add('active');
                        showDetail(item.getAttribute('data-target'));
                    });
                });
            }

            function activateFirstItem(listPane) {
                var items = listPane.querySelectorAll('.sub_service-list-item');
                items.forEach(function(it, idx) {
                    it.classList.toggle('active', idx === 0);
                });
                if (items.length) {
                    showDetail(items[0].getAttribute('data-target'));
                }
            }

            listPanes.forEach(bindListItems);

            catTabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    catTabs.forEach(function(t) {
                        t.classList.remove('active');
                    });
                    tab.classList.add('active');

                    var category = tab.getAttribute('data-category');

                    listPanes.forEach(function(pane) {
                        var match = pane.getAttribute('data-category-list') === category;
                        pane.classList.toggle('active', match);
                        if (match) activateFirstItem(pane);
                    });
                });
            });
        }

        function initSvcFaq() {
            var faqList = document.getElementById('svcFaqList');
            if (!faqList) return;

            // Guard: DOMContentLoaded ও livewire:navigated দুটোই fire হলে init দুইবার
            // চলে গিয়ে প্রতিটা বাটনে ডাবল click-listener বসে যাচ্ছিল, যার ফলে ক্লিক করলে
            // item খুলেই সাথে সাথে আবার বন্ধ হয়ে যাচ্ছিল ("off-on hoyna" বাগ)।
            // dataset flag দিয়ে দ্বিতীয়বার bind হওয়া আটকানো হলো।
            if (faqList.dataset.faqInit === '1') return;
            faqList.dataset.faqInit = '1';

            var items = faqList.querySelectorAll('.svc-faq-item');

            function openItem(item) {
                var answer = item.querySelector('.svc-faq-a');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                item.classList.add('active');
            }

            function closeItem(item) {
                var answer = item.querySelector('.svc-faq-a');
                answer.style.maxHeight = null;
                item.classList.remove('active');
            }

            items.forEach(function(item) {
                var q = item.querySelector('.svc-faq-q');
                if (item.classList.contains('active')) openItem(item);

                q.addEventListener('click', function() {
                    var isActive = item.classList.contains('active');
                    items.forEach(closeItem);
                    if (!isActive) openItem(item);
                });
            });
        }

        // wire:navigate-এর মাধ্যমে services পেজে বারবার আসা-যাওয়া করলেও এলিমেন্টগুলো
        // প্রতিবার নতুন করে DOM-এ বসে, তাই দুটো ইভেন্টেই bind করা হয়েছে
        document.addEventListener('DOMContentLoaded', initSubServiceExplorer);
        document.addEventListener('livewire:navigated', initSubServiceExplorer);
        document.addEventListener('DOMContentLoaded', initSvcFaq);
        document.addEventListener('livewire:navigated', initSvcFaq);

        function updateSvcNavOffset() {
            var navbar = document.getElementById('navbarWrap');
            if (navbar) {
                document.documentElement.style.setProperty('--svc-nav-offset', navbar.offsetHeight + 'px');
            }
        }
        document.addEventListener('DOMContentLoaded', updateSvcNavOffset);
        document.addEventListener('livewire:navigated', updateSvcNavOffset);
        window.addEventListener('load', updateSvcNavOffset);
        window.addEventListener('resize', updateSvcNavOffset);
        window.addEventListener('scroll', updateSvcNavOffset, {
            passive: true
        });
    </script>

@endsection
@section('scriptjs')
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
@endsection
