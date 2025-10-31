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
      background-color: #052C58;
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
      background: repeating-linear-gradient(to right,
          #999,
          #999 4px,
          transparent 4px,
          transparent 8px);
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
      0% {
        background-position: 0;
      }

      100% {
        background-position: 8px;
      }
    }

    .process-btn-orange {
      background-color: #052C58;
      color: white;
      padding: 12px 26px;
      border-radius: 5px;
      font-weight: 600;
      text-transform: uppercase;
      border: none;
    }

    .process-btn-orange:hover {
      background-color: #052C58;
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
      transform: rotate(90deg);
      /* or use a different SVG for down */
      /* Add margin or position tweaks as needed */
    }

    .process-description p {
      font-size: 16px !important;
      color: #333333 !important;

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
      border-color: #052C58;
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

    .quote-services .quote-input:checked+label {
      background-color: #052C58;
      color: #fff;
      border-color: #052C58;
    }

    .quote-submit-btn {
      grid-column: 1 / -1;
      padding: 7px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 2px;
      background-color: #052C58;
      color: #fff;
      cursor: pointer;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    .quote-submit-btn:hover {
      background-color: #193B62;
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

  <style>
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




    /*  */
.quote-services {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  position: relative;
}

.service-item {
  position: relative;
  z-index: 1;
}

.service-label {
  display: inline-block;
  padding: 10px 18px;
  border-radius: 25px;
  background-color: #f0f0f0;
  color: #333;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.service-checkbox:checked + .service-label {
  background-color: #052C58;
  color: #fff;
}

/* Subservices dropdown */
.subservices {
  display: none;
  flex-wrap: wrap;
  gap: 8px;
  position: absolute;
  top: 110%;
  left: 0;
  width: max-content;
  min-width: 280px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  z-index: 10;
}

.subservice-item {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 6px 10px;
  border: 1px solid #ddd;
  border-radius: 15px;
  background-color: #fafafa;
  transition: all 0.3s;
}

.subservice-item:hover {
  background-color: #e8f0fe;
}

.subservice-item label {
  border-radius: 20px;
  background: #f9f9f9;
  padding: 6px 12px;
  cursor: pointer;
}

.subservice-item input[type="checkbox"] {
  display: none;
}

.subservice-item input[type="checkbox"]:checked + label {
  background: #052C58;
  color: #fff;
}
  </style>
  <section class="about-hero-section" data-aos="fade">
    <div class="container">
      <h1>{{ __('Quote') }}</h1>
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
            <li>{{ __('Quote') }}</li>
            <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  @php
    $section_getquote = \App\Models\Section::section('get-quote');
  @endphp
  @if(isset($section_getquote))
    <section class="quoteFormSection">
      <div class="quote-container text-center">
        <h2 style="font-weight: 800" class="mb-3">{{ $section_getquote->title }}</h2>
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
  @csrf
  <input type="hidden" name="work_model" value="{{ $work_model }}">
  <input type="hidden" name="work_scope" value="{{ $work_scope }}">

  <input class="quote-input" type="text" name="name" placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
  <input class="quote-input" type="email" name="email" placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
  <input class="quote-input" type="tel" name="phone" placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>
  <input class="quote-input" type="text" name="company" placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
  <input class="quote-input" type="text" name="address" placeholder="{{ __('form.address') }}" value="{{ old('address') }}" required>
  <input class="quote-input" type="text" name="city" placeholder="{{ __('form.city') }}" value="{{ old('city') }}" required>

  <h6 style="text-align: left !important" for="prefer_contact">{{ __('form.prefer_contact') }}</h6>
  <div class="quote-radio-group">
    <label class="d-flex align-items-center">
      <input class="quote-input" type="radio" name="prefer_contact" value="1" id="pre_email" @if(old('prefer_contact') == '1') checked @else checked @endif required>Email 
    </label>
    <label class="d-flex align-items-center">
      <input class="quote-input" type="radio" name="prefer_contact" value="2" id="pre_phone" @if(old('prefer_contact') == '2') checked @endif required>Phone 
    </label>
  </div>

  <h6 style="text-align: left !important">{{ __('form.services') }}</h6>

  <div class="quote-services">
    @foreach($services as $service)
      <div class="service-item position-relative">
        <input type="checkbox" class="quote-input service-checkbox d-none" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}">
        <label class="service-label" for="service-{{ $service->id }}">{{ $service->short_title }}</label>

        @if($service->subservices && $service->subservices->count() > 0)
          <div class="subservices shadow-sm" id="subservices-{{ $service->id }}">
            @foreach($service->subservices as $sub)
              <div class="subservice-item">
                <input type="checkbox" name="sub_service[]" value="{{ $sub->short_title }}" id="sub-{{ $sub->id }}">
                <label for="sub-{{ $sub->id }}">{{ $sub->short_title }}</label>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>

  <textarea class="quote-textarea" name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>
  <input class="quote-input" type="file" name="file_path" value="{{ old('file_path') }}" id="file_path">

  <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
  @if ($errors->has('captcha'))
    <p class="text-danger">{{ $errors->first('captcha') }}</p>
  @endif
  <button class="quote-submit-btn" type="submit" name="submit-form">SUBMIT NOW</button>
</form>

      </div>
    </section>
  @endif


  @php
    $section_process = \App\Models\Section::section('process');
  @endphp

  @if(count($processes) > 0 && isset($section_process))
    {{-- process-section --}}
    <section class="process-section">
      <div class="container">
        <div class="process-section-title">
          <h2 style="padding-bottom: 30px !important">{{ $section_process->title }}</h2>
          {{-- <p class="text-muted">From research to testing, we ensure your design is intuitive, user-focused, and aligned
            with your goals.</p> --}}
        </div>

        <!-- First Row -->
        <div class="row g-4 mb-4">
          @foreach($processes as $key => $process)
            {{-- @foreach ($service->processworks as $key => $process) --}}
            <div class="col-md-4 mb-4">
              <div class="process-step-box">
                <div class="process-step-number">{{ $key + 1 }}</div>
                <div class="process-step-heading" style="font-size: 20px; color: #333333;">
                  {{-- <img style="width: 50px; height: 50px;" src="{{ asset('uploads/process/' . $process->image_path) }}"
                    class="process-step-icon" alt=""> --}}
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
    </section>
  @endif

  {{--
  <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

  // Toggle subservice visibility when main service label is clicked
  $('.service-label').on('click', function (e) {
    e.preventDefault();

    let parent = $(this).closest('.service-item');
    let checkbox = parent.find('.service-checkbox');
    let subDiv = parent.find('.subservices');

    // If service has subservices
    if (subDiv.length > 0) {
      // Always keep service checked
      if (!checkbox.is(':checked')) {
        checkbox.prop('checked', true);
      }

      // Just toggle visibility of the dropdown
      if (subDiv.is(':visible')) {
        subDiv.stop(true, true).slideUp(300); // minimize
      } else {
        subDiv.stop(true, true).slideDown(300); // open
      }
    } else {
      // No subservices → toggle checkbox normally
      checkbox.prop('checked', !checkbox.prop('checked'));
    }
  });

  // Handle subservice checkbox changes
  $(document).on('change', '.subservice-item input[type="checkbox"]', function () {
    let parentService = $(this).closest('.service-item');
    let parentCheckbox = parentService.find('.service-checkbox');
    let subDiv = parentService.find('.subservices');

    // Keep parent checked if any subservice is checked
    if (parentService.find('.subservice-item input:checked').length > 0) {
      parentCheckbox.prop('checked', true);
    } else {
      // Optional: Uncheck parent only if all subservices unchecked
      parentCheckbox.prop('checked', false);
      subDiv.stop(true, true).slideUp(300);
    }
  });

  // Optional: click outside to hide all subservice lists (keep selections)
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.service-item').length) {
      $('.subservices').slideUp(200);
    }
  });
});
</script>

@endsection