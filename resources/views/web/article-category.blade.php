@extends('web.layouts.master')

@php
use Illuminate\Support\Str;
$header = \App\Models\PageSetup::page('blog');
@endphp

@if(isset($header))
    @section('title', $header->meta_title)

    @section('top_meta_tags')
        @if(isset($header->meta_description))
            <meta name="description" content="{{ Str::limit(strip_tags($header->meta_description), 160, '...') }}">
        @else
            <meta name="description" content="{{ Str::limit(strip_tags($setting->description), 160, '...') }}">
        @endif

        @if(isset($header->meta_keywords))
            <meta name="keywords" content="{{ strip_tags($header->meta_keywords) }}">
        @else
            <meta name="keywords" content="{{ strip_tags($setting->keywords) }}">
        @endif
    @endsection
@endif

@section('content')

<style>
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
    .featured-blog, .blog-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        margin-bottom: 40px;
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

<!-- Page Title -->
<section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ __('navbar.blog') }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    <li>{{ __('navbar.blog') }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Search bar -->
<div class="container search-bar">
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <input type="text" class="form-control w-25" placeholder="Search" disabled>
        </div>
    </div>
</div>

<!-- Featured Blog -->
@if($articles->count() > 0)
<div class="container featured-blog p-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="author-info mb-2">
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
        <!-- Cards will be dynamically loaded here -->
    </div>

    <!-- Load More Button -->
    <div class="d-flex justify-content-center">
        <button id="loadMoreBtn">CLICK TO LOAD MORE</button>
    </div>
</div>

<!-- Sidebar Widgets (Optional) -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <!-- Main Blog Content -->
        </div>

        <div class="col-md-4">
            <aside class="sidebar default-sidebar">

                <!-- Search Box -->
                <div class="sidebar-widget search-box mb-4">
                    <form method="get" action="{{ route('blog.search') }}">
                        <div class="input-group">
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('search.search_field') }}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Categories -->
                @if($article_categories->count() > 0)
                <div class="sidebar-widget categories mb-4">
                    <h3>{{ __('common.categories') }}</h3>
                    <ul class="list-unstyled">
                        @foreach($article_categories as $category)
                            <li class="@if(isset($current_category) && $current_category->id == $category->id) active @endif">
                                <a href="{{ route('blog.category', $category->slug) }}">{{ $category->title }} ({{ $category->articles->where('status',1)->count() }})</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Recent Posts -->
                @if($recents->count() > 0)
                <div class="sidebar-widget latest-news mb-4">
                    <h3>{{ __('common.recent_posts') }}</h3>
                    @foreach($recents as $recent)
                        <div class="d-flex mb-3">
                            <img src="{{ asset('uploads/article/'.$recent->image_path) }}" alt="{{ $recent->title }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                            <div>
                                <h6><a href="{{ route('blog.single', $recent->slug) }}">{{ Str::limit($recent->title, 50) }}</a></h6>
                                <small class="text-muted">{{ date('F d, Y', strtotime($recent->created_at)) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

            </aside>
        </div>
    </div>
</div>

<!-- Scripts -->
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
            col.style.marginBottom = '20px';
            col.innerHTML = `
                <div class="blog-card p-3">
                    <img src="/uploads/article/${blog.image_path}" class="img-fluid" alt="blog image">
                    <div class="p-2">
                        <div class="author-info">
                            <img class="ml-0" src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
                            <div><strong>Tanim Rahman</strong></div>
                        </div>
                        <h5><a href="/blog/${blog.slug}" class="text-dark">${blog.title}</a></h5>
                        <p>${blog.description.substring(0, 120)}...</p>
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

    // Load initial blogs
    window.onload = loadBlogCards;
</script>

@endsection
