@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('contact-us');
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
    <style>
        .partner-section {
            padding: 60px 0;
            background-color: #F5F7F8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .partner-section h2 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            color: #333333;
        }

        /* ============ Hero Section — dark maroon, matches Services reference ============ */
        .about-hero-section {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(60% 65% at 15% 8%, rgba(210, 36, 29, .55) 0%, rgba(210, 36, 29, 0) 60%),
                linear-gradient(160deg, #3B0A0C 0%, #200507 45%, #0B0203 100%) !important;
            color: #fff;
            padding: 90px 0 96px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .about-hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .07) 1px, transparent 1px);
            background-size: 72px 72px;
            mask-image: radial-gradient(80% 80% at 25% 15%, #000 30%, transparent 90%);
            pointer-events: none;
        }

        .about-hero-inner {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }

        .about-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            letter-spacing: .04em;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .16);
            padding: 8px 16px;
            border-radius: 999px;
            color: rgba(255, 255, 255, .8);
            margin-bottom: 22px;
        }

        .about-hero-chip .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #EF4444;
            display: inline-block;
        }

        .about-hero-section h1 {
            font-size: 52px;
            font-weight: 700;
            line-height: 1.12;
            margin: 0 0 18px;
            color: #fff;
        }

        .about-hero-section h1 .hero-accent {
            color: #EF4444;
        }

        .about-hero-section p {
            font-size: 18px;
            line-height: 1.7;
            max-width: 560px;
            margin: 0 0 30px;
            opacity: .75;
            color: #fff;
        }

        .about-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 30px;
        }

        .about-hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 26px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: opacity .2s ease, border-color .2s ease;
        }

        .about-hero-btn.is-primary {
            background: #EF4444;
            color: #fff;
            box-shadow: 0 10px 24px -10px rgba(239, 68, 68, .6);
        }

        .about-hero-btn.is-primary:hover {
            opacity: .9;
            color: #fff;
        }

        .about-hero-btn.is-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, .4);
            color: #fff;
        }

        .about-hero-btn.is-outline:hover {
            border-color: #fff;
            color: #fff;
        }

        .about-hero-crumb {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: rgba(255, 255, 255, .5);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .about-hero-crumb a {
            color: #fff;
            text-decoration: none;
        }

        .about-hero-crumb a:hover {
            color: #EF4444;
        }

        .about-hero-crumb .sep {
            color: rgba(255, 255, 255, .3);
        }

        .about-hero-crumb .current {
            color: #EF4444;
        }

        /* contact-info bar, styled like the stat cards, overlapping the hero bottom */
        .about-hero-infobar {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            margin-top: 54px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 24px 50px -20px rgba(0, 0, 0, .5);
        }

        .about-hero-infobar>div {
            background: #fff;
            padding: 22px 24px;
        }

        .about-hero-infobar .label {
            display: block;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9CA3AF;
            margin-bottom: 8px;
        }

        .about-hero-infobar .value {
            display: block;
            font-size: 17px;
            font-weight: 700;
            color: #17181C;
        }

        .about-hero-infobar a.value {
            text-decoration: none;
            color: #17181C;
        }

        .about-hero-infobar a.value:hover {
            color: #DC2626;
        }

        @media (max-width: 768px) {
            .about-hero-section {
                padding: 60px 0 70px;
            }

            .about-hero-section h1 {
                font-size: 34px;
            }

            .about-hero-section p {
                font-size: 16px;
            }

            .about-hero-infobar {
                grid-template-columns: 1fr;
                margin-top: 36px;
            }
        }
    </style>
    <!-- Hero Section -->
    <section class="about-hero-section" data-aos="fade">
        <div class="container about-hero-inner">
            <span class="about-hero-chip"><span class="dot"></span>$ ./contact --init</span>

            <h1>Let's talk about <span class="hero-accent">your project</span>.</h1>
            <p>Tell us what you're building. We'll reply with next steps, a rough timeline, and someone who can actually
                answer your questions — no sales script.</p>

            <div class="about-hero-actions">
                <a href="tel:{{ $setting->phone ?? '' }}" class="about-hero-btn is-primary">Call Us</a>
                <a href="#contact-form" class="about-hero-btn is-outline">Send a Message</a>
            </div>

            <div class="about-hero-crumb">
                <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
                <span class="sep">/</span>
                <span class="current">{{ __('navbar.contact') }}</span>
            </div>
        </div>

        <div class="container">
            <div class="about-hero-infobar">
                <div>
                    <span class="label">Email</span>
                    <a class="value" href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '—' }}</a>
                </div>
                <div>
                    <span class="label">Phone</span>
                    <a class="value" href="tel:{{ $setting->phone ?? '' }}">{{ $setting->phone ?? '—' }}</a>
                </div>
                <div>
                    <span class="label">Location</span>
                    <span class="value">{{ $setting->address ?? '—' }}</span>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    <section>
        @include('web.inc.contact')
    </section>
    <!--End Contact Section -->

@endsection