@extends('web.layouts.master')
@section('content')


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #fff;
  }
  .hero {
    background: #003366;
    color: white;
    padding: 4rem 2rem 3rem;
    text-align: center;
  }
  .hero h1 {
    font-size: 3rem;
    font-weight: 700;
    letter-spacing: 1px;
  }
  .filters {
    padding: 2rem 0 1rem;
  }
  .filters .divider {
    padding: 0 1rem;
    font-weight: 500;
    color: #666;
  }
  .filter-btn {
    background: #ffffff;
    border: 2px solid #dcdcdc;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 1rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }
  .filter-btn i {
    font-size: 1.2rem;
  }
  .case-card {
    border: 1px solid #d6d6d6;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
    height: 100%;
    transition: transform 0.2s ease-in-out;
  }
  .case-card:hover {
    transform: translateY(-4px);
  }
  .case-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  .card-body {
    padding: 1rem 1.25rem;
  }
  .card-body h5 {
    font-size: 18px !important;
    font-weight: 700 !important;
    margin-bottom: 0.75rem !important;
  }
  .card-body p {
    margin-bottom: 0.5rem;
    font-size: 16px !important;
    color: #333333 !important;
  }
  /* .card-body .btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
  } */
  .btn-download {
    border: none;
    border-bottom: 1px solid #a7a7a7;
    color: #333333;
    font-size: 15px;
    background-color: white;
    padding: 10px 0px 5px 0px !important;
    border-radius: 0px;
    text-decoration: none !important;
  }
  .btn-download:hover {
    border: none;
    border-bottom: 2px solid #a7a7a7;
    color: #333333;
    font-size: 15px;
    background-color: white;
    padding: 10px 0px 5px 0px !important;
    border-radius: 0px;
    text-decoration: none !important;
  }
  .btn-download-icon  {
    font-size: 20px;
    padding: 10px 10px 5px 0px !important;
    color: #008939;
  }
  .btn-readmore {
    border: 2px solid #052C58;
    color: #052C58;
    font-size: 16px !important;
    font-weight: 600 !important;
    background-color: rgb(255, 255, 255);
    padding: 10px 20px;
    border-radius: 4px;
  }
  .btn-readmore:hover {
    background-color: #052C58;
    color: #ffffff;
  }
  .pagination .page-link {
    border-radius: 8px;
    margin: 0 0.15rem;
    color: #333;
    font-weight: 500;
  }
  .pagination .page-item.active .page-link {
    background-color: #052C58;
    border-color: #052C58;
    color: #fff;
  }


</style>
  <!-- Hero Banner -->
  <div class="hero">
    <h1>CASE STUDIES</h1>
  </div>

  <!-- Filters -->
  <div class="container filters d-flex justify-content-end align-items-center">
    <button class="filter-btn">
      <i class="bi bi-sliders"></i> Filter by Technology
    </button>
    <span class="divider">OR</span>
    <button class="filter-btn">
      <i class="bi bi-sliders"></i> Filter by Industry
    </button>
  </div>

  <!-- Cards Grid -->
  <div class="container pb-5">
    <div class="row g-4">
      <!-- Start of card -->
      @foreach ($case_studies as $case)
        <div class="col-md-6 col-lg-4 mb-4">
         
          <div class="card case-card">
            <img src="{{ asset('uploads/case-study/'.$case->image_path) }}" alt="{{ $case->main_title }}" class="card-img-top">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ $case->main_title }}</h5>
              <p class="industry"><strong>Industry:</strong> {{ $case->industry }}</p>
              <p><strong>Skills:</strong> {{ $case->tech_stack }}</p>
              
              <div class="mt-auto d-flex justify-content-between pt-2">
                <div class="d-flex align-items-center">
                    <i class="btn-download-icon bi bi-file-earmark-arrow-down"></i>
                    <a class="btn btn-sm btn-download">
                        DOWNLOAD
                    </a>
                </div>
                <a href="{{ route('case-study.single', $case->slug) }}" class="btn btn-sm btn-readmore">
                  READ MORE
                </a>
              </div>
            </div>
          </div>
          
        </div>
      @endforeach

    </div>
  </div>

  <!-- Pagination -->
  {{-- <div class="container pb-5 d-flex justify-content-center">
    <nav>
      <ul class="pagination">
        <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
        <li class="page-item"><a class="page-link" href="#">21</a></li>
        <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
      </ul>
    </nav>
  </div> --}}
  @endsection