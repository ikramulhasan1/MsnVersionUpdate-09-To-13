@extends('web.layouts.master')
@php
    $header = \App\Models\PageSetup::page('related-service');
@endphp
@if (isset($header))

    @section('title', content: $service->title)

    @section('top_meta_tags')
        @if (isset($service->short_desc))
            <meta name="description" content="{!! str_limit(strip_tags($service->short_desc), 200, ' ...') !!}">
        @else
            <meta name="description" content="{!! str_limit(strip_tags($service->short_desc), 200, ' ...') !!}">
        @endif

        <script type="application/ld+json">
                                                {
                                                  "@context": "http://schema.org",
                                                  "@type": "Product",
                                                  "name": "{{ $service->title }}",
                                                  "image": {
                                                    "@type": "ImageObject",
                                                    "url": "{{ asset('uploads/service/' . $service->image_path) }}",
                                                    "width": "100",
                                                    "height": "100"
                                                  },

                                                  "description": "{{ Str::limit(strip_tags($service->description), 500, '...') }}",
                                                  "url": "{{ route('service.related-single', $service->slug) }}",
                                                  "brand": {
                                                    "@type": "Brand",
                                                    "name": "MSN Softtech",
                                                    "logo": "https://msnsofttech.com/uploads/setting/Untitled-4_1739083515.png"
                                                  },
                                                  "offers": {
                                                    "@type": "Offer",
                                                    "price": "{{ $service->price ?? '999' }}",
                                                    "priceCurrency": "USD",
                                                    "availability": "https://schema.org/InStock",
                                                    "priceValidUntil": "{{ now()->addMonths(6)->format('Y-m-d') }}",
                                                    "hasMerchantReturnPolicy": {
                                                      "@type": "MerchantReturnPolicy",
                                                      "applicableCountry": "US",
                                                      "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
                                                      "returnPolicySeasonalOverride": "https://schema.org/MerchantReturnNotPermitted",
                                                      "returnShippingFeesAmount": {
                                                        "@type": "MonetaryAmount",
                                                        "value": "0.00",
                                                        "currency": "USD"
                                                      },
                                                      "merchantReturnDays": "30",
                                                      "returnMethod": "https://schema.org/ReturnByMail",
                                                      "returnFees": "FreeReturn"
                                                    },
                                                    "shippingDetails": {
                                                      "@type": "OfferShippingDetails",
                                                      "shippingRate": {
                                                        "@type": "MonetaryAmount",
                                                        "value": "0.00",
                                                        "currency": "USD"
                                                      },
                                                      "deliveryTime": {
                                                        "@type": "ShippingDeliveryTime",
                                                        "businessDays": {
                                                          "@type": "OpeningHoursSpecification",
                                                          "dayOfWeek": ["https://schema.org/Monday", "https://schema.org/Tuesday", "https://schema.org/Wednesday", "https://schema.org/Thursday", "https://schema.org/Friday", "https://schema.org/Saturday",
                                                          "https://schema.org/Sunday"]
                                                        },
                                                        "handlingTime": {
                                                          "@type": "QuantitativeValue",
                                                          "minValue": 1,
                                                          "maxValue": 2,
                                                          "unitCode": "DAY"
                                                        },
                                                        "transitTime": {
                                                          "@type": "QuantitativeValue",
                                                          "minValue": 3,
                                                          "maxValue": 5,
                                                          "unitCode": "DAY"
                                                        }
                                                      },
                                                      "shippingDestination": {
                                                        "@type": "DefinedRegion",
                                                        "addressCountry": "US"
                                                      }
                                                    }
                                                  },
                                                  "aggregateRating": {
                                                    "@type": "AggregateRating",
                                                    "ratingValue": "{{ $service->average_rating }}",
                                                    "bestRating": "5",
                                                    "worstRating": "1",
                                                    "ratingCount": "{{ $service->review_count }}",
                                                  },
                                                  "review": {
                                                    "@type": "Review",
                                                    "author": {
                                                      "@type": "Person",
                                                      "name": "Joseph Garcia"
                                                    },
                                                    "datePublished": "{{ $service->created_at->format('Y-m-d') }}",
                                                    "reviewRating": {
                                                      "@type": "Rating",
                                                      "ratingValue": "5",
                                                      "bestRating": "5",
                                                      "worstRating": "1"
                                                    },
                                                    "reviewBody": "MSN Softtech delivered an exceptional custom {{ $service->short_title }} solution that enhanced our online presence and improved performance."
                                                  }
                                                }
                                                </script>


        <!-- JSON-LD markup generated by Google Structured Data Markup Helper. -->

        @if (isset($header->meta_keywords))
            <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
        @else
            <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
        @endif
    @endsection

