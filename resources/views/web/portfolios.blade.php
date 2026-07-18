@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('portfolio');
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
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600&display=swap"
    rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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

    .description>ul>li {
      margin-left: 30px !important;
      list-style: initial;
      font-size: 16px !important;
    }

    .description>ol>li {
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

    /* ============ WORKS PAGE THEME ============ */
    #works-page {
      --ink: #0f0e0d;
      --paper: #ffffff;
      --white: #ffffff;
      --soft: #f7f5f3;
      --soft-2: #ececea;
      --line: #e2ded9;
      --muted: #5b564f;
      --faint: #9a948b;
      --accent: #D2241D;
      --accent-2: #10131A;
      --green: #1F9D6B;
      --radius: 20px;
      --ease: cubic-bezier(.22, 1, .36, 1);
      background: var(--paper);
      color: var(--ink);
      font-family: "Inter", system-ui, sans-serif;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    #works-page a {
      color: inherit;
      text-decoration: none;
    }

    #works-page button {
      font: inherit;
    }

    #works-page img {
      display: block;
      max-width: 100%;
    }

    #works-page .wrap {
      width: min(1180px, calc(100% - 40px));
      margin: 0 auto;
    }

    #works-page h1,
    #works-page h2,
    #works-page h3,
    #works-page h4 {
      font-family: "Space Grotesk", system-ui, sans-serif;
      line-height: 1.05;
    }

    #works-page p {
      color: var(--muted);
      line-height: 1.75;
    }

    #works-page .mono {
      font-family: "JetBrains Mono", monospace;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: .1em;
      text-transform: uppercase;
    }

    #works-page .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px 8px 13px;
      border-radius: 999px;
      background: #FDECEB;
      color: var(--accent-2);
      font-size: 13px;
      font-weight: 700;
    }

    #works-page .eyebrow::before {
      content: "";
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--accent);
      flex: none;
    }

    #works-page .btn {
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

    #works-page .btn:hover {
      transform: translateY(-2px);
    }

    #works-page .btn-dark {
      background: var(--accent);
      color: var(--white);
      border-color: var(--accent);
    }

    #works-page .btn-dark:hover {
      background: var(--accent-2);
      border-color: var(--accent-2);
    }

    #works-page .btn-light {
      background: var(--white);
      color: var(--ink);
      border-color: var(--line);
    }

    #works-page .btn-light:hover {
      border-color: var(--ink);
    }

    #works-page section {
      padding: clamp(64px, 8vw, 100px) 0;
    }

    #works-page .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity .7s var(--ease), transform .7s var(--ease);
    }

    #works-page .reveal.in {
      opacity: 1;
      transform: translateY(0);
    }

    /* ============ HERO — dark maroon (matches About page hero) ============ */
    #works-page .works-hero {
      position: relative;
      padding: clamp(60px, 8vw, 100px) 0 50px;
      overflow: hidden;
      border-bottom: none;
      background:
        radial-gradient(60% 55% at 15% 8%, rgba(210, 36, 29, .55) 0%, rgba(210, 36, 29, 0) 60%),
        linear-gradient(160deg, #3B0A0C 0%, #200507 45%, #0B0203 100%);
    }

    #works-page .works-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .07) 1px, transparent 1px);
      background-size: 72px 72px;
      opacity: 1;
      mask-image: linear-gradient(180deg, #000 0%, transparent 78%);
      pointer-events: none;
    }

    #works-page .works-hero-inner {
      position: relative;
      z-index: 1;
      max-width: 780px;
    }

    #works-page .works-hero .eyebrow {
      background: rgba(255, 255, 255, .07);
      border: 1px solid rgba(255, 255, 255, .16);
      color: rgba(255, 255, 255, .8);
    }

    #works-page .works-hero h1 {
      margin: 20px 0 16px;
      font-size: clamp(36px, 5.6vw, 60px);
      font-weight: 700;
      color: #fff;
    }

    #works-page .works-hero h1 span {
      color: #EF4444;
    }

    #works-page .works-hero p {
      font-size: clamp(15px, 1.5vw, 18px);
      max-width: 580px;
      color: rgba(255, 255, 255, .68);
    }

    #works-page .works-hero-stats {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      margin-top: 44px;
      border: 1px solid rgba(255, 255, 255, .12);
      background: rgba(255, 255, 255, .12);
      box-shadow: 0 24px 50px -20px rgba(0, 0, 0, .5);
    }

    #works-page .wstat {
      background: var(--white);
      padding: 20px 22px;
    }

    #works-page .wstat b {
      display: block;
      font-family: "Space Grotesk", system-ui, sans-serif;
      font-size: clamp(22px, 3vw, 32px);
      color: var(--accent);
    }

    #works-page .wstat span {
      display: block;
      margin-top: 6px;
      color: var(--muted);
      font-size: 12.5px;
    }

    /* breadcrumb, adapted to sit inside the dark hero */
    #works-page .works-breadcrumb {
      position: relative;
      z-index: 1;
      margin-top: 18px;
    }

    #works-page .works-breadcrumb ul {
      display: flex;
      gap: 8px;
      list-style: none;
      padding: 0;
      margin: 0;
      font-size: 13px;
      color: rgba(255, 255, 255, .45);
    }

    #works-page .works-breadcrumb ul li:not(:last-child)::after {
      content: "/";
      margin-left: 8px;
      color: rgba(255, 255, 255, .3);
    }

    #works-page .works-breadcrumb ul li a {
      color: rgba(255, 255, 255, .85);
      font-weight: 600;
    }

    #works-page .works-breadcrumb ul li a:hover {
      color: #EF4444;
    }

    /* optional: hero buttons, if you add them to the markup later */
    #works-page .works-hero .btn-dark {
      background: #EF4444;
      border-color: #EF4444;
    }

    #works-page .works-hero .btn-light {
      background: transparent;
      border-color: rgba(255, 255, 255, .4);
      color: #fff;
    }

    #works-page .works-hero .btn-light:hover {
      border-color: #fff;
    }

    /* ============ FILTER ============ */
    #works-page .filter-bar {
      position: sticky;
      top: 0;
      z-index: 20;
      background: rgba(255, 255, 255, .94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--line);
      padding: 16px 0;
    }

    #works-page .filter-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }

    #works-page .filter-btn {
      padding: 10px 16px;
      border-radius: 999px;
      border: 1px solid var(--line);
      background: var(--white);
      font-family: "Space Grotesk", system-ui, sans-serif;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--muted);
      cursor: pointer;
      transition: background-color .25s var(--ease), color .25s var(--ease), border-color .25s var(--ease);
    }

    #works-page .filter-btn:hover {
      border-color: var(--ink);
      color: var(--ink);
    }

    #works-page .filter-btn.active {
      background: var(--ink);
      border-color: var(--ink);
      color: #fff;
    }

    #works-page .filter-count {
      margin-left: auto;
      color: var(--faint);
      font-size: 13px;
      font-family: "JetBrains Mono", monospace;
    }

    /* ============ FEATURED SPOTLIGHT (demo template — no matching Laravel data) ============ */
    #works-page .spotlight-section {
      border-bottom: 1px solid var(--line);
      background: linear-gradient(180deg, #ffffff, var(--soft));
    }

    #works-page .spotlight-card {
      display: grid;
      grid-template-columns: 1.05fr 1fr;
      gap: 0;
      border: 1px solid var(--line);
      border-radius: 0px;
      overflow: hidden;
      background: var(--white);
      box-shadow: 0 30px 80px rgba(15, 14, 13, .08);
    }

    #works-page .spotlight-visual {
      position: relative;
      background: var(--soft);
      padding: 36px 36px 0;
      display: flex;
      align-items: flex-end;
      overflow: hidden;
    }

    #works-page .spotlight-browser {
      width: 100%;
      border-radius: 14px 14px 0 0;
      border: 1px solid var(--line);
      border-bottom: none;
      overflow: hidden;
      box-shadow: 0 -10px 40px rgba(15, 14, 13, .08);
    }

    #works-page .spotlight-bar {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 11px 14px;
      background: var(--white);
      border-bottom: 1px solid var(--line);
    }

    #works-page .spotlight-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }

    #works-page .spotlight-dot:nth-child(1) {
      background: #ff6058;
    }

    #works-page .spotlight-dot:nth-child(2) {
      background: #ffbd2e;
    }

    #works-page .spotlight-dot:nth-child(3) {
      background: #28c940;
    }

    #works-page .spotlight-url {
      flex: 1;
      margin-left: 8px;
      padding: 5px 10px;
      border-radius: 7px;
      background: var(--soft);
      font-family: "JetBrains Mono", monospace;
      font-size: 10.5px;
      color: var(--faint);
    }

    #works-page .spotlight-screen {
      height: 260px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    #works-page .spotlight-screen::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(rgba(255, 255, 255, .14) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .14) 1px, transparent 1px);
      background-size: 26px 26px;
      opacity: .5;
    }

    #works-page .spotlight-screen span {
      font-family: "Space Grotesk", system-ui, sans-serif;
      font-size: 52px;
      font-weight: 700;
      color: #fff;
      position: relative;
      z-index: 1;
    }

    #works-page .spotlight-body {
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    #works-page .spotlight-tag {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 16px;
    }

    #works-page .spotlight-live {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(31, 157, 107, .12);
      color: var(--green);
      font-family: "JetBrains Mono", monospace;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    #works-page .spotlight-dotpulse {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
      position: relative;
    }

    #works-page .spotlight-dotpulse::after {
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

    #works-page .spotlight-body h3 {
      font-size: clamp(26px, 3.4vw, 38px);
      margin-bottom: 12px;
    }

    #works-page .spotlight-body p {
      font-size: 15px;
      margin-bottom: 22px;
    }

    #works-page .spotlight-results {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--line);
      border: 1px solid var(--line);
      border-radius: 14px;
      overflow: hidden;
      margin-bottom: 26px;
    }

    #works-page .spotlight-results .r {
      background: var(--white);
      padding: 14px 12px;
    }

    #works-page .spotlight-results .r b {
      display: block;
      font-family: "Space Grotesk", system-ui, sans-serif;
      font-size: 20px;
      color: var(--accent);
    }

    #works-page .spotlight-results .r span {
      display: block;
      margin-top: 3px;
      font-size: 10.5px;
      color: var(--faint);
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    /* ============ WORKS GRID (dynamic — $portfolios) ============ */
    #works-page .works-grid-section {
      border-bottom: 1px solid var(--line);
    }

    #works-page .works-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }

    #works-page .work-card {
      display: block;
      border: 1px solid var(--line);
      border-radius: 0px;
      background: var(--white);
      overflow: hidden;
      transition: transform .32s var(--ease), box-shadow .32s var(--ease);
    }

    #works-page .work-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 26px 60px rgba(15, 14, 13, .1);
    }

    #works-page .wc-browser {
      border-bottom: 1px solid var(--line);
    }

    #works-page .wc-bar {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 9px 12px;
      background: var(--soft);
      border-bottom: 1px solid var(--line);
    }

    #works-page .wc-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--line);
    }

    #works-page .wc-url {
      flex: 1;
      margin-left: 6px;
      padding: 4px 8px;
      border-radius: 6px;
      background: var(--white);
      font-family: "JetBrains Mono", monospace;
      font-size: 9.5px;
      color: var(--faint);
      border: 1px solid var(--line);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    #works-page .wc-screen {
      height: 150px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    #works-page .wc-screen img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: relative;
      z-index: 1;
    }

    #works-page .wc-screen.no-image::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(rgba(255, 255, 255, .14) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .14) 1px, transparent 1px);
      background-size: 20px 20px;
      opacity: .5;
    }

    #works-page .wc-screen span {
      font-family: "Space Grotesk", system-ui, sans-serif;
      font-size: 30px;
      font-weight: 700;
      color: #fff;
      position: relative;
      z-index: 1;
    }

    #works-page .wc-info {
      padding: 20px;
    }

    #works-page .wc-info-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 4px;
    }

    #works-page .wc-info h3 {
      font-size: 18px;
    }

    #works-page .wc-arrow {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 1px solid var(--line);
      display: flex;
      align-items: center;
      justify-content: center;
      flex: none;
      transition: background-color .25s var(--ease), border-color .25s var(--ease), transform .25s var(--ease);
    }

    #works-page .wc-arrow svg {
      width: 13px;
      height: 13px;
      transition: transform .25s var(--ease);
    }

    #works-page .work-card:hover .wc-arrow {
      background: var(--ink);
      border-color: var(--ink);
    }

    #works-page .work-card:hover .wc-arrow svg {
      stroke: #fff;
      transform: rotate(45deg);
    }

    #works-page .wc-cat {
      color: var(--faint);
      font-size: 12px;
    }

    #works-page .works-empty {
      display: none;
      padding: 60px 22px;
      text-align: center;
      border: 1px dashed var(--line);
      border-radius: 16px;
      color: var(--muted);
      grid-column: 1/-1;
    }

    #works-page .works-empty.show {
      display: block;
    }

    /* ============ CTA (demo template — no matching Laravel data) ============ */
    #works-page .works-cta {
      padding: clamp(56px, 8vw, 92px) 0;
      text-align: center;
    }

    #works-page .works-cta-box {
      max-width: 900px;
      margin: 0 auto;
      padding: clamp(28px, 5vw, 54px);
      border: 1px solid var(--line);
      border-radius: 0px;
      background: linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(247, 245, 243, .96)), radial-gradient(circle at 14% 0%, rgba(226, 35, 26, .14), transparent 34%);
      box-shadow: 0 26px 80px rgba(15, 14, 13, .09);
    }

    #works-page .works-cta-box h2 {
      margin: 14px auto 18px;
      font-size: clamp(28px, 4.4vw, 46px);
      max-width: 660px;
    }

    #works-page .works-cta-box p {
      max-width: 540px;
      margin: 0 auto 26px;
      font-size: 16px;
    }

    @media (max-width:980px) {
      #works-page .works-hero-stats {
        grid-template-columns: repeat(2, 1fr);
      }

      #works-page .spotlight-card {
        grid-template-columns: 1fr;
      }

      #works-page .spotlight-visual {
        padding: 24px 24px 0;
      }

      #works-page .works-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width:640px) {
      #works-page .wrap {
        width: min(100% - 28px, 1180px);
      }

      #works-page .works-hero h1 {
        font-size: clamp(30px, 10vw, 44px);
      }

      #works-page .works-hero-stats {
        grid-template-columns: 1fr 1fr;
      }

      #works-page .works-grid {
        grid-template-columns: 1fr;
      }

      #works-page .spotlight-results {
        grid-template-columns: 1fr;
      }

      #works-page .filter-count {
        display: none;
      }
    }

    @media (prefers-reduced-motion:reduce) {

      #works-page *,
      #works-page *::before,
      #works-page *::after {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
      }
    }
  </style>

  <main id="works-page">

    <!-- HERO (title is dynamic, subtitle + stats are demo — no Laravel fields for these) -->
    <section class="works-hero" data-aos="fade">
      <div class="wrap works-hero-inner">
        <span class="eyebrow mono">{{ __('navbar.portfolios') }}</span>
        <h1>Real builds, <span>real numbers.</span></h1>
        <p>A running record of what we've shipped — live sites, apps, automations and the results each one produced after
          launch.</p>
        <div class="works-breadcrumb">
          <ul>
            <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
            <li>{{ __('navbar.portfolios') }}</li>
          </ul>
        </div>
      </div>
      <div class="wrap works-hero-stats">
        <div class="wstat"><b>3,700+</b><span class="mono">Projects Completed</span></div>
        <div class="wstat"><b>900+</b><span class="mono">Happy Clients</span></div>
        <div class="wstat"><b>25+</b><span class="mono">Countries Served</span></div>
        <div class="wstat"><b>4.9/5</b><span class="mono">Average Rating</span></div>
      </div>
    </section>

    @php
      $section_portfolio = \App\Models\Section::section('portfolio');
    @endphp

    @if(count($portfolios) > 0 && isset($section_portfolio))

      <!-- FILTER (dynamic — $portfolio_categories) -->
      <div class="filter-bar">
        <div class="wrap filter-row" id="filterRow">
          <button class="filter-btn active" data-filter="all">{{ __('common.all') }}</button>
          @foreach($portfolio_categories as $portfolio_category)
            <button class="filter-btn" data-filter="{{ $portfolio_category->slug }}">{{ $portfolio_category->title }}</button>
          @endforeach
          <span class="filter-count" id="filterCount"></span>
        </div>
      </div>

      <!-- FEATURED SPOTLIGHT — demo template, no matching Laravel section/data, left as-is -->
      <section class="spotlight-section">
        <div class="wrap">
          <div class="spotlight-card reveal">
            <div class="spotlight-visual">
              <div class="spotlight-browser">
                <div class="spotlight-bar">
                  <span class="spotlight-dot"></span><span class="spotlight-dot"></span><span class="spotlight-dot"></span>
                  <span class="spotlight-url">orendahome-demo.com</span>
                </div>
                <div class="spotlight-screen" style="background:linear-gradient(135deg,#95BF47,#1F9D6B);"><span>OH</span>
                </div>
              </div>
            </div>
            <div class="spotlight-body">
              <div class="spotlight-tag">
                <span class="spotlight-live"><span class="spotlight-dotpulse"></span>Live · Featured</span>
                <span class="mono" style="color:var(--faint);">Shopify · E-commerce</span>
              </div>
              <h3>Orenda Home</h3>
              <p>A full Shopify Plus rebuild for a D2C furniture retailer — faster browsing, clearer product pages, and a
                checkout that finally matched the brand's premium positioning.</p>
              <div class="spotlight-results">
                <div class="r"><b>+64%</b><span>Conversion</span></div>
                <div class="r"><b>-1.8s</b><span>Load time</span></div>
                <div class="r"><b>2.1×</b><span>Mobile orders</span></div>
              </div>
              <a href="#" class="btn btn-dark" style="width:fit-content;">View Case Study
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- WORKS GRID (dynamic — $portfolios) -->
      <section class="works-grid-section">
        <div class="wrap">
          <h2 class="mb-3" data-aos="fade-up">{{ $section_portfolio->title }}</h2>
          <div class="text description" data-aos="fade-up">{!! $section_portfolio->description !!}</div>

          <div class="works-grid mt-4" id="worksGrid">
            @foreach($portfolios as $portfolio)
              <a href="{{ route('portfolio.single', $portfolio->slug) }}" class="work-card reveal"
                data-cat="all @foreach($portfolio->categories as $category){{ $category->slug }} @endforeach">
                <div class="wc-browser">
                  <div class="wc-bar">
                    <span class="wc-dot"></span><span class="wc-dot"></span><span class="wc-dot"></span>
                    <span class="wc-url">{{ $portfolio->slug }}</span>
                  </div>
                  @if(!empty($portfolio->overview_image))
                    <div class="wc-screen">
                      <img src="{{ asset('uploads/overview_image/' . $portfolio->overview_image) }}"
                        alt="{{ $portfolio->title }}">
                    </div>
                  @else
                    <div class="wc-screen no-image" style="background:linear-gradient(135deg,#0f0e0d,#3a352f);">
                      <span>{{ strtoupper(substr($portfolio->title, 0, 2)) }}</span>
                    </div>
                  @endif
                </div>
                <div class="wc-info">
                  <div class="wc-info-top">
                    <h3>{{ $portfolio->title }}</h3>
                    <span class="wc-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 17L17 7" />
                        <path d="M7 7h10v10" />
                      </svg></span>
                  </div>
                  <div class="wc-cat">
                    @foreach($portfolio->categories as $category)
                      {{ $category->title }}{{ !$loop->last ? ' · ' : '' }}
                    @endforeach
                  </div>
                </div>
              </a>
            @endforeach
          </div>

          <div class="works-empty" id="worksEmpty">{{ __('common.no_results') ?? 'No projects match this filter yet.' }}
          </div>
        </div>
      </section>

      <!-- CTA — demo template, no matching Laravel data, left as-is -->
      <section class="works-cta">
        <div class="wrap reveal">
          <div class="works-cta-box">
            <span class="eyebrow">{{ $setting->title ?? config('app.name') }} • Start a project</span>
            <h2>Want to see your project on this page next?</h2>
            <p>Tell us what you're building and we'll show you the approach we'd take, based on the work above.</p>
            <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
              <a href="mailto:{{ $setting->email ?? '' }}" class="btn btn-light">Email the team</a>
              <a href="{{ route('home') }}#contact" class="btn btn-dark">Get a Free Consultation</a>
            </div>
          </div>
        </div>
      </section>

    @endif

  </main>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    (function () {
      AOS.init();

      var filterBtns = document.querySelectorAll("#works-page .filter-btn");
      var filterCount = document.getElementById("filterCount");
      var worksEmpty = document.getElementById("worksEmpty");

      function applyFilter(filter) {
        var visible = 0;
        document.querySelectorAll("#works-page .work-card").forEach(function (card) {
          var cats = card.dataset.cat.trim().split(/\s+/);
          var show = filter === "all" || cats.indexOf(filter) !== -1;
          card.style.display = show ? "" : "none";
          if (show) visible++;
        });
        if (filterCount) {
          filterCount.textContent = visible + " project" + (visible === 1 ? "" : "s") + " shown";
        }
        if (worksEmpty) {
          worksEmpty.classList.toggle("show", visible === 0);
        }
      }

      filterBtns.forEach(function (btn) {
        btn.addEventListener("click", function () {
          filterBtns.forEach(function (b) { b.classList.remove("active"); });
          btn.classList.add("active");
          applyFilter(btn.dataset.filter);
        });
      });

      if (filterBtns.length) {
        applyFilter("all");
      }

      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("in");
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: .1 });
      document.querySelectorAll("#works-page .reveal:not(.in)").forEach(function (el, index) {
        el.style.transitionDelay = (index % 6 * 0.06) + "s";
        observer.observe(el);
      });
    })();
  </script>
@endsection