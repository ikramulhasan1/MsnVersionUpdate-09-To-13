@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('blog');
@endphp
@if(isset($article))

    @section('title', $article->meta_title)

    @section('top_meta_tags')
    @if(isset($article->meta_desc))
    <meta name="description" content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}">
    @else
    <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
    @endif

    @if(isset($article->keywords))
    <meta name="keywords" content="{!! strip_tags($article->keywords) !!}">
    @else
    <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
    @endif


    <script type="application/ld+json">
        {
          "@context": "http://schema.org",
          "@type": "Product",
          "name": "{{ $article->meta_title }}",
          "image": {
            "@type": "ImageObject",
            "url": "{{ asset('uploads/article/'.$article->image_path) }}",
            "width": "1200",
            "height": "630"
          },
          "description": "{{ Str::limit(strip_tags($article->description), 500, '...') }}",
          "url": "{{ route('blog.single', $article->slug) }}",
          "brand": {
            "@type": "Brand",
            "name": "MSN Softtech",
            "logo": "https://cdn-icons-png.flaticon.com/128/732/732200.png"
          },
          "offers": {
            "@type": "Offer",
            "price": "999",
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
            "ratingValue": "4.9",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": "417"
          },
          "review": {
            "@type": "Review",
            "author": {
              "@type": "Person",
              "name": "Charles Wilson"
            },
            "datePublished": "{{ $article->created_at->format('Y-m-d') }}",
            "reviewRating": {
              "@type": "Rating",
              "ratingValue": "5",
              "bestRating": "5",
              "worstRating": "1"
            },
            "reviewBody": "MSN Softtech delivered an exceptional custom {{ $article->service_title }} solution that enhanced our online presence and improved performance."
          }
        }
    </script>
        
    @endsection


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
@endif

@section('social_meta_tags')
@if(isset($setting))
<meta property="og:type" content="website">
<meta property='og:site_name' content="{{ $setting->title }}" />
<meta property='og:title' content="{{ $article->title }}" />
<meta property='og:description' content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}" />
<meta property='og:url' content="{{ route('blog.single', $article->slug) }}" />
<meta property='og:image' content="{{ asset('uploads/article/'.$article->image_path) }}" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
<meta name="twitter:creator" content="@MSNSofttech" />
<meta name="twitter:url" content="{{ route('blog.single', $article->slug) }}" />
<meta name="twitter:title" content="{{ $article->title }}" />
<meta name="twitter:description" content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}" />
<meta name="twitter:image" content="{{ asset('uploads/article/'.$article->image_path) }}" />
@endif
@endsection

