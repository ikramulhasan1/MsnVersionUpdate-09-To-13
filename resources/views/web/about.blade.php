@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('about-us');
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

@section('social_meta_tags')

@endsection


@section('content')
  <style>
    /* ==========================================================================
             MSN SoftTech — About Us page styles (Minimal Clean direction)
             White background, generous whitespace, single accent (red).
             Prefix: .ap-  (About Page)   Shared/global: .msn-  (already used site-wide)
             ========================================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');

    .msn-scope {
      /* ---- Design tokens ---- */
      --ap-white: #FFFFFF;
      --ap-bg-soft: #FAFAFA;
      --ap-ink: #17181C;
      --ap-navy: #111318;
      --ap-red: #DC2626;
      --ap-red-soft: #FDEAEA;
      --ap-red-line: #F6C7C7;
      --ap-muted: #6B7280;
      --ap-muted-soft: #9CA3AF;
      --ap-line: rgba(17, 19, 24, 0.09);
      --ap-line-dark: rgba(255, 255, 255, 0.12);
      --ap-radius-lg: 16px;
      --ap-radius-md: 14px;
      --ap-radius-sm: 10px;
      --ap-shadow: 0 12px 30px -20px rgba(17, 19, 24, 0.16);

      --bp-font-display: 'Sora', sans-serif;
      --bp-font-body: 'Inter', sans-serif;
      --bp-font-mono: 'JetBrains Mono', monospace;

      background: var(--ap-white);
      color: var(--ap-ink);
      font-family: var(--bp-font-body);
      overflow-x: hidden;
    }

    .msn-scope .container {
      max-width: 1180px;
    }

    /* ---- Reveal on scroll ---- */
    .msn-reveal {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity .6s ease, transform .6s cubic-bezier(.2, .7, .2, 1);
    }

    .msn-reveal.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
      .msn-reveal {
        opacity: 1;
        transform: none;
        transition: none;
      }
    }

    /* ---- Section rhythm ---- */
    .msn-section {
      padding: clamp(64px, 9vw, 120px) 0;
    }

    .msn-section-head {
      max-width: 620px;
      margin-bottom: 48px;
    }

    .msn-section-head.msn-center {
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }

    .msn-section-head h2 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(1.6rem, 2.8vw, 2.2rem);
      letter-spacing: -0.01em;
      color: var(--ap-ink);
      margin-top: 10px;
    }

    .msn-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-mono);
      font-size: .7rem;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: var(--ap-red);
      font-weight: 500;
    }

    .msn-eyebrow::before {
      content: "";
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: var(--ap-red);
      display: inline-block;
    }

    .msn-eyebrow-on-dark {
      color: var(--ap-red-line);
    }

    .msn-eyebrow-on-dark::before {
      background: var(--ap-red-line);
    }

    /* ---- Buttons ---- */
    .msn-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-body);
      font-weight: 600;
      font-size: .93rem;
      padding: 13px 26px;
      border-radius: 999px;
      text-decoration: none;
      transition: opacity .2s ease, transform .2s ease, background .2s ease;
    }

    .msn-btn-primary {
      background: var(--ap-red);
      color: #fff;
    }

    .msn-btn-primary:hover {
      opacity: .9;
      color: #fff;
    }

    .ap-btn-shine {
      position: relative;
      overflow: hidden;
    }

    /* ==========================================================================
             HERO — dark maroon/red gradient (matches reference screenshot)
             ========================================================================== */
    .ap-hero {
      --hero-fg: #FFFFFF;
      --hero-fg-muted: rgba(255, 255, 255, .68);
      --hero-red: #EF4444;
      --hero-line: rgba(255, 255, 255, .14);
      --hero-card-bg: rgba(255, 255, 255, .06);

      position: relative;
      padding: 88px 0 76px;
      background:
        radial-gradient(60% 55% at 15% 8%, rgba(220, 38, 38, .55) 0%, rgba(220, 38, 38, 0) 60%),
        linear-gradient(160deg, #3B0A0C 0%, #200507 45%, #0B0203 100%);
      overflow: hidden;
      border-bottom: none;
    }

    .ap-hero::before {
      /* faint grid texture, like the reference */
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      opacity: .5;
      background-image:
        linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
      background-size: 42px 42px;
      mask-image: radial-gradient(80% 70% at 30% 20%, #000 30%, transparent 90%);
    }

    .ap-hero-inner {
      position: relative;
      z-index: 1;
      max-width: 680px;
      margin: 0 auto;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .ap-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-mono);
      font-size: .7rem;
      letter-spacing: .08em;
      background: var(--hero-card-bg);
      border: 1px solid var(--hero-line);
      padding: 8px 16px;
      border-radius: 8px;
      color: var(--hero-fg-muted);
      margin-bottom: 26px;
    }

    .ap-chip .dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--hero-red);
      display: inline-block;
    }

    .ap-hero-title {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(2.2rem, 5vw, 3.6rem);
      line-height: 1.1;
      letter-spacing: -0.02em;
      color: var(--hero-fg);
      margin: 0;
    }

    .ap-hero-underline {
      width: 48px;
      height: 3px;
      border-radius: 3px;
      margin: 24px 0 20px;
      background: var(--hero-red);
    }

    .ap-hero-copy {
      font-size: 1.02rem;
      line-height: 1.7;
      color: var(--hero-fg-muted);
      max-width: 500px;
      margin: 0 0 28px;
    }

    .ap-breadcrumb {
      font-family: var(--bp-font-mono);
      font-size: .8rem;
      color: rgba(255, 255, 255, .5);
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      z-index: 1;
    }

    .ap-breadcrumb a {
      color: var(--hero-fg);
      text-decoration: none;
    }

    .ap-breadcrumb a:hover {
      color: var(--hero-red);
    }

    .ap-breadcrumb .sep {
      color: rgba(255, 255, 255, .25);
    }

    .ap-breadcrumb span:last-child {
      color: var(--hero-red);
    }

    /* buttons, scoped to the hero only — rest of the site keeps pill buttons */
    .ap-hero .msn-btn {
      border-radius: 10px;
      font-weight: 700;
    }

    .ap-hero .msn-btn-primary {
      background: var(--hero-red);
      box-shadow: 0 10px 24px -10px rgba(239, 68, 68, .6);
    }

    .ap-hero .msn-btn-outline {
      background: transparent;
      border: 1px solid rgba(255, 255, 255, .4);
      color: var(--hero-fg);
    }

    .ap-hero .msn-btn-outline:hover {
      border-color: #fff;
    }

    /* optional: wrap a word in <span class="ap-accent"> inside the hero title
             to highlight it in red, e.g. <h1>Building <span class="ap-accent">production
             quality</span> software</h1> */
    .ap-hero-title .ap-accent {
      color: var(--hero-red);
    }

    /* ---- Seal (recoloured for the dark hero) ---- */
    .ap-seal {
      position: relative;
      width: 148px;
      height: 148px;
      margin: 48px auto 0;
    }

    .ap-seal-ring {
      width: 100%;
      height: 100%;
      animation: ap-spin 46s linear infinite;
    }

    @keyframes ap-spin {
      to {
        transform: rotate(360deg);
      }
    }

    .ap-seal-center {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .ap-seal-num {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 1.7rem;
      color: var(--hero-fg);
      line-height: 1;
    }

    .ap-seal-label {
      font-family: var(--bp-font-mono);
      font-size: .6rem;
      letter-spacing: .16em;
      color: var(--hero-red);
      margin-top: 4px;
    }

    @media (prefers-reduced-motion: reduce) {
      .ap-seal-ring {
        animation: none;
      }
    }

    /* ==========================================================================
             WHO WE ARE (intro)
             ========================================================================== */
    .ap-intro-grid {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 52px;
      align-items: center;
    }

    .ap-intro-card h3 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(1.4rem, 2.4vw, 1.85rem);
      color: var(--ap-ink);
    }

    .about-content {
      color: var(--ap-muted);
      line-height: 1.8;
      font-size: .98rem;
    }

    .about-content p {
      margin-bottom: 14px;
    }

    .bp-frame {
      position: relative;
      border-radius: var(--ap-radius-lg);
      overflow: hidden;
      border: 1px solid var(--ap-line);
    }

    .bp-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      aspect-ratio: 4/3;
    }

    .bp-crosshair {
      position: absolute;
      width: 16px;
      height: 16px;
      z-index: 2;
      border-color: var(--ap-red);
      opacity: .8;
    }

    .bp-crosshair.tl {
      top: 12px;
      left: 12px;
      border-top: 2px solid;
      border-left: 2px solid;
    }

    .bp-crosshair.tr {
      top: 12px;
      right: 12px;
      border-top: 2px solid;
      border-right: 2px solid;
    }

    .bp-crosshair.bl {
      bottom: 12px;
      left: 12px;
      border-bottom: 2px solid;
      border-left: 2px solid;
    }

    .bp-crosshair.br {
      bottom: 12px;
      right: 12px;
      border-bottom: 2px solid;
      border-right: 2px solid;
    }

    /* ==========================================================================
             CORE VALUES
             ========================================================================== */
    .ap-values-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      background: var(--ap-line);
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-md);
      overflow: hidden;
    }

    .ap-value-card {
      background: var(--ap-white);
      padding: 32px 26px;
      transition: background .25s ease;
    }

    .ap-value-card:hover {
      background: var(--ap-bg-soft);
    }

    .ap-value-badge {
      width: 42px;
      height: 42px;
      border-radius: var(--ap-radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-red-soft);
      color: var(--ap-red);
      font-size: 1.05rem;
      margin-bottom: 18px;
    }

    .ap-value-card h5 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      margin-bottom: 8px;
      font-size: 1rem;
    }

    .ap-value-card p {
      color: var(--ap-muted);
      font-size: .9rem;
      line-height: 1.65;
      margin: 0;
    }

    /* ==========================================================================
             MISSION & VISION
             ========================================================================== */
    .ap-mv-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }

    .msn-card {
      background: var(--ap-white);
      border-radius: var(--ap-radius-lg);
      border: 1px solid var(--ap-line);
    }

    .ap-mv-card {
      position: relative;
      padding: 38px 32px;
      overflow: hidden;
    }

    .ap-mv-index {
      position: absolute;
      top: 20px;
      right: 26px;
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 2.6rem;
      color: var(--ap-bg-soft);
      line-height: 1;
      z-index: 0;
    }

    .ap-mv-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-ink);
      color: #fff;
      font-size: 1.05rem;
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }

    .ap-mv-card--vision .ap-mv-icon {
      background: var(--ap-red);
    }

    .ap-mv-card h3 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 1.25rem;
      position: relative;
      z-index: 1;
      margin-bottom: 10px;
    }

    .ap-mv-card .about-content {
      position: relative;
      z-index: 1;
    }

    /* ==========================================================================
             WHAT WE PROVIDE
             ========================================================================== */
    .ap-provide-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    .ap-provide-card {
      padding: 28px 22px;
      text-align: left;
      transition: border-color .25s ease;
    }

    .ap-provide-card:hover {
      border-color: var(--ap-red-line);
    }

    .ap-provide-icon {
      width: 44px;
      height: 44px;
      border-radius: var(--ap-radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-bg-soft);
      color: var(--ap-red);
      font-size: 1.1rem;
      margin-bottom: 18px;
    }

    .ap-provide-card h5 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      margin-bottom: 8px;
      font-size: 1rem;
    }

    .ap-provide-card p {
      color: var(--ap-muted);
      font-size: .9rem;
      line-height: 1.65;
      margin: 0;
    }

    /* ==========================================================================
             TECH MARQUEE
             ========================================================================== */
    .ap-marquee-wrap {
      border-top: 1px solid var(--ap-line);
      border-bottom: 1px solid var(--ap-line);
      padding: 28px 0;
      overflow: hidden;
      background: var(--ap-white);
    }

    .ap-marquee-label {
      text-align: center;
      font-family: var(--bp-font-mono);
      font-size: .68rem;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: var(--ap-muted-soft);
      margin-bottom: 16px;
    }

    .ap-marquee {
      display: flex;
      gap: 12px;
      width: max-content;
      animation: ap-marquee-scroll 34s linear infinite;
    }

    .ap-marquee-wrap:hover .ap-marquee {
      animation-play-state: paused;
    }

    .ap-marquee-item {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-body);
      font-weight: 500;
      font-size: .86rem;
      color: var(--ap-ink);
      background: var(--ap-white);
      border: 1px solid var(--ap-line);
      padding: 8px 16px;
      border-radius: 999px;
      white-space: nowrap;
    }

    .ap-marquee-item i {
      color: var(--ap-red);
      font-size: .95rem;
    }

    @keyframes ap-marquee-scroll {
      from {
        transform: translateX(0);
      }

      to {
        transform: translateX(-50%);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .ap-marquee {
        animation: none;
      }
    }

    /* ==========================================================================
             STATS (dark, flat)
             ========================================================================== */
    .ap-stats-section {
      background: var(--ap-navy);
      color: #fff;
    }

    .ap-stats {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 24px;
      margin-top: 10px;
    }

    .ap-stat-col {
      flex: 1 1 200px;
      text-align: center;
    }

    .ap-stat {
      padding: 20px 10px;
      border-left: 1px solid var(--ap-line-dark);
    }

    .ap-stat-col:first-child .ap-stat {
      border-left: none;
    }

    .ap-stat-value {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(2rem, 3.6vw, 2.7rem);
      color: var(--ap-red-line);
      line-height: 1;
    }

    .ap-stat-label {
      margin-top: 10px;
      font-size: .86rem;
      color: rgba(255, 255, 255, .65);
    }

    /* ==========================================================================
             TEAM
             ========================================================================== */
    .ap-team-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    .ap-team-inner {
      background: var(--ap-white);
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-lg);
      padding: 26px 20px;
      text-align: center;
      transition: border-color .25s ease;
    }

    .ap-team-inner:hover {
      border-color: var(--ap-red-line);
    }

    .ap-team-photo {
      width: 84px;
      height: 84px;
      border-radius: 50%;
      overflow: hidden;
      margin: 0 auto 16px;
      border: 1px solid var(--ap-line);
    }

    .ap-team-photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .ap-team-name {
      font-family: var(--bp-font-display);
      font-size: 1rem;
      margin: 0 0 4px;
    }

    .ap-team-name a {
      color: var(--ap-ink);
      text-decoration: none;
    }

    .ap-team-role {
      display: block;
      color: var(--ap-red);
      font-size: .8rem;
      font-weight: 500;
      margin-bottom: 10px;
    }

    .ap-team-meta {
      display: block;
      font-size: .78rem;
      color: var(--ap-muted);
      margin-bottom: 4px;
    }

    .ap-team-social {
      list-style: none;
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 14px 0 0;
      padding: 0;
    }

    .ap-team-social a {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-bg-soft);
      color: var(--ap-ink);
      font-size: .78rem;
      transition: background .2s ease, color .2s ease;
    }

    .ap-team-social a:hover {
      background: var(--ap-red);
      color: #fff;
    }

    /* ==========================================================================
             PULL QUOTE BAND
             ========================================================================== */
    .ap-quote-band {
      background: var(--ap-ink);
      color: #fff;
      padding: clamp(56px, 8vw, 96px) 0;
      text-align: center;
    }

    .ap-quote-mark {
      font-family: var(--bp-font-display);
      font-size: 3rem;
      color: var(--ap-red);
      line-height: 1;
      display: block;
      margin-bottom: 6px;
    }

    .ap-quote-text {
      font-family: var(--bp-font-display);
      font-weight: 600;
      font-size: clamp(1.25rem, 2.4vw, 1.75rem);
      max-width: 740px;
      margin: 0 auto;
      line-height: 1.45;
    }

    .ap-quote-attrib {
      margin-top: 20px;
      font-family: var(--bp-font-mono);
      font-size: .76rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .55);
    }

    /* ==========================================================================
             PROCESS
             ========================================================================== */
    .ap-process-title {
      max-width: 620px;
    }

    .ap-process-step {
      background: var(--ap-white);
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-lg);
      padding: 30px 24px;
      height: 100%;
      position: relative;
      transition: border-color .25s ease;
    }

    .ap-process-step:hover {
      border-color: var(--ap-red-line);
    }

    .ap-process-number {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 1.4rem;
      color: var(--ap-red);
      margin-bottom: 16px;
    }

    .ap-process-heading {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 1.05rem;
      margin-bottom: 8px;
    }

    .ap-process-desc {
      color: var(--ap-muted);
      font-size: .9rem;
      line-height: 1.65;
    }

    .ap-process-arrow {
      position: relative;
      height: 0;
    }

    .ap-process-arrow::after {
      content: "\2192";
      /* → */
      position: absolute;
      top: -74px;
      right: -32px;
      font-size: 1.2rem;
      color: var(--ap-muted-soft);
    }

    .ap-process-arrow.ap-arrow-down::after {
      content: "\2193";
      top: auto;
      bottom: -44px;
      right: 50%;
      transform: translateX(50%);
    }

    .ap-process-arrow.arrow-hidden::after {
      content: "";
    }

    /* ==========================================================================
             RESPONSIVE
             ========================================================================== */
    @media (max-width: 991px) {
      .ap-intro-grid {
        grid-template-columns: 1fr;
        gap: 32px;
      }

      .ap-intro-media {
        order: -1;
      }

      .ap-values-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .ap-mv-grid {
        grid-template-columns: 1fr;
      }

      .ap-provide-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .ap-team-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .ap-stats {
        justify-content: center;
      }

      .ap-stat {
        border-left: none;
        border-top: 1px solid var(--ap-line-dark);
      }

      .ap-stat-col:first-child .ap-stat {
        border-top: none;
      }
    }

    @media (max-width: 575px) {
      .msn-section {
        padding: 44px 0 64px;
      }

      .ap-hero {
        padding: 44px 0 56px;
      }

      .ap-hero-title {
        font-size: clamp(1.85rem, 9vw, 2.4rem);
      }

      .ap-seal {
        width: 120px;
        height: 120px;
        margin-top: 32px;
      }

      .ap-seal-num {
        font-size: 1.35rem;
      }

      .ap-values-grid,
      .ap-provide-grid,
      .ap-team-grid {
        grid-template-columns: 1fr;
      }

      .ap-stat {
        padding: 16px 6px;
      }

      .ap-quote-text {
        font-size: 1.25rem;
      }

      .ap-process-arrow::after,
      .ap-process-arrow.ap-arrow-down::after {
        display: none;
      }
    }
  </style>
  <div class="msn-scope">

    <!-- Hero -->
    <section class="ap-hero ap-grain">
      <div class="container ap-hero-inner">
        <span class="ap-chip msn-reveal"><span class="dot"></span>{{ $setting->title ?? 'MSN Softtech' }}</span>
        <h1 class="ap-hero-title msn-reveal">{{ __('navbar.about') }}</h1>
        <div class="ap-hero-underline msn-reveal"></div>
        <p class="ap-hero-copy msn-reveal">Get to know the team, mission, and approach behind every project we build.
        </p>
        <div class="ap-breadcrumb msn-reveal">
          <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
          <span class="sep">/</span>
          <span>{{ __('navbar.about') }}</span>
        </div>
      </div>

      <!-- signature element: rotating red seal (matches home page accent) -->
      {{-- <div class="ap-seal msn-reveal" aria-hidden="true">
        <svg class="ap-seal-ring" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <path id="ap-seal-path" d="M100,100 m-82,0 a82,82 0 1,1 164,0 a82,82 0 1,1 -164,0" />
            <linearGradient id="ap-seal-red" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="#FCA5A5" />
              <stop offset=".5" stop-color="#DC2626" />
              <stop offset="1" stop-color="#F87171" />
            </linearGradient>
          </defs>
          <circle cx="100" cy="100" r="96" fill="none" stroke="url(#ap-seal-red)" stroke-width="1.25" />
          <circle cx="100" cy="100" r="68" fill="none" stroke="url(#ap-seal-red)" stroke-width="1"
            stroke-dasharray="1 5" />
          <text font-size="10.5" letter-spacing="3" fill="#DC2626" style="font-family:var(--bp-font-mono);">
            <textPath href="#ap-seal-path" startOffset="0%">MSN SOFTTECH • TRUSTED WORLDWIDE • MSN SOFTTECH • TRUSTED
              WORLDWIDE •</textPath>
          </text>
        </svg>
        <div class="ap-seal-center">
          <span class="ap-seal-num">10+</span>
          <span class="ap-seal-label">YEARS</span>
        </div>
      </div> --}}
    </section>

    <!-- Who We Are -->
    @if(isset($about))
      <section class="ap-intro msn-section">
        <div class="container">
          <div class="ap-intro-grid">
            <div class="ap-intro-card msn-reveal">
              <span class="msn-eyebrow">Who We Are</span>
              <h3 class="mt-3">{{ $about->title }}</h3>
              <div class="about-content">
                {!! $about->description !!}
              </div>
            </div>
            <div class="ap-intro-media bp-frame msn-reveal">
              <span class="bp-crosshair tl"></span>
              <span class="bp-crosshair tr"></span>
              <span class="bp-crosshair bl"></span>
              <span class="bp-crosshair br"></span>
              <img src="{{ asset('uploads/about/' . $about->image_path) }}" alt="{{ $about->title }}">
            </div>
          </div>
        </div>
      </section>
    @endif

    <!-- Core Values (new) -->
    <section class="ap-values msn-section" style="padding-top: 0px">
      <div class="container">
        <div class="msn-section-head msn-center msn-reveal">
          <span class="msn-eyebrow">What Drives Us</span>
          <h2>The Values Behind Every Delivery</h2>
        </div>
        <div class="ap-values-grid msn-reveal">
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-shield-check"></i></span>
            <h5>Integrity</h5>
            <p>We do what we say — clear pricing, clear timelines, no surprises.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-lightbulb"></i></span>
            <h5>Innovation</h5>
            <p>We stay curious about new tools, and use them only when they genuinely help you.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-award"></i></span>
            <h5>Excellence</h5>
            <p>Every release goes through real testing before it reaches your users.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-people"></i></span>
            <h5>Partnership</h5>
            <p>We work like an extension of your team, not a vendor that disappears after launch.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision -->
    @if(isset($about->mission_title) || isset($about->vision_title))
      <section class="">
        <div class="container">
          <div class="ap-mv-grid">
            @if(isset($about->mission_title))
              <div class="msn-card ap-mv-card ap-mv-card--mission msn-reveal">
                <span class="ap-mv-index">01</span>
                <span class="ap-mv-icon"><i class="bi bi-bullseye"></i></span>
                <h3>{{ $about->mission_title }}</h3>
                <div class="about-content">{!! $about->mission_desc !!}</div>
              </div>
            @endif
            @if(isset($about->vision_title))
              <div class="msn-card ap-mv-card ap-mv-card--vision msn-reveal">
                <span class="ap-mv-index">02</span>
                <span class="ap-mv-icon"><i class="bi bi-binoculars"></i></span>
                <h3>{{ $about->vision_title }}</h3>
                <div class="about-content">{!! $about->vision_desc !!}</div>
              </div>
            @endif
          </div>
        </div>
      </section>
    @endif

    <!-- What We Provide -->
    <section class="ap-provide msn-section">
      <div class="container">
        <div class="msn-section-head msn-center msn-reveal">
          <span class="msn-eyebrow">What We Provide</span>
          <h2>10+ Years of IT Excellence, Delivered Globally</h2>
        </div>

        <div class="ap-provide-grid">
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-laptop"></i></span>
            <h5>Software Development</h5>
            <p>Innovative, custom-built solutions that fuel growth, optimize performance, and drive digital
              transformation.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-diagram-3-fill"></i></span>
            <h5>Website Development</h5>
            <p>High-performance, responsive websites crafted to deliver outstanding user experiences and business
              growth.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-phone"></i></span>
            <h5>Mobile App Development</h5>
            <p>Seamless, high-impact mobile experiences designed to captivate users and expand your global reach.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-bar-chart-line"></i></span>
            <h5>SEO & Marketing</h5>
            <p>Strategic SEO and marketing solutions that enhance visibility, increase engagement, and deliver
              measurable success.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Tech marquee (new premium touch) -->
    <div class="ap-marquee-wrap">
      <div class="ap-marquee-label">Our Core Toolkit</div>
      <div class="ap-marquee">
        @php
          $ap_stack = [
            ['bi-code-slash', 'Laravel'],
            ['bi-filetype-jsx', 'React'],
            ['bi-phone-fill', 'Flutter'],
            ['bi-cloud-fill', 'AWS'],
            ['bi-vector-pen', 'Figma'],
            ['bi-hexagon-fill', 'Node.js'],
            ['bi-shield-check', 'Security'],
            ['bi-search', 'SEO'],
            ['bi-database-fill', 'MySQL'],
            ['bi-apple', 'iOS'],
            ['bi-google-play', 'Android'],
            ['bi-gear-fill', 'DevOps'],
          ];
        @endphp
        @for($r = 0; $r < 2; $r++)
          @foreach($ap_stack as $ap_item)
            <span class="ap-marquee-item"><i class="bi {{ $ap_item[0] }}"></i>{{ $ap_item[1] }}</span>
          @endforeach
        @endfor
      </div>
    </div>

    <!-- Stats -->
    @if(count($counters) > 0)
      <section class="ap-stats-section msn-section ap-grain">
        <div class="container">
          <div class="msn-section-head msn-center msn-reveal">
            <span class="msn-eyebrow msn-eyebrow-on-dark">By The Numbers</span>
            <h2 style="color:#fff;">Trusted by Businesses Worldwide</h2>
          </div>
          <div class="ap-stats msn-reveal">
            @foreach($counters as $counter)
              <div class="ap-stat-col">
                <div class="ap-stat">
                  <div class="ap-stat-value" data-count="{{ (int) $counter->value }}">0</div>
                  <div class="ap-stat-label">{{ $counter->title }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Team -->
    @php
      $section_team = \App\Models\Section::section('team');
    @endphp
    @if(count($members) > 0 && isset($section_team))
      <section class="ap-team-section msn-section">
        <div class="container">
          <div class="msn-section-head msn-reveal">
            <span class="msn-eyebrow">Meet The Team</span>
            <h2>{{ $section_team->title }}</h2>
            <div class="text description">{!! $section_team->description !!}</div>
          </div>

          <div class="ap-team-grid msn-reveal">
            @foreach($members as $member)
              <div class="ap-team-block">
                <div class="ap-team-inner">
                  <div class="ap-team-photo">
                    <img src="{{ asset('uploads/member/' . $member->image_path) }}" alt="{{ $member->title }}" loading="lazy">
                  </div>
                  <h3 class="ap-team-name"><a>{{ $member->title }}</a></h3>
                  <span class="ap-team-role">{{ $member->designation->title }}@if(isset($member->designation->department)),
                  {{ $member->designation->department }}@endif</span>
                  @if(isset($member->email))
                    <span class="ap-team-meta"><i class="far fa-envelope"></i> {{ $member->email }}</span>
                  @endif
                  @if(isset($member->phone))
                    <span class="ap-team-meta"><i class="fas fa-phone-volume"></i> {{ $member->phone }}</span>
                  @endif

                  <ul class="ap-team-social">
                    @if(isset($member->facebook))
                      <li><a href="{{ $member->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    @endif
                    @if(isset($member->twitter))
                      <li><a href="{{ $member->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                    @endif
                    @if(isset($member->instagram))
                      <li><a href="{{ $member->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    @endif
                    @if(isset($member->linkedin))
                      <li><a href="{{ $member->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Our Promise — pull-quote (new) -->
    <section class="ap-quote-band msn-reveal">
      <div class="container">
        <span class="ap-quote-mark">"</span>
        <p class="ap-quote-text">We don't just ship projects. We build the software your business will still be proud of
          five years from now.</p>
        <div class="ap-quote-attrib">— MSN SoftTech</div>
      </div>
    </section>

    <!-- Process -->
    @php
      $section_process = \App\Models\Section::section('process');
    @endphp
    @if(count($processes) > 0 && isset($section_process))
      <section class="ap-process-section msn-section">
        <div class="container">
          <div class="ap-process-title msn-reveal">
            <span class="msn-eyebrow">How We Deliver</span>
            <h2 style="padding-top:14px;">{{ $section_process->title }}</h2>
          </div>

          <div class="row g-4 mb-4">
            @foreach($processes as $key => $process)
              @php
                $totalSteps = count($processes);
                $showArrow = ($key != $totalSteps - 1);
              @endphp
              <div class="col-md-4 mb-4 msn-reveal">
                <div class="ap-process-step">
                  <div class="ap-process-number">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="ap-process-heading">{{ $process->title }}</div>
                  <div class="ap-process-desc">{!! $process->description !!}</div>
                  <div
                    class="ap-process-arrow d-none d-md-block {{ $showArrow ? ($key == 2 ? 'ap-arrow-down' : '') : 'arrow-hidden' }}">
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="text-center mt-5">
            <a href="https://msnsofttech.com/get-quote" class="msn-btn msn-btn-primary ap-btn-shine">Get in Touch With Us
              →</a>
          </div>
        </div>
      </section>
    @endif

  </div><!-- /.msn-scope -->

  <script>
    (function () {
      // premium touch: count the stats up when they scroll into view
      var stats = document.querySelectorAll('.ap-stat-value[data-count]');
      if (!stats.length || !('IntersectionObserver' in window)) return;
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10) || 0;
          var current = 0;
          var step = Math.max(1, Math.ceil(target / 60));
          (function tick() {
            current += step;
            if (current >= target) {
              el.textContent = target;
            } else {
              el.textContent = current;
              requestAnimationFrame(tick);
            }
          })();
          io.unobserve(el);
        });
      }, { threshold: 0.4 });
      stats.forEach(function (el) { io.observe(el); });
    })();







    // MSN SoftTech — scroll reveal for .msn-reveal elements
    // Skip this file if your global site JS already reveals .msn-reveal (used on the home page too).
    (function () {
      var els = document.querySelectorAll('.msn-reveal');
      if (!els.length) return;

      if (!('IntersectionObserver' in window)) {
        els.forEach(function (el) { el.classList.add('is-visible'); });
        return;
      }

      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

      els.forEach(function (el) { io.observe(el); });
    })();
  </script>

@endsection