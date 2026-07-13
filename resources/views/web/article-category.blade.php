@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('blog');
@endphp
@if(isset($header))

@section('title', $header->meta_title)

@section('top_meta_tags')
    @if(isset($header->meta_description))
        <meta name="description" content="{!! Str::limit(strip_tags($header->meta_description), 160, ' ...') !!}">
    @else
        <meta name="description" content="{!! Str::limit(strip_tags($setting->description), 160, ' ...') !!}">
    @endif

    @if(isset($header->meta_keywords))
        <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
    @else
        <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
    @endif
@endsection

@endif

<<<<<<< HEAD
=======
@section('social_meta_tags')
    {{-- fonts + theme should already be linked once in the master layout <head>;
         kept here too so this page still renders correctly on its own. --}}
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

>>>>>>> e734773df (msn 2.0 theme change)
@section('content')

    @php
        use Illuminate\Support\Str;
    @endphp

    <style>
<<<<<<< HEAD
        body {
            background: #F3F4F4;
            font-family: 'Segoe UI', sans-serif;
        }

        .search-bar {
            margin: 20px 0;
        }

        .search-bar input {
            border-radius: 25px;
            padding-left: 20px;
        }

        .featured-blog,
        .blog-card {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0px 1px rgba(0, 0, 0, 0.1);
        }

        .featured-blog {
            margin-bottom: 40px;
        }

        .blog-card {
            height: 100%;
        }

        .blog-card img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .author-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .author-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .read-more {
            color: #28a745;
            font-weight: bold;
            text-decoration: none;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        #loadMoreBtn {
            margin-top: 40px;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            background: #052C58;
            color: #fff;
            border: none;
        }

        #loadMoreBtn:hover {
            background: #052C58;
        }

        #loadingSpinner {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .fade-in-delay {
            animation: fadeInDelay 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDelay {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cardBlogExtra h5 {
            /* font-size: 24px; */
            color: #222222;
        }

        .cardBlogExtra p {
            font-size: 17px;
            color: #000000;
        }








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



        /* awords-section */
        .awords-section {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .awords-section-header {
            margin-top: 50px;
            font-size: 32px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        .awards-container {
            margin-top: 50px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            padding: 0px 100px;
            padding-bottom: 80px;
        }

        .award {
            flex: 0 1 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .award img {
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
        }
    </style>

    <!-- Page Title -->
    {{-- <section class="page-title">
        <div class="container">
            <div class="inner-container clearfix">
                <div class="title-box">
                    <h1>{{ __('navbar.blog') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.blog') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section> --}}

    <section class="about-hero-section" data-aos="fade">
        <div class="container">
            <h1>{{ __('navbar.blog') }}</h1>
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
                        <li>{{ __('navbar.blog') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
    <!-- Search Bar -->
    <div class="container search-bar">
        <div class="row">
            <div class="col-12 d-flex justify-content-end">
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search...">
            </div>
        </div>
    </div>


    <!-- Featured Blog -->
    @if($articles->count() > 0)
        <div class="container featured-blog ">
            <div class="row align-items-center">
                <div class="col-md-5 pl-4 mt-4 featured-blog-extra">
                    {{-- <div class="author-info mb-2">
                        <img src="{{ asset('/uploads/setting/' . $setting->logo_path) }}" alt="Author"
                  class="rounded-circle m-0 mr-3" style="width: 30px; height: 30px; object-fit: cover; object-position: center;">
                        <div><strong>MSN Softtech</strong></div>
                    </div> --}}
                    <h3><strong>{{ $articles[0]->title }}</strong></h3>
                    <p>{{ Str::limit(strip_tags($articles[0]->description), 450) }}</p>
                    <a href="{{ route('blog.single', $articles[0]->slug) }}" class="read-more">READ MORE</a>
                </div>
                <div class="col-md-7">
                    <img src="{{ asset('uploads/article/' . $articles[0]->image_path) }}" alt="{{ $articles[0]->title }}"
                        class="w-100 m-0 p-0 rounded-right ">
                </div>
            </div>
        </div>
    @endif

    <!-- Blog Cards -->
    <div class="container">
        <div class="row g-4" id="blogCardsContainer">
            <!-- Cards will be dynamically loaded here -->
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner">
            <img style="width: 15px; height: 15px;" src="https://i.imgur.com/llF5iyg.gif" alt="Loading...">
        </div>

        <!-- Load More Button -->
        <div class="d-flex justify-content-center mb-4">
            <button id="loadMoreBtn">CLICK TO LOAD MORE</button>
        </div>
    </div>

    
    <!-- Blog Loading Script -->
    <script>
        const blogData = @json($articles->skip(1)->values()); // Skips the first blog (already featured)
        let loadedCount = 0;
        const perLoad = 3;

        // Load blog cards function
=======
    /* =====================================================================
       BLOG / JOURNAL — premium pass built on the same Blueprint system as
       the About page: deep navy + teal-glass register, Space Grotesk /
       IBM Plex Sans / JetBrains Mono type stack. Scoped to .msn-scope with
       a bl- prefix and its own local token layer so it doesn't touch the
       shared theme file. Every post is treated as a numbered journal entry
       — the numbering reflects real publish order, not decoration.
       ===================================================================== */

    .msn-scope{
      --bl-navy-950:#05070d;
      --bl-navy-900:#0a0f1a;
      --bl-navy-850:#0e1526;
      --bl-navy-800:#131c31;
      --bl-teal-100:#99F6E4;
      --bl-teal-300:#2DD4BF;
      --bl-teal-500:#14B8A6;
      --bl-teal-700:#115E59;
      --bl-teal-gradient: linear-gradient(115deg,#99F6E4 0%,#2DD4BF 22%,#0F766E 45%,#5EEAD4 68%,#2DD4BF 85%,#99F6E4 100%);
      --bl-teal-gradient-hard: linear-gradient(135deg,#5EEAD4 0%,#14B8A6 50%,#115E59 100%);
      --bl-glass-bg: rgba(94,234,212,.05);
      --bl-glass-bg-strong: rgba(94,234,212,.08);
      --bl-glass-border: rgba(45,212,191,.28);
      --bl-glass-border-soft: rgba(45,212,191,.14);
    }

    /* ---------- shared utilities ---------- */
    .bl-teal-text{
      background-image: var(--bl-teal-gradient);
      background-size:220% auto;
      -webkit-background-clip:text; background-clip:text;
      -webkit-text-fill-color:transparent; color:transparent;
      animation: bl-shine 7s ease-in-out infinite;
    }
    @keyframes bl-shine{ 0%{background-position:0% center;} 50%{background-position:100% center;} 100%{background-position:0% center;} }
    @media (prefers-reduced-motion:reduce){ .bl-teal-text{ animation:none; } }

    .bl-grain{ position:relative; }
    .bl-grain::after{
      content:""; position:absolute; inset:0; z-index:0; pointer-events:none; opacity:.05; mix-blend-mode:overlay;
      background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='140' height='140'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
    }

    .bl-chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:6px 14px 6px 10px;
      border:1px solid var(--bl-glass-border);
      background:var(--bl-glass-bg);
      border-radius:2px;
      font-family:var(--bp-font-mono);
      font-size:11.5px; letter-spacing:.08em; text-transform:uppercase;
    }
    .bl-chip .dot{ width:6px;height:6px;border-radius:50%; background:var(--bl-teal-gradient-hard); box-shadow:0 0 8px rgba(45,212,191,.75); flex:0 0 auto; }
    .bl-hero .bl-chip{ color:rgba(255,255,255,.8) !important; }

    .bl-btn-shine{ position:relative; overflow:hidden; isolation:isolate; }
    .bl-btn-shine::after{
      content:""; position:absolute; top:0; left:-65%; width:35%; height:100%;
      background:linear-gradient(115deg, transparent, rgba(255,255,255,.65), transparent);
      transform:skewX(-18deg); transition:left .7s cubic-bezier(.2,.7,.3,1); pointer-events:none;
    }
    .bl-btn-shine:hover::after{ left:130%; }

    a:focus-visible, button:focus-visible, input:focus-visible{
      outline:2px solid var(--bl-teal-500); outline-offset:3px;
    }

    /* ---------- HERO ---------- */
    .bl-hero{
      position:relative;
      background-color:#05070d !important;
      background-image:
        radial-gradient(circle at 80% 10%, rgba(45,212,191,.16), transparent 42%),
        radial-gradient(circle at 6% 92%, rgba(45,212,191,.08), transparent 46%),
        linear-gradient(180deg, #05070d 0%, #0a0f1a 55%, #0e1526 100%) !important;
      padding:clamp(110px,15vw,168px) 0 clamp(96px,11vw,132px);
      overflow:hidden;
      isolation:isolate;
    }
    .bl-hero::before{
      content:""; position:absolute; inset:0;
      background-image:
        linear-gradient(rgba(45,212,191,.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(45,212,191,.08) 1px, transparent 1px);
      background-size:64px 64px, 64px 64px;
      mask-image: linear-gradient(180deg, transparent, rgba(0,0,0,.9) 30%, rgba(0,0,0,.9) 80%, transparent);
      pointer-events:none;
    }
    .bl-hero-inner{ position:relative; z-index:2; max-width:680px; }
    .bl-hero-title{
      font-family:var(--bp-font-display);
      color:#ffffff !important;
      font-size:clamp(34px,5.6vw,58px);
      font-weight:700; line-height:1.1;
      margin-top:16px;
    }
    .bl-hero-underline{
      width:64px; height:3px; margin-top:18px;
      background-image:linear-gradient(115deg,#99F6E4,#14B8A6,#115E59);
      border-radius:2px;
    }
    .bl-hero-copy{
      max-width:560px;
      font-size:clamp(15px,1.2vw,16.5px);
      color:rgba(255,255,255,.65) !important;
      margin-top:18px; line-height:1.75;
    }
    .bl-breadcrumb{
      display:flex; align-items:center; gap:8px; flex-wrap:wrap;
      margin-top:26px;
      font-family:var(--bp-font-mono); font-size:12px; letter-spacing:.05em; text-transform:uppercase;
      color:rgba(255,255,255,.45) !important;
    }
    .bl-breadcrumb a{ color:rgba(255,255,255,.8) !important; transition:color .2s var(--bp-ease); }
    .bl-breadcrumb a:hover{ color:var(--bl-teal-300) !important; }
    .bl-breadcrumb span.sep{ color:rgba(255,255,255,.3) !important; }

    /* signature: live archive plaque, top-right of hero */
    .bl-plaque{
      position:absolute; top:18%; right:6%;
      padding:20px 26px;
      border:1px solid rgba(45,212,191,.3);
      background:var(--bl-glass-bg-strong);
      backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px);
      text-align:center; z-index:1;
    }
    .bl-plaque-num{
      font-family:var(--bp-font-mono); font-weight:600; font-size:34px; line-height:1;
      background-image:var(--bl-teal-gradient-hard);
      -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; color:transparent;
    }
    .bl-plaque-label{ font-family:var(--bp-font-mono); font-size:9.5px; letter-spacing:.12em; color:rgba(255,255,255,.5); margin-top:4px; text-transform:uppercase; }
    @media (max-width:1199px){ .bl-plaque{ display:none; } }

    /* ---------- SEARCH (floating glass module) ---------- */
    .bl-search-wrap{ position:relative; z-index:5; }
    .bl-search-card{
      margin-top:-52px;
      padding:26px 30px;
      background:var(--bp-white);
      border:1px solid var(--bp-line);
      box-shadow:0 30px 60px -28px rgba(5,7,13,.35);
      display:flex; align-items:center; gap:18px; flex-wrap:wrap;
    }
    @media (max-width:575px){ .bl-search-card{ margin-top:-36px; padding:20px; } }
    .bl-search-label{
      font-family:var(--bp-font-mono); font-size:11px; letter-spacing:.08em; text-transform:uppercase;
      color:var(--bp-muted); white-space:nowrap;
    }
    .bl-search-field{ position:relative; flex:1 1 260px; }
    .bl-search-field i{ position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--bp-muted); font-size:15px; }
    .bl-search-field input{
      width:100%;
      padding:12px 16px 12px 42px;
      border:1px solid var(--bp-line);
      background:var(--bp-paper);
      font-size:14.5px;
      color:var(--bp-text);
      transition:border-color .25s var(--bp-ease), box-shadow .25s var(--bp-ease);
    }
    .bl-search-field input:focus{
      outline:none;
      border-color:var(--bl-teal-500);
      box-shadow:0 0 0 3px rgba(20,184,166,.14);
    }

    /* ---------- FEATURED ---------- */
    .bl-featured{ background:var(--bp-paper); }
    .bl-featured-grid{
      display:grid;
      grid-template-columns:.95fr 1.05fr;
      gap:56px;
      align-items:center;
    }
    @media (max-width:991px){ .bl-featured-grid{ grid-template-columns:1fr; gap:32px; } }
    .bl-featured-media{ position:relative; order:2; }
    @media (max-width:991px){ .bl-featured-media{ order:1; } }
    .bl-featured-media img{
      width:100%; display:block;
      border:1px solid var(--bp-line);
      aspect-ratio:4/3.1; object-fit:cover;
    }
    .bl-featured-text{ order:1; }
    @media (max-width:991px){ .bl-featured-text{ order:2; } }
    .bl-featured-meta{
      display:flex; align-items:center; gap:14px; flex-wrap:wrap;
      font-family:var(--bp-font-mono); font-size:11.5px; letter-spacing:.05em; text-transform:uppercase;
      color:var(--bp-muted); margin:16px 0 18px;
    }
    .bl-featured-meta .sep{ width:4px; height:4px; border-radius:50%; background:var(--bp-line); }
    .bl-featured-text h2{
      font-family:var(--bp-font-display);
      font-size:clamp(26px,3.6vw,40px);
      font-weight:700; line-height:1.18;
      margin-bottom:18px;
    }
    .bl-featured-text p{ font-size:16px; line-height:1.8; color:var(--bp-text-soft); margin-bottom:26px; }

    /* ---------- read-more arrow link (shared) ---------- */
    .bl-read-more{
      display:inline-flex; align-items:center; gap:8px;
      font-family:var(--bp-font-mono); font-size:12.5px; font-weight:600;
      letter-spacing:.05em; text-transform:uppercase;
      color:var(--bl-teal-700); text-decoration:none;
    }
    .bl-read-more svg{ width:15px; height:15px; transition:transform .25s var(--bp-ease); }
    .bl-read-more:hover{ color:var(--bl-teal-500); }
    .bl-read-more:hover svg{ transform:translateX(4px); }

    /* ---------- GRID SECTION ---------- */
    .bl-list{ background:var(--bp-white); }
    .bl-grid{
      display:grid;
      grid-template-columns:repeat(3, 1fr);
      gap:30px;
    }
    @media (max-width:991px){ .bl-grid{ grid-template-columns:repeat(2, 1fr); } }
    @media (max-width:575px){ .bl-grid{ grid-template-columns:1fr; } }

    .bl-card{
      background:var(--bp-white);
      border:1px solid var(--bp-line);
      display:flex; flex-direction:column; height:100%;
      transition:transform .35s var(--bp-ease), box-shadow .35s var(--bp-ease), border-color .35s var(--bp-ease);
    }
    .bl-card:hover{
      transform:translateY(-5px);
      box-shadow:0 34px 64px -30px rgba(20,184,166,.28);
      border-color:rgba(20,184,166,.35);
    }
    .bl-card-media{ position:relative; aspect-ratio:16/11.2; overflow:hidden; }
    .bl-card-media img{ width:100%; height:100%; object-fit:cover; filter:grayscale(.2); transition:transform .6s var(--bp-ease), filter .5s var(--bp-ease); }
    .bl-card:hover .bl-card-media img{ transform:scale(1.07); filter:grayscale(0); }
    .bl-card-index{
      position:absolute; top:12px; left:12px;
      padding:4px 10px;
      background:rgba(5,7,13,.6);
      backdrop-filter:blur(6px);
      border:1px solid rgba(45,212,191,.4);
      color:var(--bl-teal-100);
      font-family:var(--bp-font-mono); font-size:10.5px; letter-spacing:.08em; text-transform:uppercase;
    }
    .bl-card-body{ padding:22px 22px 24px; display:flex; flex-direction:column; flex:1; }
    .bl-card-date{
      font-family:var(--bp-font-mono); font-size:11px; letter-spacing:.05em; text-transform:uppercase;
      color:var(--bp-muted); margin-bottom:10px;
    }
    .bl-card h3{ font-family:var(--bp-font-display); font-size:18.5px; font-weight:700; line-height:1.32; margin-bottom:10px; }
    .bl-card h3 a{ color:var(--bp-text); text-decoration:none; }
    .bl-card h3 a:hover{ color:var(--bl-teal-700); }
    .bl-card p{ font-size:14.5px; line-height:1.7; color:var(--bp-text-soft); margin-bottom:18px; flex:1; }

    .bl-empty{
      grid-column:1 / -1;
      text-align:center;
      padding:70px 20px;
      font-family:var(--bp-font-mono);
      font-size:13px; letter-spacing:.05em; text-transform:uppercase;
      color:var(--bp-muted);
      border:1px dashed var(--bp-line);
    }

    /* ---------- load more / spinner ---------- */
    .bl-load-wrap{ text-align:center; margin-top:52px; }
    .bl-load-btn{
      position:relative; overflow:hidden; isolation:isolate;
      display:inline-flex; align-items:center; gap:10px;
      padding:14px 34px;
      background:var(--bl-navy-900);
      color:#fff;
      border:1px solid var(--bl-navy-900);
      font-family:var(--bp-font-mono); font-size:12.5px; font-weight:600;
      letter-spacing:.08em; text-transform:uppercase;
      cursor:pointer;
      transition:background .3s var(--bp-ease), border-color .3s var(--bp-ease);
    }
    .bl-load-btn:hover{ background:var(--bl-teal-700); border-color:var(--bl-teal-700); }
    .bl-load-btn:disabled{ opacity:.6; cursor:default; }
    .bl-spinner{ display:none; margin-top:22px; }
    .bl-spinner-dot{
      width:8px; height:8px; border-radius:50%;
      display:inline-block; margin:0 3px;
      background:var(--bl-teal-500);
      animation:bl-bounce 1s ease-in-out infinite;
    }
    .bl-spinner-dot:nth-child(2){ animation-delay:.15s; }
    .bl-spinner-dot:nth-child(3){ animation-delay:.3s; }
    @keyframes bl-bounce{ 0%,80%,100%{ transform:translateY(0); opacity:.4; } 40%{ transform:translateY(-6px); opacity:1; } }
    @media (prefers-reduced-motion:reduce){ .bl-spinner-dot{ animation:none; } }

    .bl-fade-in{ animation:bl-fadeIn .45s ease-in-out; }
    @keyframes bl-fadeIn{ from{opacity:0; transform:translateY(8px);} to{opacity:1; transform:translateY(0);} }
    @media (prefers-reduced-motion:reduce){ .bl-fade-in{ animation:none; } }
    </style>

    <div class="msn-scope">

        <!-- Hero -->
        <section class="bl-hero bl-grain">
            <div class="container bl-hero-inner">
                <span class="bl-chip msn-reveal"><span class="dot"></span>{{ $setting->title ?? 'MSN Softtech' }} Journal</span>
                <h1 class="bl-hero-title msn-reveal">{{ __('navbar.blog') }}</h1>
                <div class="bl-hero-underline msn-reveal"></div>
                <p class="bl-hero-copy msn-reveal">Notes, case studies, and field-tested thinking from the team building your software.</p>
                <div class="bl-breadcrumb msn-reveal">
                    <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
                    <span class="sep">/</span>
                    <span>{{ __('navbar.blog') }}</span>
                </div>
            </div>

            @if(isset($articles) && $articles->count() > 0)
                <div class="bl-plaque msn-reveal" aria-hidden="true">
                    <span class="bl-plaque-num">{{ $articles->count() }}</span>
                    <span class="bl-plaque-label">Articles Published</span>
                </div>
            @endif
        </section>

        <!-- Floating search -->
        <div class="container bl-search-wrap">
            <div class="bl-search-card msn-reveal">
                <span class="bl-search-label">Search the Journal</span>
                <div class="bl-search-field">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Try “web development”, “SEO”, “Laravel”…">
                </div>
            </div>
        </div>

        <!-- Featured article -->
        @if(isset($articles) && $articles->count() > 0)
            <section class="bl-featured msn-section">
                <div class="container">
                    <div class="bl-featured-grid">
                        <div class="bl-featured-text msn-reveal">
                            <span class="msn-eyebrow">Featured Story</span>
                            <div class="bl-featured-meta">
                                <span>No. 001</span>
                                @if($articles[0]->created_at)
                                    <span class="sep"></span>
                                    <span>{{ $articles[0]->created_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <h2>{{ $articles[0]->title }}</h2>
                            <p>{{ Str::limit(strip_tags($articles[0]->description), 320) }}</p>
                            <a href="{{ route('blog.single', $articles[0]->slug) }}" class="bl-read-more">
                                Read the Full Story
                                <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                        <div class="bl-featured-media msn-reveal">
                            <img src="{{ asset('uploads/article/' . $articles[0]->image_path) }}" alt="{{ $articles[0]->title }}">
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Blog grid -->
        <section class="bl-list msn-section">
            <div class="container">
                <div class="msn-section-head msn-reveal">
                    <span class="msn-eyebrow">Latest Articles</span>
                    <h2>More From the Journal</h2>
                </div>

                <div class="bl-grid" id="blogCardsContainer"></div>

                <div class="bl-spinner" id="loadingSpinner">
                    <span class="bl-spinner-dot"></span>
                    <span class="bl-spinner-dot"></span>
                    <span class="bl-spinner-dot"></span>
                </div>

                <div class="bl-load-wrap">
                    <button id="loadMoreBtn" class="bl-load-btn bl-btn-shine">Load More Articles</button>
                </div>
            </div>
        </section>

    </div><!-- /.msn-scope -->

    <!-- Blog Loading Script -->
    <script>
        const blogData = @json(isset($articles) ? $articles->skip(1)->values() : []); // skips the featured post
        let loadedCount = 0;
        const perLoad = 6;

        function stripHtml(html) {
            let div = document.createElement('div');
            div.innerHTML = html || '';
            return div.textContent || div.innerText || '';
        }

        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substr(0, maxLength) + '…';
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d)) return '';
            return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        }

        // Single source of truth for a card's markup, used by both the
        // initial/paginated load and the live search results.
        function createCard(blog, indexLabel) {
            const title = truncateText(stripHtml(blog.title), 60);
            const desc = truncateText(stripHtml(blog.description), 130);
            const date = formatDate(blog.created_at);
            return `
                <div class="bl-card bl-fade-in">
                    <div class="bl-card-media">
                        ${indexLabel ? `<span class="bl-card-index">${indexLabel}</span>` : ''}
                        <img src="/uploads/article/${blog.image_path}" alt="${stripHtml(blog.title)}" loading="lazy">
                    </div>
                    <div class="bl-card-body">
                        ${date ? `<span class="bl-card-date">${date}</span>` : ''}
                        <h3><a href="/blog/${blog.slug}">${title}</a></h3>
                        <p>${desc}</p>
                        <a href="/blog/${blog.slug}" class="bl-read-more">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            `;
        }

>>>>>>> e734773df (msn 2.0 theme change)
        function loadBlogCards() {
            const container = document.getElementById('blogCardsContainer');
            const spinner = document.getElementById('loadingSpinner');
            const loadMoreButton = document.getElementById('loadMoreBtn');

<<<<<<< HEAD
            // Show loading spinner while fetching data
            spinner.style.display = 'block';
            loadMoreButton.disabled = true;

            // Simulate delay (if any real-time API request)
            setTimeout(() => {
                blogData.slice(loadedCount, loadedCount + perLoad).forEach((blog, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.style.marginBottom = '20px';
                    col.classList.add('fade-in-delay');
                    col.innerHTML = `
                        <div class="blog-card p-0">
                            <img src="/uploads/article/${blog.image_path}" class="ml-0 img-fluid" alt="${blog.title}">
                            <div class="p-3 cardBlogExtra">
                                
                                <h5 class="mb-3"><strong>${truncateText(stripHtml(blog.title), 45)}</strong></h5>
                                <p>${truncateText(stripHtml(blog.description), 150)}</p>
                                <a href="/blog/${blog.slug}" class="read-more">READ MORE</a>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
=======
            spinner.style.display = 'block';
            loadMoreButton.disabled = true;

            setTimeout(() => {
                blogData.slice(loadedCount, loadedCount + perLoad).forEach((blog, i) => {
                    const num = String(loadedCount + i + 2).padStart(3, '0'); // +2: featured post is No. 001
                    container.insertAdjacentHTML('beforeend', createCard(blog, `No. ${num}`));
>>>>>>> e734773df (msn 2.0 theme change)
                });

                loadedCount += perLoad;

<<<<<<< HEAD
                // Hide button if all blogs are loaded
=======
>>>>>>> e734773df (msn 2.0 theme change)
                if (loadedCount >= blogData.length) {
                    loadMoreButton.style.display = 'none';
                }

<<<<<<< HEAD
                // Hide the loading spinner and enable the button again
                spinner.style.display = 'none';
                loadMoreButton.disabled = false;
            }, 300); // Simulate network delay
=======
                spinner.style.display = 'none';
                loadMoreButton.disabled = false;
            }, 300);
>>>>>>> e734773df (msn 2.0 theme change)
        }

        function searchBlogCards(keyword) {
            const container = document.getElementById('blogCardsContainer');
            container.innerHTML = '';

            const filtered = blogData.filter(blog =>
                stripHtml(blog.title).toLowerCase().includes(keyword.toLowerCase()) ||
                stripHtml(blog.description).toLowerCase().includes(keyword.toLowerCase())
            );

            if (filtered.length > 0) {
                filtered.forEach(blog => {
<<<<<<< HEAD
                    container.insertAdjacentHTML('beforeend', createCard(blog));
                });
                document.getElementById('loadMoreBtn').style.display = 'none';
            } else {
                container.innerHTML = '<div class="text-center">No blogs found.</div>';
                document.getElementById('loadMoreBtn').style.display = 'none';
            }
=======
                    container.insertAdjacentHTML('beforeend', createCard(blog, null));
                });
            } else {
                container.innerHTML = '<div class="bl-empty">No articles match your search.</div>';
            }

            document.getElementById('loadMoreBtn').style.display = 'none';
>>>>>>> e734773df (msn 2.0 theme change)
        }

        document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);

        document.getElementById('searchInput').addEventListener('input', function () {
            const keyword = this.value.trim();
            if (keyword.length > 0) {
                searchBlogCards(keyword);
            } else {
                document.getElementById('blogCardsContainer').innerHTML = '';
                loadedCount = 0;
                loadBlogCards();
<<<<<<< HEAD
                document.getElementById('loadMoreBtn').style.display = 'block';
            }
        });

        // Helper function to remove HTML tags
        function stripHtml(html) {
            let div = document.createElement("div");
            div.innerHTML = html;
            return div.textContent || div.innerText || "";
        }

        // Helper function to truncate text
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) {
                return text;
            }
            return text.substr(0, maxLength) + '...';
        }

        // document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);



        // Initial load
        loadBlogCards();
    </script>

@endsection
=======
                document.getElementById('loadMoreBtn').style.display = 'inline-flex';
            }
        });

        // initial load
        loadBlogCards();
    </script>

@endsection
>>>>>>> e734773df (msn 2.0 theme change)