@section('content')
{{-- <style>
    /* fonts style */
    .poppins-thin {
  font-family: "Poppins", sans-serif;
  font-weight: 100;
  font-style: normal;
}

.poppins-regular {
  font-family: "Poppins", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.poppins-medium {
  font-family: "Poppins", sans-serif;
  font-weight: 500;
  font-style: normal;
}

.poppins-semibold {
  font-family: "Poppins", sans-serif;
  font-weight: 600;
  font-style: normal;
}

.poppins-bold {
  font-family: "Poppins", sans-serif;
  font-weight: 700;
  font-style: normal;
}

.poppins-regular-italic {
  font-family: "Poppins", sans-serif;
  font-weight: 400;
  font-style: italic;
}

.poppins-medium-italic {
  font-family: "Poppins", sans-serif;
  font-weight: 500;
  font-style: italic;
}

.poppins-semibold-italic {
  font-family: "Poppins", sans-serif;
  font-weight: 600;
  font-style: italic;
}

.poppins-bold-italic {
  font-family: "Poppins", sans-serif;
  font-weight: 700;
  font-style: italic;
}

</style>
<style>
    div{
        border: none !important;
    }
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
        margin-left: 30px !important;
        all: revert;
        font-size: 16px !important;
    }
    .description>ol>li>p {
        margin-bottom: 15px !important;
        margin-bottom: 10px !important;
    }
    .description>ol>li>ul>li {
        margin-left: 30px !important;
        list-style: initial;
        font-size: 16px !important;
    }
    .description>ol>li>ul>li>p {
        margin-bottom: 5px !important;
    }
    .description>h2>p>a>b{
        color: #1064AB!important;  /* Vibrant blue for visibility */
        text-decoration: none!important; 
        font-weight: 500!important; 
    }
    .description>h3>b>a{
        color: #1064AB!important;  /* Vibrant blue for visibility */
        text-decoration: none!important; 
    }
    .description>p>a {
        color: #1064AB!important;  /* Vibrant blue for visibility */
        text-decoration: none!important; 
        font-weight: 500!important; 
    }
    .description>p>b {
        font-weight: 600!important; 
    }
    .description>ol>li>b>a {
        color: #1064AB!important;  /* Vibrant blue for visibility */
        text-decoration: none!important; 
        font-weight: 500!important; 
    }
    .description>ol>li>a>b {
        color: #1064AB!important;  /* Vibrant blue for visibility */
        text-decoration: none!important; 
        font-weight: 500!important; 
    }

    p{
        font-size: 18px !important;
    }
    .description>h1,.description>h2,.description>h3,.description>h4{
        margin-top: 30px !important; 
        margin-bottom: 15px !important; 
    }
    .description>h1>b,.description>h2>b,.description>h3>b,.description>h4>b{
        margin-top: 30px !important; 
        margin-bottom: 15px !important; 
    }
 
    .description>h1, .description>h1>b {
        font-size: 2rem!important; 
        font-weight: 700!important; 
    }

    .description>h2, .description>h2>b {
        font-size: 1.75rem!important;    /* ~28px */
        font-weight: 600!important; 
    }

    .description>h3, .description>h3>b {
        font-size: 1.5rem!important;     /* ~24px */
        font-weight: 600!important; 
    }

    .description>h4, .description>h4>b {
        font-size: 1.25rem!important;    
        font-weight: 500!important; 
    }
 --}} <style>
        .circle-container {
            width: 180px;
            height: 54px;
            background: linear-gradient(135deg, #4CAF50, #2E8B57); /* Green Gradient */
            border-radius: 12px; /* Makes it round */
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 15px; /* Space between buttons */
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.3);
            bottom: 20px;
            right: 20px;
            padding: 15px;
        }

        /* Icon Buttons */
        .circle-button {
            background-color: white;
            border: none;
            width: 40px; 
            height: 40px;
            border-radius: 50%; 
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

        /* Hover Effect */
        .circle-button:hover {
            background-color: #2E8B57;
            transform: scale(1.1); /* Slight zoom */
        }

        /* Icon Images */
        .circle-button img {
            width: 25px; /* Adjust icon size */
            height: 25px;
        }
        </style>
{{--
    .service-title {
        font-weight: 600 !important;
        text-align: left;
        color: black;
        width: 100%; /* Ensures the title spans the width of the container */
    }
    

</style> --}}
<!--Page Title-->
{{-- <section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ $article->title }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.blog-detail') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section> --}}
<!--End Page Title-->

<!-- Sidebar Page Container -->

{{-- 
<div class="sidebar-page-container">
    <div class="container">
        <div class="row clearfix">
            <!--Content Side-->
            <div class="content-side col-lg-8 col-md-12 col-sm-12" >
                <div class="blog-detail">
                    <!-- News Block -->
                    <div class="news-block">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><img src="{{ asset('uploads/article/'.$article->image_path) }}" alt="{{ $article->title }}"></figure>
                                <div class="overlay-box"><a href="{{ route('blog.single', $article->slug) }}"><i class="icon fas fa-image"></i></a></div>
                            </div>
                    
                            
                            <div style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px;" class="caption-box">
                                <div class="inner">
                                    <h3 style="margin-bottom: 10px"><a href="{{ route('blog.single', $article->slug) }}">{{ $article->title }}</a></h3>
                                   
                                    <div class="description article-description" id="article-description" style="color: black !important " >                                        
                                                {!! $article->description !!}
                                    </div>

                                    @php 
                                        $page_quote = \App\Models\PageSetup::page('get-quote');
                                        $page_contact = \App\Models\PageSetup::page('contact-us');
                                    @endphp

                                    @if(isset($page_quote))
                                    
                                        <div class="service-title mb-3">
                                            <h5 style="font-weight: 600" >Are you interested in <b style="color: #00893b">{{ $article->short_title }}</b> service? <span style="color: red"><a style="color: red" href="{{ route('get-quote') }}" target="_blank" rel="noopener noreferrer">Contact us</a></span></h5> <!-- Title text -->
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="circle-container">
                                                <!-- Service Sell Title -->
                                            
                                                <!-- Get A Quote Button -->
                                                <a href="{{ route('get-quote') }}" target="_blank" class="circle-button">
                                                    <img src="https://cdn-icons-png.flaticon.com/128/18572/18572275.png" alt="Get A Quote">
                                                </a>

                                                <!-- WhatsApp Button -->
                                                <a rel="noopener noreferrer" href="https://wa.link/vkb4au" target="_blank" class="circle-button">
                                                    <img src="https://cdn-icons-png.flaticon.com/128/733/733585.png" alt="WhatsApp">
                                                </a>
                                                
                                                <!-- Email Button -->
                                                <a href="mailto:{{$setting->email_one}}?subject=Inquiry&body={{ $article->title}}" class="circle-button">
                                                    <img src="https://cdn-icons-png.flaticon.com/128/732/732200.png" alt="Email">
                                                </a>
                                            </div>
                                            <div style="z-index: 1000 !important;">
                                                @include('web.layouts.googlemeet')
                                            </div>
                                        </div>
                                    @elseif(isset($page_contact))
                                        <a href="{{ route('contact') }}" class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="tags clearfix">
                        <span class="title">{{ __('common.category') }}:</span>
                        <ul>
                            <li><a href="{{ route('blog.category', $article->category->slug) }}">{{ $article->category->title }}</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            <!--Sidebar Side-->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar default-sidebar">

                    <!--search box-->
                    <div class="sidebar-widget search-box">
                        <form method="get" action="{{ route('blog.search') }}">
                            <div class="form-group">
                                <input type="search" name="search" value="" placeholder="{{ __('search.search_field') }}" value="@if(isset($search)){{ $search }}@endif" required="">
                                <button type="submit"><span class="icon fa fa-search"></span></button>
                            </div>
                        </form>
                    </div>

                    @if(count($article_categories) > 0)
                    <!-- Categories -->
                    <div class="sidebar-widget categories">
                        <div class="sidebar-title">
                            <h3>{{ __('common.categories') }}</h3>
                        </div>
                        <ul class="cat-list">
                            @foreach($article_categories as $article_category)
                            <li class="@if($article->category->id == $article_category->id) active @endif"><a href="{{ route('blog.category', $article_category->slug) }}">{{ $article_category->title }} <span>({{ $article_category->articles->where('status', 1)->count() }})</span></a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(count($recents) > 0)
                    <!-- Latest News -->
                    <div class="sidebar-widget latest-news">
                        <div class="sidebar-title">
                            <h3>{{ __('common.recent_posts') }}</h3>
                        </div>
                        <div class="widget-content">
                            @foreach($recents as $key => $recent)
                            <article class="post">
                                <div class="post-thumb">
                                    <a href="{{ route('blog.single', $recent->slug) }}">
                                        <img src="{{ asset('uploads/article/'.$recent->image_path) }}" alt="{{ $recent->title }}">
                                    </a>
                                </div>
                               
                                
                                <h3><a href="{{ route('blog.single', $recent->slug) }}">{!! str_limit(strip_tags($recent->title), 50, ' ...') !!}</a></h3>
                                <div class="post-info">{{ date('F d Y', strtotime($recent->created_at)) }}</div>
                            </article>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</div>
 --}} 

 <style>
    body {
    background-color: #eef2f7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  h1, h2, h5 {
    color: #212529;
  }
  
  .toc li a,
  .sidebar-list li a {
    color: #0d6efd;
    text-decoration: none;
  }
  
  .toc li a:hover,
  .sidebar-list li a:hover {
    text-decoration: underline;
  }
  
  .content-area {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
  }
  
  .sidebar-area .sidebar-list li {
    margin-bottom: 10px;
  }
  
  .sidebar-area .sidebar-list li a {
    font-size: 0.95rem;
  }
  
  section ul, section ol {
    padding-left: 1.5rem;
  }
  
  section ul li, section ol li {
    margin-bottom: 8px;
  }
  
  .author-box img {
    object-fit: cover;
  }
  
  button.btn-success {
    background-color: #00b894;
    border: none;
  }
  
  button.btn-success:hover {
    background-color: #019875;
  }
  .content-area h1{
    font-size: 30px;
    font-weight: 900;
    /* margin-bottom: 20px; */
    color: #212529
  }
  .content-area p{
    font-size: 17px;
    /* font-weight: 700; */
    /* margin-bottom: 20px; */
    color: #000000
  }

  .bg-gradient {
  background: linear-gradient(180deg, #145B72 0%, #152D64 100%);
  color: white;
}
.bg-gradient h5{
color: white;
font-weight: 800;
}

.bg-white h5{
    color: #222222;
    font-weight: 800;
}

.sidebar-list-categories li a {
  color: white;
  text-decoration: none;
}
.sidebar-list-categories li a:hover {
  text-decoration: underline;
}

.color-text a {
  color: #222222;
  text-decoration: none;
}
.color-text a:hover {
  color: red;
}
.needHelpList li a{
    color: #222222;
    text-decoration: none;
}
.needHelpList li a:hover{
    color: red;
    text-decoration: none;
}


/* help section */
.help-section {
  background: #fff;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  position: relative;
  margin-bottom: 20px;
}

.section-title::after {
  content: "";
  display: block;
  width: 30px;
  height: 2px;
  background: #007bff;
  margin-top: 8px;
}

.help-item {
  border-color: #eee;
}

.help-icon {
  width: 60px;
  height: 60px;
  object-fit: contain;
}

.help-text {
  color: #212529;
  text-decoration: none;
  font-size: 15px;
}

.help-text:hover {
  color: red;
  text-decoration: none;
}

.dotted-border {
  border-bottom: 1px dotted #ccc;
}








.case-studies-box {
  background: linear-gradient(135deg, #4b006e, #7303c0, #ec38bc);
  background-size: 400% 400%;
  animation: gradientMove 10s ease infinite;
  border-radius: 16px;
  padding: 40px 20px;
  position: relative;
  overflow: hidden;
}

.background-shape {
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
  transform: rotate(45deg);
}

.case-studies-box::before {
  content: '';
  position: absolute;
  bottom: -40px;
  left: -40px;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  transform: rotate(-30deg);
}
.case-studies-box h5{
    font-size: 30px;
    color: white;
    font-weight: 800;
}
.case-studies-box p{
    font-size: 18px;
    color: white;
}
.case-studies-box a:hover{
    color: white;
    text-decoration: underline;
}

.view-all-btn {
  background-color: #ff4d00;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 5px;
  padding: 12px 30px;
  font-size: 14px;
  text-transform: uppercase;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
  z-index: 1;
  position: relative;
}

.view-all-btn:hover {
  background-color: #e64000;
}

@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}


/* social */
.social-icons-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.social-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border: 1px solid #d1d1d1;
  border-radius: 50%;
  color: #333;
  font-size: 20px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.social-icon i {
  font-weight: 400; /* thinner lines */
}

.social-icon:hover {
  background-color: #ff4d00;
  border-color: #ff4d00;
  color: #fff;
}




.popular-posts {
  background: #fff;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  position: relative;
  margin-bottom: 20px;
}

.section-title::after {
  content: "";
  display: block;
  width: 30px;
  height: 2px;
  background: #007bff;
  margin-top: 8px;
}

.post-item {
  border-color: #eee;
}

.post-thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

.post-title {
  font-weight: 600;
  color: #212529;
  text-decoration: none;
  font-size: 15px;
  line-height: 1.3;
}

.post-title:hover {
  color: #007bff;
}





.about-content ul, .about-content ol {
    list-style: none; /* remove default bullets */
    padding-left: 0; /* remove default padding */
}

.about-content ul li, .about-content ol li {
    position: relative;
    padding-left: 20px; /* space for custom bullet */
    margin-bottom: 0px; /* optional: add spacing between li */
    font-size: 16px; 
    color: #333333; /* text color */
}

.about-content ul li::before, .about-content ol li::before {
    content: '●'; /* your custom bullet */
    position: absolute;
    left: 0;
    top: 0px;
    font-size: 18px;
    color: #00c853; /* green bullet color */
}
.about-content b{
    color: #009830;
}
.about-content b{
color: #222222;
}
.about-content a b{
    color: #009830;
}
.about-content a b:hover{
    color: #009830;
    text-decoration: underline;
}

.about-content h2{
    font-size: 24px; 
    font-weight: 800;
    margin-top: 30px; 
    margin-bottom: 20px; 
    color: #333333; /* heading color */
}
.about-content h3{
    font-size: 21px; 
    font-weight: 800;
    margin-top: 30px; 
    margin-bottom: 20px;
    color: #333333; /* heading color */
}
.about-content p{
    font-size: 17px; 
    /* margin-top: 2px; 
    margin-bottom: 2px;  */
    color: #000000; /* heading color */
}
.about-content table, .about-content th, .about-content td {
    font-size: 17px; 
    padding: 3px;
    border: 1px solid #111111; /* border color */
    /* margin-top: 2px; 
    margin-bottom: 2px;  */
    color: #222222; /* heading color */
}
.tableofContents{
    border: 2px solid #d6d6d6; /* border color */
    background-color:#FAFAFA;
    color: #009830; /* heading color */
}
.tableofContents h5{
    color: #222222; 
    font-size: 21px;
    font-weight: 800;
}
.tableofContents li {
    list-style: disc;
}
.tableofContents li a{
    color: #009830;
    font-size: 15px;
    text-decoration: none;
}

.marker{
    background-color: #ffffff; /* Vibrant green for visibility */
    color: black; /* Text color */
    padding-right: 15px;
}
.service-package h3{
    /* background-color: #FFFF00; */
    margin-top: 30px;
    color: #000000;
}
.service-package p b{
    color: #ffffff;
}



/* share */
.share-icons-section {
  background: #fff;
  /* border-radius: 8px; */
  padding: 20px;
  padding-left: 0px;
  text-align: left;
  /* box-shadow: 0 2px 8px rgba(0,0,0,0.05); */
}

.share-title {
  font-weight: bold;
  color: #555;
  margin-bottom: 15px;
  font-size: 18px;
  text-align: left;
}

.share-icons {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.share-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background-color: #eee;
  transition: transform 0.3s ease;
}

.share-icon img {
  width: 22px;
  height: 22px;
}

.share-icon:hover {
  transform: scale(1.1);
}

.sticky-sidebar {
  position: sticky;
  top: 80px; /* or whatever top spacing you want */
  z-index: 100;
  height: fit-content;
}


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
<!-- End Sidebar Container -->



<div class="full-content container my-5">
    <div class="row">
      <!-- Main Content -->
      <div class="col-lg-8">
        <div class="content-area mb-4">
          
          <!-- Blog Banner -->
          <img src="{{ asset('uploads/article/'.$article->image_path) }}" alt="{{ $article->title }}" class="w-100 rounded-top mb-4" >
  
         <div class="p-4">
             <!-- Title -->
          <h1 class="mb-3 fw-bold">{{ $article->title }}</h1>
  
          <!-- Table of Contents -->
          <div class="p-4 tableofContents rounded shadow-sm mb-4">
            <h5 class="fw-bold mb-3">Table of Contents</h5>
            <ul class=" toc">
              <li><a href="#section1">AI as a Career Catalyst for Early Entrants</a></li>
              <li><a href="#section2">AI-First Mindset: Thinking Beyond Technology</a></li>
              <li><a href="#section3">AI Technology Driving Business Transformation</a></li>
              <li><a href="#section4">AI’s Role in Enhancing Business Strategy</a></li>
              <li><a href="#section5">Final Thoughts</a></li>
            </ul>
          </div>
          <section id="section1" class="mb-5">
            
            <div class="about-content">{!! $article->description !!}</div>
          </section>
       
  
          <!-- Author Box -->
          <div class="d-flex align-items-center p-3 bg-white rounded border mb-5 shadow-sm">
            <img style="width: 80px; height: 80px;" src="https://media.licdn.com/dms/image/v2/D5603AQH-I8hrGgl2sw/profile-displayphoto-shrink_400_400/B56ZSYezwrGsAg-/0/1737725001194?e=1751500800&v=beta&t=Tv_CyfSmY0Tdycx0F0HFJ50ysKX6Yb-h8AXp8YA4qEw" class="rounded-circle m-0 mr-3" alt="Author">
            <div>
              <h6 class="mb-0 fw-bold"><strong>Ikramul Hasan</strong></h6>
              <small style="font-size: 18px">Admin</small>
              
              <div style="z-index: 1000 !important;">
                
                <div>
                  <button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
                    <div class="logo-container">
                      <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
                      <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" />
                    </div>
                    <span style="font-weight: 600; font-size: 18px; color: white; margin-left: 12px;">Book a Meeting</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          @include('web.layouts.googlemeet')
          <hr>
          <div class="share-icons-section">
            <div class="share-title">Share</div>
            <div class="share-icons">
              <a href="#" class="share-icon" style="background-color: #000;">
                <img src="https://cdn-icons-png.flaticon.com/512/5968/5968776.png" alt="X">
              </a>
              <a href="#" class="share-icon" style="background-color: #0e76a8;">
                <img src="https://cdn-icons-png.flaticon.com/512/145/145807.png" alt="LinkedIn">
              </a>
              <a href="#" class="share-icon" style="background-color: #3b5998;">
                <img src="https://cdn-icons-png.flaticon.com/512/145/145802.png" alt="Facebook">
              </a>
              <a href="#" class="share-icon" style="background-color: #25D366;">
                <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
              </a>
              <a href="#" class="share-icon" style="background-color: #ea4335;">
                <img src="https://cdn-icons-png.flaticon.com/512/281/281769.png" alt="Gmail">
              </a>
              <a href="#" class="share-icon" style="background-color: #f57c00;">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Share">
              </a>
            </div>
          </div>
          
  
         </div>
        </div>
      </div>
  
      <!-- Sidebar -->
      
      <div class="col-lg-4">
        <!-- Search Box -->
            <div class="bg-white rounded p-4 shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Search</h5>
                <form class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Search...">
                <button class="btn btn-primary" type="submit">Go</button>
                </form>
            </div>
          <div class="sidebar-area sticky-sidebar">
      
          
          

          <!-- Help Box -->
            @if(count($services) > 0)
            <div class="help-section card mb-4 p-4">
                <h5 class="section-title">I Need Help With…</h5>
                @foreach($services as $service)
                <div class="help-item d-flex align-items-center mb-0 pb-0 dotted-border">
                  <img src="{{ asset('uploads/service/'.$service->image_path) }}" class="help-icon m-0 mr-3 rounded" alt="{{ $service->short_title }}">
                  <a class="help-text" href="{{ route('service.single', $service->slug) }}">{{ $service->short_title }}</a>
                </div>
                @endforeach
            </div>
            @endif

          <!-- Categories -->
          @if (count($article_categories) > 0)
          <div class="bg-gradient rounded p-4 shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Categories</h5>
                <ul class="list-unstyled sidebar-list-categories">
                @foreach($article_categories as $article_category)
                    <div class="d-flex align-items-center mb-2">
                        <img style="width: 20px; height: 20px; margin: 0px; margin-right: 8px; " src="{{ asset('uploads/industry/checkmark.png') }}" alt="" srcset="">
                        <li class="@if($article->category->id == $article_category->id) active @endif"><a href="{{ route('blog.category', $article_category->slug) }}">{{ $article_category->title }} <span>({{ $article_category->articles->where('status', 1)->count() }})</span></a></li>
                    </div>
                @endforeach
                </ul>
          </div>
          @endif
      

          @if(count($recents) > 0)
          <div class="popular-posts card p-4 mb-4">
            <h5 class="section-title">{{ __('common.recent_posts') }}</h5>
            @foreach($recents as $key => $recent)
            <div class="post-item d-flex align-items-center mb-3 pb-3 border-bottom">
                <img src="{{ asset('uploads/article/'.$recent->image_path) }}" class="post-thumb mr-3 rounded" alt="{{ $recent->title }}">
                <div class="ms-3">
                    <a class="post-title" style="color:#000000" href="{{ route('blog.single', $recent->slug) }}">{!! str_limit(strip_tags($recent->title), 50, ' ...') !!}</a>
                </div>
            </div>
            @endforeach 
          </div>
          @endif
          <!-- Case Studies (NEW Section) -->
         <!-- Case Studies Section -->
            <div class="case-studies-box rounded p-4 mb-4 text-white position-relative overflow-hidden">
                <div class="background-shape"></div>
                <div class="text-left">
                    <p class="mb-1">Explore Our</p>
                    <h5 class="fw-bold mb-2">Case Studies</h5>
                    <p class="mb-4">For Inspiring Success Stories</p>
                </div>
                <div class="">
                    <a href="#" class="btn view-all-btn w-100" style="display: flex; justify-content: space-evenly">VIEW ALL <i class=" text-white fa-solid fa-arrow-right-long"></i></a>
                </div>
            </div>
            
           
            <!-- Subscribe Form -->
            <div class="bg-white rounded p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-center mb-3">Subscribe to Our Newsletter</h5>
                <form>
                <input type="text" class="form-control mb-3" placeholder="Enter your first name">
                <input type="email" class="form-control mb-3" placeholder="Enter your email">
                <button type="submit" class="btn btn-success w-100">Subscribe</button>
                </form>
            </div>
            <!-- Ad Banner -->
            <div class="text-center mb-4">
                <img src="https://www.capitalnumbers.com/blog/wp-content/uploads/2024/08/NodeJS-Performance-Optimization-download-ebook.jpg.webp" class="img-fluid rounded shadow-sm" alt="Advertisement">
            </div>
        
            <!-- Social Media Section (perfect match) -->
            <div class="social-icons-wrapper text-center">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-x-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-pinterest-p"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-behance"></i></a>
            </div>
          </div>
      </div>
    
    </div>

  </div>
  {{-- Awords section --}}
  <section class="awords-section">
    <header class="awords-section-header">Recent Awards & Certifications</header>
  
    <div class="awards-container">
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/new-skill-section/ft-award-2024.svg" alt="Award 1">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/clutch-global-2024.png" alt="Award 2">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/btb-2024.svg" alt="Award 3">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/clutch-champion-2024.png" alt="Award 4">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com//images/common/soc2-logo.svg" alt="Award 5">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/eba-logo-new.svg" alt="Award 6">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/times-business-award-white.svg" alt="Award 7">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/new-award-9.svg" alt="Award 8">
      </div>
      <div class="award">
        <img src="https://www.capitalnumbers.com/images/common/new-award-11.svg" alt="Award 9">
      </div>
    </div>
  
  </section>
<script>
document.addEventListener("DOMContentLoaded", function() {
        const descriptionElement = document.querySelectorAll(".article-description");
        if (descriptionElement) {
            descriptionElement.innerHTML = descriptionElement.innerHTML.replace(/serviceshow/g, '<span style="display:none;">serviceshow</span>');
        }
    });
// 
document.addEventListener("DOMContentLoaded", function () { 
    const descriptionElements = document.querySelectorAll(".article-description");
    descriptionElements.forEach(function(descriptionElement) {
        // Replace consecutive <br> tags with a single <br>
        descriptionElement.innerHTML = descriptionElement.innerHTML.replace(/(<br\s*\/?>\s*){3,}/gi, '<br><br>');
    });
});




// 
 document.querySelectorAll('.news-block').forEach(element => {
        element.setAttribute('style', 'border: none !important;');
    });
</script>
@endsection