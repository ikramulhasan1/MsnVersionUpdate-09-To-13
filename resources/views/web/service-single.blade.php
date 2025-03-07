@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('services');
@endphp
@if(isset($header))

@section('title', content: $service->title)

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

@foreach ($service->subservices as $item)
    @section('title', content: $item->title)
    <meta name="description" content="{!! str_limit(strip_tags($item->meta_description), 160, ' ...') !!}">
@endforeach



@section('social_meta_tags')
@if(isset($setting))
<meta property="og:type" content="website">
<meta property='og:site_name' content="{{ $setting->title }}" />
<meta property='og:title' content="{{ $service->title }}" />
<meta property='og:description' content="{!! str_limit(strip_tags($service->description), 160, ' ...') !!}" />
<meta property='og:url' content="{{ route('service.single', $service->slug) }}" />
<meta property='og:image' content="{{ asset('uploads/service/'.$service->image_path) }}" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
<meta name="twitter:creator" content="@HiTechParks" />
<meta name="twitter:url" content="{{ route('service.single', $service->slug) }}" />
<meta name="twitter:title" content="{{ $service->title }}" />
<meta name="twitter:description" content="{!! str_limit(strip_tags($service->description), 160, ' ...') !!}" />
<meta name="twitter:image" content="{{ asset('uploads/service/'.$service->image_path) }}" />
@endif
@endsection

@section('content')
<style>
*{
    color: black;
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
        /* list-style: decimal; */
        margin-left: 30px !important;
        all: revert;
        font-size: 16px !important;
    }


    .description>ul>li>ul>li {
        margin-left: 15px !important;
        list-style: initial;
        font-size: 16px !important;
    }

    .description>ol>li>ol>li {
        margin-left: 15px !important;
        all: revert;
        font-size: 16px !important;
    }

    .description>ol>li>ul>li {
        margin-left: 15px !important;
        list-style: initial;
        font-size: 16px !important;
    }

    .description>ul>li>ol>li {
        margin-left: 15px !important;
        all: revert;
        font-size: 16px !important;
    }

    .description>ul>li>ul {
        margin-left: 0px !important;
        margin-bottom: 15px !important;
        list-style: initial;
        font-size: 16px !important;
    }

    .description>ol>li>ol {
        margin-left: 0px !important;
        margin-bottom: 15px !important;

        all: revert;
        font-size: 16px !important;
    }

    .description>ol>li>ul {
        margin-left: 0px !important;
        margin-bottom: 15px !important;

        list-style: initial;
        font-size: 16px !important;
    }

    .description>ul>li>ol {
        margin-left: 0px !important;
        margin-bottom: 15px !important;
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

    .description>h3 {
        margin-top: 30px !important;
        margin-bottom: 10px !important;
    }





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
            /* position: fixed; */
            bottom: 20px;
            right: 20px;
            padding: 15px;
        }

        /* Icon Buttons */
        .circle-button {
            
            background-color: white;
            border: none;
            width: 40px; /* Icon size */
            height: 40px;
            border-radius: 50%; /* Makes buttons round */
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
        .hidden { display: none; }
</style>
<!--Page Title-->
<section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ $service->title }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.service-detail') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!--End Page Title-->

