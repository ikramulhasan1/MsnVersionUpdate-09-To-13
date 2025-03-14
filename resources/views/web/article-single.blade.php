@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('blog');
@endphp
@if(isset($header))

    @section('title', $article->title)

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


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

@endif

@section('social_meta_tags')
@if(isset($setting))
<meta property="og:type" content="website">
<meta property='og:site_name' content="{{ $setting->title }}" />
<meta property='og:title' content="{{ $article->title }}" />
<meta property='og:description' content="{!! str_limit(strip_tags($article->description), 160, ' ...') !!}" />
<meta property='og:url' content="{{ route('blog.single', $article->slug) }}" />
<meta property='og:image' content="{{ asset('uploads/article/'.$article->image_path) }}" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{!! '@'.str_replace(' ', '', $setting->title) !!}" />
<meta name="twitter:creator" content="@HiTechParks" />
<meta name="twitter:url" content="{{ route('blog.single', $article->slug) }}" />
<meta name="twitter:title" content="{{ $article->title }}" />
<meta name="twitter:description" content="{!! str_limit(strip_tags($article->description), 160, ' ...') !!}" />
<meta name="twitter:image" content="{{ asset('uploads/article/'.$article->image_path) }}" />
@endif
@endsection

@section('content')
<style>
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
        /* list-style: decimal; */
        margin-left: 30px !important;
        all: revert;
        font-size: 16px !important;
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


    p{
        font-size: 20px !important;
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
    font-size: 2rem!important;       /* ~32px */
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
    font-size: 1.25rem!important;    /* ~20px */
    font-weight: 500!important; 
}

</style>
<!--Page Title-->
<section class="page-title">
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
</section>
<!--End Page Title-->

<!-- Sidebar Page Container -->
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
                                    <h3><a href="{{ route('blog.single', $article->slug) }}">{{ $article->title }}</a></h3>
                                    <ul class="post-meta">
                                        <li><i class="far fa-calendar-check"></i>{{ date('d M, Y', strtotime($article->created_at)) }}</li>
                                    </ul>
                                    <div class="description" style="color: black !important " >
                                        {!! $article->description !!}
                                    </div>
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
                                <div class="post-thumb"><a href="{{ route('blog.single', $recent->slug) }}"><img src="{{ asset('uploads/article/'.$recent->image_path) }}" alt="{{ $recent->title }}"></a></div>
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
<!-- End Sidebar Container -->
<script>
 document.querySelectorAll('.news-block').forEach(element => {
        element.setAttribute('style', 'border: none !important;');
    });
</script>
@endsection