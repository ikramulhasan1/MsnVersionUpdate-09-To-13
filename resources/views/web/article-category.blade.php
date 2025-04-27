@extends('web.layouts.master')

@php
$header = \App\Models\PageSetup::page('blog');
@endphp

@if(isset($header))
@section('title', $header->meta_title)
@section('top_meta_tags')
@if(isset($header->meta_description))
<meta name="description" content="{!! Str::limit(strip_tags($header->meta_description), 160, ' ...') !!}">
@else
<meta name="description" content="{!! Str::limit(strip_tags($setting->description), 160, ' ...') !!}">
@endif

@if(isset($header->meta_keywords))
<meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
@else
<meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
@endif
@endsection
@endif

@section('content')

@php
use Illuminate\Support\Str;
@endphp

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
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0px 1px rgba(0,0,0,0.1);
    }
    .featured-blog {
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
    #loadingSpinner {
        display: none;
        margin-top: 20px;
        text-align: center;
    }
    .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
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
                    <li>{{ __('navbar.blog') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar -->
<div class="container search-bar">
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Search...">
        </div>
    </div>
</div>

<!-- Featured Blog -->
@if($articles->count() > 0)
<div class="container featured-blog p-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="author-info mb-2">
                <img class="ml-0" src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
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

    <!-- Loading Spinner -->
    <div id="loadingSpinner">
        <img style="width: 15px; height: 15px;" src="https://i.imgur.com/llF5iyg.gif" alt="Loading...">
    </div>

    <!-- Load More Button -->
    <div class="d-flex justify-content-center">
        <button id="loadMoreBtn">CLICK TO LOAD MORE</button>
    </div>
</div>

<!-- Scripts -->
<script>
    const blogData = @json($articles->skip(1)->values());
    let loadedCount = 0;
    const perLoad = 6; // Load 6 at once (faster experience)

    function stripHtml(html) {
        let div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    }

    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    }

    function createCard(blog) {
        return `
            <div class="col-md-4 fade-in">
                <div class="blog-card p-0">
                    <img src="/uploads/article/${blog.image_path}" class="img-fluid" alt="${blog.title}">
                    <div class="p-3">
                        <div class="author-info">
                            <img src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
                            <div><strong>Tanim Rahman</strong></div>
                        </div>
                        <h5><strong>${truncateText(stripHtml(blog.title), 45)}</strong></h5>
                        <p>${truncateText(stripHtml(blog.description), 150)}</p>
                        <a href="/blog/${blog.slug}" class="read-more">READ MORE</a>
                    </div>
                </div>
            </div>
        `;
    }

    function loadBlogCards() {
        const container = document.getElementById('blogCardsContainer');
        const spinner = document.getElementById('loadingSpinner');
        const loadMoreButton = document.getElementById('loadMoreBtn');

        spinner.style.display = 'block';
        loadMoreButton.disabled = true;

        setTimeout(() => {
            let items = blogData.slice(loadedCount, loadedCount + perLoad);
            items.forEach(blog => {
                container.insertAdjacentHTML('beforeend', createCard(blog));
            });

            loadedCount += perLoad;

            if (loadedCount >= blogData.length) {
                loadMoreButton.style.display = 'none';
            }

            spinner.style.display = 'none';
            loadMoreButton.disabled = false;
        }, 300); // faster delay
    }

    function searchBlogCards(keyword) {
        const container = document.getElementById('blogCardsContainer');
        container.innerHTML = '';

        const filtered = blogData.filter(blog =>
            stripHtml(blog.title).toLowerCase().includes(keyword.toLowerCase()) ||
            stripHtml(blog.description).toLowerCase().includes(keyword.toLowerCase())
        );

        if (filtered.length > 0) {
            filtered.forEach(blog => {
                container.insertAdjacentHTML('beforeend', createCard(blog));
            });
            document.getElementById('loadMoreBtn').style.display = 'none';
        } else {
            container.innerHTML = '<div class="text-center">No blogs found.</div>';
            document.getElementById('loadMoreBtn').style.display = 'none';
        }
    }

    document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);

    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.trim();
        if (keyword.length > 0) {
            searchBlogCards(keyword);
        } else {
            document.getElementById('blogCardsContainer').innerHTML = '';
            loadedCount = 0;
            loadBlogCards();
            document.getElementById('loadMoreBtn').style.display = 'block';
        }
    });

    // Initial load
    loadBlogCards();
</script>

@endsection