@if(isset($service))
<!--Sidebar Page Container-->
<div style="background-color: #f7fff9" class="sidebar-page-container">
    <div class="mx-5">
        <div class="row clearfix mb-5">
            <!--Sidebar Side-->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar services-sidebar">

                    <!--Service Category Widget-->
                    <div class="sidebar-widget sidebar-blog-category">
                        <ul class="blog-cat">
                            @foreach($service_lists as $service_list)
                            <li class="@if($service_list->id == $service->id) active @endif"><a href="{{ route('service.single', $service_list->slug) }}">{!! str_limit(strip_tags($service_list->title), 60, ' ...') !!}</a></li>
                            @endforeach
                        </ul>
                    </div>

                </aside>
            </div>

            <!--Content Side-->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">
                <div class="service-detail">
                    <div class="inner-box">
                        <div class="image-box">
                            <div class="single-item-">
                                <figure class="image"><img style="border-radius: 5px;" src="{{ asset('uploads/service/'.$service->image_path) }}" alt="{{ $service->title }}" /></figure>
                            </div>
                        </div>
                        {{-- <h2 class=" mb-3">{{ $service->title }}</h2> --}}

                        <div class="text description">
                        
                            {!! $service->description !!}
                        </div>
                    </div>
                </div>

                @php
                $page_quote = \App\Models\PageSetup::page('get-quote');
                $page_contact = \App\Models\PageSetup::page('contact-us');
                @endphp
                @if(isset($page_quote))
                <div class="circle-container">
                    <!-- Get A Quote Button -->
                    <a href="{{ route('get-quote') }}" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/18572/18572275.png" alt="Get A Quote">
                    </a>
            
                    <!-- WhatsApp Button -->
                    <a href="https://wa.link/vkb4au" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/733/733585.png" alt="WhatsApp">
                    </a>
            
                    <!-- Email Button -->
                    <a href="mailto:{{$setting->email_one}}?subject=Inquiry&body=Hello, I need your services." class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/732/732200.png" alt="Email">
                    </a>
                </div>
                @elseif(isset($page_contact))
                <a href="{{ route('contact') }}" class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
                @endif
            </div>
        </div>

        @if ($service->subservices)
        @foreach ($service->subservices as $key => $item)
        @if ($key % 2 == 1)
            
        <div class="row clearfix mb-5">
            <!--Sidebar Side-->
            <div class="sidebar-side col-lg-5 col-md-12 col-sm-12">
                <aside class="sidebar services-sidebar">

                    <!--Service Category Widget-->
                    <div class="image-box">
                        <div class="single-item-">
                            <figure class="image"><img style="border-radius: 10px;" src="{{ asset('uploads/service/'.$item->image_path) }}" alt="{{ $item->title }}" /></figure>
                        </div>
                    </div>

                </aside>
            </div>

            <!--Content Side-->
            <div class="content-side col-lg-7 col-md-12 col-sm-12">
                <div class="service-detail">
                    <div class="inner-box">
                        
                        <h2 class=" mb-3">{{ $item->title }}</h2>

                        <div class="text description">
                            <!-- {!! $item->description !!} -->
                            {!! $item->description !!}
                        </div>

                        <h2>Processed Content:</h2>
                        <p id="processedContent"></p>
                    </div>
                </div>

                @php
                $page_quote = \App\Models\PageSetup::page('get-quote');
                $page_contact = \App\Models\PageSetup::page('contact-us');
                @endphp
                @if(isset($page_quote))
                <div class="circle-container">
                    <!-- Get A Quote Button -->
                    <a href="{{ route('get-quote') }}" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/18572/18572275.png" alt="Get A Quote">
                    </a>
                    <!-- WhatsApp Button -->
                    <a href="https://wa.link/vkb4au" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/733/733585.png" alt="WhatsApp">
                    </a>
                    
                    <!-- Email Button -->
                    <a href="mailto:{{$setting->email_one}}?subject=Inquiry&body={{ $item->title}}" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/732/732200.png" alt="Email">
                    </a>
                </div>
                @elseif(isset($page_contact))
                <a href="{{ route('contact') }}" class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
                @endif
                
            </div>
        </div>
        @else
        <div class="row clearfix mb-5">
            <!--Content Side-->
            <div class="content-side col-lg-7 col-md-12 col-sm-12">
                <div class="service-detail">
                    <div class="inner-box">
                       
                        <h2 class=" mb-3">{{ $item->title }}</h2>

                        <div class="text description">
                            <!-- {!! $item->description !!} -->
                            {!! $item->description !!}
                        </div>
                    </div>
                </div>

                @php
                $page_quote = \App\Models\PageSetup::page('get-quote');
                $page_contact = \App\Models\PageSetup::page('contact-us');
                @endphp
                @if(isset($page_quote))
                <div class="circle-container">
                    <!-- Get A Quote Button -->
                    <a href="{{ route('get-quote') }}" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/18572/18572275.png" alt="Get A Quote">
                    </a>
                    <!-- WhatsApp Button -->
                    <a href="https://wa.link/vkb4au" target="_blank" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/733/733585.png" alt="WhatsApp">
                    </a>
                    
                    <!-- Email Button -->
                    <a href="mailto:{{$setting->email_one}}?subject=Inquiry&body={{ $item->title}}" class="circle-button">
                        <img src="https://cdn-icons-png.flaticon.com/128/732/732200.png" alt="Email">
                    </a>
                </div>
                @elseif(isset($page_contact))
                <a href="{{ route('contact') }}" class="theme-btn btn-style-four mt-3">{{ __('common.get_start') }}</a>
                @endif

            </div>

               <!--Sidebar Side-->
            <div class="sidebar-side col-lg-5 col-md-12 col-sm-12">
                <aside class="sidebar services-sidebar">

                    <!--Service Category Widget-->
                    <div class="image-box">
                        <div class="single-item-">
                            <figure class="image"><img style="border-radius: 10px;" src="{{ asset('uploads/service/'.$item->image_path) }}" alt="{{ $item->title }}" /></figure>
                        </div>
                    </div>

                </aside>
            </div>
        </div>
        @endif
        @endforeach
        @endif

    

    </div>
</div>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Grab the description content from the div
        let descriptionContent = document.querySelector('.text.description').innerHTML;

        // Find the index of "DDDD"
        let index = descriptionContent.indexOf("DDDD");

        if (index !== -1) {
            // Show content before "DDDD"
            document.getElementById("processedContent").textContent = descriptionContent.substring(0, index);

            // Hide content after "DDDD"
            let hiddenContent = descriptionContent.substring(index + 4).trim();
            document.getElementById("processedContent").innerHTML += "<span class='hidden'>" + hiddenContent + "</span>";
        } else {
            document.getElementById("processedContent").textContent = descriptionContent;
        }
    });
</script>
@endsection