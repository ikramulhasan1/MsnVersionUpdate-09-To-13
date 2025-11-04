@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('portfolio');
@endphp
@if(isset($header))

@section('title', $portfolio->title)

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
        <meta property='og:title' content="{{ $portfolio->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@HiTechParks" />
        <meta name="twitter:url" content="{{ route('portfolio.single', $portfolio->slug) }}" />
        <meta name="twitter:title" content="{{ $portfolio->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($portfolio->description), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" />
    @endif
@endsection

@section('content')
    <style>
        
.navbar-brand {
  font-size: 1.4rem;
  letter-spacing: 0.5px;
}

/* .portfolio-hero {
  height: 70vh;
  background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
              url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8d2ViJTIwZGV2ZWxvcG1lbnR8ZW58MHwwfDB8fHwy&auto=format&fit=crop&q=60&w=600') center/cover no-repeat;
} */

.project-info h6 {
  font-size: 0.85rem;
  letter-spacing: 1px;
}

.project-info p {
  font-size: 1rem;
}

.project-description ul li {
  margin-bottom: 10px;
}

.cta-section {
  background: linear-gradient(135deg, #007bff, #004a9f);
}
    </style>
   
<!-- Hero -->
<section class="portfolio-hero d-flex align-items-center text-center text-white"
    style="height: 70vh; background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
            url('{{ asset('uploads/portfolio/' . $portfolio->image_path) }}') center/cover no-repeat;">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-3">{{ $portfolio->title }}</h1>
        <p class="lead mb-0 text-white ">{{ $portfolio->sub_title }}</p>
    </div>
</section>


  <!-- Project Info -->
  <section class="project-info py-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-3 mb-3 mb-md-0">
          <h6 class="text-muted text-uppercase">Client</h6>
          <p class="font-weight-medium mb-0">{{ $portfolio->client }}</p>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <h6 class="text-muted text-uppercase">Category</h6>
          <p class="font-weight-medium mb-0">{{ $portfolio->categories->pluck('title')->join(', ') }}</p>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <h6 class="text-muted text-uppercase">Technologies</h6>
          <p class="font-weight-medium mb-0">{{ $portfolio->technologies->pluck('short_title')->join(', ') }}</p>
        </div>
        <div class="col-md-3">
          <h6 class="text-muted text-uppercase">Date</h6>
          <p class="font-weight-medium mb-0">{{ $portfolio->date }}</p>
        </div>
      </div>
    </div>
  </section>
@php
  $screenshotImage = json_decode($portfolio->screenshot ?? '[]', true)
@endphp
  <!-- Gallery -->
  <section class="portfolio-gallery py-5 bg-light">
    <div class="container">
      <div class="row">
        @foreach ($screenshotImage as $item)
          <div class="col-md-4 mb-4">
            <img style="width: 350px; height: 300px;" src="{{ asset('uploads/screenshot/' . $item['screenshot_image']) }}" class="img-fluid rounded shadow-sm" alt="Project Screenshot">
          </div>
        @endforeach
      </div>
    </div>
  </section>
<style>
.btn-smart {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 28px;
  border-radius: 10px;
  background-color: #0d6efd;
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  letter-spacing: 0.3px;
  border: 1px solid transparent;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.btn-smart:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
  box-shadow: 0 4px 16px rgba(13, 110, 253, 0.3);
  color: #ffffff;
  transform: translateY(-2px);
}

.btn-smart:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
}

.btn-smart .arrow {
  width: 18px;
  height: 18px;
  stroke: #fff;
  transition: transform 0.3s ease;
}

.btn-smart:hover .arrow {
  transform: translateX(5px);
}

.btn-smart::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.15);
  transition: left 0.4s ease;
}

.btn-smart:hover::after {
  left: 100%;
}
.overview-content p {
  font-size: 16px !important;
}






    .results-impact {
      background-color: #ffffff;
      color: #111;
      padding: 90px 0;
      position: relative;
    }

    .results-impact .section-heading {
      text-align: center;
      margin-bottom: 70px;
    }

    .results-impact .section-heading h2 {
      font-weight: 700;
      font-size: 2.3rem;
      letter-spacing: -0.5px;
      color: #0a0a0a;
      margin-bottom: 10px;
    }

    .results-impact .section-heading p {
      color: #6c757d;
      font-size: 1.1rem;
      max-width: 600px;
      margin: 0 auto;
    }

    .impact-card {
      background: #f9fafc;
      border-radius: 12px;
      padding: 2.2rem 1.8rem;
      border: 1px solid #eaeaea;
      transition: all 0.35s ease;
      position: relative;
      overflow: hidden;
    }

    .impact-card::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(120deg, #007bff, #00b4d8);
      opacity: 0.05;
      transition: width 0.4s ease;
      z-index: 0;
    }

    .impact-card:hover::after {
      width: 100%;
    }

    .impact-icon {
      font-size: 2rem;
      color: #007bff;
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
    }

    .impact-card h5 {
      font-weight: 600;
      color: #222;
      margin-bottom: 0.6rem;
      position: relative;
      z-index: 1;
    }

    .impact-card p {
      color: #666;
      font-size: 0.97rem;
      margin-bottom: 0;
      position: relative;
      z-index: 1;
    }

    /* Hover effect */
    /* .impact-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
      background: #ffffff;
    } */

    /* Simple fade animation */
    /* .impact-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.8s ease forwards;
    } */

    .impact-card:nth-child(1) { animation-delay: 0.2s; }
    .impact-card:nth-child(2) { animation-delay: 0.4s; }
    .impact-card:nth-child(3) { animation-delay: 0.6s; }
    .impact-card:nth-child(4) { animation-delay: 0.8s; }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
</style>
  <!-- Description -->
  <section class="project-description py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <img src="{{ asset('uploads/overview_image/' . $portfolio->overview_image) }}" class="img-fluid rounded shadow" alt="{{ $portfolio->title }}">
        </div>
        <div class="col-lg-6">
          <h3 class="font-weight-bold mb-3">Project Overview</h3>
          <div class="overview-content">
            {!! $portfolio->description !!}
          </div>
          @if (!empty($portfolio->link))
            <a href="{{ $portfolio->link }}" target="_blank" class="btn-smart my-3">
            <span>Visit Now</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7" />
            </svg>
          </a>
          @endif

        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section text-center text-white py-5">
    <div class="container">
      <h2 class="font-weight-bold mb-3">Have a Project in Mind?</h2>
      <p class="mb-4" style="font-size: 16px; color: white;" >Let’s collaborate and build your next web project together.</p>
      <a href="{{ route('contact') }}" class="btn btn-light btn-lg px-4">Contact Us</a>
    </div>
  </section>

  <section class="results-impact">
    <div class="container">
      <div class="section-heading">
        <h2>Results & Impact</h2>
        <p>Real outcomes that reflect our focus on performance, design, and scalability.</p>
      </div>

      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="impact-card h-100 text-center">
            <div class="impact-icon"><i class="fas fa-chart-line"></i></div>
            <h5>+40% Increase in Sales</h5>
            <p>Improved conversion rates through strategic UX enhancements and fast checkout design.</p>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="impact-card h-100 text-center">
            <div class="impact-icon"><i class="fas fa-users"></i></div>
            <h5>25% Higher Engagement</h5>
            <p>Enhanced brand loyalty and return visits by refining content flow and visual identity.</p>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="impact-card h-100 text-center">
            <div class="impact-icon"><i class="fas fa-tachometer-alt"></i></div>
            <h5>Faster Load Times</h5>
            <p>Optimized performance and responsiveness, resulting in smoother user experiences.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
@endsection