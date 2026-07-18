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
        .about-hero-section {
            position: relative;
            overflow: hidden;
            padding: 90px 0 60px;
            background: #0c0503;
            color: #ffffff;
            font-family: "Space Grotesk", sans-serif;
        }

        /* red radial glow - top left, like the screenshot */
        .about-hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 10% 10%, rgba(210, 36, 29, 0.55) 0%, rgba(210, 36, 29, 0.15) 35%, transparent 60%),
                linear-gradient(135deg, #1a0705 0%, #0c0503 55%, #050202 100%);
            z-index: 0;
        }

        /* subtle grid overlay */
        .about-hero-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 28px 28px;
            z-index: 0;
        }

        .about-hero-inner {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        /* top chip/badge: "$ ./contact --init" */
        .about-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            padding: 8px 18px;
            border: 1px solid rgba(210, 36, 29, 0.4);
            border-radius: 30px;
            background: rgba(210, 36, 29, 0.08);
            color: #e7c9c7;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .about-hero-chip .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #D2241D;
            box-shadow: 0 0 8px 2px rgba(210, 36, 29, 0.7);
        }

        /* Main heading */
        .about-hero-inner h1 {
            margin: 0 0 18px;
            font-size: clamp(40px, 6vw, 68px);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.1;
            color: #ffffff;
        }

        .about-hero-inner h1 .hero-accent {
            color: #D2241D;
        }

        /* small red underline like the screenshot */
        .about-hero-inner h1::after {
            content: "";
            display: block;
            width: 70px;
            height: 3px;
            margin: 20px auto 0;
            background: #D2241D;
            border-radius: 2px;
        }

        .about-hero-inner p {
            max-width: 620px;
            margin: 24px auto 0;
            color: rgba(255, 255, 255, 0.65);
            font-size: 16px;
            line-height: 1.8;
        }

        /* CTA buttons */
        .about-hero-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 32px;
        }

        .about-hero-btn {
            padding: 12px 26px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .about-hero-btn.is-primary {
            background: #D2241D;
            color: #ffffff;
            border: 1px solid #D2241D;
        }

        .about-hero-btn.is-primary:hover {
            background: #b81e18;
        }

        .about-hero-btn.is-outline {
            background: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .about-hero-btn.is-outline:hover {
            border-color: #D2241D;
            color: #D2241D;
        }

        /* breadcrumb: Home / About Us style */
        .about-hero-crumb {
            margin-top: 30px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .about-hero-crumb a {
            color: #ffffff;
            text-decoration: none;
        }

        .about-hero-crumb .sep {
            margin: 0 8px;
            color: rgba(255, 255, 255, 0.35);
        }

        .about-hero-crumb .current {
            color: #D2241D;
            font-weight: 600;
        }

        /* bottom info bar (email / phone / location) */
        .about-hero-infobar {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 50px;
            padding: 24px 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(4px);
        }

        .about-hero-infobar>div {
            text-align: center;
        }

        .about-hero-infobar .label {
            display: block;
            margin-bottom: 6px;
            color: rgba(255, 255, 255, 0.45);
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .about-hero-infobar .value {
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }

        .about-hero-infobar .value:hover {
            color: #D2241D;
        }

        @media (max-width: 767px) {
            .about-hero-infobar {
                grid-template-columns: 1fr;
                text-align: left;
            }

            .about-hero-actions {
                flex-direction: column;
                align-items: center;
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