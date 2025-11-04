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
            background: #F3F4F4;
            font-family: 'Segoe UI', sans-serif;
        }

        .search-bar {
            margin: 20px 0;
        }

        .search-bar input {
            border-radius: 25px;
            padding-left: 20px;
        }

        .featured-blog,
        .blog-card {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0px 1px rgba(0, 0, 0, 0.1);
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
            background: #052C58;
            color: #fff;
            border: none;
        }

        #loadMoreBtn:hover {
            background: #052C58;
        }

        #loadingSpinner {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .fade-in-delay {
            animation: fadeInDelay 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDelay {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cardBlogExtra h5 {
            /* font-size: 24px; */
            color: #222222;
        }

        .cardBlogExtra p {
            font-size: 17px;
            color: #000000;
        }








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



        /* awords-section */
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

    <!-- Page Title -->
    {{-- <section class="page-title">
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
    </section> --}}

    <section class="about-hero-section" data-aos="fade">
        <div class="container">
            <h1>{{ __('navbar.blog') }}</h1>
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
                        <li>{{ __('navbar.blog') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
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
        <div class="container featured-blog ">
            <div class="row align-items-center">
                <div class="col-md-5 pl-4 mt-4 featured-blog-extra">
                    <div class="author-info mb-2">
                        <img src="{{ asset('/uploads/setting/' . $setting->logo_path) }}" alt="Author"
                  class="rounded-circle m-0 mr-3" style="width: 30px; height: 30px; object-fit: cover; object-position: center;">
                        <div><strong>MSN Softtech</strong></div>
                    </div>
                    <h3><strong>{{ $articles[0]->title }}</strong></h3>
                    <p>{{ Str::limit(strip_tags($articles[0]->description), 450) }}</p>
                    <a href="{{ route('blog.single', $articles[0]->slug) }}" class="read-more">READ MORE</a>
                </div>
                <div class="col-md-7">
                    <img src="{{ asset('uploads/article/' . $articles[0]->image_path) }}" alt="{{ $articles[0]->title }}"
                        class="w-100 m-0 p-0 rounded-right ">
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
        <div class="d-flex justify-content-center mb-4">
            <button id="loadMoreBtn">CLICK TO LOAD MORE</button>
        </div>
    </div>

    {{-- Awords section --}}
    <section class="awords-section mt-5">
        <header class="awords-section-header">Recent Awards & Certifications</header>

        <div class="awards-container">
            {{-- <div class="award">
                <img src="https://www.capitalnumbers.com/images/new-skill-section/ft-award-2024.svg" alt="Award 1">
            </div> --}}
            <div class="award">
                <img src="https://www.capitalnumbers.com/images/common/clutch-global-2024.png" alt="Award 2">
            </div>
            {{-- <div class="award">
                <img src="https://www.capitalnumbers.com/images/common/btb-2024.svg" alt="Award 3">
            </div> --}}
            <div class="award">
                <img src="https://www.capitalnumbers.com/images/common/clutch-champion-2024.png" alt="Award 4">
            </div>
            {{-- <div class="award">
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
            </div> --}}
        </div>

    </section>
    <!-- Blog Loading Script -->
    <script>
        const blogData = @json($articles->skip(1)->values()); // Skips the first blog (already featured)
        let loadedCount = 0;
        const perLoad = 3;

        // Load blog cards function
        function loadBlogCards() {
            const container = document.getElementById('blogCardsContainer');
            const spinner = document.getElementById('loadingSpinner');
            const loadMoreButton = document.getElementById('loadMoreBtn');

            // Show loading spinner while fetching data
            spinner.style.display = 'block';
            loadMoreButton.disabled = true;

            // Simulate delay (if any real-time API request)
            setTimeout(() => {
                blogData.slice(loadedCount, loadedCount + perLoad).forEach((blog, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';
                    col.style.marginBottom = '20px';
                    col.classList.add('fade-in-delay');
                    col.innerHTML = `
                        <div class="blog-card p-0">
                            <img src="/uploads/article/${blog.image_path}" class="ml-0 img-fluid" alt="${blog.title}">
                            <div class="p-3 cardBlogExtra">
                                <div class="author-info">
                                    <img class="ml-0" src="https://media.licdn.com/dms/image/v2/D5603AQH-I8hrGgl2sw/profile-displayphoto-shrink_400_400/B56ZSYezwrGsAg-/0/1737725001194?e=1751500800&v=beta&t=Tv_CyfSmY0Tdycx0F0HFJ50ysKX6Yb-h8AXp8YA4qEw" alt="author">
                                    <div><strong>MSN Softtech</strong></div>
                                </div>
                                <h5 class="mb-3"><strong>${truncateText(stripHtml(blog.title), 45)}</strong></h5>
                                <p>${truncateText(stripHtml(blog.description), 150)}</p>
                                <a href="/blog/${blog.slug}" class="read-more">READ MORE</a>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                loadedCount += perLoad;

                // Hide button if all blogs are loaded
                if (loadedCount >= blogData.length) {
                    loadMoreButton.style.display = 'none';
                }

                // Hide the loading spinner and enable the button again
                spinner.style.display = 'none';
                loadMoreButton.disabled = false;
            }, 300); // Simulate network delay
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

        // document.getElementById('loadMoreBtn').addEventListener('click', loadBlogCards);



        // Initial load
        loadBlogCards();
    </script>

@endsection