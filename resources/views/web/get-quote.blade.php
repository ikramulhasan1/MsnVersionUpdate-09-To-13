@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('get-quote');
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
<<<<<<< HEAD
  <style>
    table {
      width: px;
    }

    table,
    table th,
    table td {
      border: solid;
    }

    table th,
    table td {
      border: solid;
    }

    table th>ol>li,
    table td>ul>li,
    table th>ul>li,
    table td>ol>li {
      list-style: initial !important;
      margin-left: 20px;

    }

    .marker {
      background-color: yellow;
    }

    /* </p><table border="1" cellpadding="1" cellspacing="1" style="width:500px">  */

    .description>ul>li {
      margin-left: 30px !important;
      list-style: initial;
      font-size: 16px !important;
    }


    .description>ol>li {
      /* list-style: decimal; */
      margin-left: 30px !important;
      all: revert;
      font-size: 16px !important;
    }

    .description>p>a {
      color: blue;
      font-weight: bold;
      text-decoration: underline;
    }

    .description>p {
      font-size: 18px !important;
    }




    /* process section */
    /* process */
    .process-section {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9fafc;
      padding: 80px 15px;
      /* padding-top: 50px !important;
                      padding-bottom: 50px !important; */
    }

    .process-section-title {
      text-align: left;
      margin-bottom: 40px;
    }

    .process-section-title h2 {
      font-weight: 900;
      color: #333333;
    }

    .process-step-box {
      background-color: #fff;
      border: 1px solid #e1e1e1;
      border-radius: 2px;
      padding: 30px 20px;
      height: 100%;
      position: relative;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
      transition: all 0.5s ease;
    }

    .process-step-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .process-step-number {
      width: 35px;
      height: 35px;
      background-color: #052C58;
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 16px;
      position: absolute;
      top: -20px;
      left: 20px;
    }

    .process-step-icon {
      width: 30px;
      height: 30px;
      margin-right: 10px;
    }

    .process-step-heading {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 10px;
    }

    .process-step-heading img {
      margin-left: 0px;
    }

    .process-step-arrow {
      position: absolute;
      top: 50%;
      right: -40px;
      width: 40px;
      height: 2px;
      background: repeating-linear-gradient(to right,
          #999,
          #999 4px,
          transparent 4px,
          transparent 8px);
      animation: moveArrow 1s linear infinite;
    }

    .process-step-arrow::after {
      content: '';
      position: absolute;
      right: -6px;
      top: -4px;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 6px solid #999;
    }

    @keyframes moveArrow {
      0% {
        background-position: 0;
      }

      100% {
        background-position: 8px;
      }
    }

    .process-btn-orange {
      background-color: #052C58;
      color: white;
      padding: 12px 26px;
      border-radius: 5px;
      font-weight: 600;
      text-transform: uppercase;
      border: none;
    }

    .process-btn-orange:hover {
      background-color: #052C58;
      color: white
    }

    @media (max-width: 991px) {
      .process-step-arrow {
        display: none;
      }
    }

    .process-step-arrow.arrow-hidden {
      display: none !important;
    }

    .arrow-down {
      /* Customize this arrow to look like a vertical one */
      transform: rotate(90deg);
      /* or use a different SVG for down */
      /* Add margin or position tweaks as needed */
    }

    .process-description p {
      font-size: 16px !important;
      color: #333333 !important;

    }




    * {
      box-sizing: border-box;
    }

    .quoteFormSection {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(120deg, #F5F7F8, #F5F7F8);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
    }

    .quote-container {
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(20px);
      border-radius: 5px;
      box-shadow: 0 0px 1px rgba(0, 0, 0, 0.2);
      padding: 50px;
      width: 100%;
      max-width: 960px;
      color: #333;
    }

    .quote-container h2 {
      text-align: center;
      margin-bottom: 40px;
      font-size: 32px;
      font-weight: 600;
      color: #222;
    }

    .quote-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px 30px;
    }

    .quote-input,
    .quote-textarea,
    select {
      width: 100%;
      padding: 5px 12px;
      border-radius: 2px;
      border: 1px solid #ddd;
      font-size: 15px;
      background-color: rgba(255, 255, 255, 0.6);
      transition: all 0.3s ease;
    }

    .quote-input:focus,
    .quote-textarea:focus,
    select:focus {
      outline: none;
      border-color: #052C58;
      background-color: #fff;
    }

    .quote-textarea {
      grid-column: 1 / -1;
      resize: vertical;
      min-height: 120px;
    }

    .quote-full-width {
      grid-column: 1 / -1;
    }

    .quote-radio-group {
      grid-column: 1 / -1;
      display: flex;
      gap: 40px;
      margin-top: -10px;
    }

    .quote-radio-group label {
      font-size: 14px;
    }

    .quote-radio-group .quote-input {
      margin-right: 6px;
    }

    .quote-services {
      grid-column: 1 / -1;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .quote-services label {
      background-color: #f0f0f0;
      padding: 5px 20px;
      border-radius: 30px;
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      transition: all 0.3s ease;
      border: 1px solid #ccc;
    }

    .quote-services .quote-input {
      display: none;
    }

    .quote-services .quote-input:checked+label {
      background-color: #052C58;
      color: #fff;
      border-color: #052C58;
    }

    .quote-submit-btn {
      grid-column: 1 / -1;
      padding: 7px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 2px;
      background-color: #052C58;
      color: #fff;
      cursor: pointer;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    .quote-submit-btn:hover {
      background-color: #193B62;
    }

    @media (max-width: 768px) {
      .quote-form {
        grid-template-columns: 1fr;
      }

      .quote-radio-group {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>

  <style>
    /* Hero Section */
    .about-hero-section {
      /* background: linear-gradient(135deg, rgba(106,17,203,0.9), rgba(37,117,252,0.9)), url('//images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center/cover; */
      background-color: #052C58;
      height: 40vh;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .about-hero-section h1 {
      font-size: 60px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .about-hero-section p {
      font-size: 22px;
      max-width: 700px;
      margin: 0 auto;
      opacity: 0.9;
    }




    /*  */
    .quote-services {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      position: relative;
    }

    .service-item {
      position: relative;
      z-index: 1;
    }

    .service-label {
      display: inline-block;
      padding: 10px 18px;
      border-radius: 25px;
      background-color: #f0f0f0;
      color: #333;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .service-checkbox:checked+.service-label {
      background-color: #052C58;
      color: #fff;
    }

    /* Subservices dropdown */
    .subservices {
=======

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <style>
    /* ==========================================================
       GET A QUOTE — v2, aligned to the MSN SoftTech site system:
       dark navy/teal hero, orange CTAs, "$ eyebrow" labels,
       rounded card language borrowed from Home/About.
       Scoped to .gq-scope so nothing leaks into the rest of the site.
       ========================================================== */
    .gq-scope {
      --navy-950: #070c14;
      --navy-900: #0c1626;
      --navy-800: #142238;
      --navy-700: #1b2c48;
      --paper: #f7f7f4;
      --paper-alt: #eef0ea;
      --ink: #12181f;
      --ink-soft: #5c6672;
      --ink-invert-soft: rgba(255,255,255,.66);
      --orange: #f5a623;
      --orange-dark: #d98c0f;
      --teal: #2fd6c0;
      --teal-dim: rgba(47,214,192,.14);
      --line: #e3e6df;
      --line-dark: rgba(255,255,255,.12);
      --danger: #d9483f;
      --ok: #22b378;
      --radius: 16px;

      font-family: 'Inter', sans-serif;
      color: var(--ink);
      background: var(--paper);
    }

    .gq-scope * { box-sizing: border-box; }

    .gq-display { font-family: 'Space Grotesk', sans-serif; }

    .gq-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--orange);
      margin-bottom: 18px;
    }
    .gq-eyebrow::before {
      content: '$';
      font-family: 'JetBrains Mono', monospace;
      font-weight: 500;
      color: var(--teal);
    }

    /* ---------- hero / info + form split ---------- */
    .gq-hero {
      display: grid;
      grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
      align-items: stretch;
    }

    @media (max-width: 991px) {
      .gq-hero { grid-template-columns: 1fr; }
    }

    /* ----- left: dark info panel ----- */
    .gq-canvas {
      position: relative;
      background:
        radial-gradient(720px 480px at 15% 15%, rgba(47,214,192,.16), transparent 60%),
        radial-gradient(640px 420px at 90% 85%, rgba(245,166,35,.10), transparent 55%),
        linear-gradient(180deg, var(--navy-950), var(--navy-900));
      color: #fff;
      padding: 88px 56px 64px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    .gq-canvas h1 {
      font-size: clamp(30px, 3.4vw, 44px);
      font-weight: 700;
      line-height: 1.16;
      margin-bottom: 18px;
      max-width: 480px;
    }

    .gq-canvas .gq-sub {
      font-size: 16px;
      line-height: 1.7;
      color: var(--ink-invert-soft);
      max-width: 440px;
    }
    .gq-canvas .gq-sub * { color: inherit !important; font-size: inherit !important; }
    .gq-canvas .gq-sub a { color: var(--teal) !important; font-weight: 600; text-decoration: underline; }

    /* trust strip — mirrors the homepage impact-numbers pattern */
    .gq-trust {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--line-dark);
      border: 1px solid var(--line-dark);
      border-radius: 12px;
      overflow: hidden;
      margin-top: 34px;
      max-width: 460px;
    }
    .gq-trust-item {
      background: rgba(255,255,255,.03);
      padding: 16px 14px;
    }
    .gq-trust-item strong {
      display: block;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 22px;
      color: var(--teal);
    }
    .gq-trust-item span {
      display: block;
      font-size: 11px;
      color: var(--ink-invert-soft);
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-top: 4px;
    }

    /* "what happens next" — rounded cards w/ ghost numeral, borrows Our Mission/Vision pattern */
    .gq-next {
      margin-top: 40px;
      display: flex;
      flex-direction: column;
      gap: 14px;
    }
    .gq-next-card {
      position: relative;
      background: var(--navy-800);
      border: 1px solid var(--line-dark);
      border-radius: var(--radius);
      padding: 18px 20px;
      overflow: hidden;
    }
    .gq-next-card .gq-ghost-num {
      position: absolute;
      right: 14px;
      top: -6px;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 46px;
      font-weight: 700;
      color: rgba(255,255,255,.05);
      line-height: 1;
    }
    .gq-next-card .gq-num-badge {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      background: var(--teal-dim);
      color: var(--teal);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'JetBrains Mono', monospace;
      font-size: 13px;
      margin-bottom: 10px;
    }
    .gq-next-card h4 {
      font-size: 15px;
      font-weight: 600;
      color: #fff;
      margin-bottom: 4px;
      position: relative;
    }
    .gq-next-card p {
      font-size: 13.5px;
      color: var(--ink-invert-soft);
      line-height: 1.55;
      margin: 0;
      position: relative;
    }

    .gq-canvas-foot {
      margin-top: 44px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 12.5px;
      color: var(--ink-invert-soft);
    }
    .gq-canvas-foot .gq-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: var(--teal);
      box-shadow: 0 0 0 4px rgba(47,214,192,.18);
    }

    /* ----- right: form panel ----- */
    .gq-form-panel {
      background: #fff;
      padding: 70px 60px 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    @media (max-width: 1200px) { .gq-form-panel { padding: 56px 42px; } }
    @media (max-width: 991px) { .gq-canvas, .gq-form-panel { padding: 52px 28px; } }
    @media (max-width: 560px) { .gq-canvas, .gq-form-panel { padding: 42px 18px; } }

    .gq-sheet-head { margin-bottom: 28px; }
    .gq-sheet-head h2 {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 6px;
      color: var(--ink);
    }
    .gq-sheet-head p {
      font-size: 14px;
      color: var(--ink-soft);
      margin: 0;
    }

    .gq-alert {
      border: 1px solid var(--line);
      border-left: 3px solid var(--ok);
      background: #f2faf6;
      padding: 12px 40px 12px 16px;
      font-size: 14px;
      margin-bottom: 20px;
      position: relative;
      border-radius: 8px;
    }
    .gq-alert.alert-danger { border-left-color: var(--danger); background: #fdf3f2; }
    .gq-alert ul { margin: 0; padding-left: 18px; }
    .gq-alert .close {
      position: absolute; right: 10px; top: 8px;
      font-size: 18px; line-height: 1; color: var(--ink-soft); opacity: .7;
    }

    .gq-form { display: flex; flex-direction: column; gap: 28px; }

    .gq-group-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--ink);
      text-transform: uppercase;
      letter-spacing: .03em;
      margin-bottom: 14px;
    }

    .gq-fields {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px 18px;
    }
    @media (max-width: 620px) { .gq-fields { grid-template-columns: 1fr; } }

    .gq-field { position: relative; }
    .gq-field.gq-full { grid-column: 1 / -1; }

    .gq-field label {
      display: block;
      font-size: 11.5px;
      color: var(--ink-soft);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .03em;
    }

    .gq-input, .gq-textarea, .gq-select {
      width: 100%;
      border: 1.5px solid var(--line);
      border-radius: 10px;
      background: var(--paper);
      padding: 12px 14px;
      font-size: 14.5px;
      font-family: 'Inter', sans-serif;
      color: var(--ink);
      transition: border-color .2s ease, background .2s ease;
    }
    .gq-input:focus, .gq-textarea:focus, .gq-select:focus {
      outline: none;
      border-color: var(--teal);
      background: #fff;
    }
    .gq-input::placeholder, .gq-textarea::placeholder { color: #9aa2a8; }
    .gq-textarea { resize: vertical; min-height: 110px; }

    /* prefer contact */
    .gq-radio-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .gq-radio {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      cursor: pointer;
      border: 1.5px solid var(--line);
      border-radius: 999px;
      padding: 9px 16px;
      background: var(--paper);
      transition: border-color .2s ease, background .2s ease;
    }
    .gq-radio:has(input:checked) {
      border-color: var(--navy-900);
      background: var(--navy-900);
      color: #fff;
    }
    .gq-radio input {
      appearance: none;
      width: 14px; height: 14px;
      border-radius: 50%;
      border: 1.5px solid var(--line);
      position: relative;
      cursor: pointer;
      margin: 0;
      flex: none;
    }
    .gq-radio input:checked { border-color: var(--teal); background: var(--teal); }

    /* services as tags */
    .gq-services { display: flex; flex-wrap: wrap; gap: 10px; position: relative; }
    .gq-service { position: relative; }
    .gq-service-label {
      display: inline-block;
      padding: 9px 18px;
      border: 1.5px solid var(--line);
      border-radius: 999px;
      font-size: 13.5px;
      font-weight: 500;
      cursor: pointer;
      transition: all .2s ease;
      background: #fff;
    }
    .gq-service-input:checked + .gq-service-label {
      background: var(--navy-900);
      border-color: var(--orange);
      color: #fff;
    }
    .gq-subservices {
>>>>>>> e734773df (msn 2.0 theme change)
      display: none;
      flex-wrap: wrap;
      gap: 8px;
      position: absolute;
<<<<<<< HEAD
      top: 110%;
      left: 0;
      width: max-content;
      min-width: 280px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      z-index: 10;
    }

    .subservice-item {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 6px 10px;
      border: 1px solid #ddd;
      border-radius: 15px;
      background-color: #fafafa;
      transition: all 0.3s;
    }

    .subservice-item:hover {
      background-color: #e8f0fe;
    }

    .subservice-item label {
      border-radius: 20px;
      background: #f9f9f9;
      padding: 6px 12px;
      cursor: pointer;
    }

    .subservice-item input[type="checkbox"] {
      display: none;
    }

    .subservice-item input[type="checkbox"]:checked+label {
      background: #052C58;
      color: #fff;
    }
  </style>

  <section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>{{ __('Quote') }}</h1>
      <!-- <p>Building the Future of Technology and Business Innovation Together.</p> -->
    </div>
  </section>
  <section class="page-title p-0" style="background-color: black;">
    <div class="container d-flex" style="height: 40px; align-items: center; justify-content: flex-end;">
      <div class="inner-container clearfix">
        {{-- <div class="title-box">
          <h1>{{ __('navbar.contact') }}</h1>
        </div> --}}
        <div class="bread-crumb">
          <ul class="p-0">
            <li>{{ __('Quote') }}</li>
            <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  @php
    $section_getquote = \App\Models\Section::section('get-quote');
  @endphp
  @if(isset($section_getquote))
    <section class="quoteFormSection">
      <div class="text-center">
        <h2 style="font-weight: 800" class="mb-3">{{ $section_getquote->title }}</h2>
        <div class="text description mb-4 text-center">{!! $section_getquote->description !!}</div>

        {{-- message --}}
        <!-- Message Display -->
        @if(Session::has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
=======
      top: 112%;
      left: 0;
      width: max-content;
      max-width: 320px;
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 12px;
      box-shadow: 0 14px 34px rgba(7,12,20,.14);
      z-index: 10;
    }
    .gq-subservice { display: flex; align-items: center; }
    .gq-subservice input { display: none; }
    .gq-subservice label {
      font-size: 12.5px;
      padding: 7px 13px;
      border-radius: 999px;
      background: var(--paper-alt);
      cursor: pointer;
      margin: 0;
      white-space: nowrap;
    }
    .gq-subservice input:checked + label { background: var(--orange); color: #fff; }

    /* uploads */
    .gq-dropzone.dropzone {
      border: 1.5px dashed var(--line);
      border-radius: 12px;
      background: var(--paper);
      padding: 22px;
      min-height: auto;
      font-family: 'Inter', sans-serif;
    }
    .gq-dropzone.dropzone .dz-message { margin: 0; font-size: 14px; color: var(--ink-soft); }
    .gq-dropzone.dropzone .dz-message::before { content: '⤒ '; color: var(--orange-dark); }

    .gq-captcha { display: flex; }

    /* submit — matches the site's orange pill CTA */
    .gq-submit {
      align-self: flex-start;
      background: var(--orange);
      color: #fff;
      border: none;
      padding: 15px 32px;
      font-size: 14.5px;
      font-weight: 600;
      letter-spacing: .01em;
      border-radius: 999px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: background .2s ease, transform .2s ease;
    }
    .gq-submit:hover { background: var(--orange-dark); transform: translateY(-1px); }
    .gq-submit svg { transition: transform .25s ease; }
    .gq-submit:hover svg { transform: translateX(4px); }

    @media (max-width: 560px) { .gq-submit { width: 100%; justify-content: center; } }

    /* ==========================================================
       PROCESS — dark grid, matches "How We Make Work Successful"
       Wraps instead of squeezing into one row, so it never goes
       thin-and-tall regardless of how many steps there are.
       ========================================================== */
    .gq-process {
      background: var(--navy-950);
      padding: 96px 15px 104px;
    }
    .gq-process-head {
      max-width: 640px;
      margin: 0 auto 48px;
      text-align: center;
    }
    .gq-process-head .gq-eyebrow { justify-content: center; }
    .gq-process-head h2 {
      font-size: clamp(26px, 3vw, 36px);
      font-weight: 700;
      color: #fff;
    }

    .gq-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    .gq-grid-card {
      background: var(--navy-800);
      border: 1px solid var(--line-dark);
      border-radius: var(--radius);
      padding: 28px 24px;
      transition: transform .2s ease, border-color .2s ease;
    }
    .gq-grid-card:hover { transform: translateY(-3px); border-color: rgba(245,166,35,.4); }

    .gq-grid-num {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--orange);
      color: var(--navy-950);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: 14px;
      margin-bottom: 18px;
    }
    .gq-grid-card h3 {
      font-size: 17px;
      font-weight: 600;
      color: #fff;
      margin-bottom: 8px;
    }
    .gq-grid-card .gq-grid-desc, .gq-grid-card .gq-grid-desc p {
      font-size: 14.5px !important;
      color: var(--ink-invert-soft) !important;
      line-height: 1.6;
      margin: 0;
    }

    .gq-process-cta { text-align: center; margin-top: 50px; }
    .gq-process-cta a {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--orange);
      color: var(--navy-950);
      padding: 14px 30px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      transition: background .2s ease;
    }
    .gq-process-cta a:hover { background: var(--orange-dark); color: #fff; }
  </style>

  <div class="gq-scope">

    <section class="gq-hero" id="quoteHero">
      <div class="gq-canvas">
        <div>
          <div class="gq-eyebrow">Get a Quote</div>

          @if(isset($section_getquote))
            <h1 class="gq-display">{{ $section_getquote->title }}</h1>
            <div class="gq-sub">{!! $section_getquote->description !!}</div>
          @else
            <h1 class="gq-display">{{ __('Quote') }}</h1>
          @endif

          <div class="gq-trust">
            <div class="gq-trust-item"><strong>1 day</strong><span>Avg. first reply</span></div>
            <div class="gq-trust-item"><strong>3700+</strong><span>Projects shipped</span></div>
            <div class="gq-trust-item"><strong>56+</strong><span>Developers on call</span></div>
          </div>

          <div class="gq-next">
            <div class="gq-next-card">
              <span class="gq-ghost-num gq-display">01</span>
              <div class="gq-num-badge">01</div>
              <h4>We read every brief</h4>
              <p>A developer — not a salesperson — looks at what you've sent within one business day.</p>
            </div>
            <div class="gq-next-card">
              <span class="gq-ghost-num gq-display">02</span>
              <div class="gq-num-badge">02</div>
              <h4>You get a real quote</h4>
              <p>A clear scope, timeline, and price. No placeholder ranges, no follow‑up calls required to get one.</p>
            </div>
            <div class="gq-next-card">
              <span class="gq-ghost-num gq-display">03</span>
              <div class="gq-num-badge">03</div>
              <h4>We schedule the kickoff</h4>
              <p>Once you're happy with the plan, we lock a start date and assign your team.</p>
            </div>
          </div>
        </div>

        <div class="gq-canvas-foot">
          <span class="gq-dot"></span>
          <span>Currently accepting new projects</span>
        </div>
      </div>

      <div class="gq-form-panel">
        <div class="gq-sheet-head">
          <h2 class="gq-display">Project Brief</h2>
          <p>Tell us what you're trying to build — we'll reply with a clear scope, timeline, and a real quote.</p>
        </div>

        @if(Session::has('success'))
          <div class="gq-alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
>>>>>>> e734773df (msn 2.0 theme change)
            {{ Session::get('success') }}
          </div>
        @endif

<<<<<<< HEAD
        <!-- Message Display -->
        @if(Session::has('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
=======
        @if(Session::has('error'))
          <div class="gq-alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
>>>>>>> e734773df (msn 2.0 theme change)
            {{ Session::get('error') }}
          </div>
        @endif

<<<<<<< HEAD
        <!-- Error Display -->
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
=======
        @if ($errors->any())
          <div class="gq-alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
>>>>>>> e734773df (msn 2.0 theme change)
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

<<<<<<< HEAD

        <form id="quoteForm" class="quote-form" method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data" accept-charset="utf-8">
    @csrf
    <input type="hidden" name="work_model" value="{{ $work_model }}">
    <input type="hidden" name="work_scope" value="{{ $work_scope }}">

    <!-- Name, Email, Phone -->
    <input class="quote-input" type="text" name="name" placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
    <input class="quote-input" type="email" name="email" placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
    <input class="quote-input" type="tel" name="phone" placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>

    <!-- Company, Address, City -->
    <input class="quote-input" type="text" name="company" placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
    <input class="quote-input" type="text" name="address" placeholder="{{ __('form.address') }}" value="{{ old('address') }}" required>
    <input class="quote-input" type="text" name="city" placeholder="{{ __('form.city') }}" value="{{ old('city') }}" required>

    <!-- Prefer Contact -->
    <h6 style="text-align: left !important" for="prefer_contact">{{ __('form.prefer_contact') }}</h6>
    <div class="quote-radio-group">
        <label class="d-flex align-items-center">
            <input class="quote-input" type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact') == '1') checked @else checked @endif required>Email
        </label>
        <label class="d-flex align-items-center">
            <input class="quote-input" type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact') == '2') checked @endif required>Phone
        </label>
    </div>

    <!-- Services -->
    <h6 style="text-align: left !important">{{ __('form.services') }}</h6>
    <div class="quote-services">
        @foreach($services as $service)
        <div class="service-item position-relative">
            <input type="checkbox" class="quote-input service-checkbox d-none" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}">
            <label class="service-label" for="service-{{ $service->id }}">{{ $service->short_title }}</label>

            @if($service->subservices && $service->subservices->count() > 0)
            <div class="subservices shadow-sm" id="subservices-{{ $service->id }}">
                @foreach($service->subservices as $sub)
                <div class="subservice-item">
                    <input type="checkbox" name="sub_service[]" value="{{ $sub->short_title }}" id="sub-{{ $sub->id }}">
                    <label for="sub-{{ $sub->id }}">{{ $sub->short_title }}</label>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Message -->
    <textarea class="quote-textarea" name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>

    <!-- ✅ Dropzone Upload -->
    <div class="form-group">
        <label>Upload Files</label>
        <div id="quoteDropzone" class="dropzone border border-2 border-secondary rounded p-4 bg-light"></div>
    </div>

    <!-- Google reCAPTCHA -->
    <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
    @if ($errors->has('captcha'))
        <p class="text-danger">{{ $errors->first('captcha') }}</p>
    @endif

    <button class="quote-submit-btn" type="submit" name="submit-form">SUBMIT NOW</button>
</form>


      </div>
    </section>
  @endif


  @php
    $section_process = \App\Models\Section::section('process');
  @endphp

  @if(count($processes) > 0 && isset($section_process))
    {{-- process-section --}}
    <section class="process-section">
      <div class="container">
        <div class="process-section-title">
          <h2 style="padding-bottom: 30px !important">{{ $section_process->title }}</h2>
          {{-- <p class="text-muted">From research to testing, we ensure your design is intuitive, user-focused, and aligned
            with your goals.</p> --}}
        </div>

        <!-- First Row -->
        <div class="row g-4 mb-4">
          @foreach($processes as $key => $process)
            {{-- @foreach ($service->processworks as $key => $process) --}}
            <div class="col-md-4 mb-4">
              <div class="process-step-box">
                <div class="process-step-number">{{ $key + 1 }}</div>
                <div class="process-step-heading" style="font-size: 20px; color: #333333;">
                  {{-- <img style="width: 50px; height: 50px;" src="{{ asset('uploads/process/' . $process->image_path) }}"
                    class="process-step-icon" alt=""> --}}
                  {{ $process->title }}
                </div>

                <div class="process-description">
                  {!! $process->description !!}
                </div>
                {{-- Show arrow after every item except the last one --}}
                @php
                  $totalSteps = count($processes);
                  $showArrow = ($key != $totalSteps - 1); // hide arrow for last step
                @endphp

                <div
                  class="process-step-arrow d-none d-md-block 
                                                                        {{ $showArrow ? ($key == 2 ? 'arrow-down' : '') : 'arrow-hidden' }}">
                </div>
              </div>
            </div>
            {{-- @endforeach --}}
          @endforeach
        </div>

        <!-- CTA -->
        <div class="text-center mt-5">
          <a href="https://msnsofttech.com/get-quote" class="btn process-btn-orange">Get in Touch With Us →</a>
        </div>
      </div>
    </section>
  @endif

  {{--
  <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}
=======
        <form id="quoteForm" class="gq-form" method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
          <input type="hidden" name="work_model" value="{{ $work_model }}">
          <input type="hidden" name="work_scope" value="{{ $work_scope }}">

          <div>
            <div class="gq-group-label">Contact details</div>
            <div class="gq-fields">
              <div class="gq-field">
                <label for="q_name">{{ __('form.your_name') }}</label>
                <input class="gq-input" id="q_name" type="text" name="name" placeholder="Jane Cooper" value="{{ old('name') }}" required>
              </div>
              <div class="gq-field">
                <label for="q_email">{{ __('form.email_address') }}</label>
                <input class="gq-input" id="q_email" type="email" name="email" placeholder="jane@company.com" value="{{ old('email') }}" required>
              </div>
              <div class="gq-field">
                <label for="q_phone">{{ __('form.phone_no') }}</label>
                <input class="gq-input" id="q_phone" type="tel" name="phone" placeholder="+1 (___) ___ ____" value="{{ old('phone') }}" required>
              </div>
              <div class="gq-field">
                <label for="q_company">{{ __('form.company') }}</label>
                <input class="gq-input" id="q_company" type="text" name="company" placeholder="Optional" value="{{ old('company') }}">
              </div>
              <div class="gq-field">
                <label for="q_address">{{ __('form.address') }}</label>
                <input class="gq-input" id="q_address" type="text" name="address" placeholder="Street address" value="{{ old('address') }}" required>
              </div>
              <div class="gq-field">
                <label for="q_city">{{ __('form.city') }}</label>
                <input class="gq-input" id="q_city" type="text" name="city" placeholder="City" value="{{ old('city') }}" required>
              </div>
            </div>
          </div>

          <div>
            <div class="gq-group-label">{{ __('form.prefer_contact') }}</div>
            <div class="gq-radio-row">
              <label class="gq-radio">
                <input type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact') != '2') checked @endif required> Email
              </label>
              <label class="gq-radio">
                <input type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact') == '2') checked @endif required> Phone
              </label>
            </div>
          </div>

          <div>
            <div class="gq-group-label">{{ __('form.services') }}</div>
            <div class="gq-services">
              @foreach($services as $service)
                <div class="gq-service">
                  <input type="checkbox" class="gq-service-input" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}">
                  <label class="gq-service-label" for="service-{{ $service->id }}">{{ $service->short_title }}</label>

                  @if($service->subservices && $service->subservices->count() > 0)
                    <div class="gq-subservices" id="subservices-{{ $service->id }}">
                      @foreach($service->subservices as $sub)
                        <div class="gq-subservice">
                          <input type="checkbox" name="sub_service[]" value="{{ $sub->short_title }}" id="sub-{{ $sub->id }}">
                          <label for="sub-{{ $sub->id }}">{{ $sub->short_title }}</label>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>

          <div>
            <div class="gq-group-label">Tell us about the project</div>
            <div class="gq-field gq-full">
              <textarea class="gq-textarea" name="message" placeholder="What are you building? What does success look like?" required>{{ old('message') }}</textarea>
            </div>
          </div>

          <div>
            <div class="gq-group-label">Attachments</div>
            <div id="quoteDropzone" class="gq-dropzone dropzone"></div>
          </div>

          <div class="gq-captcha">
            <div>
              <div class="g-recaptcha mb-2" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              @if ($errors->has('captcha'))
                <p class="text-danger" style="font-size:13px;color:var(--danger);">{{ $errors->first('captcha') }}</p>
              @endif
            </div>
          </div>

          <button class="gq-submit" type="submit" name="submit-form">
            Send Project Brief
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 8H14M14 8L9 3M14 8L9 13" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </form>
      </div>
    </section>

    @php
      $section_process = \App\Models\Section::section('process');
    @endphp

    @if(count($processes) > 0 && isset($section_process))
      <section class="gq-process">
        <div class="container">
          <div class="gq-process-head">
            <div class="gq-eyebrow">How it works</div>
            <h2 class="gq-display">{{ $section_process->title }}</h2>
          </div>

          <div class="gq-grid">
            @foreach($processes as $key => $process)
              <div class="gq-grid-card">
                <div class="gq-grid-num gq-display">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
                <h3>{{ $process->title }}</h3>
                <div class="gq-grid-desc">{!! $process->description !!}</div>
              </div>
            @endforeach
          </div>

          <div class="gq-process-cta">
            <a href="#quoteHero">
              Back to the form
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M14 8H2M2 8L7 3M2 8L7 13" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          </div>
        </div>
      </section>
    @endif

  </div>

>>>>>>> e734773df (msn 2.0 theme change)
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {

      // Toggle subservice visibility when main service label is clicked
<<<<<<< HEAD
      $('.service-label').on('click', function (e) {
        e.preventDefault();

        let parent = $(this).closest('.service-item');
        let checkbox = parent.find('.service-checkbox');
        let subDiv = parent.find('.subservices');

        // If service has subservices
        if (subDiv.length > 0) {
          // Always keep service checked
          if (!checkbox.is(':checked')) {
            checkbox.prop('checked', true);
          }

          // Just toggle visibility of the dropdown
          if (subDiv.is(':visible')) {
            subDiv.stop(true, true).slideUp(300); // minimize
          } else {
            subDiv.stop(true, true).slideDown(300); // open
          }
        } else {
          // No subservices → toggle checkbox normally
=======
      $('.gq-service-label').on('click', function (e) {
        e.preventDefault();

        let parent = $(this).closest('.gq-service');
        let checkbox = parent.find('.gq-service-input');
        let subDiv = parent.find('.gq-subservices');

        if (subDiv.length > 0) {
          if (!checkbox.is(':checked')) {
            checkbox.prop('checked', true);
          }
          if (subDiv.is(':visible')) {
            subDiv.stop(true, true).slideUp(300);
          } else {
            subDiv.stop(true, true).slideDown(300);
          }
        } else {
>>>>>>> e734773df (msn 2.0 theme change)
          checkbox.prop('checked', !checkbox.prop('checked'));
        }
      });

<<<<<<< HEAD
      // Handle subservice checkbox changes
      $(document).on('change', '.subservice-item input[type="checkbox"]', function () {
        let parentService = $(this).closest('.service-item');
        let parentCheckbox = parentService.find('.service-checkbox');
        let subDiv = parentService.find('.subservices');

        // Keep parent checked if any subservice is checked
        if (parentService.find('.subservice-item input:checked').length > 0) {
          parentCheckbox.prop('checked', true);
        } else {
          // Optional: Uncheck parent only if all subservices unchecked
=======
      $(document).on('change', '.gq-subservice input[type="checkbox"]', function () {
        let parentService = $(this).closest('.gq-service');
        let parentCheckbox = parentService.find('.gq-service-input');
        let subDiv = parentService.find('.gq-subservices');

        if (parentService.find('.gq-subservice input:checked').length > 0) {
          parentCheckbox.prop('checked', true);
        } else {
>>>>>>> e734773df (msn 2.0 theme change)
          parentCheckbox.prop('checked', false);
          subDiv.stop(true, true).slideUp(300);
        }
      });

<<<<<<< HEAD
      // Optional: click outside to hide all subservice lists (keep selections)
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.service-item').length) {
          $('.subservices').slideUp(200);
=======
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.gq-service').length) {
          $('.gq-subservices').slideUp(200);
>>>>>>> e734773df (msn 2.0 theme change)
        }
      });
    });
  </script>
<<<<<<< HEAD
  <!-- ✅ Dropzone JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

<script>
document.addEventListener("DOMContentLoaded", function () {
  Dropzone.autoDiscover = false;

  const dzElem = document.getElementById("quoteDropzone");
  if (!dzElem) {
    console.error("Dropzone element not found!");
    return;
  }

  const quoteDropzone = new Dropzone(dzElem, {
    url: "{{ route('quote.upload') }}",
    paramName: "file",
    maxFilesize: 20, // MB
    acceptedFiles:
      ".jpg,.jpeg,.png,.gif,.svg,.webp,.pdf,.doc,.docx,.txt,.zip,.rar,.csv,.xls,.xlsx,.ppt,.pptx,.mp3,.avi,.mp4,.mpeg,.3gp",
    addRemoveLinks: true,
    dictDefaultMessage: "Drag and drop files here or click to upload",
    headers: {
      "X-CSRF-TOKEN": "{{ csrf_token() }}",
    },

    success: function (file, response) {
      if (response.file_name) {
        // Create a hidden input for this uploaded file
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "uploaded_files[]";
        hiddenInput.value = response.file_name;

        // Append it to the main form
        document.querySelector("#quoteForm").appendChild(hiddenInput);

        // Store reference to remove later if needed
        file._hiddenInput = hiddenInput;
      }
    },

    removedfile: function (file) {
      if (file.previewElement) file.previewElement.remove();
      if (file._hiddenInput) file._hiddenInput.remove();
    },

    error: function (file, response) {
      console.error("Dropzone error:", response);
      alert("File upload failed!");
    },
  });

  console.log("✅ Dropzone initialized successfully");
});
</script>

{{-- <script>
Dropzone.autoDiscover = false;

let uploadedFiles = []; // store file names returned from Laravel

const quoteDropzone = new Dropzone("#file-dropzone", {
    url: "{{ route('quote.upload') }}", // Laravel upload route
    paramName: "file", // matches $request->file('file')
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    acceptedFiles: ".jpg,.png,.pdf,.doc,.docx,.zip",

    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },

    success: function(file, response) {
        if (response.file_name) {
            uploadedFiles.push(response.file_name);
            // update hidden input field
            document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);
        }
    },

    removedfile: function(file) {
        // remove file preview
        file.previewElement.remove();
        // optionally remove file name from hidden input
        uploadedFiles = uploadedFiles.filter(name => name !== file.name);
        document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);
    }
});
</script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

@endsection
=======

  <!-- Dropzone -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      Dropzone.autoDiscover = false;

      const dzElem = document.getElementById("quoteDropzone");
      if (!dzElem) {
        console.error("Dropzone element not found!");
        return;
      }

      const quoteDropzone = new Dropzone(dzElem, {
        url: "{{ route('quote.upload') }}",
        paramName: "file",
        maxFilesize: 20,
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.svg,.webp,.pdf,.doc,.docx,.txt,.zip,.rar,.csv,.xls,.xlsx,.ppt,.pptx,.mp3,.avi,.mp4,.mpeg,.3gp",
        addRemoveLinks: true,
        dictDefaultMessage: "Drag files here, or click to browse",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },

        success: function (file, response) {
          if (response.file_name) {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "uploaded_files[]";
            hiddenInput.value = response.file_name;
            document.querySelector("#quoteForm").appendChild(hiddenInput);
            file._hiddenInput = hiddenInput;
          }
        },

        removedfile: function (file) {
          if (file.previewElement) file.previewElement.remove();
          if (file._hiddenInput) file._hiddenInput.remove();
        },

        error: function (file, response) {
          console.error("Dropzone error:", response);
          alert("File upload failed!");
        },
      });
    });
  </script>

@endsection
>>>>>>> e734773df (msn 2.0 theme change)
