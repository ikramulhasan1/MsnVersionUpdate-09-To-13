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
  {{-- NOTE: fonts + msn-theme.css should be linked once in your master layout
       <head>, after Bootstrap. Kept here too so this page still works if you
       haven't wired the master layout yet: --}}
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

<style>
/* =====================================================================
   ABOUT PAGE — "Brushed Metal" premium pass on top of the Blueprint
   system. Same deep navy base, now paired with a vivid teal/cyan brand
   accent for a glass-and-teal register that pops content forward
   instead of blending into the dark background. Everything is scoped
   to .msn-scope with an ap- prefix and a small set of page-local
   tokens, so nothing here depends on editing the shared theme file.
   ===================================================================== */

.msn-scope{
  /* -- premium token layer (local, additive to the shared bp- tokens) -- */
  --ap-navy-950:#05070d;
  --ap-navy-900:#0a0f1a;
  --ap-navy-850:#0e1526;
  --ap-navy-800:#131c31;
  --ap-teal-100:#99F6E4;
  --ap-teal-300:#2DD4BF;
  --ap-teal-500:#14B8A6;
  --ap-teal-700:#115E59;
  --ap-teal-gradient: linear-gradient(115deg,#99F6E4 0%,#2DD4BF 22%,#0F766E 45%,#5EEAD4 68%,#2DD4BF 85%,#99F6E4 100%);
  --ap-teal-gradient-hard: linear-gradient(135deg,#5EEAD4 0%,#14B8A6 50%,#115E59 100%);
  --ap-glass-bg: rgba(94,234,212,.045);
  --ap-glass-bg-strong: rgba(94,234,212,.07);
  --ap-glass-border: rgba(45,212,191,.28);
  --ap-glass-border-soft: rgba(45,212,191,.14);
}

/* ---------- shared premium utilities ---------- */
.ap-teal-text{
  background-image: var(--ap-teal-gradient);
  background-size:220% auto;
  background-position:0% center;
  -webkit-background-clip:text;
  background-clip:text;
  -webkit-text-fill-color:transparent;
  color:transparent;
  animation: ap-shine 7s ease-in-out infinite;
}
@keyframes ap-shine{
  0%{ background-position:0% center; }
  50%{ background-position:100% center; }
  100%{ background-position:0% center; }
}
@media (prefers-reduced-motion:reduce){
  .ap-teal-text{ animation:none; }
}

.ap-grain{ position:relative; }
.ap-grain::after{
  content:"";
  position:absolute; inset:0;
  z-index:0;
  pointer-events:none;
  opacity:.05;
  mix-blend-mode:overlay;
  background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='140' height='140'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
}

.ap-chip{
  display:inline-flex; align-items:center; gap:8px;
  padding:6px 14px 6px 10px;
  border:1px solid var(--ap-glass-border);
  background:var(--ap-glass-bg);
  border-radius:2px;
  font-family:var(--bp-font-mono);
  font-size:11.5px;
  letter-spacing:.08em;
  text-transform:uppercase;
}
.ap-chip .dot{
  width:6px;height:6px;border-radius:50%;
  background:var(--ap-teal-gradient-hard);
  box-shadow:0 0 8px rgba(45,212,191,.75);
  flex:0 0 auto;
}
.ap-hero .ap-chip{ color:rgba(255,255,255,.8) !important; }

.ap-btn-shine{ position:relative; overflow:hidden; isolation:isolate; }
.ap-btn-shine::after{
  content:"";
  position:absolute; top:0; left:-65%;
  width:35%; height:100%;
  background:linear-gradient(115deg, transparent, rgba(255,255,255,.65), transparent);
  transform:skewX(-18deg);
  transition:left .7s cubic-bezier(.2,.7,.3,1);
  pointer-events:none;
}
.ap-btn-shine:hover::after{ left:130%; }

/* ---------- HERO ---------- */
.ap-hero{
  position:relative;
  background-color:#05070d !important;
  background-image:
    radial-gradient(circle at 78% 8%, rgba(45,212,191,.16), transparent 42%),
    radial-gradient(circle at 8% 92%, rgba(45,212,191,.08), transparent 46%),
    linear-gradient(180deg, #05070d 0%, #0a0f1a 55%, #0e1526 100%) !important;
  padding:clamp(120px,16vw,180px) 0 clamp(56px,7vw,84px);
  overflow:hidden;
  isolation:isolate;
}
.ap-hero::before{
  content:"";
  position:absolute; inset:0;
  background-image:
    linear-gradient(rgba(45,212,191,.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(45,212,191,.08) 1px, transparent 1px);
  background-size: 64px 64px, 64px 64px;
  mask-image: linear-gradient(180deg, transparent, rgba(0,0,0,.9) 30%, rgba(0,0,0,.9) 80%, transparent);
  pointer-events:none;
}
.ap-hero-inner{ position:relative; z-index:2; max-width:640px; }
.ap-hero-title{
  font-family:var(--bp-font-display);
  color:#ffffff !important;
  font-size:clamp(30px,5.2vw,52px);
  font-weight:700;
  line-height:1.15;
  margin-top:16px;
}
.ap-hero-title .ap-teal-text{ display:inline-block; }
.ap-hero-underline{
  width:64px; height:3px; margin-top:18px;
  background-image:linear-gradient(115deg,#99F6E4,#14B8A6,#115E59);
  border-radius:2px;
}
.ap-hero-copy{
  max-width:600px;
  font-size:clamp(15px,1.2vw,16.5px);
  color:rgba(255,255,255,.65) !important;
  margin-top:18px;
  line-height:1.75;
}
.ap-breadcrumb{
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
  margin-top:26px;
  font-family:var(--bp-font-mono);
  font-size:12px;
  letter-spacing:.05em;
  text-transform:uppercase;
  color:rgba(255,255,255,.45) !important;
}
.ap-breadcrumb a{ color:rgba(255,255,255,.8) !important; transition:color .2s var(--bp-ease); }
.ap-breadcrumb a:hover{ color:var(--ap-teal-300) !important; }
.ap-breadcrumb span.sep{ color:rgba(255,255,255,.3) !important; }

/* -- signature element: the rotating teal seal -- */
.ap-seal{
  position:absolute;
  top:16%; right:6%;
  width:150px; height:150px;
  z-index:1;
}
.ap-seal-ring{ width:100%; height:100%; animation:ap-seal-spin 48s linear infinite; }
@keyframes ap-seal-spin{ to{ transform:rotate(360deg); } }
@media (prefers-reduced-motion:reduce){ .ap-seal-ring{ animation:none; } }
.ap-seal-center{
  position:absolute; inset:0;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  text-align:center;
}
.ap-seal-num{
  font-family:var(--bp-font-mono);
  font-weight:600;
  font-size:26px;
  background-image:var(--ap-teal-gradient-hard);
  -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; color:transparent;
}
.ap-seal-label{
  font-family:var(--bp-font-mono);
  font-size:9px; letter-spacing:.12em;
  color:rgba(255,255,255,.5);
  margin-top:2px;
}
@media (max-width:1199px){ .ap-seal{ display:none; } }

/* ---------- INTRO / WHO WE ARE ---------- */
.ap-intro{ background:var(--bp-paper); }
.ap-intro-grid{
  display:grid;
  grid-template-columns: 1.2fr .8fr;
  gap:56px;
  align-items:center;
}
@media (max-width:991px){
  .ap-intro-grid{ grid-template-columns:1fr; gap:36px; }
}
.ap-intro-media{ position:relative; }
.ap-intro-media img{
  width:100%;
  display:block;
  border:1px solid var(--bp-line);
  aspect-ratio:4/3.4;
  object-fit:cover;
  transition:filter .5s var(--bp-ease);
}
.ap-intro-media::after{
  content:"";
  position:absolute; inset:0;
  border:1px solid transparent;
  box-shadow:0 0 0 0 rgba(20,184,166,0);
  transition:box-shadow .4s var(--bp-ease), border-color .4s var(--bp-ease);
  pointer-events:none;
}
.ap-intro-media:hover::after{
  border-color:rgba(45,212,191,.55);
  box-shadow:0 20px 60px -20px rgba(20,184,166,.35);
}
.ap-intro-card h3{
  font-family:var(--bp-font-display);
  font-size:clamp(26px,3.4vw,38px);
  font-weight:700;
  line-height:1.15;
  margin-bottom:20px;
}
.ap-intro-card .about-content{
  color:var(--bp-text-soft);
  font-size:16px;
  line-height:1.8;
}
.ap-intro-card .about-content p{ margin-bottom:14px; }
.ap-intro-card .about-content ul{
  list-style:none; margin:18px 0 0; padding:0;
}
.ap-intro-card .about-content ul li{
  position:relative;
  padding-left:28px;
  margin-bottom:12px;
  font-size:15.5px;
  color:var(--bp-text-soft);
}
.ap-intro-card .about-content ul li::before{
  content:"";
  position:absolute; left:0; top:6px;
  width:9px; height:9px;
  background-image:var(--ap-teal-gradient-hard);
  box-shadow:0 0 6px rgba(20,184,166,.55);
}
.ap-intro-card .about-content b{ color:var(--bp-text); }
.ap-intro-card .about-content a{ color:var(--ap-teal-700); font-weight:600; text-decoration:underline; text-underline-offset:3px; }

/* ---------- MISSION / VISION — elevated to a dark glass panel ---------- */
.ap-mv{
  position:relative;
  overflow:hidden;
  background-color:#0a0f1a !important;
  background-image:
    radial-gradient(circle at 15% 15%, rgba(45,212,191,.1), transparent 55%),
    radial-gradient(circle at 85% 85%, rgba(45,212,191,.07), transparent 50%),
    linear-gradient(180deg, #0a0f1a 0%, #0e1526 100%) !important;
}
.ap-mv-grid{
  position:relative; z-index:1;
  display:grid;
  grid-template-columns:repeat(2, 1fr);
  gap:32px;
}
@media (max-width:767px){ .ap-mv-grid{ grid-template-columns:1fr; } }
.ap-mv-card{
  position:relative;
  overflow:hidden;
  padding:44px 36px 40px;
  background:rgba(94,234,212,.05) !important;
  border:1px solid rgba(45,212,191,.16) !important;
  border-left:3px solid var(--ap-teal-500) !important;
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
  box-shadow:0 30px 70px -30px rgba(0,0,0,.6), inset 0 1px 0 rgba(255,255,255,.05);
  transition:border-color .35s var(--bp-ease), transform .35s var(--bp-ease), box-shadow .35s var(--bp-ease);
}
/* asymmetric stagger: vision card sits lower, mission's icon anchors top */
.ap-mv-card--vision{ margin-top:36px; }
@media (max-width:767px){ .ap-mv-card--vision{ margin-top:0; } }
.ap-mv-card:hover{
  border-color:rgba(45,212,191,.32) !important;
  transform:translateY(-6px);
  box-shadow:0 40px 90px -30px rgba(20,184,166,.22), inset 0 1px 0 rgba(255,255,255,.07);
}
.ap-mv-index{
  position:absolute;
  top:-6px; right:16px;
  font-family:var(--bp-font-display);
  font-weight:700;
  font-size:110px;
  line-height:1;
  color:transparent;
  -webkit-text-stroke:1px rgba(45,212,191,.16);
  pointer-events:none;
  user-select:none;
}
.ap-mv-icon{
  position:relative;
  display:flex; align-items:center; justify-content:center;
  width:76px; height:76px;
  margin-bottom:26px;
  border-radius:50%;
  border:1px solid rgba(45,212,191,.45);
  background:radial-gradient(circle at 32% 28%, rgba(45,212,191,.28), transparent 72%);
  box-shadow:0 0 0 1px rgba(45,212,191,.08), 0 0 30px rgba(20,184,166,.25);
  font-size:30px;
  color:var(--ap-teal-300);
  transition:transform .35s var(--bp-ease), box-shadow .35s var(--bp-ease);
}
.ap-mv-card:hover .ap-mv-icon{
  transform:scale(1.08) rotate(-4deg);
  box-shadow:0 0 0 6px rgba(45,212,191,.1), 0 0 34px rgba(20,184,166,.4);
}
.ap-mv-card h3{
  position:relative;
  font-family:var(--bp-font-display);
  font-size:25px;
  font-weight:700;
  margin-bottom:16px;
  color:#ffffff !important;
}
.ap-mv-card .about-content{
  position:relative;
  color:rgba(255,255,255,.65) !important;
  font-size:15.5px;
  line-height:1.8;
}
.ap-mv-card .about-content b{ color:rgba(255,255,255,.92) !important; }
.ap-mv-card .about-content a{ color:var(--ap-teal-300) !important; }

/* ---------- WHAT WE PROVIDE ---------- */
.ap-provide{ background:var(--bp-white); }
.ap-provide-grid{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:22px;
}
@media (max-width:991px){ .ap-provide-grid{ grid-template-columns:repeat(2, 1fr); } }
@media (max-width:575px){ .ap-provide-grid{ grid-template-columns:1fr; } }
.ap-provide-card{
  padding:30px 24px 28px;
  height:100%;
  display:flex;
  flex-direction:column;
  transition:transform .35s var(--bp-ease), box-shadow .35s var(--bp-ease), border-color .35s var(--bp-ease);
}
.ap-provide-card:hover{
  transform:translateY(-5px);
  box-shadow:0 30px 60px -28px rgba(20,184,166,.35);
  border-color:rgba(20,184,166,.4);
}
.ap-provide-icon{
  width:46px;height:46px;
  display:flex;align-items:center;justify-content:center;
  border:1px solid var(--bp-line);
  background:linear-gradient(135deg, rgba(45,212,191,.12), transparent);
  color:var(--ap-teal-700);
  font-size:20px;
  margin-bottom:20px;
  transition:border-color .3s var(--bp-ease), box-shadow .3s var(--bp-ease);
}
.ap-provide-card:hover .ap-provide-icon{
  border-color:var(--ap-teal-500);
  box-shadow:0 0 0 4px rgba(45,212,191,.1);
}
.ap-provide-card h5{
  font-family:var(--bp-font-display);
  font-size:19px;
  font-weight:700;
  margin-bottom:10px;
}
.ap-provide-card p{
  font-size:14.5px;
  line-height:1.7;
  color:var(--bp-muted);
  margin:0;
}

/* ---------- TECH MARQUEE (new premium section) ---------- */
.ap-marquee-wrap{
  background:var(--bp-paper);
  border-top:1px solid var(--bp-line);
  border-bottom:1px solid var(--bp-line);
  padding:22px 0;
  overflow:hidden;
}
.ap-marquee-label{
  font-family:var(--bp-font-mono);
  font-size:11px;
  letter-spacing:.1em;
  text-transform:uppercase;
  color:var(--bp-muted);
  text-align:center;
  margin-bottom:14px;
}
.ap-marquee{
  display:flex;
  width:max-content;
  animation:ap-marquee-scroll 32s linear infinite;
}
.ap-marquee:hover{ animation-play-state:paused; }
@keyframes ap-marquee-scroll{
  from{ transform:translateX(0); }
  to{ transform:translateX(-50%); }
}
.ap-marquee-item{
  display:flex; align-items:center; gap:10px;
  padding:0 34px;
  font-family:var(--bp-font-mono);
  font-size:14px;
  color:var(--bp-text-soft);
  white-space:nowrap;
  border-right:1px solid var(--bp-line);
}
.ap-marquee-item i{ color:var(--ap-teal-700); font-size:16px; }
@media (prefers-reduced-motion:reduce){ .ap-marquee{ animation:none; } }

/* ---------- STATS ---------- */
.ap-stats-section{ background:var(--ap-navy-950); position:relative; overflow:hidden; }
.ap-stats-section::before{
  content:"";
  position:absolute; inset:0;
  background-image:
    linear-gradient(var(--bp-line-dark-2) 1px, transparent 1px),
    linear-gradient(90deg, var(--bp-line-dark-2) 1px, transparent 1px);
  background-size:56px 56px;
  mask-image: radial-gradient(circle at 50% 40%, rgba(0,0,0,.9), transparent 75%);
  pointer-events:none;
}
.ap-stats{
  position:relative; z-index:1;
  display:flex; flex-wrap:wrap;
  background:var(--ap-navy-900);
  border:1px solid var(--bp-line-dark);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
}
.ap-stats .ap-stat-col{ flex:1 1 220px; border-left:1px solid var(--bp-line-dark); transition:background .3s var(--bp-ease); }
.ap-stats .ap-stat-col:first-child{ border-left:none; }
.ap-stats .ap-stat-col:hover{ background:rgba(45,212,191,.045); }
.ap-stat{
  padding:36px 26px;
  text-align:center;
}
.ap-stat-value{
  font-family:var(--bp-font-mono);
  font-size:clamp(28px,3.2vw,40px);
  font-weight:600;
  line-height:1;
  background-image:var(--ap-teal-gradient);
  background-size:220% auto;
  -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; color:transparent;
}
.ap-stat-value::after{ content:"+"; }
.ap-stat-label{
  margin-top:12px;
  color:rgba(255,255,255,.5);
  font-size:12.5px;
  font-weight:500;
  font-family:var(--bp-font-mono);
  text-transform:uppercase;
  letter-spacing:.05em;
}
@media (max-width:767px){
  .ap-stats .ap-stat-col{ flex:1 1 50%; }
  .ap-stats .ap-stat-col:nth-child(2n){ border-left:1px solid var(--bp-line-dark); }
  .ap-stats .ap-stat-col:nth-child(n+3){ border-top:1px solid var(--bp-line-dark); }
}

/* ---------- TEAM ---------- */
.ap-team-section{ background:var(--bp-white); }
.ap-team-grid{
  display:flex; flex-wrap:wrap; gap:1px; margin-top:10px;
  background:var(--bp-line); border:1px solid var(--bp-line); box-shadow:var(--bp-shadow-soft);
}
.ap-team-block{ flex:1 1 260px; max-width:280px; background:var(--bp-white); transition:box-shadow .35s var(--bp-ease); position:relative; z-index:1; }
.ap-team-block:hover{ box-shadow:0 30px 60px -26px rgba(20,184,166,.3); z-index:2; }
.ap-team-inner{ padding:28px 20px; text-align:center; height:100%; }
.ap-team-photo{
  width:88px;height:88px;margin:0 auto 16px;
  overflow:hidden; border:1px solid var(--bp-line);
  position:relative;
  transition:box-shadow .3s var(--bp-ease), border-color .3s var(--bp-ease);
}
.ap-team-block:hover .ap-team-photo{
  border-color:var(--ap-teal-500);
  box-shadow:0 0 0 4px rgba(45,212,191,.12);
}
.ap-team-photo img{ width:100%;height:100%;object-fit:cover;filter:grayscale(1);transition:filter .3s var(--bp-ease); }
.ap-team-block:hover .ap-team-photo img{ filter:grayscale(0); }
.ap-team-name{ font-family:var(--bp-font-display); font-size:17.5px; font-weight:700; margin-bottom:4px; }
.ap-team-role{ display:block; color:var(--ap-teal-700); font-size:11.5px; font-weight:600; margin-bottom:10px; font-family:var(--bp-font-mono); text-transform:uppercase; letter-spacing:.04em; }
.ap-team-meta{ display:block; color:var(--bp-muted); font-size:12.5px; margin-top:4px; }
.ap-team-social{ list-style:none; display:flex; justify-content:center; gap:8px; margin:16px 0 0; padding:0; }
.ap-team-social a{
  width:30px;height:30px; display:flex;align-items:center;justify-content:center;
  border:1px solid var(--bp-line); color:var(--bp-text); transition:all .2s var(--bp-ease);
}
.ap-team-social a:hover{ background:var(--ap-navy-900); border-color:var(--ap-navy-900); color:var(--ap-teal-300); }

/* ---------- PROCESS ---------- */
.ap-process-section{ background:var(--ap-navy-950); position:relative; }
.ap-process-title h2{
  font-family:var(--bp-font-display); font-weight:700;
  font-size:clamp(28px,4.2vw,44px); color:#fff;
}
.ap-process-title .msn-eyebrow{ color:var(--ap-teal-100); }
.ap-process-title .msn-eyebrow::before{ color:var(--ap-teal-500); }
.ap-process-step{
  background:var(--ap-navy-900);
  border:1px solid var(--bp-line-dark);
  padding:30px 24px 26px;
  height:100%;
  position:relative;
  overflow:hidden;
  transition:border-color .3s var(--bp-ease), box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.ap-process-step::before{
  content:"";
  position:absolute; top:0; left:0; right:0; height:2px;
  background-image:var(--ap-teal-gradient-hard);
  transform:scaleX(0); transform-origin:left;
  transition:transform .4s var(--bp-ease);
}
.ap-process-step:hover::before{ transform:scaleX(1); }
.ap-process-step:hover{ border-color:rgba(45,212,191,.5); box-shadow:0 20px 50px -24px rgba(45,212,191,.3); transform:translateY(-3px); }
.ap-process-number{
  display:inline-flex; align-items:center; justify-content:center;
  width:46px; height:46px;
  border:1px solid var(--ap-teal-500);
  border-radius:50%;
  font-family:var(--bp-font-mono);
  font-weight:600; font-size:16px;
  color:var(--ap-teal-300);
  background:radial-gradient(circle at 30% 30%, rgba(45,212,191,.16), transparent 70%);
  box-shadow:0 0 0 1px rgba(45,212,191,.1);
  margin-bottom:16px;
}
.ap-process-heading{
  font-family:var(--bp-font-display);
  font-weight:700; font-size:18px; color:#fff;
  margin-bottom:10px;
}
.ap-process-desc p{ font-size:14.5px !important; color:rgba(255,255,255,.55) !important; line-height:1.7; }
.ap-process-arrow{
  position:absolute; top:50%; right:-32px; width:32px; height:1px;
  background:repeating-linear-gradient(to right,var(--bp-line-dark),var(--bp-line-dark) 4px,transparent 4px,transparent 8px);
}
.ap-process-arrow::after{
  content:''; position:absolute; right:-5px; top:-4px;
  border-top:5px solid transparent; border-bottom:5px solid transparent; border-left:5px solid rgba(255,255,255,.3);
}
@media (max-width:991px){ .ap-process-arrow{ display:none; } }
.ap-process-arrow.arrow-hidden{ display:none !important; }
.ap-arrow-down{ transform:rotate(90deg); }

/* rich-text description tables (kept from original page, restyled to match tokens) */
.about-content table{ width:100%; border:1px solid var(--bp-line); margin:16px 0; }
.about-content table th, .about-content table td{ border:1px solid var(--bp-line); padding:8px 12px; font-size:15px; }
.about-content .marker{ background-color:rgba(45,212,191,.18); }

/* ---------- CORE VALUES (new) ---------- */
.ap-values{ background:var(--bp-paper); }
.ap-values-grid{
  display:grid; grid-template-columns:repeat(4, 1fr); gap:22px; margin-top:8px;
}
@media (max-width:991px){ .ap-values-grid{ grid-template-columns:repeat(2, 1fr); } }
@media (max-width:575px){ .ap-values-grid{ grid-template-columns:1fr; } }
.ap-value-card{ text-align:center; padding:6px; }
.ap-value-badge{
  width:64px; height:64px; margin:0 auto 18px;
  border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  border:1px solid var(--ap-teal-500);
  background:radial-gradient(circle at 30% 30%, rgba(45,212,191,.14), transparent 70%);
  color:var(--ap-teal-700);
  font-size:23px;
  transition:box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.ap-value-card:hover .ap-value-badge{ box-shadow:0 0 0 6px rgba(45,212,191,.1); transform:translateY(-3px); }
.ap-value-card h5{ font-family:var(--bp-font-display); font-size:18px; font-weight:700; margin-bottom:8px; }
.ap-value-card p{ font-size:14px; line-height:1.7; color:var(--bp-muted); margin:0; }

/* ---------- OUR PROMISE — pull-quote band (new) ---------- */
.ap-quote-band{
  position:relative;
  background-color:#05070d !important;
  background-image:radial-gradient(circle at 50% 0%, rgba(45,212,191,.12), transparent 60%) !important;
  padding:clamp(64px,9vw,100px) 0;
  text-align:center;
  overflow:hidden;
}
.ap-quote-mark{
  font-family:var(--bp-font-display);
  font-size:clamp(60px,9vw,110px);
  line-height:1;
  background-image:var(--ap-teal-gradient);
  -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; color:transparent;
  display:block;
  margin-bottom:-8px;
}
.ap-quote-text{
  font-family:var(--bp-font-display);
  font-size:clamp(22px,3.2vw,34px);
  font-weight:700;
  color:#ffffff !important;
  max-width:820px;
  margin:0 auto;
  line-height:1.3;
}
.ap-quote-attrib{
  margin-top:22px;
  font-family:var(--bp-font-mono);
  font-size:12.5px;
  letter-spacing:.1em;
  text-transform:uppercase;
  color:rgba(255,255,255,.5) !important;
}
</style>

@section('content')

  <div class="msn-scope">

    <!-- Hero -->
    <section class="ap-hero ap-grain">
      <div class="container ap-hero-inner">
        <span class="ap-chip msn-reveal"><span class="dot"></span>{{ $setting->title ?? 'MSN Softtech' }}</span>
        <h1 class="ap-hero-title msn-reveal">{{ __('navbar.about') }}</h1>
        <div class="ap-hero-underline msn-reveal"></div>
        <p class="ap-hero-copy msn-reveal">Get to know the team, mission, and approach behind every project we build.</p>
        <div class="ap-breadcrumb msn-reveal">
          <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
          <span class="sep">/</span>
          <span>{{ __('navbar.about') }}</span>
        </div>
      </div>

      <!-- signature element: rotating teal seal -->
      <div class="ap-seal msn-reveal" aria-hidden="true">
        <svg class="ap-seal-ring" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <path id="ap-seal-path" d="M100,100 m-82,0 a82,82 0 1,1 164,0 a82,82 0 1,1 -164,0"/>
            <linearGradient id="ap-seal-teal" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="#99F6E4"/>
              <stop offset=".5" stop-color="#14B8A6"/>
              <stop offset="1" stop-color="#5EEAD4"/>
            </linearGradient>
          </defs>
          <circle cx="100" cy="100" r="96" fill="none" stroke="url(#ap-seal-teal)" stroke-width="1.25"/>
          <circle cx="100" cy="100" r="68" fill="none" stroke="url(#ap-seal-teal)" stroke-width="1" stroke-dasharray="1 5"/>
          <text font-size="10.5" letter-spacing="3" fill="#2DD4BF" style="font-family:var(--bp-font-mono);">
            <textPath href="#ap-seal-path" startOffset="0%">MSN SOFTTECH • TRUSTED WORLDWIDE • MSN SOFTTECH • TRUSTED WORLDWIDE •</textPath>
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
              <img src="{{ asset('uploads/about/'.$about->image_path) }}" alt="{{ $about->title }}">
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
            <p>Innovative, custom-built solutions that fuel growth, optimize performance, and drive digital transformation.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-diagram-3-fill"></i></span>
            <h5>Website Development</h5>
            <p>High-performance, responsive websites crafted to deliver outstanding user experiences and business growth.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-phone"></i></span>
            <h5>Mobile App Development</h5>
            <p>Seamless, high-impact mobile experiences designed to captivate users and expand your global reach.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-bar-chart-line"></i></span>
            <h5>SEO & Marketing</h5>
            <p>Strategic SEO and marketing solutions that enhance visibility, increase engagement, and deliver measurable success.</p>
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
            ['bi-code-slash','Laravel'], ['bi-filetype-jsx','React'], ['bi-phone-fill','Flutter'],
            ['bi-cloud-fill','AWS'], ['bi-vector-pen','Figma'], ['bi-hexagon-fill','Node.js'],
            ['bi-shield-check','Security'], ['bi-search','SEO'], ['bi-database-fill','MySQL'],
            ['bi-apple','iOS'], ['bi-google-play','Android'], ['bi-gear-fill','DevOps'],
          ];
        @endphp
        @for($r=0; $r<2; $r++)
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
                    <img src="{{ asset('uploads/member/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">
                  </div>
                  <h3 class="ap-team-name"><a>{{ $member->title }}</a></h3>
                  <span class="ap-team-role">{{ $member->designation->title }}@if(isset($member->designation->department)), {{ $member->designation->department }}@endif</span>
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
        <p class="ap-quote-text">We don't just ship projects. We build the software your business will still be proud of five years from now.</p>
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
                  <div class="ap-process-arrow d-none d-md-block {{ $showArrow ? ($key == 2 ? 'ap-arrow-down' : '') : 'arrow-hidden' }}"></div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="text-center mt-5">
            <a href="https://msnsofttech.com/get-quote" class="msn-btn msn-btn-primary ap-btn-shine">Get in Touch With Us →</a>
          </div>
        </div>
      </section>
    @endif

  </div><!-- /.msn-scope -->

  <script>
  (function(){
    // premium touch: count the stats up when they scroll into view
    var stats = document.querySelectorAll('.ap-stat-value[data-count]');
    if(!stats.length || !('IntersectionObserver' in window)) return;
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(!entry.isIntersecting) return;
        var el = entry.target;
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var current = 0;
        var step = Math.max(1, Math.ceil(target / 60));
        (function tick(){
          current += step;
          if(current >= target){
            el.textContent = target;
          } else {
            el.textContent = current;
            requestAnimationFrame(tick);
          }
        })();
        io.unobserve(el);
      });
    }, { threshold: 0.4 });
    stats.forEach(function(el){ io.observe(el); });
  })();
  </script>

@endsection
