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
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .featured-blog {
        margin-bottom: 40px;
    }
    .blog-card {
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



    .blog-card {
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
}

.blog-card.show {
  opacity: 1;
}

#blogCardsContainer {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.blog-card img {
  max-width: 100%;
  height: auto;
  margin-bottom: 15px;
}

.blog-card .p-2 {
  padding: 15px;
}

.read-more {
  text-decoration: none;
  color: #007bff;
  font-weight: bold;
}

.read-more:hover {
  text-decoration: underline;
}

#loadMoreBtn {
  cursor: pointer;
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
            <input type="text" class="form-control w-25" placeholder="Search" disabled>
        </div>
    </div>
</div>

<!-- Featured Blog -->
<div class="container">
    <!-- Featured Blog (first blog) -->
    <div class="featured-blog">
      <h2>{{ $articles->first()->title }}</h2>
      <img src="/uploads/article/{{ $articles->first()->image_path }}" class="img-fluid" alt="{{ $articles->first()->title }}">
      <p>{{ stripHtml($articles->first()->description) }}</p>
      <a href="/blog/{{ $articles->first()->slug }}" class="btn btn-primary">Read More</a>
    </div>
  
    <!-- Blog Cards (after featured blog) -->
    <div id="blogCardsContainer" class="row mt-5"></div>
  
    <!-- Load More Button -->
    <div class="text-center mt-4">
      <button id="loadMoreBtn" class="btn btn-outline-primary">Load More</button>
    </div>
  </div>
  

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Blog Loading Script -->
<script>
    const blogData = @json($articles->skip(1)->values()); // Skips the first blog (already featured)
  
    let loadedCount = 0;
    const perLoad = 3;
  
    // Function to load blog cards dynamically
    function loadBlogCards() {
      const container = document.getElementById('blogCardsContainer');
  
      blogData.slice(loadedCount, loadedCount + perLoad).forEach(blog => {
        const col = document.createElement('div');
        col.className = 'col-md-4 blog-card';
        col.style.marginBottom = '20px';
        col.innerHTML = `
          <div class="p-3">
            <img src="/uploads/article/${blog.image_path}" class="img-fluid" alt="${blog.title}">
            <div class="p-2">
              <div class="author-info">
                <img class="ml-0" src="https://getpaidstock.com/tmp/[GetPaidStock.com]-680e80c61e4ab.jpg" alt="author">
                <div><strong>Tanim Rahman</strong></div>
              </div>
              <h5><strong>${blog.title}</strong></h5>
              <p>${truncateText(stripHtml(blog.description), 150)}</p>
              <a href="/blog/${blog.slug}" class="read-more">READ MORE</a>
            </div>
          </div>
        `;
        
        container.appendChild(col);
  
        // Trigger fade-in animation for new blog cards
        setTimeout(() => {
          col.classList.add('show');
        }, 100);  // Small delay before fade-in
  
      });
  
      loadedCount += perLoad;
  
      // Hide button if all blogs are loaded
      if (loadedCount >= blogData.length) {
        document.getElementById('loadMoreBtn').style.display = 'none';
      }
    }
  
    // Helper function to remove HTML tags
    function stripHtml(html) {
      let div = document.createElement("div");
      div.innerHTML = html;
      return div.textContent || div.innerText || "";
    }
  
    // Helper function to truncate text
    function truncateText(text, maxLength) {
      if (text.length <= maxLength) {
        return text;
      }
      return text.substr(0, maxLength) + '...';
    }
  
    document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);
  
    // Initial load (first 3 blogs)
    loadBlogCards();
  </script>
  
  

@endsection
