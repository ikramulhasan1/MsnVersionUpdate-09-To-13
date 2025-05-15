@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('get-quote');
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

    /* </p><table border="1" cellpadding="1" cellspacing="1" style="width:500px">  */

    .description>ul>li {
        margin-left: 30px !important;
        list-style: initial;
        font-size: 16px !important;
    }


    .description>ol>li {
        /* list-style: decimal; */
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



    
/* process section */
  /* process */
  .process-section {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9fafc;
      padding: 80px 15px;
      /* padding-top: 50px !important;
      padding-bottom: 50px !important; */
    }

    .process-section-title {
      text-align: left;
      margin-bottom: 40px;
    }

    .process-section-title h2 {
      font-weight: 900;
      color: #333333;
    }

    .process-step-box {
      background-color: #fff;
      border: 1px solid #e1e1e1;
      border-radius: 2px;
      padding: 30px 20px;
      height: 100%;
      position: relative;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
      transition: all 0.5s ease;
    }

    .process-step-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .process-step-number {
      width: 35px;
      height: 35px;
      background-color: #0d6efd;
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 16px;
      position: absolute;
      top: -20px;
      left: 20px;
    }

    .process-step-icon {
      width: 30px;
      height: 30px;
      margin-right: 10px;
    }

    .process-step-heading {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 10px;
    }

    .process-step-heading img {
      margin-left: 0px;
    }
    .process-step-arrow {
      position: absolute;
      top: 50%;
      right: -40px;
      width: 40px;
      height: 2px;
      background: repeating-linear-gradient(
        to right,
        #999,
        #999 4px,
        transparent 4px,
        transparent 8px
      );
      animation: moveArrow 1s linear infinite;
    }

    .process-step-arrow::after {
      content: '';
      position: absolute;
      right: -6px;
      top: -4px;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 6px solid #999;
    }

    @keyframes moveArrow {
      0% { background-position: 0; }
      100% { background-position: 8px; }
    }

    .process-btn-orange {
      background-color: #ff6a00;
      color: white;
      padding: 12px 26px;
      border-radius: 5px;
      font-weight: 600;
      text-transform: uppercase;
      border: none;
    }

    .process-btn-orange:hover {
      background-color: #e55c00;
      color: white
    }

    @media (max-width: 991px) {
      .process-step-arrow {
        display: none;
      }
    }
    .process-step-arrow.arrow-hidden {
    display: none !important;
}
.arrow-down {
    /* Customize this arrow to look like a vertical one */
    transform: rotate(90deg); /* or use a different SVG for down */
    /* Add margin or position tweaks as needed */
}

.process-description p{
    font-size: 16px !important; color: #333333 !important;

}




* {
      box-sizing: border-box;
    }

    .quoteFormSection {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(120deg, #F5F7F8, #F5F7F8);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
    }

    .quote-container {
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(20px);
      border-radius: 5px;
      box-shadow: 0 0px 1px rgba(0, 0, 0, 0.2);
      padding: 50px;
      width: 100%;
      max-width: 960px;
      color: #333;
    }

    .quote-container h2 {
      text-align: center;
      margin-bottom: 40px;
      font-size: 32px;
      font-weight: 600;
      color: #222;
    }

    .quote-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px 30px;
    }

    .quote-input,
    .quote-textarea,
    select {
      width: 100%;
      padding: 5px 12px;
      border-radius: 2px;
      border: 1px solid #ddd;
      font-size: 15px;
      background-color: rgba(255, 255, 255, 0.6);
      transition: all 0.3s ease;
    }

    .quote-input:focus,
    .quote-textarea:focus,
    select:focus {
      outline: none;
      border-color: #3f7cf4;
      background-color: #fff;
    }

    .quote-textarea {
      grid-column: 1 / -1;
      resize: vertical;
      min-height: 120px;
    }

    .quote-full-width {
      grid-column: 1 / -1;
    }

    .quote-radio-group {
      grid-column: 1 / -1;
      display: flex;
      gap: 40px;
      margin-top: -10px;
    }

    .quote-radio-group label {
      font-size: 14px;
    }

    .quote-radio-group .quote-input {
      margin-right: 6px;
    }

    .quote-services {
      grid-column: 1 / -1;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .quote-services label {
      background-color: #f0f0f0;
      padding: 5px 20px;
      border-radius: 30px;
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      transition: all 0.3s ease;
      border: 1px solid #ccc;
    }

    .quote-services .quote-input {
      display: none;
    }

    .quote-services .quote-input:checked + label {
      background-color: #3f7cf4;
      color: #fff;
      border-color: #3f7cf4;
    }

    .quote-submit-btn {
      grid-column: 1 / -1;
      padding: 7px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 2px;
      background-color: #3f7cf4;
      color: #fff;
      cursor: pointer;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    .quote-submit-btn:hover {
      background-color: #2c64d4;
    }

    @media (max-width: 768px) {
      .quote-form {
        grid-template-columns: 1fr;
      }

      .quote-radio-group {
        flex-direction: column;
        gap: 10px;
      }
    }
</style>
<!--Page Title-->
{{-- <section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ __('navbar.get_quote') }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.get_quote') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section> --}}
<!--End Page Title-->

<!-- Contact Section -->
{{-- <section class="contact-section">
    <div class="container">
        <div class="row">

            @php
            $section_getquote = \App\Models\Section::section('get-quote');
            @endphp
            @if(isset($section_getquote))
            <!-- Form Column -->
            <div class="form-column col-lg-12 col-md-12 col-sm-12">
                <div class="sec-title left">
                    <h2>{{ $section_getquote->title }}</h2>
                    <div class="text description">{!! $section_getquote->description !!}</div>
                    <div class="separater"></div>
                </div>
                <div class="inner-column">

                    <div class="text-center">
                        <!-- Message Display -->
                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ Session::get('success') }}
                        </div>
                        @endif

                        <!-- Message Display -->
                        @if(Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ Session::get('error') }}
                        </div>
                        @endif

                        <!-- Error Display -->
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form">
                        <form method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data" accept-charset="utf-8">
                            @csrf
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" name="name" placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="email" name="email" placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" name="phone" placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" name="company" placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" name="address" placeholder="{{ __('form.address') }}" value="{{ old('address') }}" required>
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <input type="text" name="city" placeholder="{{ __('form.city') }}" value="{{ old('city') }}" required>
                                </div>

                                <div class="col-lg-12 col-md-12">
                                    <div class="form-element margin-top-20">
                                        <label for="prefer_contact">{{ __('form.prefer_contact') }}</label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4">
                                    <div class="custom-control custom-radio margin-bottom-30">
                                        <input class="custom-control-input" type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact')=='1' ) checked @else checked @endif required>

                                        <label class="custom-control-label" for="pre_email">
                                            {{ __('form.phone') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4">
                                    <div class="custom-control custom-radio margin-bottom-30">
                                        <input class="custom-control-input" type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact')=='2' ) checked @endif required>

                                        <label class="custom-control-label" for="pre_phone">
                                            {{ __('form.email') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12">
                                    <label for="services">{{ __('form.services') }}
                                    </label>
                                </div>
                                <div class="form-group col-lg-12 col-md-12">
                                    <div class="row">
                                        @foreach($services as $service)
                                        <div class="col-lg-3 col-md-6">
                                            @if (!empty($service->short_title))
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="services[]" class="custom-control-input" value="{{ $service->id }}" @if(old('services')==$service->id) checked @endif id="service-{{ $service->id }}">
                                                    <label class="custom-control-label" for="service-{{ $service->id }}">{{ $service->short_title }}</label>
                                                </div>
                                            @else
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="services[]" class="custom-control-input" value="{{ $service->id }}" @if(old('services')==$service->id) checked @endif id="service-{{ $service->id }}">
                                                    <label class="custom-control-label" for="service-{{ $service->id }}">{{ $service->title }}</label>
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group col-lg-12 col-md-12">
                                    <textarea name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>
                                </div>

                                <div class="form-group col-lg-6 col-md-12">
                                    <div class="custom-file">
                                        <input type="file" name="file_path" class="custom-file-input" value="{{ old('file_path') }}" id="file_path">
                                        <label class="custom-file-label" for="file_path">{{ __('form.upload_file') }}</label>
                                    </div>
                                </div>

                                <div class="form-group col-lg-6 col-md-12 text-right">
                                    <button class="theme-btn btn-style-one" type="submit" name="submit-form">{{ __('form.submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</section> --}}
<!--End Contact Section -->
@php
  $section_getquote = \App\Models\Section::section('get-quote');
@endphp
 @if(isset($section_getquote))
<section class="quoteFormSection">
    <div class="quote-container text-center">
      <h2 style="font-weight: 800" class="mb-3" >{{ $section_getquote->title }}</h2>
      <div class="text description mb-4 text-center">{!! $section_getquote->description !!}</div>

      {{-- message --}}
      <!-- Message Display -->
      @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          {{ Session::get('success') }}
      </div>
      @endif

      <!-- Message Display -->
      @if(Session::has('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          {{ Session::get('error') }}
      </div>
      @endif

      <!-- Error Display -->
      @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif

      <form class="quote-form" method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data" accept-charset="utf-8">
        <input class="quote-input" type="text" name="name" placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
        <input class="quote-input" type="email" name="email" placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
        <input class="quote-input" type="tel" name="phone" placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>
        <input class="quote-input" type="text" name="company" placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
        <input class="quote-input" type="text" name="address" placeholder="{{ __('form.address') }}" value="{{ old('address') }}" required>
        <input class="quote-input" type="text" name="city" placeholder="{{ __('form.city') }}" value="{{ old('city') }}" required>
  
        <h6 style="text-align: left !important" for="prefer_contact">{{ __('form.prefer_contact') }}</h6>
        <div class="quote-radio-group">
          
          <label><input class="quote-input" type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact')=='1' ) checked @else checked @endif required>Email </label>
          <label><input class="quote-input" type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact')=='2' ) checked @endif required>Phone </label>
        </div>

        <h6 style="text-align: left !important">{{ __('form.services') }}</h6>
        <div class="quote-services">
          @foreach($services as $service)
            @if (!empty($service->short_title))
              <input class="quote-input" type="checkbox" name="services[]" value="{{ $service->id }}" @if(old('services')==$service->id) checked @endif id="service-{{ $service->id }}"><label for="service-{{ $service->id }}">{{ $service->short_title }}</label>
            @else
              <input class="quote-input" type="checkbox" name="services[]" value="{{ $service->id }}" @if(old('services')==$service->id) checked @endif id="service-{{ $service->id }}"><label for="service-{{ $service->id }}">{{ $service->short_title }}</label>
            @endif
          @endforeach
          
        </div>
  
        <textarea class="quote-textarea" name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>
        <input class="quote-input" type="file" name="file_path" value="{{ old('file_path') }}" id="file_path">
        <button class="quote-submit-btn" type="submit" name="submit-form">SUBMIT NOW</button>
      </form>
    </div>
</section>
@endif

{{-- @php
$section_process = \App\Models\Section::section('process');
@endphp
@if(count($processes) > 0 && isset($section_process))
<!--Feautred Section -->
<section class="feautred-section style-two" style="background-image: url({{ asset('web/images/background/process-bg.png') }});">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sec-title left">
                    <h2>{{ $section_process->title }}</h2>
                    <div class="text description">{!! $section_process->description !!}</div>
                    <div class="separater"></div>
                </div>
            </div>
        </div>
        <div class="featured-box row clearfix">
            @foreach($processes as $key => $process)
            <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="{{ ($key + 1) * 200 }}ms">
                <div class="inner-box">
                    <div class="title-box">
                        <h4><span class="numbe-post">{{ $key + 1 }}</span>{{ $process->title }}</h4>
                    </div>
                    <div class="lower-content">
                        <div class="text description">{!! $process->description !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!--End Feautred Section -->
@endif --}}
@php
$section_process = \App\Models\Section::section('process');
@endphp

@if(count($processes) > 0 && isset($section_process))
{{-- process-section --}}
<section class="process-section px-5">
  <div class="container">
    <div class="process-section-title">
      <h2 style="padding-bottom: 30px !important">{{ $section_process->title }}</h2>
      {{-- <p class="text-muted">From research to testing, we ensure your design is intuitive, user-focused, and aligned with your goals.</p> --}}
    </div>

    <!-- First Row -->
    <div class="row g-4 mb-4">
        @foreach($processes as $key => $process)
            {{-- @foreach ($service->processworks as $key => $process) --}}
            <div class="col-md-4 mb-4">
                <div class="process-step-box">
                    <div class="process-step-number">{{ $key + 1 }}</div>
                    <div class="process-step-heading" style="font-size: 20px; color: #333333;">
                        {{-- <img style="width: 50px; height: 50px;" src="{{ asset('uploads/process/' . $process->image_path) }}" class="process-step-icon" alt=""> --}}
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

                    <div class="process-step-arrow d-none d-md-block 
                        {{ $showArrow ? ($key == 2 ? 'arrow-down' : '') : 'arrow-hidden' }}">
                    </div>
                </div>
            </div>
            {{-- @endforeach --}}
        @endforeach
    </div>
  
    <!-- CTA -->
    <div class="text-center mt-5">
      <a href="https://msnsofttech.com/get-quote" class="btn process-btn-orange">Get in Touch With Us →</a>
    </div>
  </div>
</section >
@endif


@endsection