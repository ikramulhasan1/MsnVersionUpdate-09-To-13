@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('blog');
@endphp
@if (isset($article))

    @section('title', $article->meta_title)

    @section('top_meta_tags')
        @if (isset($article->meta_desc))
            <meta name="description" content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}">
        @else
            <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
        @endif

        @if (isset($article->keywords))
            <meta name="keywords" content="{!! strip_tags($article->keywords) !!}">
        @else
            <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
        @endif


        <script type="application/ld+json">
                  {
                    "@@context": "http://schema.org",
                    "@@type": "Product",
                    "name": "{{ $article->meta_title }}",
                    "image": {
                      "@@type": "ImageObject",
                      "url": "{{ asset('uploads/article/' . $article->image_path) }}",
                      "width": "1200",
                      "height": "630"
                    },
                    "description": "{{ Str::limit(strip_tags($article->description), 500, '...') }}",
                    "url": "{{ route('blog.single', $article->slug) }}",
                    "brand": {
                      "@@type": "Brand",
                      "name": "MSN Softtech",
                      "logo": "https://cdn-icons-png.flaticon.com/128/732/732200.png"
                    },
                    "offers": {
                      "@@type": "Offer",
                      "price": "999",
                      "priceCurrency": "USD",
                      "availability": "https://schema.org/InStock",
                      "priceValidUntil": "{{ now()->addMonths(6)->format('Y-m-d') }}",
                      "hasMerchantReturnPolicy": {
                        "@@type": "MerchantReturnPolicy",
                        "applicableCountry": "US",
                        "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
                        "returnPolicySeasonalOverride": "https://schema.org/MerchantReturnNotPermitted",
                        "returnShippingFeesAmount": {
                          "@@type": "MonetaryAmount",
                          "value": "0.00",
                          "currency": "USD"
                        },
                        "merchantReturnDays": "30",
                        "returnMethod": "https://schema.org/ReturnByMail",
                        "returnFees": "FreeReturn"
                      },
                      "shippingDetails": {
                        "@@type": "OfferShippingDetails",
                        "shippingRate": {
                          "@@type": "MonetaryAmount",
                          "value": "0.00",
                          "currency": "USD"
                        },
                        "deliveryTime": {
                          "@@type": "ShippingDeliveryTime",
                          "businessDays": {
                            "@@type": "OpeningHoursSpecification",
                            "dayOfWeek": ["https://schema.org/Monday", "https://schema.org/Tuesday", "https://schema.org/Wednesday", "https://schema.org/Thursday", "https://schema.org/Friday", "https://schema.org/Saturday",
                            "https://schema.org/Sunday"]
                          },
                          "handlingTime": {
                            "@@type": "QuantitativeValue",
                            "minValue": 1,
                            "maxValue": 2,
                            "unitCode": "DAY"
                          },
                          "transitTime": {
                            "@@type": "QuantitativeValue",
                            "minValue": 3,
                            "maxValue": 5,
                            "unitCode": "DAY"
                          }
                        },
                        "shippingDestination": {
                          "@@type": "DefinedRegion",
                          "addressCountry": "US"
                        }
                      }
                    },
                    "aggregateRating": {
                      "@@type": "AggregateRating",
                      "ratingValue": "4.9",
                      "bestRating": "5",
                      "worstRating": "1",
                      "ratingCount": "417"
                    },
                    "review": {
                      "@@type": "Review",
                      "author": {
                        "@@type": "Person",
                        "name": "Charles Wilson"
                      },
                      "datePublished": "{{ $article->created_at->format('Y-m-d') }}",
                      "reviewRating": {
                        "@@type": "Rating",
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
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
@endif

@section('social_meta_tags')
    @if (isset($setting))
        <meta property="og:type" content="website">
        <meta property='og:site_name' content="{{ $setting->title }}" />
        <meta property='og:title' content="{{ $article->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('blog.single', $article->slug) }}" />
        <meta property='og:image' content="{{ asset('uploads/article/' . $article->image_path) }}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@@MSNSofttech" />
        <meta name="twitter:url" content="{{ route('blog.single', $article->slug) }}" />
        <meta name="twitter:title" content="{{ $article->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($article->meta_desc), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('uploads/article/' . $article->image_path) }}" />
    @endif
@endsection

@section('content')

    <style>
        /* ============================================================
                                                                                                 MSN SoftTech — Blog Single (redesign)
                                                                                                 Tokens: dark navy hero, teal accent, orange CTA, mono utility
                                                                                                 ============================================================ */
        .bp-page {
            --bp-navy-900: #080C16;
            --bp-navy-800: #0C1424;
            --bp-navy-700: #122036;
            --bp-teal: #17C9A8;
            --bp-teal-light: #17C9A8;
            --bp-orange: #17C9A8;
            --bp-ink: #1B1F27;
            --bp-muted: #66707E;
            --bp-paper: #F4F6FA;
            --bp-line: #E6E9F0;
            --bp-font-display: 'Poppins', sans-serif;
            --bp-font-mono: 'IBM Plex Mono', monospace;
            background: var(--bp-paper);
            font-family: var(--bp-font-display);
            color: var(--bp-ink);
        }

        .bp-page * {
            box-sizing: border-box;
        }

        .bp-page a {
            text-decoration: none;
        }

        .bp-page ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .bp-page,
        .bp-page h1,
        .bp-page h2,
        .bp-page h3,
        .bp-page h4,
        .bp-page h5,
        .bp-page h6,
        .bp-page p,
        .bp-page span,
        .bp-page a,
        .bp-page li,
        .bp-page label,
        .bp-page input,
        .bp-page button,
        .bp-page td,
        .bp-page th,
        .bp-page strong,
        .bp-page b {
            font-family: var(--bp-font-display) !important;
        }

        .bp-eyebrow,
        .bp-meta,
        .bp-meta *,
        .bp-terminal-badge,
        .bp-terminal-badge *,
        .bp-toc-filename,
        .bp-toc-body li a,
        .bp-toc-body li a::before,
        .bp-tags-label,
        .bp-share-label,
        .bp-cat-count,
        .bp-recent-date,
        .bp-case-eyebrow,
        .bp-crumb,
        .bp-crumb * {
            /* font-family: var(--bp-font-mono) !important; */
        }

        .bp-progress {
            position: sticky;
            top: 0;
            z-index: 200;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.06);
        }

        .bp-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--bp-teal), var(--bp-orange));
            transition: width .1s linear;
        }

        .bp-hero {
            position: relative;
            background-color: #0d0405;
            background-image:
                radial-gradient(circle at 12% 15%, rgba(178, 24, 24, .75) 0%, rgba(120, 16, 16, .35) 30%, rgba(13, 4, 5, 0) 60%),
                linear-gradient(135deg, #2a0a0a 0%, #150607 45%, #0d0405 100%);
            padding: 56px 0 96px;
            overflow: hidden;
        }

        .bp-hero-glow {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255, 255, 255, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .04) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: linear-gradient(180deg, transparent, rgba(0, 0, 0, .9) 30%, rgba(0, 0, 0, .9) 80%, transparent);
            pointer-events: none;
        }

        .bp-crumb {
            position: relative;
            font-family: var(--bp-font-mono);
            font-size: 12px;
            letter-spacing: .03em;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 28px;
        }

        .bp-crumb a {
            color: rgba(255, 255, 255, 0.6);
        }

        .bp-crumb a:hover {
            color: #fff;
        }

        .bp-crumb span {
            margin: 0 8px;
            color: rgba(255, 255, 255, 0.25);
        }

        .bp-crumb-current {
            color: rgba(255, 255, 255, 0.85);
        }

        .bp-hero-grid {
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 40px;
        }

        .bp-hero-main {
            max-width: 720px;
        }

        .bp-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            /* font-family: var(--bp-font-mono); */
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: white;
            background: rgba(0, 137, 59, 0.12);
            border: 1px solid rgba(18, 196, 107, 0.35);
            padding: 7px 14px;
            border-radius: 100px;
            margin-bottom: 20px;
        }

        .bp-eyebrow-mark {
            color: white;
            font-weight: 700;
        }

        .bp-title {
            font-size: 44px;
            line-height: 1.18;
            font-weight: 700;
            color: #fff;
            margin: 0 0 20px;
            letter-spacing: -.01em;
        }

        .bp-meta {
            font-family: var(--bp-font-mono);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .bp-meta-dot {
            color: rgba(255, 255, 255, 0.3);
        }

        .bp-hero-side {
            flex: 0 0 280px;
        }

        .bp-terminal-badge {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 16px 18px;
            backdrop-filter: blur(6px);
            font-family: var(--bp-font-mono);
            font-size: 12.5px;
        }

        .bp-terminal-dots {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
        }

        .bp-terminal-dots span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .bp-terminal-dots span:nth-child(1) {
            background: #FF5F57;
        }

        .bp-terminal-dots span:nth-child(2) {
            background: #FEBC2E;
        }

        .bp-terminal-dots span:nth-child(3) {
            background: #28C840;
        }

        .bp-terminal-line {
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 8px;
        }

        .bp-prompt {
            color: var(--bp-teal-light);
            margin-right: 6px;
        }

        .bp-terminal-out {
            color: rgba(255, 255, 255, 0.5);
            padding-left: 14px;
            margin-bottom: 4px;
        }

        .bp-status-live {
            color: white;
        }

        .bp-content-wrap {
            margin-top: 0;
        }

        .bp-banner-card {
            margin-top: -64px;
            position: relative;
            z-index: 2;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 30px 60px -20px rgba(8, 12, 22, 0.35);
            border: 6px solid #fff;
            margin-bottom: 44px;
        }

        .bp-banner-card img {
            width: 100%;
            display: block;
            max-height: 460px;
            object-fit: cover;
        }

        .bp-row {
            margin-top: 0;
        }

        .bp-article-card {
            background: #fff;
            border: 1px solid var(--bp-line);
            border-radius: 14px;
            padding: 40px;
            margin-bottom: 32px;
        }

        @media (max-width:575px) {
            .bp-article-card {
                padding: 22px;
            }
        }

        .bp-toc {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #1c2b45;
            margin-bottom: 40px;
            box-shadow: 0 12px 30px -14px rgba(8, 12, 22, 0.25);
        }

        .bp-toc-head {
            background: var(--bp-navy-800);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
        }

        .bp-toc-dots {
            display: flex;
            gap: 6px;
        }

        .bp-toc-dots span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .bp-toc-dots span:nth-child(1) {
            background: #FF5F57;
        }

        .bp-toc-dots span:nth-child(2) {
            background: #FEBC2E;
        }

        .bp-toc-dots span:nth-child(3) {
            background: #28C840;
        }

        .bp-toc-filename {
            font-family: var(--bp-font-mono);
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.55);
        }

        .bp-toc-body {
            background: #0F1A2C;
            padding: 20px 24px;
        }

        .bp-toc-body ol {
            counter-reset: toc;
        }

        .bp-toc-body li {
            counter-increment: toc;
            margin-bottom: 10px;
        }

        .bp-toc-body li:last-child {
            margin-bottom: 0;
        }

        .bp-toc-body li a {
            font-family: var(--bp-font-mono);
            font-size: 14px;
            color: #B9C2D0;
            display: flex;
            gap: 10px;
        }

        .bp-toc-body li a::before {
            content: counter(toc, decimal-leading-zero);
            color: red;
            flex: 0 0 auto;
        }

        .bp-toc-body li a:hover {
            color: #fff;
        }

        .bp-description,
        .bp-description * {
            font-family: var(--bp-font-display) !important;
            box-sizing: border-box !important;
        }

        .bp-description {
            font-size: 17px !important;
            line-height: 1.8 !important;
            color: #2A2F3A !important;
        }

        .bp-description h1,
        .bp-description h2,
        .bp-description h3,
        .bp-description h4,
        .bp-description h5,
        .bp-description h6,
        .bp-description h1 *,
        .bp-description h2 *,
        .bp-description h3 *,
        .bp-description h4 *,
        .bp-description h5 *,
        .bp-description h6 * {
            font-weight: 700 !important;
            color: var(--bp-ink) !important;
            margin: 34px 0 16px !important;
            text-decoration: none !important;
            background: none !important;
            letter-spacing: normal !important;
        }

        .bp-description h1,
        .bp-description h1 * {
            font-size: 30px !important;
        }

        .bp-description h2,
        .bp-description h2 * {
            font-size: 25px !important;
        }

        .bp-description h3,
        .bp-description h3 * {
            font-size: 21px !important;
        }

        .bp-description h4,
        .bp-description h4 * {
            font-size: 18px !important;
        }

        .bp-description h5,
        .bp-description h5 * {
            font-size: 16px !important;
        }

        .bp-description p {
            margin: 0 0 18px !important;
            color: #2A2F3A !important;
            font-size: 17px !important;
            font-weight: 400 !important;
        }

        .bp-description a,
        .bp-description a * {
            color: red !important;
            font-weight: 500 !important;
            text-decoration: underline !important;
            text-underline-offset: 2px !important;
            background: none !important;
        }

        .bp-description a:hover,
        .bp-description a:hover * {
            color: var(--bp-navy-900) !important;
        }

        .bp-description b,
        .bp-description strong {
            color: var(--bp-ink) !important;
            font-weight: 600 !important;
            background: none !important;
        }

        .bp-description ul,
        .bp-description ol {
            list-style: none !important;
            list-style-image: none !important;
            padding-left: 0 !important;
            margin: 0 0 18px !important;
        }

        .bp-description ul li,
        .bp-description ol li {
            list-style: none !important;
            list-style-image: none !important;
            background: none !important;
            position: relative !important;
            padding-left: 24px !important;
            margin: 0 0 10px !important;
            color: #2A2F3A !important;
            font-size: 17px !important;
        }

        .bp-description ul li::marker,
        .bp-description ol li::marker {
            content: '' !important;
        }

        .bp-description ul li::before,
        .bp-description ol li::before {
            content: '●' !important;
            position: absolute !important;
            left: 0 !important;
            top: 8px !important;
            font-size: 7px !important;
            line-height: 1 !important;
            color: var(--bp-teal) !important;
            background: none !important;
            width: auto !important;
            height: auto !important;
        }

        .bp-description ul li img,
        .bp-description ol li img {
            display: none;
        }

        .bp-description table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 0 0 20px !important;
        }

        .bp-description table th,
        .bp-description table td {
            border: 1px solid var(--bp-line) !important;
            padding: 10px 12px !important;
            font-size: 15px !important;
            color: #2A2F3A !important;
        }

        .bp-description table th {
            background: var(--bp-paper) !important;
            color: var(--bp-ink) !important;
            font-weight: 600 !important;
        }

        .bp-description img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 10px !important;
            margin: 12px 0 !important;
        }

        .bp-description hr {
            border: none !important;
            border-top: 1px solid var(--bp-line) !important;
            margin: 28px 0 !important;
        }

        .bp-description blockquote {
            margin: 24px 0 !important;
            padding: 16px 20px !important;
            border-left: 3px solid var(--bp-teal) !important;
            background: var(--bp-paper) !important;
            color: var(--bp-ink) !important;
            font-style: normal !important;
        }

        .bp-meet-card {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            flex-wrap: wrap !important;
            background: linear-gradient(135deg, var(--bp-navy-900), var(--bp-navy-700)) !important;
            border-radius: 12px !important;
            padding: 26px 28px !important;
            margin: 36px 0 0 !important;
        }

        .bp-meet-text {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
        }

        .bp-meet-text strong {
            color: #fff !important;
            font-size: 17px !important;
        }

        .bp-meet-text span {
            color: rgba(255, 255, 255, 0.55) !important;
            font-size: 14px !important;
        }

        .bp-cta-orange {
            background: var(--bp-orange) !important;
            border: none !important;
            border-radius: 8px !important;
            color: var(--bp-navy-900) !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            padding: 13px 22px !important;
            display: flex !important;
            align-items: center !important;
            cursor: pointer !important;
        }

        .bp-cta-orange .logo-container img {
            height: 20px !important;
        }

        .bp-tags {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            margin: 36px 0 0 !important;
            padding-top: 24px !important;
            border-top: 1px solid var(--bp-line) !important;
        }

        .bp-tags-label {
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: .08em !important;
            color: var(--bp-muted) !important;
        }

        .bp-tag-pill {
            background: #FDECEB !important;
            color: red !important;
            /* border: 1px solid rgba(0, 137, 59, 0.25) !important; */
            padding: 6px 14px !important;
            border-radius: 100px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        .bp-tag-pill:hover {
            background: red !important;
            color: #ffffff !important;
        }

        .bp-share {
            margin-top: 28px !important;
        }

        .bp-share-label {
            display: block !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: .08em !important;
            color: var(--bp-muted) !important;
            margin-bottom: 14px !important;
        }

        .bp-share-icons {
            display: flex !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
            list-style: none !important;
        }

        .bp-share-icons a {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            background: var(--bp-paper) !important;
            border: 1px solid var(--bp-line) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all .2s ease !important;
        }

        .bp-share-icons a img {
            width: 17px !important;
            height: 17px !important;
            opacity: .65 !important;
            filter: grayscale(1) !important;
            transition: all .2s ease !important;
            margin: 0 !important;
        }

        .bp-share-icons a:hover {
            background: var(--bp-navy-900) !important;
            border-color: var(--bp-navy-900) !important;
        }

        .bp-share-icons a:hover img {
            opacity: 1 !important;
            filter: brightness(0) invert(1) !important;
        }

        .bp-sidebar {
            position: sticky !important;
            top: 24px !important;
        }

        .bp-widget {
            background: #fff !important;
            border: 1px solid var(--bp-line) !important;
            border-radius: 12px !important;
            padding: 24px !important;
            margin: 0 0 22px !important;
            list-style: none !important;
        }

        .bp-widget-title {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: var(--bp-ink) !important;
            margin: 0 0 16px !important;
            padding-bottom: 14px !important;
            border-bottom: 1px solid var(--bp-line) !important;
            position: relative !important;
        }

        .bp-widget-title::after {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            bottom: -1px !important;
            width: 26px !important;
            height: 2px !important;
            background: var(--bp-orange) !important;
        }

        .bp-search-widget form {
            display: flex !important;
            border: 1px solid var(--bp-line) !important;
            border-radius: 8px !important;
            overflow: hidden !important;
        }

        .bp-search-widget input {
            flex: 1 !important;
            border: none !important;
            outline: none !important;
            padding: 12px 14px !important;
            font-size: 14px !important;
            width: auto !important;
            margin: 0 !important;
        }

        .bp-search-widget button {
            border: none !important;
            background: var(--bp-navy-900) !important;
            color: #fff !important;
            padding: 0 18px !important;
            cursor: pointer !important;
        }

        .bp-help-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 0 !important;
            border-bottom: 1px dotted var(--bp-line) !important;
            margin: 0 !important;
        }

        .bp-help-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }

        .bp-help-item img {
            width: 36px !important;
            height: 36px !important;
            object-fit: contain !important;
            border-radius: 8px !important;
            flex: 0 0 auto !important;
        }

        .bp-help-item a {
            font-size: 14.5px !important;
            color: var(--bp-ink) !important;
            font-weight: 500 !important;
            text-decoration: none !important;
        }

        .bp-help-item a:hover {
            color: var(--bp-teal) !important;
        }

        .bp-widget-dark {
            background: linear-gradient(160deg, var(--bp-navy-900), var(--bp-navy-700)) !important;
            border: none !important;
        }

        .bp-widget-dark .bp-widget-title {
            color: #fff !important;
            border-bottom-color: rgba(255, 255, 255, 0.15) !important;
        }

        .bp-cat-list,
        .bp-cat-list li {
            list-style: none !important;
            background: none !important;
            margin: 0 0 2px !important;
            padding: 0 !important;
        }

        .bp-cat-list li a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 10px 6px !important;
            border-radius: 8px !important;
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 14.5px !important;
            text-decoration: none !important;
        }

        .bp-cat-list li a:hover,
        .bp-cat-list li.active a {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.06) !important;
        }

        .bp-cat-dot {
            width: 6px !important;
            height: 6px !important;
            border-radius: 50% !important;
            background: var(--bp-teal-light) !important;
            flex: 0 0 auto !important;
        }

        .bp-cat-count {
            margin-left: auto !important;
            font-size: 12px !important;
            color: rgba(255, 255, 255, 0.4) !important;
        }

        .bp-recent-item {
            display: flex !important;
            gap: 12px !important;
            padding: 14px 0 !important;
            border-bottom: 1px solid var(--bp-line) !important;
            margin: 0 !important;
        }

        .bp-recent-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }

        .bp-recent-item img {
            width: 58px !important;
            height: 58px !important;
            object-fit: cover !important;
            border-radius: 8px !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
        }

        .bp-recent-item a {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: var(--bp-ink) !important;
            line-height: 1.4 !important;
            display: block !important;
            text-decoration: none !important;
        }

        .bp-recent-item a:hover {
            color: var(--bp-teal) !important;
        }

        .bp-recent-date {
            font-size: 11.5px !important;
            color: var(--bp-muted) !important;
            display: block !important;
            margin-top: 4px !important;
        }

        .bp-case-widget {
            background: linear-gradient(135deg, var(--bp-navy-900) 0%, var(--bp-teal) 140%) !important;
            position: relative !important;
            overflow: hidden !important;
            color: #fff !important;
        }

        .bp-case-widget::before {
            content: '' !important;
            position: absolute !important;
            top: -40px !important;
            right: -40px !important;
            width: 150px !important;
            height: 150px !important;
            border-radius: 50% !important;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.16), transparent 70%) !important;
        }

        .bp-case-eyebrow {
            font-size: 12px !important;
            color: rgba(255, 255, 255, 0.6) !important;
            margin-bottom: 6px !important;
            position: relative !important;
        }

        .bp-case-widget h5 {
            color: #fff !important;
            font-size: 22px !important;
            font-weight: 700 !important;
            margin: 0 0 8px !important;
            position: relative !important;
        }

        .bp-case-widget p {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 14px !important;
            margin-bottom: 20px !important;
            position: relative !important;
        }

        .bp-case-btn {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            background: var(--bp-orange) !important;
            color: var(--bp-navy-900) !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            text-transform: uppercase !important;
            padding: 12px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
        }

        .bp-case-btn:hover {
            background: #fff !important;
        }

        @media (max-width:991px) {
            .bp-hero {
                padding: 44px 0 80px;
            }

            .bp-hero-grid {
                flex-direction: column;
                align-items: flex-start;
                gap: 24px;
            }

            .bp-hero-side {
                flex: 1 1 auto;
                width: 100%;
            }

            .bp-title {
                font-size: 34px;
            }

            .bp-banner-card {
                margin-top: -56px;
            }

            .bp-sidebar {
                position: static;
                margin-top: 8px;
            }
        }

        @media (max-width:575px) {
            .bp-title {
                font-size: 27px;
            }

            .bp-hero {
                padding: 34px 0 64px;
            }

            .bp-banner-card {
                margin-top: -40px;
                margin-bottom: 28px;
                border-width: 4px;
            }

            .bp-meet-card {
                padding: 20px;
            }

            .bp-crumb {
                font-size: 10.5px;
            }
        }
    </style>

    <!-- reading progress -->
    <div class="bp-progress">
        <div class="bp-progress-fill" id="bpProgressFill"></div>
    </div>

    <div class="bp-page">

        <!-- HERO -->
        <section class="bp-hero">
            <div class="bp-hero-glow"></div>
            <div class="container">
                <div class="bp-crumb">
                    <a href="{{ route('home') }}" wire:navigate>{{ __('navbar.home') }}</a>
                    <span>/</span>
                    <span class="bp-crumb-current">{{ Str::limit($article->title, 46) }}</span>
                </div>

                <div class="bp-hero-grid">
                    <div class="bp-hero-main">
                        <div class="bp-eyebrow"><span class="bp-eyebrow-mark">§</span>
                            {{ $article->category->title ?? __('navbar.blog-detail') }}</div>
                        <h1 class="bp-title">{{ $article->title }}</h1>
                        <div class="bp-meta">
                            <span class="bp-meta-item">{{ $article->created_at->format('M d, Y') }}</span>
                            <span class="bp-meta-dot">•</span>
                            <span
                                class="bp-meta-item">{{ max(1, (int) ceil(str_word_count(strip_tags($article->description)) / 200)) }}
                                min read</span>
                            <span class="bp-meta-dot">•</span>
                            <span class="bp-meta-item">MSN SoftTech</span>
                        </div>
                    </div>
                    <div class="bp-hero-side">
                        <div class="bp-terminal-badge">
                            <div class="bp-terminal-dots"><span></span><span></span><span></span></div>
                            <div class="bp-terminal-line"><span class="bp-prompt">$</span> cat article.meta</div>
                            <div class="bp-terminal-out">category: "{{ $article->category->title ?? 'general' }}"</div>
                            <div class="bp-terminal-out">status: <span class="bp-status-live">published</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container bp-content-wrap">

            <div class="bp-banner-card">
                <img src="{{ asset('uploads/article/' . $article->image_path) }}" alt="{{ $article->title }}">
            </div>

            <div class="row bp-row">
                <!-- Content Side -->
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <div class="bp-article-card">

                        <div class="bp-toc">
                            <div class="bp-toc-head">
                                <div class="bp-toc-dots"><span></span><span></span><span></span></div>
                                <span class="bp-toc-filename">table_of_contents.md</span>
                            </div>
                            <div class="bp-toc-body">
                                <ol class="toc">
                                    <li><a href="#section1">AI as a Career Catalyst for Early Entrants</a></li>
                                    <li><a href="#section2">AI-First Mindset: Thinking Beyond Technology</a></li>
                                    <li><a href="#section3">AI Technology Driving Business Transformation</a></li>
                                    <li><a href="#section4">AI's Role in Enhancing Business Strategy</a></li>
                                    <li><a href="#section5">Final Thoughts</a></li>
                                </ol>
                            </div>
                        </div>

                        <section id="section1">
                            <div class="bp-description article-description" id="article-description">
                                {!! $article->description !!}
                            </div>
                        </section>

                        {{-- <div class="bp-meet-card">
                            <div class="bp-meet-text">
                                <strong>Have a question about this?</strong>
                                <span>Book a quick call with our team — no sales pitch.</span>
                            </div>
                            <div>
                                <button id="open-modal" class="button google-meet-button bp-cta-orange">
                                    <div class="logo-container">
                                        <img id="google-meet-img"
                                            src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png"
                                            alt="Google Meet Logo" />
                                        <img id="zoom-img"
                                            src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg"
                                            alt="Zoom Logo" />
                                    </div>
                                    <span style="margin-left:10px;">Book a Meeting</span>
                                </button>
                            </div>
                        </div>
                        @include('web.layouts.googlemeet') --}}

                        <div class="bp-tags">
                            <span class="bp-tags-label">{{ __('common.category') }}</span>
                            <a href="{{ route('blog.category', $article->category->slug) }}" wire:navigate
                                class="bp-tag-pill">{{ $article->category->title }}</a>
                        </div>

                        <div class="bp-share">
                            <span class="bp-share-label">Share this article</span>
                            <div class="bp-share-icons">
                                <a href="#" title="Share on X"><img
                                        src="https://cdn-icons-png.flaticon.com/512/5968/5968776.png" alt="X"></a>
                                <a href="#" title="Share on LinkedIn"><img
                                        src="https://cdn-icons-png.flaticon.com/512/145/145807.png" alt="LinkedIn"></a>
                                <a href="#" title="Share on Facebook"><img
                                        src="https://cdn-icons-png.flaticon.com/512/145/145802.png" alt="Facebook"></a>
                                <a href="#" title="Share on WhatsApp"><img
                                        src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp"></a>
                                <a href="#" title="Share via Gmail"><img
                                        src="https://cdn-icons-png.flaticon.com/512/281/281769.png" alt="Gmail"></a>
                                <a href="#" title="Copy link"><img
                                        src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Copy link"></a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Sidebar Side -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="bp-sidebar">

                        <div class="bp-widget bp-search-widget">
                            <form method="get" action="{{ route('blog.search') }}">
                                <input type="search" name="search"
                                    value="@if (isset($search)) {{ $search }} @endif"
                                    placeholder="{{ __('search.search_field') }}" required>
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>

                        @if (count($services) > 0)
                            <div class="bp-widget">
                                <h5 class="bp-widget-title">I Need Help With…</h5>
                                @foreach ($services as $service)
                                    <div class="bp-help-item">
                                        <img src="{{ asset('uploads/service/' . $service->image_path) }}"
                                            alt="{{ $service->short_title }}">
                                        <a href="{{ route('service.single', $service->slug) }}"
                                            wire:navigate>{{ $service->short_title }}</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- @if (count($article_categories) > 0)
                            <div class="bp-widget bp-widget-dark">
                                <h5 class="bp-widget-title">Categories</h5>
                                <ul class="bp-cat-list">
                                    @foreach ($article_categories as $article_category)
                                        <li class="@if ($article->category->id == $article_category->id) active @endif">
                                            <a href="{{ route('blog.category', $article_category->slug) }}" wire:navigate>
                                                <span class="bp-cat-dot"></span>
                                                {{ $article_category->title }}
                                                <span
                                                    class="bp-cat-count">{{ $article_category->articles->where('status', 1)->count() }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}

                        @if (count($recents) > 0)
                            <div class="bp-widget">
                                <h5 class="bp-widget-title">{{ __('common.recent_posts') }}</h5>
                                @foreach ($recents as $key => $recent)
                                    <div class="bp-recent-item">
                                        <img src="{{ asset('uploads/article/' . $recent->image_path) }}"
                                            alt="{{ $recent->title }}">
                                        <div>
                                            <a href="{{ route('blog.single', $recent->slug) }}"
                                                wire:navigate>{!! str_limit(strip_tags($recent->title), 50, ' ...') !!}</a>
                                            <span
                                                class="bp-recent-date">{{ date('F d, Y', strtotime($recent->created_at)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- <div class="bp-widget bp-case-widget">
                            <div class="bp-case-eyebrow">§ EXPLORE OUR</div>
                            <h5>Case Studies</h5>
                            <p>Inspiring success stories from real projects.</p>
                            <a href="{{ route('case') }}" wire:navigate class="bp-case-btn">VIEW ALL <i
                                    class="fa-solid fa-arrow-right-long"></i></a>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initArticleSingle() {
            // wire:navigate দিয়ে আগের পেজের window-level scroll listener জমে থাকতে পারে,
            // তাই নতুন করে বাইন্ড করার আগে পুরনোটা সরিয়ে নেওয়া হচ্ছে (duplicate call আটকাতে)
            if (window._bpProgressScrollHandler) {
                window.removeEventListener('scroll', window._bpProgressScrollHandler);
            }

            // reading progress bar
            var fill = document.getElementById('bpProgressFill');
            var articleCard = document.querySelector('.bp-article-card');

            function updateProgress() {
                if (!fill || !articleCard) return;
                var rect = articleCard.getBoundingClientRect();
                var total = articleCard.offsetHeight - window.innerHeight;
                var scrolled = -rect.top;
                var pct = total > 0 ? Math.min(100, Math.max(0, (scrolled / total) * 100)) : 0;
                fill.style.width = pct + '%';
            }

            window._bpProgressScrollHandler = updateProgress;
            window.addEventListener('scroll', updateProgress);
            updateProgress();

            // clean up article description markup (preserved from previous version)
            const descriptionElement = document.querySelectorAll(".article-description");
            if (descriptionElement) {
                descriptionElement.innerHTML = descriptionElement.innerHTML.replace(/serviceshow/g,
                    '<span style="display:none;">serviceshow</span>');
            }

            const descriptionElements = document.querySelectorAll(".article-description");
            descriptionElements.forEach(function(descriptionElement) {
                descriptionElement.innerHTML = descriptionElement.innerHTML.replace(/(<br\s*\/?>\s*){3,}/gi,
                    '<br><br>');
            });

            document.querySelectorAll('.news-block').forEach(element => {
                element.setAttribute('style', 'border: none !important;');
            });
        }

        document.addEventListener("DOMContentLoaded", initArticleSingle);
        document.addEventListener("livewire:navigated", initArticleSingle);
    </script>
@endsection
