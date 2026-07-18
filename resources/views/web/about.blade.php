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
         MSN SoftTech — About Us page styles
         Matches home page vibe: warm cream bg, red seal accent, dark navy stat
         sections, gold numbering on dark, pill buttons, soft card shadows.
         Prefix: .ap-  (About Page)   Shared/global: .msn-  (already used site-wide)
         ========================================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');

    .msn-scope {
      /* ---- Design tokens ---- */
      --ap-cream: #FCF6F1;
      --ap-cream-deep: #F7EAE3;
      --ap-ink: #171A21;
      --ap-navy: #0D1424;
      --ap-navy-soft: #16203A;
      --ap-red: #DC2626;
      --ap-red-light: #F87171;
      --ap-red-pale: #FCA5A5;
      --ap-gold: #D9A94E;
      --ap-muted: #6B7280;
      --ap-line: rgba(23, 26, 33, 0.08);
      --ap-line-dark: rgba(255, 255, 255, 0.12);
      --ap-radius-lg: 22px;
      --ap-radius-md: 16px;
      --ap-radius-sm: 10px;
      --ap-shadow: 0 20px 45px -20px rgba(23, 26, 33, 0.18);

      --bp-font-display: 'Sora', sans-serif;
      --bp-font-body: 'Inter', sans-serif;
      --bp-font-mono: 'JetBrains Mono', monospace;

      background: var(--ap-cream);
      color: var(--ap-ink);
      font-family: var(--bp-font-body);
      overflow-x: hidden;
    }

    .msn-scope .container {
      max-width: 1180px;
    }

    /* ---- Grain overlay ---- */
    .ap-grain {
      position: relative;
    }

    .ap-grain::before {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      opacity: .5;
      mix-blend-mode: multiply;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.35'/%3E%3C/svg%3E");
    }

    /* ---- Reveal on scroll ---- */
    .msn-reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1);
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
      padding: clamp(56px, 9vw, 110px) 0;
    }

    .msn-section-head {
      max-width: 640px;
      margin-bottom: 44px;
    }

    .msn-section-head.msn-center {
      max-width: 640px;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }

    .msn-section-head h2 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(1.7rem, 3vw, 2.4rem);
      letter-spacing: -0.01em;
      color: var(--ap-ink);
      margin-top: 10px;
    }

    .msn-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-mono);
      font-size: .72rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--ap-red);
      font-weight: 500;
    }

    .msn-eyebrow::before {
      content: "";
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--ap-red);
      display: inline-block;
    }

    .msn-eyebrow-on-dark {
      color: var(--ap-gold);
    }

    .msn-eyebrow-on-dark::before {
      background: var(--ap-gold);
    }

    /* ---- Buttons ---- */
    .msn-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--bp-font-body);
      font-weight: 600;
      font-size: .95rem;
      padding: 14px 28px;
      border-radius: 999px;
      text-decoration: none;
      transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .msn-btn-primary {
      background: linear-gradient(135deg, var(--ap-red), #B91C1C);
      color: #fff;
      box-shadow: 0 14px 30px -12px rgba(220, 38, 38, .55);
    }

    .msn-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 36px -12px rgba(220, 38, 38, .65);
      color: #fff;
    }

    .ap-btn-shine {
      position: relative;
      overflow: hidden;
    }

    .ap-btn-shine::after {
      content: "";
      position: absolute;
      top: 0;
      left: -60%;
      width: 40%;
      height: 100%;
      background: linear-gradient(115deg, transparent, rgba(255, 255, 255, .55), transparent);
      transform: skewX(-20deg);
      animation: ap-shine 3.2s ease-in-out infinite;
    }

    @keyframes ap-shine {
      0% {
        left: -60%;
      }

      45%,
      100% {
        left: 130%;
      }
    }

    /* ==========================================================================
         HERO
         ========================================================================== */
    .ap-hero {
      position: relative;
      padding: 64px 0 88px;
      background: radial-gradient(120% 100% at 50% -10%, var(--ap-cream-deep) 0%, var(--ap-cream) 55%);
    }

    .ap-hero-inner {
      max-width: 720px;
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
      font-size: .72rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      background: #fff;
      border: 1px solid var(--ap-line);
      padding: 8px 16px;
      border-radius: 999px;
      color: var(--ap-red);
      margin-bottom: 22px;
      box-shadow: 0 6px 16px -10px rgba(23, 26, 33, .25);
    }

    .ap-chip .dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--ap-red);
      display: inline-block;
      animation: ap-pulse 1.8s ease-in-out infinite;
    }

    @keyframes ap-pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: .35;
      }
    }

    .ap-hero-title {
      font-family: var(--bp-font-display);
      font-weight: 800;
      font-size: clamp(2.3rem, 5.5vw, 4rem);
      line-height: 1.05;
      letter-spacing: -0.02em;
      color: var(--ap-ink);
      margin: 0;
    }

    .ap-hero-underline {
      width: 64px;
      height: 4px;
      border-radius: 4px;
      margin: 22px 0 20px;
      background: linear-gradient(90deg, var(--ap-red-pale), var(--ap-red));
    }

    .ap-hero-copy {
      font-size: 1.05rem;
      line-height: 1.7;
      color: var(--ap-muted);
      max-width: 520px;
      margin: 0 0 26px;
    }

    .ap-breadcrumb {
      font-family: var(--bp-font-mono);
      font-size: .82rem;
      color: var(--ap-muted);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ap-breadcrumb a {
      color: var(--ap-ink);
      text-decoration: none;
    }

    .ap-breadcrumb a:hover {
      color: var(--ap-red);
    }

    .ap-breadcrumb .sep {
      color: var(--ap-line);
    }

    .ap-breadcrumb span:last-child {
      color: var(--ap-red);
    }

    /* ---- Seal ---- */
    .ap-seal {
      position: relative;
      width: 168px;
      height: 168px;
      margin: 46px auto 0;
    }

    .ap-seal-ring {
      width: 100%;
      height: 100%;
      animation: ap-spin 42s linear infinite;
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
      font-weight: 800;
      font-size: 1.9rem;
      color: var(--ap-ink);
      line-height: 1;
    }

    .ap-seal-label {
      font-family: var(--bp-font-mono);
      font-size: .62rem;
      letter-spacing: .18em;
      color: var(--ap-red);
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
      gap: 48px;
      align-items: center;
    }

    .ap-intro-card h3 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: clamp(1.5rem, 2.6vw, 2rem);
      color: var(--ap-ink);
    }

    .about-content {
      color: var(--ap-muted);
      line-height: 1.8;
      font-size: 1rem;
    }

    .about-content p {
      margin-bottom: 14px;
    }

    .bp-frame {
      position: relative;
      border-radius: var(--ap-radius-lg);
      overflow: hidden;
      box-shadow: var(--ap-shadow);
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
      width: 18px;
      height: 18px;
      z-index: 2;
      border-color: var(--ap-red);
      opacity: .85;
    }

    .bp-crosshair.tl {
      top: 14px;
      left: 14px;
      border-top: 2px solid;
      border-left: 2px solid;
    }

    .bp-crosshair.tr {
      top: 14px;
      right: 14px;
      border-top: 2px solid;
      border-right: 2px solid;
    }

    .bp-crosshair.bl {
      bottom: 14px;
      left: 14px;
      border-bottom: 2px solid;
      border-left: 2px solid;
    }

    .bp-crosshair.br {
      bottom: 14px;
      right: 14px;
      border-bottom: 2px solid;
      border-right: 2px solid;
    }

    /* ==========================================================================
         CORE VALUES
         ========================================================================== */
    .ap-values-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
    }

    .ap-value-card {
      background: #fff;
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-md);
      padding: 30px 24px;
      transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .ap-value-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--ap-shadow);
      border-color: transparent;
    }

    .ap-value-badge {
      width: 48px;
      height: 48px;
      border-radius: var(--ap-radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--ap-red-pale), var(--ap-red));
      color: #fff;
      font-size: 1.15rem;
      margin-bottom: 18px;
    }

    .ap-value-card h5 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .ap-value-card p {
      color: var(--ap-muted);
      font-size: .92rem;
      line-height: 1.65;
      margin: 0;
    }

    /* ==========================================================================
         MISSION & VISION
         ========================================================================== */
    .ap-mv-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 28px;
    }

    .msn-card {
      background: #fff;
      border-radius: var(--ap-radius-lg);
      border: 1px solid var(--ap-line);
      box-shadow: var(--ap-shadow);
    }

    .ap-mv-card {
      position: relative;
      padding: 40px 34px;
      overflow: hidden;
    }

    .ap-mv-index {
      position: absolute;
      top: 18px;
      right: 24px;
      font-family: var(--bp-font-display);
      font-weight: 800;
      font-size: 3.2rem;
      color: var(--ap-cream-deep);
      line-height: 1;
      z-index: 0;
    }

    .ap-mv-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-ink);
      color: #fff;
      font-size: 1.2rem;
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
      font-size: 1.35rem;
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
      gap: 22px;
    }

    .ap-provide-card {
      padding: 30px 24px;
      text-align: left;
      transition: transform .3s ease, box-shadow .3s ease;
    }

    .ap-provide-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--ap-shadow);
    }

    .ap-provide-icon {
      width: 50px;
      height: 50px;
      border-radius: var(--ap-radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-cream-deep);
      color: var(--ap-red);
      font-size: 1.25rem;
      margin-bottom: 18px;
    }

    .ap-provide-card h5 {
      font-family: var(--bp-font-display);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .ap-provide-card p {
      color: var(--ap-muted);
      font-size: .92rem;
      line-height: 1.65;
      margin: 0;
    }

    /* ==========================================================================
         TECH MARQUEE
         ========================================================================== */
    .ap-marquee-wrap {
      border-top: 1px solid var(--ap-line);
      border-bottom: 1px solid var(--ap-line);
      padding: 26px 0;
      overflow: hidden;
      background: #fff;
    }

    .ap-marquee-label {
      text-align: center;
      font-family: var(--bp-font-mono);
      font-size: .7rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--ap-muted);
      margin-bottom: 16px;
    }

    .ap-marquee {
      display: flex;
      gap: 14px;
      width: max-content;
      animation: ap-marquee-scroll 32s linear infinite;
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
      font-size: .88rem;
      color: var(--ap-ink);
      background: var(--ap-cream);
      border: 1px solid var(--ap-line);
      padding: 9px 18px;
      border-radius: 999px;
      white-space: nowrap;
    }

    .ap-marquee-item i {
      color: var(--ap-red);
      font-size: 1rem;
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
         STATS (dark)
         ========================================================================== */
    .ap-stats-section {
      background: var(--ap-navy);
      background-image: radial-gradient(90% 140% at 50% 0%, var(--ap-navy-soft) 0%, var(--ap-navy) 60%);
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
      padding: 22px 10px;
      border-left: 1px solid var(--ap-line-dark);
    }

    .ap-stat-col:first-child .ap-stat {
      border-left: none;
    }

    .ap-stat-value {
      font-family: var(--bp-font-display);
      font-weight: 800;
      font-size: clamp(2.1rem, 4vw, 3rem);
      color: var(--ap-gold);
      line-height: 1;
    }

    .ap-stat-label {
      margin-top: 10px;
      font-size: .88rem;
      color: rgba(255, 255, 255, .7);
    }

    /* ==========================================================================
         TEAM
         ========================================================================== */
    .ap-team-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    .ap-team-block {}

    .ap-team-inner {
      background: #fff;
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-lg);
      padding: 24px 20px;
      text-align: center;
      transition: transform .3s ease, box-shadow .3s ease;
    }

    .ap-team-inner:hover {
      transform: translateY(-6px);
      box-shadow: var(--ap-shadow);
    }

    .ap-team-photo {
      width: 92px;
      height: 92px;
      border-radius: 50%;
      overflow: hidden;
      margin: 0 auto 16px;
      border: 3px solid var(--ap-cream-deep);
    }

    .ap-team-photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .ap-team-name {
      font-family: var(--bp-font-display);
      font-size: 1.05rem;
      margin: 0 0 4px;
    }

    .ap-team-name a {
      color: var(--ap-ink);
      text-decoration: none;
    }

    .ap-team-role {
      display: block;
      color: var(--ap-red);
      font-size: .82rem;
      font-weight: 500;
      margin-bottom: 10px;
    }

    .ap-team-meta {
      display: block;
      font-size: .8rem;
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
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--ap-cream);
      color: var(--ap-ink);
      font-size: .82rem;
      transition: background .25s ease, color .25s ease;
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
      padding: clamp(50px, 8vw, 90px) 0;
      text-align: center;
    }

    .ap-quote-mark {
      font-family: var(--bp-font-display);
      font-size: 3.5rem;
      color: var(--ap-red);
      line-height: 1;
      display: block;
      margin-bottom: 6px;
    }

    .ap-quote-text {
      font-family: var(--bp-font-display);
      font-weight: 600;
      font-size: clamp(1.3rem, 2.6vw, 1.9rem);
      max-width: 760px;
      margin: 0 auto;
      line-height: 1.45;
    }

    .ap-quote-attrib {
      margin-top: 20px;
      font-family: var(--bp-font-mono);
      font-size: .78rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--ap-gold);
    }

    /* ==========================================================================
         PROCESS
         ========================================================================== */
    .ap-process-title {
      max-width: 640px;
    }

    .ap-process-step {
      background: #fff;
      border: 1px solid var(--ap-line);
      border-radius: var(--ap-radius-lg);
      padding: 32px 26px;
      height: 100%;
      position: relative;
      transition: transform .3s ease, box-shadow .3s ease;
    }

    .ap-process-step:hover {
      transform: translateY(-6px);
      box-shadow: var(--ap-shadow);
    }

    .ap-process-number {
      font-family: var(--bp-font-display);
      font-weight: 800;
      font-size: 1.6rem;
      color: var(--ap-red-pale);
      -webkit-text-stroke: 1px var(--ap-red);
      margin-bottom: 16px;
    }

    .ap-process-heading {
      font-family: var(--bp-font-display);
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .ap-process-desc {
      color: var(--ap-muted);
      font-size: .92rem;
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
      top: -78px;
      right: -34px;
      font-size: 1.4rem;
      color: var(--ap-red-pale);
    }

    .ap-process-arrow.ap-arrow-down::after {
      content: "\2193";
      top: auto;
      bottom: -46px;
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
        gap: 34px;
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
        padding: 44px 0 60px;
      }

      .ap-hero-title {
        font-size: clamp(1.9rem, 9vw, 2.6rem);
      }

      .ap-seal {
        width: 132px;
        height: 132px;
        margin-top: 34px;
      }

      .ap-seal-num {
        font-size: 1.5rem;
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
        font-size: 1.35rem;
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
      <div class="ap-seal msn-reveal" aria-hidden="true">
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
      </div>
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
    <section class="ap-values msn-section">
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
      <section class="ap-mv msn-section">
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