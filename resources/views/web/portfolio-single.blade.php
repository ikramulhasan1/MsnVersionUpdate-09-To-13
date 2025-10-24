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

.portfolio-hero {
  height: 70vh;
  background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
              url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8d2ViJTIwZGV2ZWxvcG1lbnR8ZW58MHwwfDB8fHwy&auto=format&fit=crop&q=60&w=600') center/cover no-repeat;
}

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
  <section class="portfolio-hero d-flex align-items-center text-center text-white">
    <div class="container">
      <h1 class="display-4 font-weight-bold mb-3">{{ $portfolio->title }}</h1>
      <p class="lead mb-0">{{ $portfolio->sub_title }}</p>
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
          <p class="font-weight-medium mb-0">{{ $portfolio->technologies->pluck('title')->join(', ') }}</p>
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
        <div class="col-md-4 mb-4">
          <img src="https://images.unsplash.com/photo-1585247226801-bc613c441316?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8d2ViJTIwZGV2ZWxvcG1lbnR8ZW58MHwyfDB8fHwy&auto=format&fit=crop&q=60&w=600" class="img-fluid rounded shadow-sm" alt="Project Image 1">
        </div>
        <div class="col-md-4 mb-4">
          <img src="https://images.unsplash.com/photo-1516131206008-dd041a9764fd?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8d2ViJTIwZGV2ZWxvcG1lbnR8ZW58MHwyfDB8fHwy&auto=format&fit=crop&q=60&w=600" class="img-fluid rounded shadow-sm" alt="Project Image 2">
        </div>
        <div class="col-md-4 mb-4">
          <img src="https://images.unsplash.com/photo-1493020258366-be3ead1b3027?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fHdlYiUyMGRldmVsb3BtZW50fGVufDB8MnwwfHx8Mg%3D%3D&auto=format&fit=crop&q=60&w=600" class="img-fluid rounded shadow-sm" alt="Project Image 3">
        </div>
      </div>
    </div>
  </section>

  <!-- Description -->
  <section class="project-description py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <img src="https://media.istockphoto.com/id/2212360504/photo/holographic-ui-ux-display-icons-of-ux-ui-designer-creative-planning-data-visualization-web.webp?a=1&b=1&s=612x612&w=0&k=20&c=tglFI9NeJVQNtibp78qcgjGBPkX43btcoxkLc1RN2_o=" class="img-fluid rounded shadow" alt="Main Project Image">
        </div>
        <div class="col-lg-6">
          <h3 class="font-weight-bold mb-3">Project Overview</h3>
          <p class="text-muted">
            {!! $portfolio->description !!}
          </p>
          
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section text-center text-white py-5">
    <div class="container">
      <h2 class="font-weight-bold mb-3">Have a Project in Mind?</h2>
      <p class="mb-4">Let’s collaborate and build your next web project together.</p>
      <a href="#" class="btn btn-light btn-lg px-4">Contact Us</a>
    </div>
  </section>


  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
@endsection