@endif

@section('social_meta_tags')
    @if (isset($setting))
        <meta property="og:type" content="website">
        <meta property='og:site_name' content="{{ $setting->title }}" />
        <meta property='og:title' content="{{ $service->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($service->short_desc), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('service.related-single', $service->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/service/' . $service->image_path) }}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@MSNSOFTTECH" />
        <meta name="twitter:url" content="{{ route('service.related-single', $service->slug) }}" />
        <meta name="twitter:title" content="{{ $service->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($service->short_desc), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/service/' . $service->image_path) }}" />
    @endif
@endsection

{{-- schema section --}}

@section('content')

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- intl-tel-input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap 4.1 CSS -->
    {{--
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> --}}

    <!-- Bootstrap Icons (works independently) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Base */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #052C58;
        }

        /* HERO */
        .hero-section {
            /* background: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1350&q=80') center/cover no-repeat;
                                        color: #fff;
                                        padding: 120px 0;
                                        position: relative; */
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Section titles */
        .section-title {
            text-align: center;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 2.5rem;
        }

        /* Service details */
        .service-detail img {
            border-radius: 10px;
        }

        .service-detail p {
            line-height: 1.8;
            color: #555;
        }

        /* Feature */
        .feature-card {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all .3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-card i {
            font-size: 2rem;
            color: #052C58;
            margin-bottom: 15px;
        }

        /* Process */
        .process-step {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all .3s ease;
        }

        .process-step:hover {
            transform: translateY(-5px);
        }

        /* Project / portfolio */
        .project-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            transition: transform .4s ease;
        }

        .project-item img {
            display: block;
            width: 100%;
            height: auto;
        }

        .project-item:hover {
            transform: scale(1.05);
        }

        .project-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: all .4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .project-item:hover .project-overlay {
            opacity: 1;
        }

        /* CTA */
        .cta-section {
            background: #052C58;
            color: #fff;
            padding: 60px 0;
            text-align: center;
            border-radius: 10px;
        }

        /* FAQ (Bootstrap 4) */
        .faq-section .card-header {
            background: #fff;
            border-bottom: none;
        }

        .faq-section .btn-link {
            text-decoration: none;
            font-weight: 500;
            color: #052C58;
        }

        /* Testimonials small tweaks */
        .testimonial {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        /* Project journey scroller */
        .journey-scroll {
            display: flex;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            gap: 1rem;
        }

        .journey-scroll>div {
            min-width: 250px;
        }

        /* Utility fixes for Bootstrap4 compatibility */
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .g-4 {
            margin-left: 0;
            margin-right: 0;
        }

        /* placeholder so markup with g-4 won't break layout */


        .iti.iti--allow-dropdown {
            width: 100%;
        }

        .contact-wrapper {
            width: 90%;
            max-width: 1150px;
            margin: 60px auto;
            background: #fff;
            border-radius: 4px;
            /* box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); */
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
        }

        /* Left Form Section */
        .form-section {
            flex: 1;
            padding: 60px 50px;
            background: #fff;
        }

        .form-section h2 {
            font-weight: 700;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .form-section p {
            color: #6c757d;
            font-size: 15px;
            margin-bottom: 35px;
        }

        .form-control {
            border-radius: 4px;
            height: 46px;
            font-size: 14px;
        }

        textarea.form-control {
            height: auto;
        }

        .btn-primary {
            background-color: #6c4ef7;
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #5639d1;
        }

        .form-check-label {
            font-size: 14px;
            color: #6c757d;
        }

        /* Right Image Section */
        .image-section {
            flex: 1;
            background: url('https://images.unsplash.com/photo-1615840287214-7ff58936c4cf?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=387') center center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-height: 650px;
        }

        .info-overlay {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(40px);
            border-radius: 6px;
            color: #fff;
            width: 90%;
            margin-bottom: 40px;
            padding: 30px 20px;
            text-align: center;
            /* display: flex; */

        }

        .info-overlay .contact-box {
            margin-bottom: 30px;
        }

        .info-overlay .contact-box:last-child {
            margin-bottom: 0;
        }

        .info-overlay .contact-box i {
            font-size: 20px;
            margin-bottom: 8px;
            display: block;
        }

        .info-overlay h6 {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-overlay p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        @media (max-width: 991px) {
            .contact-wrapper {
                flex-direction: column;
            }

            .image-section {
                min-height: 300px;
                order: -1;
            }

            .form-section {
                padding: 40px 30px;
            }

            .info-overlay {
                position: relative;
                margin: 20px auto;
            }
        }
    </style>


    <!-- HERO -->
    @php
        $banners = json_decode($service->banner_steps ?? '[]', true);
        $features = json_decode($service->features_steps ?? '[]', true);
        $process = json_decode($service->process_steps ?? '[]', true);
        $why_we = json_decode($service->why_we_steps ?? '[]', true);
        $industries = json_decode($service->industries_steps ?? '[]', true);
        $achievements = json_decode($service->achievements_steps ?? '[]', true);
        $success_stories = json_decode($service->success_stories_steps ?? '[]', true);
        $clients_say = json_decode($service->clients_say_steps ?? '[]', true);
        $faq = json_decode($service->faq_steps ?? '[]', true);
        $our_promise = json_decode($service->our_promise ?? '[]', true);
        $cta = json_decode($service->cta_steps ?? '[]', true);
    @endphp

    @foreach ($banners as $item)
        <section class="hero-section d-flex align-items-center justify-content-center text-center"
            style="background: url('{{ asset('uploads/banner/' . $item['banner_image'] ?? 'default.jpg') }}') center/cover no-repeat; color: #fff; padding: 120px 0; position: relative;">
            <div class="container hero-content">
                <h1 class="display-4 font-weight-bold">{{ $item['title'] }}</h1>
                <p class="lead text-white">{{ $item['sub_title'] }}</p>
                <a href="#contact" style="background-color: #052C58; color: #ffffff;" class="btn btn-lg mt-3">Get
                    Started</a>
            </div>
        </section>
    @endforeach
    <!-- SERVICE DETAILS -->
    <section class="py-5 service-detail">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('uploads/subservices/' . $service->image_path ?? 'default.jpg') }}"
                        class="img-fluid" alt="Web Development">
                </div>
                <div class="col-lg-6">
                    <h2 style="font-weight: 800" class=" mb-3">{{ $service->title }}</h2>
                    <p>{!! $service->description !!}</p>

                </div>
            </div>
        </div>
    </section>

    <!-- CORE FEATURES -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 style="font-weight: 800" class="section-title">Our Core Features</h2>
            <p class="section-subtitle">Empowering your business with next-level web technology.</p>

            <div class="row">
                @foreach ($features as $item)
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="feature-card h-100 text-center">
                            <div class="icon-box mb-3">
                                <i class="{{ $item['icon_class'] }}"></i>
                            </div>
                            <h5 class="font-weight-bold">{{ $item['title'] }}</h5>
                            <p>{{ $item['bottom_text'] }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- SERVICE PROCESS -->
    <section class="py-5">
        <div class="container">
            <h2 style="font-weight: 800" class="section-title">Our Work Process</h2>
            <p class="section-subtitle">We follow a streamlined workflow for perfect project delivery.</p>

            <div class="row text-center">
                @foreach ($process as $key => $item)
                    <div class="col-md-3 mb-4">
                        <div class="process-step h-100">
                            <div style="color: #052C58" class="step-number display-4 font-weight-bold">0{{ $key + 1 }}
                            </div>
                            <h5 class="mt-3 font-weight-bold">{{ $item['title'] }}</h5>
                            <p>{{ $item['bottom_text'] }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 style="font-weight: 800" class="section-title mb-4">Why Choose MSNSoftech?</h2>
            <div class="row">
                @foreach ($why_we as $item)
                    <div class="col-md-4 mb-4">
                        <div class="p-4 border rounded h-100 shadow-sm">
                            <i class="{{ $item['icon_class'] }} display-4 mb-3" style="color: #052C58"></i>
                            <h5 class="font-weight-bold">{{ $item['title'] }}</h5>
                            <p>{{ $item['bottom_text'] }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>


    <!-- INDUSTRIES SECTION -->
    <section class="container my-5">
        <div class="text-center mb-4">
            <h2 style="font-weight: 800; color: #052C58;" class="">Industries We Serve</h2>
            <p class="text-muted">From eCommerce to SaaS and healthcare</p>
        </div>

        <div class="row justify-content-center">
            @foreach ($industries as $item)
                <div class="col-6 col-md-3 mb-3">
                    <div class="p-4 bg-white rounded shadow-sm text-center h-100">
                        <i class="{{ $item['icon_class'] }}" style="font-size:2rem; color: #052C58;"></i>
                        <div class="mt-2 text-muted font-weight-bold">{{ $item['title'] }}</div>
                    </div>
                </div>
            @endforeach

        </div>
    </section>

    <!-- STATS / ACHIEVEMENTS SECTION -->
    <section class="container my-5">
        <div class="text-center mb-4">
            <h2 style="font-weight: 800; color: #052C58;" class="">Achievements</h2>
            <p class="text-muted font-weight-bold">Numbers that show our impact</p>
        </div>

        <div class="row text-center">
            @foreach ($achievements as $item)
                <div class="col-md-3 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h2 style="color: #052C58;" class="font-weight-bold stat-number"
                            data-target="{{ $item['count_number'] }}">0</h2>
                        <p class="mb-0 text-muted font-weight-bold">{{ $item['title'] }}</p>
                    </div>
                </div>
            @endforeach

        </div>
    </section>

    <!-- COUNTER SCRIPT (works in Bootstrap 4) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var counters = document.querySelectorAll('.stat-number');
            counters.forEach(function(counter) {
                var target = +counter.getAttribute('data-target');
                var count = 0;
                var increment = target / 100;

                function updateCounter() {
                    count += increment;
                    if (count < target) {
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = target;
                    }
                }

                updateCounter();
            });
        });
    </script>

    <!-- STYLING -->
    <style>
        .stat-number {
            font-size: 2.5rem;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <!-- PORTFOLIO -->
    @if (!empty($service->portfolios) && count($service->portfolios) > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <h2 style="font-weight: 800" class="section-title">Recent Projects</h2>
                <p style="font-weight: 800" class="section-subtitle">Explore some of our successful work for clients
                    around the
                    world.</p>

                <div class="row">
                    @foreach ($service->portfolios as $portfolio)
                        <div class="col-md-4 mb-4">
                            <div class="project-item position-relative overflow-hidden rounded shadow-sm"
                                style="width: 350px; height:233px ">
                                <img src="{{ asset('uploads/portfolio/' . $portfolio->image_path) }}" class="img-fluid"
                                    alt="{{ $portfolio->title }}">
                                <div class="project-overlay">
                                    <h5><a href="{{ route('portfolio.single', $portfolio->slug) }}"
                                            class="text-white font-weight-bold">{{ $portfolio->title }}</a></h5>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif
    <!-- TECHNOLOGIES -->
    @if (!empty($service->technologies) && count($service->technologies) > 0)
        <section class="py-5 bg-light">
            <div class="container text-center">
                <h2 style="font-weight: 800" class="section-title">Technologies We Use</h2>
                <p class="section-subtitle">We combine creativity and the latest tools to deliver high-quality solutions.
                </p>

                <div class="row justify-content-center align-items-center">
                    @foreach ($service->technologies as $technology)
                        <a href="{{ route('service.technology', $technology->slug) }}" class="col-4 col-md-2 mb-3">
                            <img src="{{ asset('uploads/technology/' . $technology->logo_path) }}" class="img-fluid"
                                title="{{ $technology->title }}" alt="{{ $technology->title }}"
                                style="max-height:70px;">
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <!-- CASE STUDIES / RESULTS -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 style="font-weight: 800" class="section-title text-center">Our Success Stories</h2>
            <p class="section-subtitle text-center">See how we’ve helped clients achieve measurable results.</p>

            <div class="row mt-4">
                @foreach ($success_stories as $item)
                    <div class="col-md-4 mb-4">
                        <div class="case-card bg-white shadow-sm rounded p-4 h-100">
                            <h5 class="font-weight-bold mb-2">{{ $item['title'] }}</h5>
                            <p>{{ $item['bottom_text'] }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <style>
        .project-item {
            transition: transform .4s ease;
        }

        .project-item:hover {
            transform: scale(1.05);
        }

        .project-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: all .4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .project-item:hover .project-overlay {
            opacity: 1;
        }
    </style>

    <!-- TESTIMONIALS + FAQ -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- TESTIMONIALS -->
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 style="font-weight: 800" class="section-title">What Our Clients Say</h2>
                    @foreach ($clients_say as $item)
                        <div class="testimonial p-4 bg-light rounded shadow-sm mb-4">
                            <p>{{ $item['meassage'] }}</p>
                            <h6 class="font-weight-bold mb-0">— {{ $item['title'] }}</h6>
                        </div>
                    @endforeach

                </div>

                <!-- FAQ (Bootstrap 4 collapse) -->
                @if (!empty($faq))
                    <div class="col-lg-6">
                        <h2 style="font-weight: 800" class="section-title">Have Questions?</h2>
                        <div id="accordionFaq" class="faq-section">
                            @foreach ($faq as $key => $item)
                                <div class="card">
                                    <div class="card-header" id="faqHeading{{ $key }}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse"
                                                data-target="#faq{{ $key }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="faq{{ $key }}">
                                                {{ $item['question'] }}
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="faq{{ $key }}" class="collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="faqHeading{{ $key }}" data-parent="#accordionFaq">
                                        <div class="card-body">
                                            {!! $item['answer'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div> <!-- /.accordion -->
                    </div>
                @endif

            </div>
    </section>

    <!-- PROMISE SECTION -->
    @foreach ($our_promise as $item)
        <section class="py-5 text-white text-center" style="background-color: #052C58;">
            <div class="container">
                <h2 class="font-weight-bold mb-3">Our Promise</h2>
                <p class="mb-4 lead text-white">{{ $item['bottom_text'] }}</p>
            </div>
        </section>
    @endforeach

    <div id="contact" class="contact-wrapper">
        <!-- Left: Contact Form -->
        <div class="form-section">
            <h2>We're here to help</h2>
            <p>Our dedicated team is ready to support you.</p>

            <form method="post" action="{{ route('get-quote.store') }}" enctype="multipart/form-data"
                accept-charset="utf-8">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Full name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name"
                            placeholder="{{ __('form.your_name') }}" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Company<span class="text-danger">(optional)</span></label>
                        <input type="text" class="form-control" name="company"
                            placeholder="{{ __('form.company') }}" value="{{ old('company') }}">
                    </div>
                </div>

                <div class="row">
                <div class="form-group col-lg-6 col-md-6 col-12">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email"
                        placeholder="{{ __('form.email_address') }}" value="{{ old('email') }}" required>
                </div>

                <div class="form-group col-lg-6 col-md-6 col-12">
                    <label>Phone number <span class="text-danger">*</span></label><br>
                    <input id="phone" type="tel" class="form-control" name="phone"
                        placeholder="{{ __('form.phone_no') }}" value="{{ old('phone') }}" required>
                </div>
</div>
                <div class="form-group">
                    <label>Choose a topic <span class="text-danger">*</span></label>
                    <select name="services[]" class="form-control" required>
                        @foreach ($all_service as $service)
                            @if (!empty($service->short_title))
                                <option @if(old('services') == $service->id) selected @endif id="service-{{ $service->id }}" value="{{ $service->id }}">{{ $service->short_title }}</option>
                            @else
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" rows="3" name="message" placeholder="{{ __('form.your_massage') }}" required>{{ old('message') }}</textarea>
                </div>

                {{-- <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="privacyCheck">
                    <label class="form-check-label" for="privacyCheck">
                        By checking this, you agree to our privacy policy.
                    </label>
                </div> --}}
                <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                @if ($errors->has('captcha'))
                    <p class="text-danger">{{ $errors->first('captcha') }}</p>
                @endif
                <button type="submit" class="btn btn-primary btn-block">Send message</button>
            </form>
        </div>

        <!-- Right: Image & Info -->
        <div class="image-section">
            <div class="info-overlay row g-5">
                <div class="contact-box col-6">
                    <i class="fas fa-envelope"></i>
                    <h6>Email</h6>
                    <p class="text-white">info@metaballs.studio</p>
                </div>
                <div class="contact-box col-6">
                    <i class="fas fa-phone"></i>
                    <h6>Phone</h6>
                    <p class="text-white">+1 (800) 123-4567</p>
                </div>
                <div class="contact-box col-6">
                    <i class="fas fa-map-marker-alt"></i>
                    <h6>US Office</h6>
                    <p class="text-white">123 Metaballs Lane, Innovation City, TX 78901</p>
                </div>
                <div class="contact-box col-6">
                    <i class="fas fa-map-marker-alt"></i>
                    <h6>BD Office</h6>
                    <p class="text-white">7/53 Metaballs Lane, Modern City, Jhenaidah</p>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA -->
    @foreach ($cta as $item)
        <section class="cta-section my-5 mx-3">
            <div class="container text-center">
                <h2 class="font-weight-bold">Ready to Start Your Project?</h2>
                <p class="lead mb-4 text-white">{{ $item['bottom_text'] }}</p>
                <a href="#contact" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </section>
    @endforeach


    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        var input = document.querySelector("#phone");
        window.intlTelInput(input, {
            initialCountry: "us",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
    </script>


    <!-- Scripts: jQuery, Popper, Bootstrap 4 -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    {{--
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}

    <!-- Optional: small UX scripts (smooth scroll for anchor links) -->
    <script>
        // Smooth scroll for anchor links
        $(document).on('click', 'a[href^="#"]', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 20
                }, 600);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.stat-number');

            counters.forEach(counter => {
                const targetText = counter.getAttribute('data-target');
                const numericValue = parseFloat(targetText.replace(/[^\d.]/g, '')) || 0;
                const suffix = targetText.replace(/[0-9.]/g, '');
                let current = 0;
                const duration = 2000;
                const increment = numericValue / (duration / 16);

                function updateCounter() {
                    current += increment;
                    if (current < numericValue) {
                        counter.textContent = Math.floor(current) + suffix;
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = targetText;
                    }
                }

                updateCounter();
            });
        });
    </script>


@endsection
