@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('blog');
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
    /* table {
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

    .description>p>a {
        color: blue;
        font-weight: bold;
        text-decoration: underline;
    }

    .description>p {
        font-size: 18px !important;
    }

     */




     /* body {
      background: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }

    .search-bar {
      margin: 20px 0;
    }

    .search-bar input {
      border-radius: 25px;
      padding-left: 20px;
    }

    .featured-blog {
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }

    .blog-card {
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: 100%;
    }

    .blog-card img {
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .author-info {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .author-info img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }

    .read-more {
      color: #28a745;
      font-weight: bold;
      text-decoration: none;
    }

    .read-more:hover {
      text-decoration: underline;
    }

    #loadMoreBtn {
      margin-top: 40px;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: bold;
      background: #ff6600;
      color: #fff;
      border: none;
    }
    #loadMoreBtn:hover {
      background: #e65c00;
    } */

    body {
      background: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }

    .search-bar {
      margin: 20px 0;
    }

    .search-bar input {
      border-radius: 25px;
      padding-left: 20px;
    }

    .featured-blog {
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }

    .blog-card {
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: 100%;
    }

    .blog-card img {
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .author-info {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .author-info img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }

    .read-more {
      color: #28a745;
      font-weight: bold;
      text-decoration: none;
    }

    .read-more:hover {
      text-decoration: underline;
    }

    #loadMoreBtn {
      margin-top: 40px;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: bold;
      background: #ff6600;
      color: #fff;
      border: none;
    }
    #loadMoreBtn:hover {
      background: #e65c00;
    }
</style>
<!--Page Title-->
<section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ __('navbar.blog') }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.blog') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- Sidebar Page Container -->
{{-- <div class="sidebar-page-container">
    <div class="container">
        <div class="row clearfix">
            <!--Content Side-->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">
                <div class="blog-classic">
                    @foreach($articles as $article)
                    <!-- News Block -->
                    <div class="news-block fadeIn animated">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><img src="{{ asset('uploads/article/'.$article->image_path) }}" alt="{{ $article->title }}"></figure>
                                <div class="overlay-box"><a href="{{ route('blog.single', $article->slug) }}"><i class="icon fas fa-image"></i></a></div>
                            </div>
                            <div class="caption-box">
                                <h3><a href="{{ route('blog.single', $article->slug) }}">{{ $article->title }}</a></h3>
                                <ul class="post-meta">
                                    <li><i class="far fa-calendar-check"></i>{{ date('d M, Y', strtotime($article->created_at)) }}</li>
                                </ul>
                                <div class="text description">{!! str_limit(strip_tags($article->description), 150, ' ...') !!}</div>
                                <a href="{{ route('blog.single', $article->slug) }}" class="readmore-btn">{{ __('common.read_more') }}</a>
                            </div>

                        </div>
                    </div>
                    @endforeach

                    @if(count($articles) == 0)
                    <h3>{{ __('search.no_result') }}</h3>
                    @endif

                </div>


                {{ $articles->appends(Request::only('search'))->links() }}

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
                            <li class="@if(isset($current_category)) @if($current_category->id == $article_category->id) active @endif @endif"><a href="{{ route('blog.category', $article_category->slug) }}">{{ $article_category->title }} <span>({{ $article_category->articles->where('status', 1)->count() }})</span></a></li>
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
</div> --}}

@php
    use Illuminate\Support\Str;
@endphp

<!-- Search bar -->
<div class="container search-bar">
  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      <input type="text" class="form-control w-25" placeholder="Search" disabled>
    </div>
  </div>
</div>

<!-- Featured Blog -->
@if($articles->count()>0)
<div class="container featured-blog p-4">
  <div class="row align-items-center">
    <div class="col-md-6">
      <div class="author-info mb-2">
        {{-- <img src="{{ asset('uploads/author_images/'.$articles[0]->author_image) }}" alt="author"> --}}
        <img src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
        <div><strong>Tanim Rahman</strong></div>
      </div>
      <h3><strong>{{ $articles[0]->title }}</strong></h3>
      <p>{{ Str::limit(strip_tags($articles[0]->description), 450) }}</p>
      <a href="{{ route('blog.single', $articles[0]->slug) }}" class="read-more">READ MORE</a>
    </div>
    <div class="col-md-6">
      <img src="{{ asset('uploads/article/'.$articles[0]->image_path) }}" alt="{{ $articles[0]->title }}" class="img-fluid rounded">
    </div>
  </div>
</div>
@endif

<!-- Blog Cards -->
<div class="container">
  <div class="row g-4" id="blogCardsContainer">
    <!-- Cards will be dynamically loaded here by JavaScript -->
  </div>

  <!-- Load More Button -->
  <div class="d-flex justify-content-center">
    <button id="loadMoreBtn">CLICK TO LOAD MORE</button>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const blogData = @json($articles->skip(1)->values());

  let loadedCount = 0;
  const perLoad = 3;

  function loadBlogCards() {
    const container = document.getElementById('blogCardsContainer');

    blogData.slice(loadedCount, loadedCount + perLoad).forEach(blog => {
      const col = document.createElement('div');
      col.className = 'col-md-4';
      col.innerHTML = `
      
        <div class="blog-card p-3">
          <img src="/uploads/article/${blog.image_path}" class="img-fluid" alt="blog image">
          <div class="p-2">
            <div class="author-info">
              <img src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
              <small>Tanim Rahman</small>
            </div>
            <h5>${blog.title}</h5>
            <p>${blog.description.length > 300 ? blog.description.substr(0, 300) + '...' : blog.description}</p>
            <a href="/blog/${blog.slug}" class="read-more">READ MORE</a>
          </div>
        </div>
      `;
      container.appendChild(col);
    });

    loadedCount += perLoad;

    if (loadedCount >= blogData.length) {
      document.getElementById('loadMoreBtn').style.display = 'none';
    }
  }

  document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);

  // Initially load 3 blogs
  loadBlogCards();
</script>

<!-- End Sidebar Container -->

@endsection