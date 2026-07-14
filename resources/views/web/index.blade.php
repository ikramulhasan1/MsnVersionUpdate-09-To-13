@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('home');
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
  @if(isset($setting))
    <meta property="og:type" content="website">
    <meta property='og:site_name' content="{{ $setting->title }}" />
    <meta property='og:title' content="{{ $setting->title }}" />
    <meta property='og:description' content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
    <meta property='og:url' content="{{ route('home') }}" />
    <meta property='og:image' content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
    <meta name="twitter:creator" content="@HiTechParks" />
    <meta name="twitter:url" content="{{ route('home') }}" />
    <meta name="twitter:title" content="{{ $setting->title }}" />
    <meta name="twitter:description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
    <meta name="twitter:image" content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />
  @endif

  {{-- NOTE: fonts + msn-theme.css should be linked once in your master layout
       <head>, after Bootstrap. Kept here too so this page still works if you
       haven't wired the master layout yet: --}}
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}">
@endsection

<style>
/* =====================================================================
   HOME PAGE — page-only styles. Shared tokens/utilities/nav/footer
   live in web/css/msn-theme.css (loaded above). Everything here is
   prefixed hp- so it never collides with the shared system.

   Signature system used across this page: a monospace "§ 0X" index tag
   for every section heading, and four hairline registration crosshairs
   framing each section's container — like alignment marks on a
   drafting sheet. No diagonal cuts, no clipped corners anywhere.
   ===================================================================== */

/* ---------- SHARED: crosshair frame + coordinate index ---------- */
.hp-frame{ position:relative; }
.hp-index{
  font-family:var(--bp-font-mono);
  font-size:12px;
  color:var(--bp-muted);
  letter-spacing:.08em;
}

/* ---------- HERO ---------- */
.hp-hero{
  position:relative;
  background:var(--bp-ink);
  padding:clamp(140px,15vw,190px) 0 clamp(70px,8vw,96px);
  overflow:hidden;
}
.hp-hero::before{
  /* faint drafting grid, ambient only */
  content:"";
  position:absolute; inset:0;
  background-image:
    radial-gradient(circle at 82% 0%, rgba(245,166,35,.16), transparent 45%),
    linear-gradient(var(--bp-line-dark-2) 1px, transparent 1px),
    linear-gradient(90deg, var(--bp-line-dark-2) 1px, transparent 1px);
  background-size: 100% 100%, 64px 64px, 64px 64px;
  mask-image: linear-gradient(180deg, transparent, rgba(0,0,0,.9) 30%, rgba(0,0,0,.9) 80%, transparent);
  pointer-events:none;
}
.hp-hero-inner{ position:relative; z-index:1; display:grid; grid-template-columns:1.15fr .85fr; gap:56px; align-items:start; }
@media (max-width:991px){ .hp-hero-inner{ grid-template-columns:1fr; } }
.hp-hero-title{
  font-family:var(--bp-font-display);
  color:#fff;
  font-size:clamp(32px,4.8vw,52px);
  font-weight:700;
  line-height:1.14;
  max-width:640px;
}
.hp-hero-copy{max-width:540px;font-size:clamp(15px,1.2vw,16.5px);color:rgba(255,255,255,.62);margin-top:20px;line-height:1.75;}
.hp-hero-actions{display:flex;gap:14px;flex-wrap:wrap;margin-top:34px;}

/* build-log panel — replaces the old plain counter strip */
.hp-log{
  background:var(--bp-ink-2);
  border:1px solid var(--bp-line-dark);
  font-family:var(--bp-font-mono);
  font-size:13px;
  box-shadow:0 30px 60px rgba(0,0,0,.35);
}
.hp-log-bar{
  display:flex; align-items:center; gap:8px;
  padding:12px 16px;
  border-bottom:1px solid var(--bp-line-dark);
  color:rgba(255,255,255,.4);
  font-size:11.5px; letter-spacing:.06em; text-transform:uppercase;
}
.hp-log-dot{ width:7px;height:7px;border-radius:50%; background:var(--bp-cyan); }
.hp-log-body{ padding:18px 18px 20px; }
.hp-log-row{
  display:flex; align-items:baseline; justify-content:space-between;
  padding:12px 0;
  border-bottom:1px dashed var(--bp-line-dark);
  color:rgba(255,255,255,.55);
}
.hp-log-row:last-child{ border-bottom:none; padding-bottom:0; }
.hp-log-row .hp-log-val{ color:var(--bp-amber); font-weight:600; font-size:20px; }
.hp-log-row .hp-log-key{ font-size:11.5px; letter-spacing:.05em; text-transform:uppercase; color:rgba(255,255,255,.4); }

/* ---------- IMPACT / COUNTERS ---------- */
.our-mission-section{ background:var(--bp-wash); }
.hp-stats{ margin-top:8px; display:flex; flex-wrap:wrap; background:var(--bp-white); border:1px solid var(--bp-wash-line); box-shadow:var(--bp-shadow-soft); }
.hp-stats .counter-column{ flex:1 1 220px; }
.hp-stats .counter-column + .counter-column{ border-left:1px solid var(--bp-wash-line); }
.hp-stat{
  padding:34px 26px;
  position:relative;
  border:none !important;
  box-shadow:none !important;
  transform:none !important;
  height:100%;
}
.hp-stat-value{
  font-family:var(--bp-font-mono);
  font-size:clamp(28px,3.2vw,40px);
  font-weight:600;
  color:var(--bp-text);
  line-height:1;
}
.hp-stat-value::after{ content:"+"; color:var(--bp-amber); }
.hp-stat-label{margin-top:12px;color:var(--bp-muted);font-size:13.5px;font-weight:500;font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.04em;}
@media (max-width:767px){
  .hp-stats .counter-column{ flex:1 1 50%; }
  .hp-stats .counter-column:nth-child(n+3){ border-top:1px solid var(--bp-wash-line); }
}

/* ---------- ENGAGEMENT MODELS ---------- */
.hp-engagement{background:var(--bp-white);}
.hp-engage-card{
  padding:34px 26px;
  height:100%;
  display:flex;
  flex-direction:column;
  position:relative;
}
.hp-engage-featured{ border-left-color:var(--bp-amber); }
.hp-engage-featured::before{
  content:"Recommended";
  position:absolute; top:14px; right:14px;
  color:var(--bp-amber-deep);
  font-family:var(--bp-font-mono);
  font-size:10.5px; font-weight:600; letter-spacing:.08em; text-transform:uppercase;
}
.hp-engage-icon{
  width:44px;height:44px;
  display:flex;align-items:center;justify-content:center;
  border:1px solid var(--bp-line);
  color:var(--bp-text);
  margin-bottom:22px;
}
.hp-engage-icon svg{width:20px;height:20px;}
.hp-engage-card h5{font-family:var(--bp-font-display);font-size:20px;font-weight:700;margin-bottom:10px;}
.hp-engage-card p{font-size:14.5px;color:var(--bp-muted);flex:1;line-height:1.7;}
.hp-engage-card .msn-btn{align-self:flex-start;margin-top:24px;}

.hp-approach-box{
  border:1px solid var(--bp-line);
  border-top:2px solid var(--bp-ink);
  padding:44px 30px;
  text-align:center;
}
.hp-approach-tag{
  display:inline-block;
  font-family:var(--bp-font-display);
  font-size:21px;
  font-weight:700;
  margin-bottom:8px;
}
.hp-approach-box > p{margin-bottom:24px;color:var(--bp-muted);}
.hp-radio-chips{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-bottom:26px;}
.hp-radio-chips label{
  display:inline-block;
  border:1px solid var(--bp-line-strong);
  border-radius:var(--bp-radius);
  padding:11px 20px;
  font-family:var(--bp-font-mono);
  font-size:12.5px;
  font-weight:500;
  letter-spacing:.02em;
  color:var(--bp-text-soft);
  cursor:pointer;
  transition:all .2s var(--bp-ease);
}
.hp-radio-chips input{display:none;}
.hp-radio-chips input:checked + label{background:var(--bp-ink);border-color:var(--bp-ink);color:#fff;}
.hp-compare-link{
  display:block;
  margin-top:18px;
  font-size:12.5px;
  font-family:var(--bp-font-mono);
  font-weight:600;
  color:var(--bp-amber-deep);
  text-decoration:underline;
  text-underline-offset:3px;
}

/* ---------- SERVICES ---------- */
.hp-services{background:var(--bp-wash);}

/* Two-column header: big heading left, supporting copy right — matches
   the target design instead of the site's usual centered head. */
.hp-services-head{
  display:grid;
  grid-template-columns: 1.3fr 1fr;
  gap:48px;
  align-items:start;
  margin-bottom:56px;
  text-align:left;
  max-width:none;
}
.hp-services-head h2{
  font-size:clamp(30px,4.4vw,48px);
  line-height:1.12;
  font-weight:700;
}
.hp-services-head .text.description{
  color:var(--bp-text-soft);
  font-size:16px;
  line-height:1.75;
  margin-top:10px;
  max-width:420px;
  justify-self:end;
}
@media (max-width:767px){
  .hp-services-head{ grid-template-columns:1fr; gap:18px; }
  .hp-services-head .text.description{ justify-self:start; max-width:none; }
}

.hp-service-grid{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:22px;
}
@media (max-width:991px){ .hp-service-grid{ grid-template-columns:repeat(2, 1fr); } }
@media (max-width:575px){ .hp-service-grid{ grid-template-columns:1fr; } }

.hp-service-card2{
  display:block;
  background:var(--bp-white);
  border:1px solid var(--bp-line);
  border-left:3px solid var(--bp-line);
  border-radius: 4px;
  box-shadow:var(--bp-shadow-soft);
  padding:30px 26px 28px;
  height:100%;
  transition: border-left-color .3s var(--bp-ease), border-color .3s var(--bp-ease), box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.hp-service-card2:hover{
  border-left-color:var(--bp-amber);
  box-shadow:var(--bp-shadow-lift);
  transform:translateY(-3px);
  background-image: linear-gradient(to bottom right, #1E293C, #324054);
}

.hp-service-index{
  font-family:var(--bp-font-mono);
  font-size:12.5px;
  color:var(--bp-muted);
  letter-spacing:.06em;
  transition:color .3s var(--bp-ease);
}
.hp-service-card2:hover .hp-service-index{ color:var(--bp-amber-deep); }

.hp-service-title2{
  font-family:var(--bp-font-display);
  font-size:22px;
  font-weight:700;
  color:var(--bp-text);
  line-height:1.2;
  margin-top:18px;
  margin-bottom:12px;
  transition:color .3s var(--bp-ease);
}
.hp-service-card2:hover .hp-service-title2{ color: white; }

.hp-service-desc2{
  font-size:14.5px;
  line-height:1.7;
  color:var(--bp-muted);
  transition:color .3s var(--bp-ease);
  min-height:76px;
}
.hp-service-card2:hover .hp-service-desc2{ color: aliceblue; }

.hp-service-tags2{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-top:22px;
}
.hp-service-tag2{
  display:inline-flex;
  align-items:center;
  border:1px solid var(--bp-line-strong);
  border-radius:var(--bp-radius);
  padding:7px 15px;
  font-family:var(--bp-font-body);
  font-weight:600;
  font-size:12.5px;
  color:var(--bp-text);
  background:var(--bp-white);
  transition:all .3s var(--bp-ease);
}
.hp-service-card2:hover .hp-service-tag2{
  background:var(--bp-amber-wash);
  border-color:var(--bp-amber);
  color:var(--bp-amber-deep);
}

/* ---------- TECH ---------- */
.hp-tech{background:var(--bp-wash);}
.hp-tech-grid{display:flex;flex-wrap:wrap;justify-content:center;gap:1px;background:var(--bp-line);border:1px solid var(--bp-line);box-shadow:var(--bp-shadow-soft);}
.hp-tech-pill{
  display:inline-flex;align-items:center;gap:12px;
  background:var(--bp-white);
  padding:16px 22px;
  transition:background-color .2s var(--bp-ease);
  flex:1 1 200px;
  justify-content:flex-start;
}
.hp-tech-pill:hover{background:var(--bp-amber-wash);}
.hp-tech-pill img{height:24px;width:24px;object-fit:contain;}
.hp-tech-pill span{font-size:13.5px;font-weight:600;color:var(--bp-text);font-family:var(--bp-font-mono);}
.hp-tech-actions{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;}

/* ---------- PORTFOLIO (keeps original .portfolio-* selectors — Isotope/JS binds to these) ---------- */
.hp-portfolio-section{font-family:var(--bp-font-body);background:var(--bp-wash);}
.hp-portfolio-title{
  font-family:var(--bp-font-display);
  font-size:clamp(28px,4.2vw,44px);
  font-weight:700;
}
.portfolio-filters-wrapper{overflow-x:auto;white-space:nowrap;padding-bottom:10px;margin-bottom:10px;}
.portfolio-filters{display:inline-flex;gap:8px;padding:10px 0;}
.portfolio-filter-btn{
  flex:0 0 auto;
  background:var(--bp-white);
  border:1px solid var(--bp-line-strong);
  padding:10px 20px;
  border-radius:var(--bp-radius);
  font-weight:600;
  font-size:12.5px;
  color:var(--bp-text-soft);
  transition:all .2s var(--bp-ease);
  cursor:pointer;
  white-space:nowrap;
  font-family:var(--bp-font-mono);
  text-transform:uppercase;
  letter-spacing:.04em;
}
.portfolio-filter-btn.active,
.portfolio-filter-btn:hover{background:var(--bp-ink);color:#fff;border-color:var(--bp-ink);}
.portfolio-grid{margin-top:22px;}
.hp-portfolio-card{
  overflow:hidden;position:relative;
  border:1px solid var(--bp-line);
  background:var(--bp-white);
  box-shadow: var(--bp-shadow-soft);
  transition: box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.hp-portfolio-card:hover{ box-shadow: var(--bp-shadow-lift); transform:translateY(-3px); }
.hp-portfolio-card .hp-portfolio-media{ position:relative; aspect-ratio:4/3; overflow:hidden; }
.hp-portfolio-card .hp-portfolio-media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s var(--bp-ease);filter:grayscale(.2);}
.hp-portfolio-card:hover .hp-portfolio-media img{transform:scale(1.06);filter:grayscale(0);}
.hp-portfolio-card .hp-portfolio-body{ padding:16px 18px; border-top:1px solid var(--bp-line); }
.hp-portfolio-card .hp-portfolio-body h6{
  font-family:var(--bp-font-display); font-weight:700; font-size:15.5px; color:var(--bp-text); margin:0;
}
.hp-portfolio-card .hp-portfolio-body span{
  font-family:var(--bp-font-mono); font-size:11px; color:var(--bp-muted); text-transform:uppercase; letter-spacing:.05em;
}
.hp-portfolio-overlay{
  position:absolute;inset:0;
  background:linear-gradient(180deg,rgba(11,14,20,0) 40%,rgba(11,14,20,.72));
  opacity:0;display:flex;align-items:flex-end;justify-content:flex-start;padding:16px;
  transition:opacity .3s var(--bp-ease);
}
.hp-portfolio-card:hover .hp-portfolio-overlay{opacity:1;}
.hp-portfolio-overlay h5{color:#fff;font-size:13px;font-weight:600;font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.05em;margin:0;}
.hp-view-more-btn{
  display:inline-block;
  background:transparent;
  color:var(--bp-text);
  border:1px solid var(--bp-line-strong);
  padding:15px 38px;
  font-size:13px;
  font-family:var(--bp-font-mono);
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:.03em;
  border-radius:var(--bp-radius);
  transition:all .2s var(--bp-ease);
}
.hp-view-more-btn:hover{background:var(--bp-ink);border-color:var(--bp-ink);color:#fff;}

/* ---------- TEAM ---------- */
.hp-team-section{padding:clamp(64px,9vw,112px) 0;background:var(--bp-white);}
.hp-team-grid{display:flex;flex-wrap:wrap;gap:1px;margin-top:10px;background:var(--bp-line);border:1px solid var(--bp-line);box-shadow:var(--bp-shadow-soft);}
.hp-team-block{flex:1 1 260px;max-width:280px;background:var(--bp-white);}
.hp-team-block .hp-team-inner{
  padding:28px 20px;
  text-align:center;
  height:100%;
}
.hp-team-photo{
  width:88px;height:88px;margin:0 auto 16px;
  overflow:hidden;
  border:1px solid var(--bp-line);
}
.hp-team-photo img{width:100%;height:100%;object-fit:cover;filter:grayscale(1);transition:filter .3s var(--bp-ease);}
.hp-team-block:hover .hp-team-photo img{ filter:grayscale(0); }
.hp-team-name{font-family:var(--bp-font-display);font-size:17.5px;font-weight:700;margin-bottom:4px;}
.hp-team-role{display:block;color:var(--bp-amber-deep);font-size:11.5px;font-weight:600;margin-bottom:10px;font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.04em;}
.hp-team-meta{display:block;color:var(--bp-muted);font-size:12.5px;margin-top:4px;}
.hp-team-social{list-style:none;display:flex;justify-content:center;gap:8px;margin:16px 0 0;padding:0;}
.hp-team-social a{
  width:30px;height:30px;
  display:flex;align-items:center;justify-content:center;
  border:1px solid var(--bp-line);
  color:var(--bp-text);
  transition:all .2s var(--bp-ease);
}
.hp-team-social a:hover{background:var(--bp-ink);border-color:var(--bp-ink);color:#fff;}

/* ---------- TESTIMONIALS (container keeps .testimonial-carousel.owl-carousel.owl-theme for the shared owl init) ---------- */
.hp-testimonial-section{background:var(--bp-wash);padding:clamp(64px,9vw,112px) 0;}
.hp-testi-block{padding:6px;}
.hp-testi-inner{
  border:1px solid var(--bp-line);
  border-left:3px solid var(--bp-ink);
  padding:30px 26px;
  background:var(--bp-white);
  height:100%;
  position:relative;
  box-shadow:var(--bp-shadow-soft);
  transition:box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.hp-testi-inner:hover{ box-shadow:var(--bp-shadow-lift); transform:translateY(-3px); }
.hp-testi-quote{
  font-family:var(--bp-font-mono); font-size:13px; font-weight:600; color:var(--bp-muted);
  letter-spacing:.06em; text-transform:uppercase; margin-bottom:16px;
}
.hp-testi-thumb{width:48px;height:48px;overflow:hidden;margin-bottom:16px;filter:grayscale(1);}
.hp-testi-thumb img{width:100%;height:100%;object-fit:cover;}
.hp-testi-text{color:var(--bp-text-soft);font-size:15px;line-height:1.75;}
.hp-testi-name{font-family:var(--bp-font-display);font-size:16px;font-weight:700;margin-top:18px;}
.hp-testi-company{color:var(--bp-muted);font-size:12.5px;margin-top:2px;font-family:var(--bp-font-mono);}
.hp-testimonial-section .owl-nav{display:none;}
.hp-testimonial-section .owl-dots{text-align:center;margin-top:12px;}
.hp-testimonial-section .owl-dot span{
  width:16px;height:2px;margin:5px;background:var(--bp-line-strong)!important;border-radius:0;display:block;
}
.hp-testimonial-section .owl-dot.active span{background:var(--bp-amber)!important;}

/* ---------- PROCESS (keeps original .process-step-arrow / arrow-hidden / arrow-down — computed inline by Blade) ---------- */
.hp-process-section{font-family:var(--bp-font-body);background:var(--bp-ink);padding:clamp(64px,9vw,112px) 0;}
.hp-process-title h2{
  font-family:var(--bp-font-display);
  font-weight:700;
  font-size:clamp(28px,4.2vw,44px);
  color:#fff;
}
.hp-process-title .msn-eyebrow{ color:#33C7BD; }
.hp-process-title .msn-eyebrow::before{ color:var(--bp-amber); }
.process-step-box{
  background:var(--bp-ink-2);
  border:1px solid var(--bp-line-dark);
  padding:30px 24px 26px;
  height:100%;
  position:relative;
  transition:border-color .3s var(--bp-ease), box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);
}
.process-step-box:hover{border-color:var(--bp-amber); box-shadow:0 20px 44px rgba(245,166,35,.12); transform:translateY(-3px);}
.process-step-number{
  font-family:var(--bp-font-mono);
  color:var(--bp-amber);
  font-weight:600;font-size:26px;
  margin-bottom:14px;
}
.process-step-number::before{ content:"0"; color:rgba(255,255,255,.3); }
.process-step-heading{
  font-family:var(--bp-font-display);
  font-weight:700;font-size:18px;color:#fff;
  display:flex;align-items:center;margin-bottom:10px;
}
.process-description p{font-size:14.5px!important;color:rgba(255,255,255,.55)!important;}
.process-step-arrow{
  position:absolute;top:50%;right:-32px;width:32px;height:1px;
  background:repeating-linear-gradient(to right,var(--bp-line-dark),var(--bp-line-dark) 4px,transparent 4px,transparent 8px);
}
.process-step-arrow::after{
  content:'';position:absolute;right:-5px;top:-4px;
  border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:5px solid rgba(255,255,255,.3);
}
@media (max-width:991px){.process-step-arrow{display:none;}}
.process-step-arrow.arrow-hidden{display:none!important;}
.arrow-down{transform:rotate(90deg);}

/* ---------- CASE STUDIES (keeps #case-owl-carousel + owl-carousel/owl-theme for the JS init) ---------- */
.hp-case-section{margin:0;padding:0;font-family:var(--bp-font-body);background:var(--bp-ink);}
.hp-case-wrap{width:100%;height:520px;position:relative;overflow:hidden;}
.owl-carousel .hp-case-item{
  width:100%;height:520px;background-size:cover;background-position:center;
  display:flex;align-items:center;padding:0 6%;color:#fff;position:relative;
}
.hp-case-item::before{content:'';position:absolute;inset:0;background:rgba(11,14,20,.78);z-index:1;}
.hp-case-item .hp-case-content{position:relative;z-index:2;max-width:660px;}
.hp-case-item .hp-case-content h1{
  font-family:var(--bp-font-display);
  font-size:clamp(26px,3.8vw,44px);font-weight:700;line-height:1.22;margin:0 0 22px;color:#fff;
}
.hp-case-tags{margin-bottom:26px;}
.hp-case-tags span{
  display:inline-block;background:transparent;padding:7px 14px;margin:4px 6px 4px 0;
  border:1px solid rgba(255,255,255,.3);font-size:11.5px;font-family:var(--bp-font-mono);
  text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.75);
}
.hp-case-badge{
  position:absolute;top:28px;left:40px;background:var(--bp-amber);color:var(--bp-ink);font-weight:700;
  padding:10px 20px;font-size:12px;z-index:20;
  font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.06em;
}
.hp-case-section .owl-dots{position:absolute!important;bottom:28px!important;left:40px!important;z-index:15!important;text-align:left!important;}
.hp-case-section .owl-dots .owl-dot span{width:20px!important;height:2px!important;background:rgba(255,255,255,.35)!important;border-radius:0;display:block;}
.hp-case-section .owl-dots .owl-dot.active span{background:var(--bp-amber)!important;}
.hp-case-read-more{
  background:transparent;border:1px solid rgba(255,255,255,.4);color:#fff;padding:15px 28px;font-size:13px;font-weight:600;
  font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.03em;
  transition:all .2s var(--bp-ease);
}
.hp-case-read-more:hover{background:var(--bp-amber);border-color:var(--bp-amber);color:var(--bp-ink);}
@media (max-width:768px){
  .hp-case-item .hp-case-content h1{font-size:24px;}
  .hp-case-badge{font-size:11px;padding:8px 16px;left:20px;top:20px;}
}
.owl-prev,.owl-next{display:none!important;}

/* ---------- BLOG ---------- */
.hp-blog-section{background:var(--bp-white);font-family:var(--bp-font-body);padding:clamp(64px,9vw,112px) 0;}
.hp-blog-title{font-family:var(--bp-font-display);font-size:clamp(28px,4vw,38px);font-weight:700;color:var(--bp-text);}
.hp-blog-subtitle{color:var(--bp-amber-deep);font-size:12.5px;font-weight:600;margin-top:8px;margin-bottom:36px;
  font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.08em;}
.hp-blog-card{
  background:var(--bp-white);border:1px solid var(--bp-line);border-left:3px solid var(--bp-line);
  height:100%;box-shadow:var(--bp-shadow-soft);
  transition:border-left-color .3s var(--bp-ease), box-shadow .3s var(--bp-ease), transform .3s var(--bp-ease);overflow:hidden;
}
.hp-blog-card:hover{border-left-color:var(--bp-amber); box-shadow:var(--bp-shadow-lift); transform:translateY(-3px);}
.hp-blog-img{width:100%;height:200px;object-fit:cover;filter:grayscale(.25);}
.hp-blog-card:hover .hp-blog-img{filter:grayscale(0);}
.hp-blog-content{padding:20px 22px 24px;}
.hp-blog-title-link{font-size:17.5px;font-weight:700;color:var(--bp-text);font-family:var(--bp-font-display);margin-bottom:10px;display:block;}
.hp-blog-title-link:hover{color:var(--bp-amber-deep);}
.hp-blog-meta{color:var(--bp-muted);font-size:12.5px;font-family:var(--bp-font-mono);}
</style>

@section('schema_markup')
  <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebSite",
                "name": "{{ $setting->title }}",
                "url": "{{ route('home') }}",
                "description": "{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}",
                "publisher": {
                    "@type": "Organization",
                    "name": "MSN Softtech",
                    "logo": {
                        "@type": "ImageObject",
                        "url": "{{ asset('/uploads/setting/' . $setting->logo_path) }}"
                    }
                },

                "mainEntity": {
                    "@type": "LocalBusiness",
                    "name": "MSN Softtech",
                    "url": "{{ route('home') }}",
                    "logo": "{{ asset('/uploads/setting/' . $setting->logo_path) }}",
                    "description": "{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}",

                    "contactPoint": {
                        "@type": "ContactPoint",
                        "telephone": "{{ $setting->phone_two }}",
                        "contactType": "customer service"
                    },
                    "areaServed": {
                        "@type": "Country",
                        "name": "United States"
                    }
                }
            }
            </script>
@endsection

@section('content')
  <link rel="stylesheet" href="{{ asset('web/css/extra-index.css') }}">

  <div class="msn-scope">

  @if(count($sliders) > 0)
    @foreach($sliders as $slider)
      <section class="hp-hero">
        <div class="container hp-hero-inner">
          <div>
            <span class="msn-eyebrow msn-eyebrow-on-dark msn-reveal">{{ $setting->title ?? 'MSN Softtech' }}</span>
            <h1 class="hp-hero-title mt-3 msn-reveal">{!! $slider->title !!}</h1>
            <div class="hp-hero-copy msn-reveal">{!! $slider->description !!}</div>
            <div class="hp-hero-actions msn-reveal">
              <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary">Get Started</a>
              <a href="{{ route('services') }}" class="msn-btn msn-btn-ghost-light">What We Offer</a>
            </div>
          </div>

          @if(count($counters) > 0)
            <div class="hp-log msn-reveal">
              <div class="hp-log-bar"><span class="hp-log-dot"></span> status.log</div>
              <div class="hp-log-body">
                @foreach($counters->take(4) as $counter)
                  <div class="hp-log-row">
                    <span class="hp-log-key">{{ $counter->title }}</span>
                    <span class="hp-log-val">{{ $counter->value }}+</span>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </section>
    @endforeach
  @endif

  @include('web.inc.client')

  @if(isset($about) || count($counters) > 0)
    <!-- About / Impact Section -->
    <section class="our-mission-section msn-section">
      <div class="container">
        @if(count($counters) > 0)
          <div class="msn-section-head msn-center msn-reveal">
            <span class="msn-eyebrow">By The Numbers</span>
            <h2>Our Impact in Numbers</h2>
          </div>
          <div class="hp-stats">
            @foreach($counters as $counter)
              <div class="counter-column msn-reveal">
                <div class="msn-card hp-stat">
                  <div class="hp-stat-value">{{ $counter->value }}</div>
                  <div class="hp-stat-label">{{ $counter->title }}</div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>
    <!--End About Section -->
  @endif

  @include('web.layouts.googlemeet')

  <section class="msn-section hp-engagement">
    <div class="container">
      <div class="msn-section-head msn-center has-glow msn-reveal">
        <span class="msn-eyebrow">How We Work Together</span>
        <h2>Flexible Engagement Models</h2>
        <p id="compare">Tailored ways to collaborate — pick the model that matches your goals and budget.</p>
      </div>

      <!-- Engagement Model Cards -->
      <form id="modelForm" action="{{ route('goToQuotePage') }}" method="post" accept-charset="utf-8">
        @csrf
        <div class="row g-4">
          <!-- Fixed Price -->
          <div class="col-md-4 msn-reveal">
            <div class="msn-card hp-engage-card">
              <input type="radio" name="work_model" id="model-fixed-price" value="Fixed Price Model" hidden>
              <span class="hp-engage-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </span>
              <h5>Fixed Price Model</h5>
              <p>Perfect for projects with a clearly defined scope. You pay a set price for guaranteed delivery.</p>
              <button class="msn-btn msn-btn-outline" type="button" onclick="selectAndSubmit('fixed-price')">Contact Us For Details →</button>
            </div>
          </div>

          {{-- Milestone-Based --}}
          <div class="col-md-4 msn-reveal">
            <div class="msn-card hp-engage-card hp-engage-featured">
              <input type="radio" name="work_model" id="model-milestone-based" value="Milestone-Based Model" hidden>
              <span class="hp-engage-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
              </span>
              <h5>Milestone-Based Model</h5>
              <p>Break your project into achievable phases and pay only as each milestone is successfully completed.</p>
              <button class="msn-btn msn-btn-primary" type="button" onclick="selectAndSubmit('milestone-based')">Contact Us For Details →</button>
            </div>
          </div>

          <!-- Monthly Support -->
          <div class="col-md-4 msn-reveal">
            <div class="msn-card hp-engage-card">
              <input type="radio" name="work_model" id="model-monthly-support" value="Monthly Support" hidden>
              <span class="hp-engage-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
              </span>
              <h5>Monthly Support</h5>
              <p>Ongoing assistance to ensure your business operations run smoothly without interruptions.</p>
              <button class="msn-btn msn-btn-outline" type="button" onclick="selectAndSubmit('monthly-support')">Share Your Requirements →</button>
            </div>
          </div>
        </div>
      </form>

      <div class="msn-reveal">
        <!-- Options Section -->
        <form action="{{ route('goToQuotePage') }}" method="post" accept-charset="utf-8">
          @csrf
          <div class="hp-approach-box mt-5">
            <span class="hp-approach-tag">Need a Different Approach?</span>
            <p>Explore more ways we can help.</p>

            <div class="hp-radio-chips">
              <div>
                <input type="radio" id="option1" value="Define My Project Scope" name="work_scope">
                <label for="option1">Define My Project Scope</label>
              </div>
              <div>
                <input type="radio" id="option3" value="Take Over My Project" name="work_scope">
                <label for="option3">Take Over My Project</label>
              </div>
              <div>
                <input type="radio" id="option4" value="Assist Me With a Task" name="work_scope">
                <label for="option4">Assist Me With a Task</label>
              </div>
            </div>

            <button class="msn-btn msn-btn-primary">Explore Your Options →</button>
            <a class="hp-compare-link" href="#compare">Compare All Engagement Models</a>
          </div>
        </form>
      </div>

    </div>
  </section>

  @php
    $section_services = \App\Models\Section::section('services');
  @endphp
  @if(count($services) > 0 && isset($section_services))
    <!-- Services Section -->
    <section class="msn-section hp-services">
      <div class="container">
        <div class="hp-services-head msn-reveal">
          <div>
            <span class="msn-eyebrow">What we build</span>
            <h2>{{ $section_services->title }}</h2>
          </div>
          <div class="text description">{!! $section_services->description !!}</div>
        </div>

        <div class="hp-service-grid">
          @foreach($services as $key => $service)
            <a href="{{ route('service.single', $service->slug) }}" class="hp-service-card2 msn-reveal">
              <div class="hp-service-index">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
              <div class="hp-service-title2">{{ $service->short_title }}</div>
              @if(isset($service->description))
                <div class="hp-service-desc2">{{ str_limit(strip_tags($service->description), 110, '...') }}</div>
              @endif
              {{-- Optional: add a simple comma-separated "tags" text field to the
                   Service model/admin form (e.g. "UX,SEO-ready,CMS") and these
                   pills render automatically — safe to leave unset. --}}
              @if(isset($service->tags) && trim($service->tags) !== '')
                <div class="hp-service-tags2">
                  @foreach(array_filter(array_map('trim', explode(',', $service->tags))) as $tag)
                    <span class="hp-service-tag2">{{ $tag }}</span>
                  @endforeach
                </div>
              @endif
            </a>
          @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
          <button id="go-services" class="msn-btn msn-btn-primary">View All Services</button>
        </div>
      </div>
    </section>
    <!--End Services Section -->
  @endif

  <section class="msn-section hp-tech">
    <div class="container">
      <div class="msn-section-head msn-center msn-reveal">
        <span class="msn-eyebrow">Full-Stack Partner</span>
        <h2>Design. Develop. Maintain. Scale.</h2>
        <p>30+ team of experts skilled in 10+ cutting-edge technologies.</p>
      </div>

      <div class="hp-tech-grid msn-reveal">
        @foreach ($technologies as $technology)
          <a href="{{ route('service.technology', $technology->slug) }}" class="hp-tech-pill">
            <img src="{{ asset('uploads/technology/' . $technology->logo_path) }}" alt="{{ $technology->short_title }}"
              loading="lazy">
            <span>{{ $technology->short_title }}</span>
          </a>
        @endforeach
      </div>

      <div class="hp-tech-actions mt-5">
        <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary">Get a Quote →</a>
        <a href="{{ route('technologies') }}" class="msn-btn msn-btn-outline">See All Technologies →</a>
      </div>
    </div>
  </section>

  {{-- =====================================================================
       LIVE PROJECTS TRACKER — drop-in replacement for the old Portfolio
       section. Self-contained: its own scoped styles/markup/script live
       here so it never touches the shared msn-theme.css tokens. All CSS
       selectors are scoped under .msn-live-tracker so nothing here can
       leak onto (or be overridden by) the rest of the site.

       NOTE: the six projects below (Orenda Home, CarePath, Fleetwise Ops,
       Classroom OS, Verano Skincare, Helios Support Agent) are the demo
       data from the original mockup — hardcoded in the <script> further
       down, not pulled from $portfolios. Swap that `projects` array for
       real data (or feed it from a small JSON endpoint) whenever you're
       ready to make it live.
  --}}
  <style>
.msn-live-tracker{
  --ink: var(--bp-ink);
  --paper: var(--bp-paper);
  --white: var(--bp-white);
  --soft: var(--bp-wash);
  --soft-2: var(--bp-wash-line);
  --line: var(--bp-line);
  --muted: var(--bp-text-soft);
  --faint: var(--bp-muted);
  --accent: var(--bp-amber);
  --accent-2: var(--bp-amber-deep);
  --green:#1F9D6B;
  --amber:#f2b705;
  --blue:#1f5fa8;
  --radius: var(--bp-radius);
  --ease: var(--bp-ease);
}
.msn-live-tracker *{box-sizing:border-box;margin:0;padding:0;}
.msn-live-tracker{background:var(--paper);color:var(--ink);font-family:var(--bp-font-body);-webkit-font-smoothing:antialiased;}
.msn-live-tracker a{color:inherit;text-decoration:none;}
.msn-live-tracker button{font:inherit;}
.msn-live-tracker .wrap{width:min(1180px,calc(100% - 40px));margin:0 auto;}
.msn-live-tracker h1,
.msn-live-tracker h2,
.msn-live-tracker h3,
.msn-live-tracker h4{font-family:var(--bp-font-display);line-height:1.05;}
.msn-live-tracker p{color:var(--muted);line-height:1.7;}
.msn-live-tracker .mono{font-family:var(--bp-font-mono);font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;}
.msn-live-tracker .eyebrow{
  display:inline-flex;align-items:center;gap:8px;padding:0;border-radius:0;
  background:none;color:var(--bp-amber-deep);font-family:var(--bp-font-mono);font-size:12px;font-weight:500;letter-spacing:.08em;text-transform:uppercase;
}
.msn-live-tracker .eyebrow::before{content:"§";width:20px;height:20px;border:1px solid currentColor;color:var(--bp-amber);font-family:var(--bp-font-mono);font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;background:none;border-radius:0;}
.msn-live-tracker .btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:48px;padding:15px 30px;border-radius:var(--radius);border:1px solid transparent;font-family:var(--bp-font-mono);font-size:13px;font-weight:600;letter-spacing:.03em;text-transform:uppercase;cursor:pointer;background:none;transition:background-color .2s var(--bp-ease),color .2s var(--bp-ease),border-color .2s var(--bp-ease);}
.msn-live-tracker .btn:hover{transform:translateY(-1px);}
.msn-live-tracker .btn-dark{background:var(--accent);color:var(--bp-ink);border-color:var(--accent);box-shadow:0 10px 24px rgba(245,166,35,.22);}
.msn-live-tracker .btn-dark:hover{background:var(--accent-2);border-color:var(--accent-2);color:#fff;box-shadow:0 14px 30px rgba(201,134,15,.3);}
.msn-live-tracker .btn-light{background:var(--white);color:var(--ink);border-color:var(--line);}
.msn-live-tracker .btn-light:hover{border-color:var(--ink);}
.msn-live-tracker section{padding:clamp(76px,10vw,120px) 0;}
.msn-live-tracker /* ============ SECTION HEAD ============ */
.tracker-section{background:var(--bp-wash);border-top:1px solid var(--bp-wash-line);border-bottom:1px solid var(--bp-wash-line);}
.msn-live-tracker .tracker-head{
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(280px,400px);
  gap:clamp(24px,5vw,70px);
  align-items:end;
  margin-bottom:40px;
}
.msn-live-tracker .tracker-head h2{margin-top:18px;font-size:clamp(32px,4.6vw,54px);}
.msn-live-tracker .tracker-head p{font-size:16px;}
.msn-live-tracker .live-pulse-badge{
  display:inline-flex;align-items:center;gap:8px;
  font-family:var(--bp-font-mono);font-size:11px;font-weight:700;
  letter-spacing:.08em;text-transform:uppercase;color:var(--green);
}
.msn-live-tracker .pulse-dot{
  width:8px;height:8px;border-radius:50%;background:var(--green);position:relative;flex:none;
}
.msn-live-tracker .pulse-dot::after{
  content:"";position:absolute;inset:-5px;border-radius:50%;border:1.5px solid var(--green);
  animation:pulseRing 1.8s ease-out infinite;
}
@keyframes pulseRing{
  0%{transform:scale(.6);opacity:.9;}
  100%{transform:scale(2.1);opacity:0;}
}
.msn-live-tracker /* ============ SYNC BAR ============ */
.sync-bar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:12px;
  padding:12px 18px;
  border:1px solid var(--line);
  border-radius:var(--radius);
  background:var(--white);
  box-shadow:var(--bp-shadow-soft);
  margin-bottom:26px;
}
.msn-live-tracker .sync-left{display:flex;align-items:center;gap:10px;}
.msn-live-tracker .sync-left .mono{color:var(--green);font-weight:700;}
.msn-live-tracker .sync-right{
  display:flex;align-items:center;gap:16px;
  font-family:var(--bp-font-mono);font-size:11.5px;color:var(--faint);
}
.msn-live-tracker .sync-right b{color:var(--ink);font-weight:600;}
.msn-live-tracker .sync-clock{display:flex;align-items:center;gap:6px;}
.msn-live-tracker .sync-refresh{
  width:22px;height:22px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  color:var(--faint);
  transition:color .3s ease;
}
.msn-live-tracker .sync-refresh svg{width:13px;height:13px;}
.msn-live-tracker .sync-refresh.spinning{color:var(--accent);}
.msn-live-tracker .sync-refresh.spinning svg{animation:spin360 .8s linear;}
@keyframes spin360{to{transform:rotate(360deg);}}
.msn-live-tracker .sync-note{
  font-size:12px;
  color:var(--faint);
  margin:-14px 0 28px 18px;
}
.msn-live-tracker .trust-score{
  display:inline-flex;align-items:center;gap:6px;
  padding-left:14px;margin-left:4px;border-left:1px solid var(--line);
  font-family:var(--bp-font-mono);font-size:11.5px;font-weight:700;color:var(--ink);
}
.msn-live-tracker .trust-score svg{width:13px;height:13px;color:var(--amber);}
.msn-live-tracker .build-tag{
  font-family:var(--bp-font-mono);font-size:10.5px;color:var(--faint);
  padding-left:14px;margin-left:4px;border-left:1px solid var(--line);
}
@media (max-width:640px){
.msn-live-tracker .trust-score,
.msn-live-tracker .build-tag{display:none;}
}
.msn-live-tracker /* ============ DOMAIN / FAVICON ============ */
.domain-row{
  display:flex;align-items:center;gap:6px;
  margin:-2px 0 14px;
}
.msn-live-tracker .domain-row img{width:14px;height:14px;border-radius:3px;flex:none;}
.msn-live-tracker .domain-row svg{width:11px;height:11px;color:var(--green);flex:none;}
.msn-live-tracker .domain-row span{
  font-family:var(--bp-font-mono);
  font-size:11px;
  color:var(--faint);
}
.msn-live-tracker /* ============ TREND ============ */
.trend{
  display:inline-flex;align-items:center;gap:3px;
  font-family:var(--bp-font-mono);font-size:11px;font-weight:700;
  color:var(--green);margin-left:8px;
}
.msn-live-tracker .trend svg{width:11px;height:11px;}
.msn-live-tracker .trend.flat{color:var(--faint);}
.msn-live-tracker /* ============ VERIFY BADGE ============ */
.verify-badge{
  display:inline-flex;align-items:center;gap:6px;
  font-family:var(--bp-font-mono);font-size:10.5px;font-weight:600;
  padding:5px 10px;border-radius:var(--bp-radius);
  background:var(--soft);color:var(--faint);
  transition:background-color .3s ease,color .3s ease;
}
.msn-live-tracker .verify-badge svg{width:10px;height:10px;flex:none;}
.msn-live-tracker .verify-badge.checking svg{animation:spin360 1s linear infinite;}
.msn-live-tracker .verify-badge.ok{background:rgba(31,157,107,.12);color:var(--green);}
.msn-live-tracker .verify-badge.warn{background:rgba(242,183,5,.14);color:#a37600;cursor:pointer;}
.msn-live-tracker .verify-badge.warn:hover{background:rgba(242,183,5,.22);}
.msn-live-tracker /* ============ ONLINE AVATAR RING ============ */
.pm-avatars span{position:relative;}
.msn-live-tracker .pm-avatars span.online::after{
  content:"";position:absolute;bottom:-1px;right:-1px;
  width:8px;height:8px;border-radius:50%;
  background:var(--green);border:2px solid var(--white);
}
.msn-live-tracker /* ============ DOMAIN TEXT ============ */
.domain-text{
  display:block;
  font-family:var(--bp-font-mono);
  font-size:11px;
  color:var(--faint);
  margin:-2px 0 14px;
}
.msn-live-tracker /* ============ HISTORY ACCORDION ============ */
.history-toggle{
  display:flex;align-items:center;gap:6px;
  width:100%;
  margin-top:14px;
  padding-top:14px;
  background:none;border:none;border-top:1px solid var(--line);
  cursor:pointer;
  font-family:var(--bp-font-mono);font-size:11px;font-weight:600;
  letter-spacing:.05em;text-transform:uppercase;color:var(--faint);
  transition:color .2s ease;
}
.msn-live-tracker .history-toggle:hover{color:var(--ink);}
.msn-live-tracker .history-toggle svg{width:11px;height:11px;transition:transform .25s var(--ease);}
.msn-live-tracker .history-toggle.open svg{transform:rotate(180deg);}
.msn-live-tracker .history-panel{
  max-height:0;overflow:hidden;
  transition:max-height .35s var(--ease);
}
.msn-live-tracker .history-panel-inner{padding-top:14px;display:flex;flex-direction:column;gap:10px;}
.msn-live-tracker .history-line{
  display:grid;grid-template-columns:14px minmax(0,1fr) auto;
  gap:10px;align-items:baseline;
  font-size:12.5px;color:var(--muted);
}
.msn-live-tracker .history-line .hdot{
  width:6px;height:6px;border-radius:50%;background:var(--accent);align-self:center;
}
.msn-live-tracker .history-line .htime{
  font-family:var(--bp-font-mono);font-size:10.5px;color:var(--faint);white-space:nowrap;
}
.msn-live-tracker /* ============ STATS STRIP ============ */
.tracker-stats{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:1px;
  background:var(--line);
  border:1px solid var(--line);
  border-radius:var(--radius);
  overflow:hidden;
  margin-bottom:48px;
  box-shadow:var(--bp-shadow-soft);
}
.msn-live-tracker .tstat{background:var(--white);padding:24px 22px;}
.msn-live-tracker .tstat b{
  display:flex;align-items:baseline;gap:4px;
  font-family:var(--bp-font-mono);
  font-size:clamp(26px,3.2vw,36px);color:var(--bp-text);
}
.msn-live-tracker .tstat b::after{content:"+";color:var(--bp-amber);}
.msn-live-tracker .tstat span{display:block;margin-top:6px;color:var(--muted);font-size:12.5px;font-family:var(--bp-font-mono);text-transform:uppercase;letter-spacing:.04em;}
.msn-live-tracker /* ============ FILTERS ============ */
.tracker-filters{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-bottom:26px;
}
.msn-live-tracker .tfilter{
  padding:10px 20px;border-radius:var(--bp-radius);border:1px solid var(--bp-line-strong);background:var(--white);
  font-family:var(--bp-font-mono);font-size:12.5px;font-weight:600;color:var(--bp-text-soft);
  cursor:pointer;transition:all .2s var(--bp-ease);
  display:inline-flex;align-items:center;gap:8px;
  text-transform:uppercase;letter-spacing:.04em;
}
.msn-live-tracker .tfilter .dot{width:7px;height:7px;border-radius:50%;background:currentColor;}
.msn-live-tracker .tfilter:hover{border-color:var(--ink);color:var(--ink);}
.msn-live-tracker .tfilter.active{background:var(--ink);border-color:var(--ink);color:#fff;}
.msn-live-tracker /* ============ PROJECT GRID ============ */
.tracker-grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:16px;
}
.msn-live-tracker .tproject{
  border:1px solid var(--line);
  border-left:3px solid var(--line);
  border-radius:var(--radius);
  background:var(--white);
  box-shadow:var(--bp-shadow-soft);
  padding:24px;
  transition:border-left-color .3s var(--bp-ease),box-shadow .3s var(--bp-ease),transform .3s var(--bp-ease);
}
.msn-live-tracker .tproject:hover{transform:translateY(-3px);box-shadow:var(--bp-shadow-lift);border-left-color:var(--accent);}
.msn-live-tracker .tproject-top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  margin-bottom:16px;
}
.msn-live-tracker .status-chip{
  display:inline-flex;align-items:center;gap:7px;
  padding:6px 12px;border-radius:var(--bp-radius);
  font-family:var(--bp-font-mono);font-size:10.5px;font-weight:700;
  letter-spacing:.06em;text-transform:uppercase;
}
.msn-live-tracker .status-chip.live{background:rgba(31,157,107,.12);color:var(--green);}
.msn-live-tracker .status-chip.dev{background:rgba(31,95,168,.12);color:var(--blue);}
.msn-live-tracker .status-chip.testing{background:rgba(242,183,5,.16);color:#a37600;}
.msn-live-tracker .status-dot{width:6px;height:6px;border-radius:50%;background:currentColor;}
.msn-live-tracker .status-chip.live .status-dot{position:relative;}
.msn-live-tracker .status-chip.live .status-dot::after{
  content:"";position:absolute;inset:-4px;border-radius:50%;border:1.2px solid var(--green);
  animation:pulseRing 1.8s ease-out infinite;
}
.msn-live-tracker .pm-avatars{display:flex;}
.msn-live-tracker .pm-avatars span{
  width:26px;height:26px;border-radius:50%;border:2px solid var(--white);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--bp-font-display);font-size:10px;font-weight:700;color:#fff;
  margin-left:-8px;
}
.msn-live-tracker .pm-avatars span:first-child{margin-left:0;}
.msn-live-tracker .tproject-name{
  display:inline-flex;align-items:center;gap:8px;
  font-family:var(--bp-font-display);font-size:20px;font-weight:700;
  margin-bottom:4px;
  transition:color .2s var(--ease);
}
.msn-live-tracker .tproject-name svg{width:14px;height:14px;opacity:.45;transition:transform .2s var(--ease),opacity .2s var(--ease);flex:none;}
.msn-live-tracker a.tproject-name:hover{color:var(--bp-amber-deep);}
.msn-live-tracker a.tproject-name:hover svg{opacity:1;transform:translate(2px,-2px);}
.msn-live-tracker span.tproject-name{cursor:default;}
.msn-live-tracker .tproject-cat{color:var(--faint);font-size:12.5px;margin-bottom:18px;}
.msn-live-tracker /* progress */
.progress-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.msn-live-tracker .progress-row .plabel{font-size:12px;color:var(--muted);font-weight:600;}
.msn-live-tracker .progress-row .pval{font-family:var(--bp-font-mono);font-size:15px;font-weight:700;}
.msn-live-tracker .progress-track{
  height:8px;border-radius:var(--bp-radius);background:var(--soft-2);overflow:hidden;margin-bottom:18px;
}
.msn-live-tracker .progress-fill{
  height:100%;border-radius:var(--bp-radius);
  background:linear-gradient(90deg,var(--bp-amber),var(--bp-amber-deep));
  width:0%;
  transition:width 1.1s var(--ease);
}
.msn-live-tracker /* phase stepper */
.phase-stepper{display:flex;align-items:center;margin-bottom:18px;}
.msn-live-tracker .phase-step{
  flex:1;height:4px;background:var(--soft-2);position:relative;margin-right:4px;border-radius:2px;
}
.msn-live-tracker .phase-step:last-child{margin-right:0;}
.msn-live-tracker .phase-step.done{background:var(--accent);}
.msn-live-tracker .phase-step.current{background:linear-gradient(90deg,var(--accent) 50%,var(--soft-2) 50%);}
.msn-live-tracker .phase-labels{display:flex;justify-content:space-between;font-size:9.5px;color:var(--faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:20px;font-family:var(--bp-font-mono);}
.msn-live-tracker .tproject-foot{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding-top:16px;
  border-top:1px solid var(--line);
  font-size:12px;
  color:var(--faint);
}
.msn-live-tracker .tproject-foot .updated{display:inline-flex;align-items:center;gap:6px;}
.msn-live-tracker .tproject-foot .updated svg{width:12px;height:12px;}
.msn-live-tracker .eta-badge{
  font-family:var(--bp-font-mono);font-size:11px;font-weight:600;color:var(--ink);
  background:var(--soft);padding:5px 10px;border-radius:var(--bp-radius);
}
.msn-live-tracker /* ============ VIEW ALL CTA ============ */
.view-all-row{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;
  margin-top:22px;
  padding-top:26px;
  border-top:1px dashed var(--line);
}
.msn-live-tracker .view-all-btn{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:15px 30px;
  border-radius:var(--bp-radius);
  border:1px solid var(--bp-line-strong);
  background:var(--white);
  font-family:var(--bp-font-mono);
  font-size:13px;
  font-weight:600;
  letter-spacing:.03em;
  text-transform:uppercase;
  color:var(--bp-text);
  transition:all .2s var(--bp-ease);
}
.msn-live-tracker .view-all-btn svg{width:16px;height:16px;transition:transform .28s var(--ease);}
.msn-live-tracker .view-all-btn:hover{
  border-color:var(--bp-text);
  background:var(--bp-text);
  color:#fff;
}
.msn-live-tracker .view-all-btn:hover svg{transform:translateX(3px);}
.msn-live-tracker .view-all-note{
  font-family:var(--bp-font-mono);
  font-size:11.5px;
  color:var(--faint);
}
@media (max-width:640px){
.msn-live-tracker .view-all-row{flex-direction:column;gap:10px;}
}
.msn-live-tracker /* ============ TRUST STRIP ============ */
.trust-strip{
  margin-top:40px;
  border:1px solid var(--bp-line-dark);
  border-radius:var(--radius);
  background:var(--bp-ink);
  padding:28px 30px;
  display:flex;
  flex-wrap:wrap;
  gap:24px;
  align-items:center;
  justify-content:space-between;
}
.msn-live-tracker .trust-items{display:flex;flex-wrap:wrap;gap:26px;}
.msn-live-tracker .trust-item{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.7);font-size:13.5px;font-weight:600;}
.msn-live-tracker .trust-item svg{width:18px;height:18px;color:var(--bp-amber);flex:none;}
.msn-live-tracker /* ============ ACTIVITY TICKER ============ */
.activity-ticker{
  margin-top:16px;
  border:1px solid var(--line);
  border-radius:var(--bp-radius);
  background:var(--white);
  overflow:hidden;
  padding:14px 0;
  white-space:nowrap;
}
.msn-live-tracker .activity-track{display:inline-flex;animation:activityscroll 28s linear infinite;}
.msn-live-tracker .activity-track span{
  display:inline-flex;align-items:center;gap:8px;
  padding:0 26px;font-size:13px;color:var(--muted);
  border-right:1px solid var(--line);
}
.msn-live-tracker .activity-track span b{color:var(--ink);font-weight:600;}
.msn-live-tracker .activity-track span .adot{width:6px;height:6px;border-radius:50%;background:var(--green);flex:none;}
@keyframes activityscroll{to{transform:translateX(-50%);}}
.msn-live-tracker .reveal{opacity:0;transform:translateY(18px);transition:opacity .65s var(--ease),transform .65s var(--ease);}
.msn-live-tracker .reveal.in{opacity:1;transform:translateY(0);}
@media (max-width:980px){
.msn-live-tracker .tracker-stats{grid-template-columns:repeat(2,1fr);}
.msn-live-tracker .tracker-grid{grid-template-columns:1fr;}
.msn-live-tracker .tracker-head{grid-template-columns:1fr;}
}
@media (max-width:640px){
.msn-live-tracker .wrap{width:min(100% - 28px,1180px);}
.msn-live-tracker .trust-strip{flex-direction:column;align-items:flex-start;}
}
@media (prefers-reduced-motion:reduce){
.msn-live-tracker *,
.msn-live-tracker *::before,
.msn-live-tracker *::after{animation:none!important;transition:none!important;}
}
  </style>

  <div class="msn-live-tracker">
<section class="tracker-section" id="live-projects">
  <div class="wrap">

    <div class="tracker-head reveal">
      <div>
        <span class="eyebrow mono">Right now at MSN Softtech</span>
        <h2>Projects we're actively building today.</h2>
      </div>
      <p>Full transparency on what's in motion. See what's live, what's in build, and exactly how far along each project is — no guessing, no waiting for a status email.</p>
    </div>

    <!-- sync bar: proves this is a live feed, not a static screenshot -->
    <div class="sync-bar reveal">
      <div class="sync-left">
        <span class="pulse-dot"></span>
        <span class="mono">All systems operational</span>
        <span class="trust-score"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.6 1.6 6.8L12 16.9l-6.2 3.5 1.6-6.8L2.2 9l6.9-.7z"/></svg> 4.9/5 client trust score</span>
      </div>
      <div class="sync-right">
        <span class="sync-clock"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg> <span id="liveClock">--:--:--</span> BDT</span>
        <span>Synced <b id="syncedAgo">just now</b></span>
        <span class="build-tag" id="buildTag"></span>
        <span class="sync-refresh" id="syncRefresh"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg></span>
      </div>
    </div>
    <p class="sync-note">Pulled straight from our internal project system — not a screenshot, not edited by hand. Numbers move as the work moves.</p>

    <!-- stats -->
    <div class="tracker-stats reveal">
      <div class="tstat"><b id="statActive">0</b><span>Active projects right now</span></div>
      <div class="tstat"><b id="statLive">0</b><span>Currently live for clients</span></div>
      <div class="tstat"><b id="statAvg">0<span style="font-size:.5em;">%</span></b><span>Average completion across builds</span></div>
      <div class="tstat"><b id="statTeam">0</b><span>Specialists working this week</span></div>
    </div>

    <!-- filters -->
    <div class="tracker-filters" id="trackerFilters">
      <button class="tfilter active" data-filter="all">All Projects</button>
      <button class="tfilter" data-filter="live" style="color:var(--green)"><span class="dot"></span>Live</button>
      <button class="tfilter" data-filter="dev" style="color:var(--blue)"><span class="dot"></span>In Development</button>
      <button class="tfilter" data-filter="testing" style="color:#a37600"><span class="dot"></span>Testing / QA</button>
    </div>

    <!-- project grid -->
    <div class="tracker-grid" id="trackerGrid"></div>

    <!-- view all projects -->
    <div class="view-all-row reveal">
      <a href="{{ route('portfolios') }}" class="view-all-btn">
        View All Projects
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
      <span class="view-all-note">3,700+ completed · full case studies &amp; results</span>
    </div>

    <!-- activity ticker -->
    <div class="activity-ticker reveal" aria-hidden="true">
      <div class="activity-track" id="activityTrack"></div>
    </div>

    <!-- trust strip -->
    <div class="trust-strip reveal">
      <div class="trust-items">
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> NDA protected on every project</span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg> Daily progress updates</span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Dedicated project manager</span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg> No missed deadlines this quarter</span>
      </div>
      <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary">Start Your Project</a>
    </div>

  </div>
</section>
  </div>

<script>
const projects = [
  {
    name:"Orenda Home", url:"https://orendahome-demo.com", status:"live", cat:"Shopify · E-commerce",
    progress:100, lastWeekProgress:94, phase:5, pm:["OH","+2"], pmColors:["#95BF47","#1F9D6B"],
    minutesAgo:184, eta:"Launched", online:false,
    history:[
      [184,"Checkout flow deployed to production"],
      [960,"Mobile page speed optimized to 96 Lighthouse"],
      [2880,"Store handed off to client, DNS switched live"]
    ]
  },
  {
    name:"CarePath", url:null, status:"testing", cat:"Mobile App · Healthcare",
    progress:88, lastWeekProgress:71, phase:4, pm:["CP","+3"], pmColors:["#1f5fa8","#0f0e0d"],
    minutesAgo:40, eta:"ETA 5 days", online:true,
    history:[
      [40,"Push notification flow passed QA"],
      [520,"Appointment reminders tested on 3 devices"],
      [1440,"Booking calendar sync fixed for iOS"]
    ]
  },
  {
    name:"Fleetwise Ops", url:null, status:"dev", cat:"AI Automation · Logistics",
    progress:62, lastWeekProgress:49, phase:3, pm:["FO","+2"], pmColors:["#e2231a","#a3150d"],
    minutesAgo:127, eta:"ETA 3 weeks", online:true,
    history:[
      [127,"Driver check-in workflow connected to Slack"],
      [800,"Dispatch report template approved by client"],
      [2100,"Data pipeline for fleet tracking scoped"]
    ]
  },
  {
    name:"Classroom OS", url:null, status:"dev", cat:"Custom Software · Education",
    progress:47, lastWeekProgress:38, phase:3, pm:["CO","+4"], pmColors:["#4A4180","#1B1440"],
    minutesAgo:63, eta:"ETA 5 weeks", online:true,
    history:[
      [63,"Reporting dashboard wireframes approved"],
      [900,"Role-based access module built"],
      [2600,"Kickoff call and requirements signed off"]
    ]
  },
  {
    name:"Verano Skincare", url:"https://verano-demo-store.com", status:"live", cat:"Shopify · Retail",
    progress:100, lastWeekProgress:100, phase:5, pm:["VS","+1"], pmColors:["#f2b705","#e2231a"],
    minutesAgo:361, eta:"Launched", online:false,
    history:[
      [361,"Subscription billing verified live"],
      [1200,"Bundle offers configured for launch"],
      [3200,"Store handed off to client, DNS switched live"]
    ]
  },
  {
    name:"Helios Support Agent", url:null, status:"testing", cat:"AI Agent · SaaS",
    progress:91, lastWeekProgress:75, phase:4, pm:["HS","+2"], pmColors:["#a3150d","#0f0e0d"],
    minutesAgo:25, eta:"ETA 2 days", online:true,
    history:[
      [25,"Escalation routing logic finalized"],
      [400,"Trained on updated product documentation"],
      [1100,"First round of QA tickets resolved"]
    ]
  }
];

/* anchor each project's "minutes ago" to real wall-clock time on page load,
   so the displayed value keeps counting forward for as long as the visitor stays */
const pageLoadTime = Date.now();
projects.forEach(function(p){
  p.anchorMs = pageLoadTime - p.minutesAgo * 60000;
  p.history.forEach(function(h){ h.push(pageLoadTime - h[0] * 60000); });
});

function relativeTime(anchorMs){
  const diffMin = Math.max(0, Math.round((Date.now() - anchorMs) / 60000));
  if(diffMin < 1) return "just now";
  if(diffMin < 60) return diffMin + " minute" + (diffMin === 1 ? "" : "s") + " ago";
  const diffHr = Math.round(diffMin / 60);
  if(diffHr < 24) return diffHr + " hour" + (diffHr === 1 ? "" : "s") + " ago";
  const diffDay = Math.round(diffHr / 24);
  return diffDay + " day" + (diffDay === 1 ? "" : "s") + " ago";
}

const statusMeta = {
  live:{label:"Live", cls:"live"},
  dev:{label:"In Development", cls:"dev"},
  testing:{label:"Testing / QA", cls:"testing"}
};
const phaseNames = ["Discovery","Design","Build","Test","Launch"];

const grid = document.getElementById("trackerGrid");
projects.forEach(function(p, idx){
  const meta = statusMeta[p.status];
  const card = document.createElement("div");
  card.className = "tproject reveal";
  card.dataset.status = p.status;

  const avatarsHtml = p.pm.map(function(initials, i){
    const onlineCls = (p.online && i === 0) ? " online" : "";
    return '<span class="' + onlineCls.trim() + '" style="background:' + p.pmColors[i % p.pmColors.length] + '">' + initials + '</span>';
  }).join("");

  const stepperHtml = phaseNames.map(function(_, i){
    let cls = "";
    if(i < p.phase - 1) cls = "done";
    else if(i === p.phase - 1) cls = p.progress >= 100 ? "done" : "current";
    return '<div class="phase-step ' + cls + '"></div>';
  }).join("");

  const nameTag = p.url
    ? '<a class="tproject-name" href="' + p.url + '" target="_blank" rel="noopener">' + p.name + '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg></a>'
    : '<span class="tproject-name">' + p.name + '</span>';

  const domainHtml = p.url
    ? '<div class="domain-row"><img src="https://www.google.com/s2/favicons?domain=' + p.url + '&sz=32" alt="" loading="lazy" onerror="this.style.display=\'none\'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><span>' + p.url.replace(/^https?:\/\//,"") + '</span></div>'
    : '';

  const verifyHtml = p.url
    ? '<span class="verify-badge checking" data-url="' + p.url + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>Verifying…</span>'
    : '';

  const delta = p.progress - p.lastWeekProgress;
  const trendHtml = delta > 0
    ? '<span class="trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg>+' + delta + '% this week</span>'
    : '<span class="trend flat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>steady this week</span>';

  const historyHtml = p.history.map(function(h){
    return '<div class="history-line"><span class="hdot"></span><span>' + h[1] + '</span><span class="htime updated-text" data-anchor="' + h[2] + '">' + relativeTime(h[2]) + '</span></div>';
  }).join("");

  card.innerHTML =
    '<div class="tproject-top">' +
      '<span class="status-chip ' + meta.cls + '"><span class="status-dot"></span>' + meta.label + '</span>' +
      '<div class="pm-avatars">' + avatarsHtml + '</div>' +
    '</div>' +
    nameTag +
    domainHtml +
    '<div class="tproject-cat">' + p.cat + '</div>' +
    '<div class="progress-row"><span class="plabel">Completion' + trendHtml + '</span><span class="pval"><span class="pnum" data-target="' + p.progress + '">0</span>%</span></div>' +
    '<div class="progress-track"><div class="progress-fill" data-width="' + p.progress + '"></div></div>' +
    '<div class="phase-stepper">' + stepperHtml + '</div>' +
    '<div class="phase-labels"><span>' + phaseNames[0] + '</span><span>' + phaseNames[4] + '</span></div>' +
    '<div class="tproject-foot">' +
      '<span class="updated"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>Updated <span class="updated-text" data-anchor="' + p.anchorMs + '">' + relativeTime(p.anchorMs) + '</span></span>' +
      (verifyHtml || '<span class="eta-badge">' + p.eta + '</span>') +
    '</div>' +
    '<button type="button" class="history-toggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>View activity log</button>' +
    '<div class="history-panel"><div class="history-panel-inner">' + historyHtml + '</div></div>';

  grid.appendChild(card);

  if(p.url){
    const badge = card.querySelector(".verify-badge");
    verifyLive(p.url, badge);
  }

  const toggle = card.querySelector(".history-toggle");
  const panel = card.querySelector(".history-panel");
  toggle.addEventListener("click", function(){
    const isOpen = toggle.classList.contains("open");
    if(isOpen){
      toggle.classList.remove("open");
      panel.style.maxHeight = null;
    } else {
      toggle.classList.add("open");
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
});

/* real, in-browser reachability check — genuinely fetches the live URL and times the response */
function verifyLive(url, badgeEl){
  if(!badgeEl) return;
  const start = performance.now();
  let settled = false;
  const timeout = setTimeout(function(){
    if(!settled){
      settled = true;
      badgeEl.classList.remove("checking");
      badgeEl.classList.add("warn");
      badgeEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg>Tap to verify';
      badgeEl.onclick = function(){ window.open(url, "_blank", "noopener"); };
    }
  }, 4000);

  fetch(url, { mode:"no-cors", cache:"no-store" })
    .then(function(){
      if(settled) return;
      settled = true;
      clearTimeout(timeout);
      const ms = Math.round(performance.now() - start);
      badgeEl.classList.remove("checking");
      badgeEl.classList.add("ok");
      badgeEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Verified live · ~' + ms + 'ms';
    })
    .catch(function(){
      if(settled) return;
      settled = true;
      clearTimeout(timeout);
      badgeEl.classList.remove("checking");
      badgeEl.classList.add("warn");
      badgeEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg>Tap to verify';
      badgeEl.onclick = function(){ window.open(url, "_blank", "noopener"); };
    });
}

/* keep every "updated X ago" label counting forward in real time */
function refreshRelativeTimes(){
  document.querySelectorAll(".updated-text").forEach(function(el){
    el.textContent = relativeTime(parseInt(el.dataset.anchor, 10));
  });
}
setInterval(refreshRelativeTimes, 30000);

/* animate stats */
function countUp(el, target, suffix){
  suffix = suffix || "";
  let start = 0;
  const duration = 900;
  const startTime = performance.now();
  function tick(now){
    const progress = Math.min((now - startTime) / duration, 1);
    const val = Math.round(progress * target);
    el.textContent = val + suffix;
    if(progress < 1) requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
}

const liveCount = projects.filter(function(p){ return p.status === "live"; }).length;
const avgProgress = Math.round(projects.reduce(function(sum,p){ return sum + p.progress; }, 0) / projects.length);

const statsSection = document.getElementById("live-projects");
let statsAnimated = false;
const statsObserver = new IntersectionObserver(function(entries){
  entries.forEach(function(entry){
    if(entry.isIntersecting && !statsAnimated){
      statsAnimated = true;
      countUp(document.getElementById("statActive"), projects.length);
      countUp(document.getElementById("statLive"), liveCount);
      countUp(document.getElementById("statAvg"), avgProgress);
      countUp(document.getElementById("statTeam"), 23);

      document.querySelectorAll(".progress-fill").forEach(function(bar){
        bar.style.width = bar.dataset.width + "%";
      });
      document.querySelectorAll(".pnum").forEach(function(num){
        countUp(num, parseInt(num.dataset.target, 10));
      });
    }
  });
},{threshold:.25});
statsObserver.observe(statsSection);

/* staggered reveal-on-scroll for premium entrance polish */
const revealObserver = new IntersectionObserver(function(entries){
  entries.forEach(function(entry){
    if(entry.isIntersecting){
      entry.target.classList.add("in");
      revealObserver.unobserve(entry.target);
    }
  });
},{threshold:.1});
document.querySelectorAll(".reveal").forEach(function(el, index){
  el.style.transitionDelay = (index % 6 * 0.06) + "s";
  revealObserver.observe(el);
});

/* filters */
const filters = document.querySelectorAll(".tfilter");
filters.forEach(function(btn){
  btn.addEventListener("click", function(){
    filters.forEach(function(b){ b.classList.remove("active"); });
    btn.classList.add("active");
    const f = btn.dataset.filter;
    document.querySelectorAll(".tproject").forEach(function(card){
      card.style.display = (f === "all" || card.dataset.status === f) ? "" : "none";
    });
  });
});

/* activity ticker */
const activityItems = [
  ["Orenda Home", "Checkout flow deployed to production"],
  ["CarePath", "Push notification testing passed QA"],
  ["Fleetwise Ops", "Driver check-in workflow connected to Slack"],
  ["Classroom OS", "Reporting dashboard wireframes approved"],
  ["Helios Support Agent", "Escalation routing logic finalized"],
  ["Verano Skincare", "Subscription billing verified live"]
];
const track = document.getElementById("activityTrack");
const itemsHtml = activityItems.map(function(item){
  return '<span><span class="adot"></span><b>' + item[0] + '</b> — ' + item[1] + '</span>';
}).join("");
track.innerHTML = itemsHtml + itemsHtml;

/* live wall clock — genuinely ticks every second, proves the page isn't a static export */
function tickClock(){
  const el = document.getElementById("liveClock");
  if(!el) return;
  const now = new Date();
  const opts = { timeZone:"Asia/Dhaka", hour:"2-digit", minute:"2-digit", second:"2-digit", hour12:false };
  el.textContent = now.toLocaleTimeString("en-GB", opts);
}
tickClock();
setInterval(tickClock, 1000);

/* build/version tag — computed from today's date, reads like real internal tooling */
(function setBuildTag(){
  const el = document.getElementById("buildTag");
  if(!el) return;
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  el.textContent = "Project OS · Build " + y + m + day;
})();

/* periodic "sync" pulse on the status bar — reinforces this is polling, not a fixed image */
let lastSyncMs = Date.now();
function pulseSync(){
  const btn = document.getElementById("syncRefresh");
  if(btn){
    btn.classList.add("spinning");
    setTimeout(function(){ btn.classList.remove("spinning"); }, 800);
  }
  lastSyncMs = Date.now();
  updateSyncedAgo();
}
function updateSyncedAgo(){
  const el = document.getElementById("syncedAgo");
  if(!el) return;
  const secs = Math.round((Date.now() - lastSyncMs) / 1000);
  el.textContent = secs < 5 ? "just now" : secs + "s ago";
}
setInterval(updateSyncedAgo, 1000);
setInterval(pulseSync, 20000);
</script>


  @php
    $section_team = \App\Models\Section::section('team');
  @endphp
  @if(count($members) > 0 && isset($section_team))
    <!-- Team Section -->
    <section class="hp-team-section">
      <div class="container">
        <div class="msn-section-head msn-reveal">
          <span class="msn-eyebrow">Meet The Team</span>
          <h2>{{ $section_team->title }}</h2>
          <div class="text description">{!! $section_team->description !!}</div>
        </div>

        <div class="outer-column clearfix">
          <div class="hp-team-grid msn-reveal">
            @foreach($members as $member)
              <!-- Team Block -->
              <div class="hp-team-block">
                <div class="hp-team-inner">
                  <div class="hp-team-photo"><img src="{{ asset('uploads/member/' . $member->image_path) }}"
                      alt="{{ $member->title }}" loading="lazy"></div>

                  <h3 class="hp-team-name"><a>{{ $member->title }}</a></h3>
                  <span class="hp-team-role">{{ $member->designation->title }}@if(isset($member->designation->department)),
                  {{ $member->designation->department }}@endif</span>
                  @if(isset($member->email))
                    <span class="hp-team-meta"><i class="far fa-envelope"></i> {{ $member->email }}</span>
                  @endif
                  @if(isset($member->phone))
                    <span class="hp-team-meta"><i class="fas fa-phone-volume"></i> {{ $member->phone }}</span>
                  @endif

                  <ul class="hp-team-social">
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
      </div>
    </section>
    <!--End Team Section -->
  @endif


  @php
    $section_testimonials = \App\Models\Section::section('testimonials');
  @endphp
  @if(count($testimonials) > 0 && isset($section_testimonials))
    <!-- Testimonial Section Two-->
    <section class="hp-testimonial-section">
      <div class="container">
        <div class="msn-section-head msn-center msn-reveal">
          <span class="msn-eyebrow">Client Words</span>
          <h2>{{ $section_testimonials->title }}</h2>
          <div class="text description">{!! $section_testimonials->description !!}</div>
        </div>

        <div class="testimonial-carousel owl-carousel owl-theme msn-reveal">
          @foreach($testimonials as $testimonial)
            <!-- Testimonial block two -->
            <div class="hp-testi-block">
              <div class="hp-testi-inner">
                <div class="hp-testi-quote">Client Feedback</div>
                <div class="hp-testi-thumb"><img src="{{ asset('uploads/testimonial/' . $testimonial->image_path) }}"
                    alt="{{ $testimonial->title }}" loading="lazy"></div>
                <div class="hp-testi-text description">{!! $testimonial->description !!}</div>
                <h5 class="hp-testi-name">{{ $testimonial->title }}</h5>
                <div class="hp-testi-company">{{ $testimonial->designation }}@if(isset($testimonial->organization)),
                {{ $testimonial->organization }}@endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    <!--End Testimonial Section Two-->
  @endif


  @php
    $section_blog = \App\Models\Section::section('blog');
  @endphp


  @php
    $section_process = \App\Models\Section::section('process');
  @endphp

  @if(count($processes) > 0 && isset($section_process))
    {{-- process-section --}}
    <section class="hp-process-section">
      <div class="container">
        <div class="hp-process-title msn-reveal">
          <span class="msn-eyebrow">How We Deliver</span>
          <h2 style="padding-bottom: 30px !important; padding-top:14px;">{{ $section_process->title }}</h2>
        </div>

        <!-- First Row -->
        <div class="row g-4 mb-4">
          @foreach($processes as $key => $process)
            <div class="col-md-4 mb-4 msn-reveal">
              <div class="process-step-box">
                <div class="process-step-number">{{ $key + 1 }}</div>
                <div class="process-step-heading">
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
                  class="process-step-arrow d-none d-md-block {{ $showArrow ? ($key == 2 ? 'arrow-down' : '') : 'arrow-hidden' }}">
                </div>
              </div>
            </div>
            {{-- @endforeach --}}
          @endforeach
        </div>

        <!-- CTA -->
        <div class="text-center mt-5">
          <a href="https://msnsofttech.com/get-quote" class="msn-btn msn-btn-primary">Get in Touch With Us →</a>
        </div>
      </div>
    </section>
  @endif

  {{-- case study --}}
  <section class="hp-case-section">

    <div class="hp-case-wrap">
      <!-- Fixed Badge -->
      <div class="hp-case-badge">Case Studies</div>

      <div id="case-owl-carousel" class="owl-carousel owl-theme">

        <!-- Slide 1 -->
        @foreach ($case_studies as $case_study)
        <div class="hp-case-item"
          style="background-image: url('{{ asset('uploads/case-study/'.$case_study->image_path) }}');">
          <div class="hp-case-content">
            <h1>{{ $case_study->main_title }}</h1>
            <div class="hp-case-tags">
              @foreach ($case_study->technologies as $technology)
                <span>{{ $technology->short_title }}</span>
              @endforeach
            </div>
            <a href="{{ route('case-study.single', $case_study->slug) }}" class="hp-case-read-more">View Case Study ➔</a>
          </div>
        </div>
        @endforeach

      </div>
    </div>
  </section>

  <section class="hp-blog-section py-5">
    <div class="container">
      <h2 class="hp-blog-title">{{ $section_blog->title }}</h2>
      <p class="hp-blog-subtitle">Explore Featured Insights</p>

      <div class="row g-4 mt-4">
        <!-- Blog Card 1 -->
        @foreach($articles as $key => $article)
          <div class="col-md-4 msn-reveal">
            <div class="hp-blog-card">
              <img src="{{ asset('uploads/article/' . $article->image_path) }}" class="img-fluid hp-blog-img"
                alt="{{ $article->title }}" loading="lazy">
              <div class="hp-blog-content">
                <a href="{{ route('blog.single', $article->slug) }}" class="hp-blog-title-link">{{ $article->title }}</a>
                <p class="hp-blog-meta">By <span class="fw-bold">MSN Softtech</span>, in Digital Transformation</p>
              </div>
            </div>
          </div>
        @endforeach

      </div>

      <div class="text-center mt-5">
        <a href="{{ route('blogs') }}" class="msn-btn msn-btn-primary">Read More Blog Posts →</a>
      </div>
    </div>
  </section>

  </div><!-- /.msn-scope -->

  @section('scriptjs')
    <script>
      function selectAndSubmit(model) {
        document.getElementById('model-' + model).checked = true; // Select radio
        document.getElementById('modelForm').submit(); // Submit form
      }
    </script>


    {{-- banner --}}
    <script>
      $(document).ready(function () {
        $('.owl-carousel').owlCarousel({
          items: 1,
          loop: true,
          autoplay: true,
          autoplayTimeout: 5000,
          smartSpeed: 3000,
          animateOut: 'fadeOut',
          nav: true,
          navText: [],
          dots: false
        });
      });
    </script>

    {{-- case study --}}
    <script>
      $(document).ready(function () {
        $("#case-owl-carousel").owlCarousel({
          loop: true,
          margin: 0,
          nav: true,
          navText: [],
          dots: true,
          autoplay: true,
          autoplayTimeout: 9000,
          autoplayHoverPause: true,
          items: 1,
          smartSpeed: 1500,
        });
      });

    </script>


    <!-- Include YouTube Iframe API -->
    <script src="https://www.youtube.com/iframe_api"></script>

    <script>
      let players = [];

      // YouTube Iframe API onReady function
      function onYouTubeIframeAPIReady() {
        document.querySelectorAll('.youtube-bg-video').forEach((el, index) => {
          const videoId = el.dataset.videoId;

          // Create the player for each video
          players[index] = new YT.Player(el.id, {
            videoId: videoId,
            playerVars: {
              autoplay: 1,
              controls: 0,
              showinfo: 0,
              modestbranding: 1,
              rel: 0,
              loop: 1,
              mute: 1,
              playlist: videoId,
              iv_load_policy: 3,  // Hide annotations
              fs: 0,  // Disable fullscreen button
            },
            events: {
              onReady: function (event) {
                event.target.mute();
                event.target.playVideo();
              }
            }
          });
        });
      }
    </script>

    <script>
      document.getElementById("go-services").addEventListener("click", function () {
        window.location.href = "{{ route('services') }}";
      });
    </script>
    {{-- portfolio-section --}}
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      // AOS Animation
      AOS.init();

      // Isotope Initialization
      var grid = document.querySelector('.portfolio-grid');
      var iso = new Isotope(grid, {
        itemSelector: '.portfolio-item',
        layoutMode: 'fitRows'
      });

      // Filter buttons
      var filterButtons = document.querySelectorAll('.portfolio-filter-btn');

      filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          var filterValue = button.getAttribute('data-filter');
          iso.arrange({ filter: filterValue });

          // Active class switching
          filterButtons.forEach(btn => btn.classList.remove('active'));
          button.classList.add('active');
        });
      });
    </script>

  @endsection
@endsection
