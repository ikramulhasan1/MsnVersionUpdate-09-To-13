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
  background-color: #a6b6ce;
  border-color: #0a58ca;
  box-shadow: 0 4px 16px rgba(13, 110, 253, 0.3);
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
          <p class="text-muted">
            {!! $portfolio->description !!}
          </p>
<a href="{{ $portfolio->link }}" target="_blank" class="btn-smart">
  <span>Visit Now</span>
  <svg xmlns="http://www.w3.org/2000/svg" class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7" />
  </svg>
</a>



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


  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
@endsection