@extends('admin.layouts.master')
@section('title', $title)
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        /* ===== Premium Sub-Service Editor (shared with edit.blade.php) ===== */
        :root {
            --ps-primary: #5b56e8;
            --ps-primary-soft: #eeedfd;
            --ps-primary-dark: #4640c4;
            --ps-ink: #15172b;
            --ps-ink-soft: #6c7086;
            --ps-line: #e9e9f2;
            --ps-bg: #f6f6fb;
            --ps-surface: #ffffff;
            --ps-success: #16a34a;
            --ps-success-soft: #ecfdf3;
            --ps-danger: #e0356b;
            --ps-danger-soft: #fdeef2;
            --ps-radius: 12px;
            --ps-shadow-sm: 0 1px 2px rgba(21, 23, 43, .05);
            --ps-shadow-md: 0 6px 20px rgba(21, 23, 43, .06);
            --ps-shadow-lg: 0 18px 40px rgba(21, 23, 43, .10);
        }

        body {
            background: var(--ps-bg);
        }

        .ps-shell .card {
            border: 1px solid var(--ps-line);
            border-radius: 16px;
            box-shadow: var(--ps-shadow-lg);
            overflow: hidden;
        }

        .ps-page-header {
            position: relative;
            background: var(--ps-ink);
            padding: 28px 32px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .ps-page-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -.01em;
        }

        .ps-page-header p {
            margin: 4px 0 0;
            opacity: .6;
            font-size: 13px;
        }

        .ps-page-header .ps-header-badge {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            color: rgba(255, 255, 255, .85);
            padding: 7px 15px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
        }

        .ps-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 14px 26px;
            background: var(--ps-surface);
            border-bottom: 1px solid var(--ps-line);
        }

        .ps-progress-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 220px;
        }

        .ps-progress-track {
            flex: 1;
            height: 5px;
            border-radius: 6px;
            background: var(--ps-line);
            overflow: hidden;
        }

        .ps-progress-fill {
            height: 100%;
            border-radius: 6px;
            background: var(--ps-primary);
            width: 0%;
            transition: width .3s ease;
        }

        .ps-progress-label {
            font-size: 12px;
            color: var(--ps-ink-soft);
            white-space: nowrap;
        }

        .ps-toolbar-actions {
            display: flex;
            gap: 8px;
        }

        .ps-toolbar-actions button {
            border: 1px solid var(--ps-line);
            background: #fff;
            color: var(--ps-ink);
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .ps-toolbar-actions button:hover {
            border-color: var(--ps-primary);
            color: var(--ps-primary-dark);
            background: var(--ps-primary-soft);
        }

        .ps-shell .card-body {
            background: var(--ps-bg);
            padding: 22px 24px 6px;
        }

        .premium-section {
            background: var(--ps-surface);
            border: 1px solid var(--ps-line);
            border-radius: var(--ps-radius);
            margin-bottom: 14px;
            scroll-margin-top: 90px;
            box-shadow: var(--ps-shadow-sm);
            transition: box-shadow .2s ease, border-color .2s ease;
            overflow: hidden;
        }

        .premium-section:hover {
            box-shadow: var(--ps-shadow-md);
        }

        .premium-section-head {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            padding: 16px 20px;
            cursor: pointer;
            user-select: none;
        }

        .premium-section:not(.ps-collapsed) .premium-section-head {
            border-bottom: 1px solid var(--ps-line);
        }

        .premium-section-title {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--ps-ink);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .premium-icon {
            width: 32px;
            height: 32px;
            flex: none;
            border-radius: 9px;
            background: var(--ps-primary-soft);
            color: var(--ps-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .premium-section-sub {
            font-size: 12.5px;
            color: var(--ps-ink-soft);
            margin-right: auto;
        }

        .premium-chevron {
            color: var(--ps-ink-soft);
            font-size: 12px;
            transition: transform .25s ease;
            flex: none;
        }

        .premium-section:not(.ps-collapsed) .premium-chevron {
            transform: rotate(180deg);
            color: var(--ps-primary);
        }

        .premium-section-body {
            padding: 18px 20px 20px;
            max-height: 6000px;
            opacity: 1;
            transition: max-height .35s ease, opacity .25s ease, padding .25s ease;
        }

        .premium-section-body.ps-collapsed {
            max-height: 0;
            opacity: 0;
            padding-top: 0;
            padding-bottom: 0;
            overflow: hidden;
        }

        .premium-section label {
            font-weight: 600;
            font-size: 12.5px;
            color: var(--ps-ink);
        }

        .premium-section .form-control {
            border-radius: 8px;
            border-color: var(--ps-line);
        }

        .premium-section .form-control:focus {
            border-color: var(--ps-primary);
            box-shadow: 0 0 0 .15rem var(--ps-primary-soft);
        }

        #sec-basic-info .form-group,
        #sec-stack-portfolio .form-group {
            margin-bottom: 16px;
        }

        .repeater {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 14px;
        }

        .repeater-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #fbfbfd;
            border: 1px solid var(--ps-line);
            border-radius: 10px;
            padding: 12px;
            transition: border-color .15s ease, background .15s ease;
        }

        .repeater-item:hover {
            border-color: #d7d8ea;
            background: #f8f8fd;
        }

        .repeater-num {
            flex: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--ps-primary-soft);
            color: var(--ps-primary-dark);
            font-size: 11.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
        }

        .repeater-fields {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .repeater-fields .form-control {
            margin: 0 !important;
        }

        .repeater-del {
            flex: none;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--ps-line);
            background: #fff;
            color: var(--ps-ink-soft);
            font-size: 12px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .repeater-del:hover {
            border-color: var(--ps-danger);
            color: var(--ps-danger);
            background: var(--ps-danger-soft);
        }

        .btn-add-row {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1px dashed #c9cbe6;
            background: transparent;
            color: var(--ps-primary-dark);
            font-size: 12.5px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: 9px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .btn-add-row:hover {
            border-style: solid;
            border-color: var(--ps-primary);
            background: var(--ps-primary-soft);
        }

        .ps-shell .card-footer {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--ps-line);
            padding: 14px 26px;
            display: flex;
            justify-content: flex-end;
        }

        .ps-shell .card-footer .btn-primary {
            background: var(--ps-primary);
            border: none;
            padding: 10px 34px;
            border-radius: 9px;
            font-weight: 600;
            box-shadow: var(--ps-shadow-md);
            transition: background .15s ease, transform .12s ease;
        }

        .ps-shell .card-footer .btn-primary:hover {
            background: var(--ps-primary-dark);
            transform: translateY(-1px);
        }

        .ps-quicknav {
            position: fixed;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 40;
            background: #fff;
            border: 1px solid var(--ps-line);
            border-radius: 14px;
            box-shadow: var(--ps-shadow-lg);
            padding: 10px 6px;
            max-height: 74vh;
            overflow-y: auto;
        }

        .ps-quicknav a {
            display: block;
            padding: 7px 13px;
            font-size: 12px;
            font-weight: 500;
            color: var(--ps-ink-soft);
            text-decoration: none;
            border-radius: 7px;
            white-space: nowrap;
            margin-bottom: 2px;
            transition: all .15s ease;
        }

        .ps-quicknav a:hover {
            background: var(--ps-primary-soft);
            color: var(--ps-primary-dark);
        }

        .ps-quicknav a.active {
            background: var(--ps-primary);
            color: #fff;
            font-weight: 600;
        }

        @media (max-width: 1400px) {
            .ps-quicknav {
                display: none;
            }
        }
    </style>

    <div class="container-fluid">
        @include('admin.inc.breadcrumb')

        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.subservices.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        <nav class="ps-quicknav" id="psQuickNav">
            <a href="#sec-basic-info">Basic Info</a>
            <a href="#sec-stack-portfolio">Stack &amp; Work</a>
            <a href="#sec-core-features">Core Features</a>
            <a href="#sec-deliverables">Deliverables</a>
            <a href="#sec-who-is-this-for">Who Is This For</a>
            <a href="#sec-hero-badges">Hero Badges</a>
            <a href="#sec-achievements">Achievements</a>
            <a href="#sec-whats-included">What's Included</a>
            <a href="#sec-client-voices">Client Voices</a>
            <a href="#sec-how-we-work">How We Work</a>
            <a href="#sec-faqs">FAQs</a>
            <a href="#sec-industries">Industries</a>
            <a href="#sec-cta">Call to Action</a>
            <a href="#sec-guarantee">Guarantee</a>
            <a href="#sec-seo">SEO &amp; Meta</a>
            <a href="#sec-media-pricing">Media &amp; Pricing</a>
            <a href="#sec-section-headings">Section Headings</a>
        </nav>

        {{-- ===== JSON Bulk Import Modal (works for whole-form AND single-section imports) ===== --}}
        <div id="psImportModal"
            style="display:none; position:fixed; inset:0; background:rgba(21,23,43,.55); z-index:1050; align-items:center; justify-content:center; padding:20px;">
            <div
                style="background:#fff; border-radius:14px; max-width:760px; width:100%; max-height:88vh; overflow:auto; box-shadow:0 20px 60px rgba(0,0,0,.35);">
                <div
                    style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--ps-line);">
                    <h5 id="psImportTitle" style="margin:0; font-weight:700; font-size:16px;"><i
                            class="fa fa-file-import"></i>&nbsp; JSON দিয়ে পুরো ফর্ম পূরণ করুন</h5>
                    <button type="button" onclick="psCloseImport()"
                        style="border:none;background:none;font-size:22px;line-height:1;cursor:pointer;color:var(--ps-ink-soft);">&times;</button>
                </div>
                <div style="padding:20px 22px;">
                    <p id="psImportHint" style="font-size:12.5px; color:var(--ps-ink-soft); margin-bottom:14px;">
                        নিচে JSON পেস্ট করে <strong>"ফর্মে বসান"</strong> চাপুন — সব সেকশনের ফিল্ড এবং রিপিটার (Features,
                        FAQ, Guarantee ইত্যাদি) অটোমেটিক পূরণ হয়ে যাবে।
                        <br>⚠️ থাম্বনেইল ইমেজ ব্রাউজার নিরাপত্তার কারণে JSON দিয়ে বসানো সম্ভব না — সেটা import এর পর
                        ম্যানুয়ালি সিলেক্ট করে দিতে হবে।
                    </p>
                    <div id="psRepeaterModeWrap"
                        style="display:none; flex-direction:column; gap:6px; margin-bottom:14px; padding:10px 12px; background:#f8f9fc; border-radius:8px; font-size:12.5px; color:var(--ps-ink-soft);">
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            <input type="radio" name="psRepeaterMode" value="replace" checked> রিপ্লেস — সব পুরনো row মুছে
                            নতুন গুলো বসবে
                        </label>
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            <input type="radio" name="psRepeaterMode" value="append"> অ্যাড — শেষে নতুন row যোগ হবে, পুরনো
                            গুলো অক্ষত থাকবে
                        </label>
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            <input type="radio" name="psRepeaterMode" value="update"> আপডেট — শুধু নির্দিষ্ট row(গুলো)
                            এডিট হবে, প্রতিটা item-এ <code>"_row"</code> (row নাম্বার, 1 থেকে শুরু) দিন
                        </label>
                    </div>
                    <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">
                        <button type="button" class="btn-add-row" onclick="psDownloadTemplate()"><i
                                class="fa fa-download"></i> <span id="psTemplateBtnLabel">নমুনা JSON টেমপ্লেট ডাউনলোড
                                করুন</span></button>
                    </div>
                    <textarea id="psImportTextarea" rows="14" class="form-control" placeholder='এখানে JSON পেস্ট করুন...'
                        style="font-family:monospace; font-size:12.5px;"></textarea>
                    <div id="psImportError"
                        style="display:none; margin-top:10px; padding:10px 12px; background:var(--ps-danger-soft); color:var(--ps-danger); border-radius:8px; font-size:12.5px;">
                    </div>
                </div>
                <div
                    style="padding:14px 22px; border-top:1px solid var(--ps-line); display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="psCloseImport()"
                        style="border:1px solid var(--ps-line); background:#fff; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px;">বাতিল</button>
                    <button type="button" onclick="psApplyImport()" class="btn btn-primary"
                        style="padding:8px 22px; border-radius:8px;"><i class="fa fa-check"></i> ফর্মে বসান</button>
                </div>
            </div>
        </div>

        <div class="row ps-shell">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="ps-page-header">
                        <div>
                            <h4>{{ __('dashboard.add') }} {{ $title }}</h4>
                            <p>Fill in every section below to publish a complete, ready-to-launch service page</p>
                        </div>
                        <span class="ps-header-badge"><i class="fa fa-plus"></i> New Sub Service</span>
                    </div>
                    <div class="ps-toolbar">
                        <div class="ps-progress-wrap">
                            <div class="ps-progress-track">
                                <div class="ps-progress-fill" id="psProgressFill"></div>
                            </div>
                            <span class="ps-progress-label" id="psProgressLabel">0 / 16 sections open</span>
                        </div>
                        <div class="ps-toolbar-actions">
                            <button type="button" onclick="psOpenImport()"
                                style="border-color:var(--ps-primary); color:var(--ps-primary-dark); background:var(--ps-primary-soft);"><i
                                    class="fa fa-file-import"></i> JSON Import</button>
                            <button type="button" onclick="psToggleAll(true)"><i class="fa fa-expand"></i> Expand
                                all</button>
                            <button type="button" onclick="psToggleAll(false)"><i class="fa fa-compress"></i> Collapse
                                all</button>
                        </div>
                    </div>
                    <form class="needs-validation" novalidate action="{{ route('admin.subservices.store') }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            {{-- Basic Information --}}
                            <div class="premium-section" id="sec-basic-info">
                                <div class="premium-section-head" onclick="psToggleSection(this)">
                                    <div class="premium-section-title"><span class="premium-icon"><i
                                                class="fa fa-info-circle"></i></span>Basic Information</div>
                                    <div class="premium-section-sub">Core details clients see first</div>
                                    <i class="fa fa-chevron-down premium-chevron"></i>
                                </div>
                                <div class="premium-section-body">
                                    <div class="form-group">
                                        <label for="status">{{ __('dashboard.select_status') }}</label>
                                        <select class="wide" name="service_id" id="status"
                                            data-plugin="customselect">
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ old('title') }}" required>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.title') }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-4">
                                            <label for="slug">{{ __('dashboard.slug') }} <span>* [Write a unique
                                                    slug]</span></label>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                value="{{ old('slug') }}" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.slug') }}</div>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="short_title">{{ __('dashboard.short_title') }}
                                                <span>*</span></label>
                                            <input type="text" class="form-control" name="short_title"
                                                id="short_title" value="{{ old('short_title') }}" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.short_title') }}</div>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="sub_service_icon">{{ __('dashboard.sub_service_icon') }}
                                                <span>*</span></label>
                                            <input type="text" class="form-control" name="sub_service_icon"
                                                id="sub_service_icon" value="{{ old('sub_service_icon') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                                        <textarea class="form-control" name="description" id="editor1" rows="8" required>{{ old('description') }}</textarea>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.description') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Stack & Recent Work --}}
                            <div class="premium-section" id="sec-stack-portfolio">
                                <div class="premium-section-head" onclick="psToggleSection(this)">
                                    <div class="premium-section-title"><span class="premium-icon"><i
                                                class="fa fa-layer-group"></i></span>Stack &amp; Recent Work</div>
                                    <div class="premium-section-sub">Link the technologies and portfolio items used for
                                        this service</div>
                                    <i class="fa fa-chevron-down premium-chevron"></i>
                                </div>
                                <div class="premium-section-body">
                                    <div class="form-group mb-4">
                                        <label for="technologies" class="block text-sm font-medium text-gray-700 mb-1">The
                                            Stack</label>
                                        <select name="technologies[]" id="technologies" multiple>
                                            @foreach ($allTechnologies as $tech)
                                                <option value="{{ $tech->id }}">{{ $tech->short_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="portfolios"
                                            class="block text-sm font-medium text-gray-700 mb-1">Recent Work</label>
                                        <select name="portfolios[]" id="portfolios" multiple>
                                            @foreach ($allPortfolios as $portfolio)
                                                <option value="{{ $portfolio->id }}">{{ $portfolio->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{--
                                All 12 dynamic "repeater" sections share one config-driven
                                partial — see partials/repeater-section.blade.php.
                                On create, every list simply starts empty.
                            --}}

                            @php
                                $repeaterSections = [
                                    [
                                        'id' => 'sec-core-features',
                                        'icon' => 'fa-star',
                                        'title' => 'Core Features',
                                        'subtitle' => 'Highlight what makes this service stand out',
                                        'row' => 'features-row',
                                        'group' => 'features-group',
                                        'prefix' => 'core_features',
                                        'items' => $features ?? [],
                                        'fields' => [
                                            ['k' => 'icon_class', 'l' => 'Icon Class'],
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'bottom_text', 'l' => 'Bottom Text'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-deliverables',
                                        'icon' => 'fa-tasks',
                                        'title' => 'Deliverables',
                                        'subtitle' => 'Step-by-step delivery process shown to clients',
                                        'row' => 'process-row',
                                        'group' => 'process-group',
                                        'prefix' => 'deliverables',
                                        'items' => $process_steps ?? [],
                                        'fields' => [
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'bottom_text', 'l' => 'Bottom Text'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-who-is-this-for',
                                        'icon' => 'fa-users',
                                        'title' => 'Who Is This For',
                                        'subtitle' => 'Describe the ideal client for this service',
                                        'row' => 'WhyWe-row',
                                        'group' => 'WhyWe-group',
                                        'prefix' => 'who_is_this_for',
                                        'items' => $whyWeSteps ?? [],
                                        'fields' => [
                                            ['k' => 'icon_class', 'l' => 'Icon Class'],
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'bottom_text', 'l' => 'Bottom Text'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-hero-badges',
                                        'icon' => 'fa-certificate',
                                        'title' => 'Hero Badges',
                                        'subtitle' => 'Badges shown in the hero banner',
                                        'row' => 'industries-row',
                                        'group' => 'industry-group',
                                        'prefix' => 'hero_badges',
                                        'items' => $industriesSteps ?? [],
                                        'fields' => [
                                            ['k' => 'icon_class', 'l' => 'Icon Class'],
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'description', 'l' => 'Description'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-achievements',
                                        'icon' => 'fa-chart-line',
                                        'title' => 'Achievements Stats',
                                        'subtitle' => 'Numbers that build trust',
                                        'row' => 'achievement-row',
                                        'group' => 'achievement-group',
                                        'prefix' => 'achievements',
                                        'items' => $achievementsSteps ?? [],
                                        'fields' => [
                                            ['k' => 'count_number', 'l' => 'Count Number'],
                                            ['k' => 'title', 'l' => 'Title'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-whats-included',
                                        'icon' => 'fa-check-circle',
                                        'title' => "What's Included",
                                        'subtitle' => 'Key outcomes included in this service',
                                        'row' => 'success-stories-row',
                                        'group' => 'SuccessStories-group',
                                        'prefix' => 'whats_included',
                                        'items' => $successStoriesSteps ?? [],
                                        'fields' => [
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'icon', 'l' => 'Icon'],
                                            ['k' => 'bottom_text', 'l' => 'Bottom Text'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-client-voices',
                                        'icon' => 'fa-quote-left',
                                        'title' => 'Client Voices',
                                        'subtitle' => 'Testimonials from real clients',
                                        'row' => 'clients-say-row',
                                        'group' => 'clients-group',
                                        'prefix' => 'client_voices',
                                        'items' => $clientsSaySteps ?? [],
                                        'fields' => [
                                            ['k' => 'title', 'l' => 'Name'],
                                            ['k' => 'designation', 'l' => 'Designation'],
                                            ['k' => 'meassage', 'l' => 'Message'],
                                            ['k' => 'rating', 'l' => 'Rating', 'd' => '★★★★★'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-how-we-work',
                                        'icon' => 'fa-cogs',
                                        'title' => 'How We Work',
                                        'subtitle' => 'Explain your working process',
                                        'row' => 'works-say-row',
                                        'group' => 'works-group',
                                        'prefix' => 'how_we_work',
                                        'items' => $howWeWork ?? [],
                                        'fields' => [
                                            ['k' => 'title', 'l' => 'Top Title'],
                                            ['k' => 'designation', 'l' => 'Bottom Title'],
                                            ['k' => 'meassage', 'l' => 'Message'],
                                            ['k' => 'icon', 'l' => 'Icon'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-faqs',
                                        'icon' => 'fa-question-circle',
                                        'title' => 'FAQs',
                                        'subtitle' => 'Answer common client questions',
                                        'row' => 'faq-row',
                                        'group' => 'faq-group',
                                        'prefix' => 'faqs',
                                        'items' => $faqSteps ?? [],
                                        'fields' => [
                                            ['k' => 'question', 'l' => 'Question'],
                                            ['k' => 'answer', 'l' => 'Answer'],
                                        ],
                                    ],

                                    [
                                        'id' => 'sec-industries',
                                        'icon' => 'fa-industry',
                                        'title' => 'Industries',
                                        'subtitle' => 'Industries this service serves',
                                        'row' => 'promise-row',
                                        'group' => 'promise-group',
                                        'prefix' => 'industries',
                                        'items' => $ourPromise ?? [],
                                        'fields' => [['k' => 'bottom_text', 'l' => 'Bottom Text']],
                                    ],

                                    [
                                        'id' => 'sec-cta',
                                        'icon' => 'fa-bullhorn',
                                        'title' => 'Call to Action',
                                        'subtitle' => 'The closing pitch on the page',
                                        'row' => 'cta-row',
                                        'group' => 'cta-group',
                                        'prefix' => 'call_to_action',
                                        'items' => $ctaSteps ?? [],
                                        'fields' => [['k' => 'bottom_text', 'l' => 'Bottom Text']],
                                    ],

                                    [
                                        'id' => 'sec-guarantee',
                                        'icon' => 'fa-shield-alt',
                                        'title' => 'Our Guarantee',
                                        'subtitle' => 'Reassure clients with guarantees',
                                        'row' => 'guarantee-row',
                                        'group' => 'guarantee-group',
                                        'prefix' => 'guarantee',
                                        'items' => $guaranteeSteps ?? [],
                                        'fields' => [
                                            ['k' => 'icon', 'l' => 'Icon'],
                                            ['k' => 'title', 'l' => 'Title'],
                                            ['k' => 'description', 'l' => 'Description'],
                                        ],
                                    ],
                                ];
                            @endphp

                            @foreach ($repeaterSections as $section)
                                @include('admin.subservices.partials.repeater-section', [
                                    'section' => $section,
                                ])
                            @endforeach

                            {{-- Section Headings (title / subtitle text shown on the live page for each block) --}}
                            <div class="premium-section" id="sec-section-headings">
                                <div class="premium-section-head" onclick="psToggleSection(this)">
                                    <div class="premium-section-title"><span class="premium-icon"><i
                                                class="fa fa-heading"></i></span>Section Headings</div>
                                    <div class="premium-section-sub">Title &amp; subtitle text shown above each section
                                        on the live page</div>
                                    <i class="fa fa-chevron-down premium-chevron"></i>
                                </div>
                                <div class="premium-section-body">

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="client_voices_section_title">Client Voices &mdash; Title</label>
                                            <input type="text" class="form-control" name="client_voices_section_title"
                                                id="client_voices_section_title"
                                                value="{{ old('client_voices_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="industries_section_title">Industries &mdash; Title</label>
                                            <input type="text" class="form-control" name="industries_section_title"
                                                id="industries_section_title"
                                                value="{{ old('industries_section_title') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="guarantee_section_title">Our Guarantee &mdash; Title</label>
                                            <input type="text" class="form-control" name="guarantee_section_title"
                                                id="guarantee_section_title"
                                                value="{{ old('guarantee_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="guarantee_section_subtitle">Our Guarantee &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control" name="guarantee_section_subtitle"
                                                id="guarantee_section_subtitle"
                                                value="{{ old('guarantee_section_subtitle') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="deliverables_section_title">Deliverables &mdash; Title</label>
                                            <input type="text" class="form-control" name="deliverables_section_title"
                                                id="deliverables_section_title"
                                                value="{{ old('deliverables_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="deliverables_section_subtitle">Deliverables &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control"
                                                name="deliverables_section_subtitle" id="deliverables_section_subtitle"
                                                value="{{ old('deliverables_section_subtitle') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="why_msn_softtech_section_title">Why MSN Softtech &mdash;
                                                Title</label>
                                            <input type="text" class="form-control"
                                                name="why_msn_softtech_section_title" id="why_msn_softtech_section_title"
                                                value="{{ old('why_msn_softtech_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="stack_section_title">The Stack &mdash; Title</label>
                                            <input type="text" class="form-control" name="stack_section_title"
                                                id="stack_section_title" value="{{ old('stack_section_title') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="core_features_section_title">Core Features &mdash;
                                                Title</label>
                                            <input type="text" class="form-control" name="core_features_section_title"
                                                id="core_features_section_title"
                                                value="{{ old('core_features_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="core_features_section_subtitle">Core Features &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control"
                                                name="core_features_section_subtitle" id="core_features_section_subtitle"
                                                value="{{ old('core_features_section_subtitle') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="how_we_work_section_title">How We Work &mdash; Title</label>
                                            <input type="text" class="form-control" name="how_we_work_section_title"
                                                id="how_we_work_section_title"
                                                value="{{ old('how_we_work_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="how_we_work_section_subtitle">How We Work &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control"
                                                name="how_we_work_section_subtitle" id="how_we_work_section_subtitle"
                                                value="{{ old('how_we_work_section_subtitle') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="whats_included_section_title">What's Included &mdash;
                                                Title</label>
                                            <input type="text" class="form-control"
                                                name="whats_included_section_title" id="whats_included_section_title"
                                                value="{{ old('whats_included_section_title') }}">
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="whats_included_section_subtitle">What's Included &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control"
                                                name="whats_included_section_subtitle"
                                                id="whats_included_section_subtitle"
                                                value="{{ old('whats_included_section_subtitle') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6 mb-0">
                                            <label for="who_is_this_for_section_title">Who Is This For &mdash;
                                                Title</label>
                                            <input type="text" class="form-control"
                                                name="who_is_this_for_section_title" id="who_is_this_for_section_title"
                                                value="{{ old('who_is_this_for_section_title') }}">
                                        </div>
                                        <div class="form-group col-6 mb-0">
                                            <label for="who_is_this_for_section_subtitle">Who Is This For &mdash;
                                                Subtitle</label>
                                            <input type="text" class="form-control"
                                                name="who_is_this_for_section_subtitle"
                                                id="who_is_this_for_section_subtitle"
                                                value="{{ old('who_is_this_for_section_subtitle') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- SEO & Meta --}}
                            <div class="premium-section" id="sec-seo">
                                <div class="premium-section-head" onclick="psToggleSection(this)">
                                    <div class="premium-section-title"><span class="premium-icon"><i
                                                class="fa fa-search"></i></span>SEO &amp; Meta</div>
                                    <div class="premium-section-sub">Controls how this page appears in search results</div>
                                    <i class="fa fa-chevron-down premium-chevron"></i>
                                </div>
                                <div class="premium-section-body">
                                    <div class="form-group">
                                        <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="meta_title" id="meta_title"
                                            value="{{ old('meta_title') }}" required>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.meta_title') }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="short_desc">{{ __('dashboard.meta_description') }}
                                            <span>*</span></label>
                                        <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ old('short_desc') }}</textarea>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.meta_description') }}</div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                        <input type="text" class="form-control tagin" data-tagin-separator=" "
                                            name="keywords" value="{{ old('keywords') }}" required>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.meta_keywords') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Media, Pricing & Visibility --}}
                            <div class="premium-section ps-collapsed" id="sec-media-pricing">
                                <div class="premium-section-head" onclick="psToggleSection(this)">
                                    <div class="premium-section-title"><span class="premium-icon"><i
                                                class="fa fa-tags"></i></span>Media, Pricing &amp; Visibility</div>
                                    <div class="premium-section-sub">Thumbnail, pricing details and publish status</div>
                                    <i class="fa fa-chevron-down premium-chevron"></i>
                                </div>
                                <div class="premium-section-body ps-collapsed">
                                    <div class="form-group">
                                        <label for="image">{{ __('dashboard.thumbnail') }} <span>*</span>
                                            <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                        <input type="file" class="form-control" name="image" id="image"
                                            required>
                                        <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                            {{ __('dashboard.thumbnail') }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="price">{{ __('dashboard.price') }} <span>* </span></label>
                                            <input type="number" class="form-control" name="price" id="price"
                                                value="499" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.price') }}</div>
                                        </div>
                                        <div class="form-group col">
                                            <label for="starting_price">{{ __('dashboard.starting_price') }}
                                                <span>*</span></label>
                                            <input type="number" class="form-control" name="starting_price"
                                                id="starting_price" value="499" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.starting_price') }}</div>
                                        </div>
                                        <div class="form-group col">
                                            <label for="review_count">{{ __('dashboard.review_count') }}
                                                <span>*</span></label>
                                            <input type="number" class="form-control" name="review_count"
                                                id="review_count" value="150" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.review_count') }}</div>
                                        </div>
                                        <div class="form-group col">
                                            <label for="priceCurrency">{{ __('dashboard.priceCurrency') }}
                                                <span>*</span></label>
                                            <input type="text" class="form-control" name="priceCurrency"
                                                id="priceCurrency" value="USD" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.priceCurrency') }}</div>
                                        </div>
                                        <div class="form-group col">
                                            <label for="average_rating">{{ __('dashboard.average_rating') }}
                                                <span>*</span></label>
                                            <input type="text" class="form-control" name="average_rating"
                                                id="average_rating" value="4.9" required>
                                            <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                                {{ __('dashboard.average_rating') }}</div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 50px">
                                        <div class="form-group col">
                                            <label for="manu">Manu</label>
                                            <select class="wide" name="manu" id="manu"
                                                data-plugin="customselect">
                                                <option value="0">Hidden</option>
                                                <option value="1">Show</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>
                                    {{ __('dashboard.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        // ---- Tagin (meta keywords) ----
        function psInitTagin() {
            document.querySelectorAll('.tagin').forEach(input => new Tagin(input, {
                separator: ',',
                duplicate: false,
                enter: true,
                maxTags: 100
            }));
        }
        document.addEventListener('DOMContentLoaded', psInitTagin);

        // ---- CKEditor (description + meta description share the same rules) ----
        ['editor', 'editor1'].forEach(function(id) {
            if (!document.getElementById(id)) return;
            CKEDITOR.replace(id, {
                on: {
                    instanceReady: function() {
                        this.dataProcessor.writer.setRules('strong', {
                            indent: false,
                            breakBeforeOpen: false,
                            breakAfterOpen: false,
                            breakBeforeClose: false,
                            breakAfterClose: false
                        });
                    }
                },
                coreStyles_bold: {
                    element: 'b',
                    overrides: 'strong'
                }
            });
        });

        // ---- Choices.js (technologies + portfolios share the same config) ----
        window.psChoicesInstances = {};
        const psChoicesConfig = [{
                el: '#technologies',
                placeholder: 'Select technologies',
                search: 'Search technologies...'
            },
            {
                el: '#portfolios',
                placeholder: 'Select portfolios',
                search: 'Search portfolios...'
            },
        ];

        function psInitChoices() {
            psChoicesConfig.forEach(function(cfg) {
                const el = document.querySelector(cfg.el);
                if (!el) return;
                if (window.psChoicesInstances[cfg.el]) {
                    window.psChoicesInstances[cfg.el].destroy();
                }
                window.psChoicesInstances[cfg.el] = new Choices(el, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: cfg.placeholder,
                    searchPlaceholderValue: cfg.search,
                    shouldSort: false
                });
            });
        }
        document.addEventListener('DOMContentLoaded', psInitChoices);

        // ---- Generic repeater engine: replaces every add/removeXxx() pair ----
        function addRepeaterRow(btn) {
            const body = btn.closest('.premium-section-body');
            const wrapper = body.querySelector('.repeater');
            const fields = JSON.parse(wrapper.dataset.fields);
            const prefix = wrapper.dataset.prefix;
            const group = wrapper.dataset.group;
            const idx = wrapper.children.length;

            const item = document.createElement('div');
            item.className = `repeater-item ${group}`;
            item.innerHTML = `
                <span class="repeater-num">${idx + 1}</span>
                <div class="repeater-fields">
                    ${fields.map(f => `<input type="text" class="form-control mb-1" name="${prefix}[${idx}][${f.k}]" placeholder="${idx + 1}. ${f.l}"${f.d ? ` value="${f.d}"` : ''}>`).join('')}
                </div>
                <button type="button" class="repeater-del" onclick="this.closest('.repeater-item').remove()" title="Remove">
                    <i class="fa fa-trash"></i>
                </button>`;
            wrapper.appendChild(item);
            psUpdateProgress();
        }

        // ======================================================
        // ---- JSON Bulk Import Engine (whole-form + per-section) ----
        // ======================================================

        // Field names that are handled specially, never as plain inputs
        const PS_SPECIAL_KEYS = ['image'];

        // null = importing into the whole form. A DOM element = importing into just that .premium-section
        let psImportScope = null;

        function psOpenImport() {
            psImportScope = null;
            document.getElementById('psImportTitle').innerHTML =
                '<i class="fa fa-file-import"></i>&nbsp; JSON দিয়ে পুরো ফর্ম পূরণ করুন';
            document.getElementById('psImportHint').innerHTML =
                `নিচে JSON পেস্ট করে <strong>"ফর্মে বসান"</strong> চাপুন — সব সেকশনের ফিল্ড এবং রিপিটার (Features, FAQ, Guarantee ইত্যাদি) অটোমেটিক পূরণ হয়ে যাবে.
                <br>⚠️ থাম্বনেইল ইমেজ ব্রাউজার নিরাপত্তার কারণে JSON দিয়ে বসানো সম্ভব না — সেটা import এর পর ম্যানুয়ালি সিলেক্ট করে দিতে হবে।`;
            document.getElementById('psTemplateBtnLabel').textContent = 'নমুনা JSON টেমপ্লেট ডাউনলোড করুন';
            document.getElementById('psRepeaterModeWrap').style.display = 'none';
            psResetRepeaterMode();
            document.getElementById('psImportTextarea').value = '';
            document.getElementById('psImportError').style.display = 'none';
            document.getElementById('psImportModal').style.display = 'flex';
        }

        // Opens the same modal scoped to a single .premium-section (repeater OR plain-field section)
        function psOpenSectionImport(section) {
            psImportScope = section;
            const label = (section.querySelector('.premium-section-title')?.textContent || 'এই সেকশন').trim();
            const wrapper = section.querySelector('.repeater');

            document.getElementById('psImportTitle').innerHTML =
                `<i class="fa fa-file-import"></i>&nbsp; JSON দিয়ে "${label}" সেকশন পূরণ করুন`;

            if (wrapper) {
                document.getElementById('psImportHint').innerHTML =
                    `এই সেকশনে বর্তমানে <strong>${wrapper.querySelectorAll('.repeater-item').length}</strong> টা row আছে। নিচে মোড বেছে JSON <strong>array</strong> পেস্ট করুন, যেমন: <code>[{...}, {...}]</code>।`;
                document.getElementById('psRepeaterModeWrap').style.display = 'flex';
            } else {
                document.getElementById('psImportHint').innerHTML =
                    `শুধু "${label}" সেকশনের ফিল্ডগুলো নিয়ে একটা JSON <strong>object</strong> পেস্ট করুন। অন্য কোনো সেকশন এতে প্রভাবিত হবে না।`;
                document.getElementById('psRepeaterModeWrap').style.display = 'none';
            }
            psResetRepeaterMode();
            document.getElementById('psTemplateBtnLabel').textContent = `"${label}" সেকশনের টেমপ্লেট ডাউনলোড করুন`;
            document.getElementById('psImportTextarea').value = '';
            document.getElementById('psImportError').style.display = 'none';
            document.getElementById('psImportModal').style.display = 'flex';
        }

        function psResetRepeaterMode() {
            const radios = document.querySelectorAll('input[name="psRepeaterMode"]');
            radios.forEach(r => r.checked = (r.value === 'replace'));
        }

        function psGetRepeaterMode() {
            const checked = document.querySelector('input[name="psRepeaterMode"]:checked');
            return checked ? checked.value : 'replace';
        }

        function psCloseImport() {
            document.getElementById('psImportModal').style.display = 'none';
            document.getElementById('psImportError').style.display = 'none';
            psImportScope = null;
        }

        function psShowImportError(msg) {
            const box = document.getElementById('psImportError');
            box.textContent = msg;
            box.style.display = 'block';
        }

        function psOpenSection(section) {
            section.classList.remove('ps-collapsed');
            const body = section.querySelector('.premium-section-body');
            if (body) body.classList.remove('ps-collapsed');
            psUpdateProgress();
        }

        function psDownloadJson(obj, filename) {
            const blob = new Blob([JSON.stringify(obj, null, 2)], {
                type: 'application/json'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }

        // Rebuilds (or appends to) one repeater section's rows from an array of {field: value} objects
        function psFillRepeater(wrapper, items, mode = 'replace') {
            if (!Array.isArray(items)) return;
            const fields = JSON.parse(wrapper.dataset.fields);
            const prefix = wrapper.dataset.prefix;
            const group = wrapper.dataset.group;
            if (mode === 'replace') wrapper.innerHTML = '';
            const startIndex = mode === 'append' ? wrapper.querySelectorAll('.repeater-item').length : 0;
            items.forEach((item, i) => {
                const idx = startIndex + i;
                const row = document.createElement('div');
                row.className = `repeater-item ${group}`;
                row.innerHTML = `
                    <span class="repeater-num">${idx + 1}</span>
                    <div class="repeater-fields">
                        ${fields.map(f => {
                            const raw = (item && item[f.k] !== undefined && item[f.k] !== null) ? String(item[f.k]) : (f.d || '');
                            const safe = raw.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
                            return `<input type="text" class="form-control mb-1" name="${prefix}[${idx}][${f.k}]" placeholder="${idx + 1}. ${f.l}" value="${safe}">`;
                        }).join('')}
                    </div>
                    <button type="button" class="repeater-del" onclick="this.closest('.repeater-item').remove()" title="Remove">
                        <i class="fa fa-trash"></i>
                    </button>`;
                wrapper.appendChild(row);
            });
        }

        // Updates only specific existing rows (by 1-based "_row" index) with the given fields.
        // Fields not present on an item are left untouched (partial update). Returns which _row values were invalid.
        function psUpdateRepeaterRows(wrapper, items) {
            const fields = JSON.parse(wrapper.dataset.fields);
            const rows = Array.from(wrapper.querySelectorAll('.repeater-item'));
            const skipped = [];
            items.forEach(item => {
                const rowNum = parseInt(item && item._row, 10);
                if (!rowNum || rowNum < 1 || rowNum > rows.length) {
                    skipped.push(item && item._row !== undefined ? item._row : '?');
                    return;
                }
                const row = rows[rowNum - 1];
                fields.forEach(f => {
                    if (!(f.k in item)) return; // key not supplied -> leave existing value as-is
                    const input = row.querySelector(`[name$="[${f.k}]"]`);
                    if (input) input.value = item[f.k] ?? '';
                });
            });
            return {
                skipped
            };
        }

        function psReadSectionFields(section) {
            const obj = {};
            section.querySelectorAll('[name]').forEach(el => {
                const name = el.getAttribute('name');
                if (!name || name.includes('[')) return;
                if (el.type === 'file') return;
                if (el.id === 'editor1' || el.id === 'editor') {
                    obj[name] = (window.CKEDITOR && CKEDITOR.instances[el.id]) ? CKEDITOR.instances[el.id]
                        .getData() : el.value;
                } else if (el.tagName === 'SELECT' && el.multiple) {
                    const selected = Array.from(el.selectedOptions).map(o => o.value);
                    obj[name] = selected.length ? selected : ['<' + name.slice(0, -1) + '_id>'];
                } else {
                    obj[name] = el.value || '';
                }
            });
            return obj;
        }

        // Builds a downloadable JSON template — whole form, or just the scoped section if psImportScope is set
        function psDownloadTemplate() {
            if (psImportScope) {
                const section = psImportScope;
                const wrapper = section.querySelector('.repeater');
                const prefix = wrapper ? wrapper.dataset.prefix : (section.id || 'section');
                if (wrapper) {
                    const fields = JSON.parse(wrapper.dataset.fields);
                    const rows = Array.from(wrapper.querySelectorAll('.repeater-item'));
                    const items = rows.length ? rows.map((row, i) => {
                        const obj = {
                            _row: i + 1
                        };
                        fields.forEach(f => {
                            const input = row.querySelector(`[name$="[${f.k}]"]`);
                            obj[f.k] = input ? input.value : '';
                        });
                        return obj;
                    }) : [(() => {
                        const sample = {};
                        fields.forEach(f => sample[f.k] = '');
                        return sample;
                    })()];
                    psDownloadJson(items, `${prefix}-template.json`);
                } else {
                    psDownloadJson(psReadSectionFields(section), `${prefix}-template.json`);
                }
                return;
            }

            const template = {};

            document.querySelectorAll('.premium-section-body [name]').forEach(el => {
                const name = el.getAttribute('name');
                if (!name || name.endsWith('[]') || name.includes('['))
                    return; // skip multi-selects & repeater rows
                if (el.type === 'file') return;
                if (template[name] !== undefined) return;
                if (el.id === 'editor1' || el.id === 'editor') {
                    template[name] = (window.CKEDITOR && CKEDITOR.instances[el.id]) ? CKEDITOR.instances[el.id]
                        .getData() : el.value;
                } else {
                    template[name] = el.value || '';
                }
            });

            ['technologies', 'portfolios'].forEach(key => {
                const el = document.querySelector(`#${key}`);
                if (!el) return;
                const selected = Array.from(el.selectedOptions).map(o => o.value);
                template[key] = selected.length ? selected : ['<' + key.slice(0, -1) + '_id>'];
            });

            document.querySelectorAll('.repeater').forEach(wrapper => {
                const prefix = wrapper.dataset.prefix;
                const fields = JSON.parse(wrapper.dataset.fields);
                const rows = Array.from(wrapper.querySelectorAll('.repeater-item'));
                if (rows.length) {
                    template[prefix] = rows.map(row => {
                        const obj = {};
                        fields.forEach(f => {
                            const input = row.querySelector(`[name$="[${f.k}]"]`);
                            obj[f.k] = input ? input.value : '';
                        });
                        return obj;
                    });
                } else {
                    const sample = {};
                    fields.forEach(f => sample[f.k] = '');
                    template[prefix] = [sample];
                }
            });

            psDownloadJson(template, 'subservice-template.json');
        }

        // Applies plain-field values (text/select/tagin/ckeditor) from a flat object onto a given root element
        function psApplyPlainFields(root, data) {
            Object.keys(data).forEach(key => {
                if (PS_SPECIAL_KEYS.includes(key)) return;
                const el = root.querySelector(`[name="${key}"]`);
                if (!el) return;
                const value = data[key];

                if (el.id === 'editor1' || el.id === 'editor') {
                    if (window.CKEDITOR && CKEDITOR.instances[el.id]) {
                        CKEDITOR.instances[el.id].setData(value || '');
                    } else {
                        el.value = value || '';
                    }
                } else if (el.classList.contains('tagin')) {
                    el.value = Array.isArray(value) ? value.join(',') : (value || '');
                } else if (el.tagName === 'SELECT' && el.multiple) {
                    const wanted = (Array.isArray(value) ? value : [value]).map(String);
                    Array.from(el.options).forEach(opt => opt.selected = wanted.includes(opt.value));
                    psInitChoices();
                } else if (el.tagName === 'SELECT') {
                    el.value = value;
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                    if (window.jQuery) jQuery(el).trigger('change');
                } else {
                    el.value = value ?? '';
                }
            });
        }

        function psApplyImport() {
            const raw = document.getElementById('psImportTextarea').value.trim();
            if (!raw) {
                psShowImportError('আগে JSON পেস্ট করুন।');
                return;
            }

            let data;
            try {
                data = JSON.parse(raw);
            } catch (e) {
                psShowImportError('JSON ফরম্যাট ঠিক নেই: ' + e.message);
                return;
            }

            document.getElementById('psImportError').style.display = 'none';

            // ---- Section-scoped import ----
            if (psImportScope) {
                const section = psImportScope;
                const wrapper = section.querySelector('.repeater');

                if (wrapper) {
                    if (!Array.isArray(data)) {
                        psShowImportError('এই সেকশনের জন্য JSON একটা অ্যারে [ ] আকারে দিতে হবে, object নয়।');
                        return;
                    }
                    const mode = psGetRepeaterMode();
                    let warnMsg = '';
                    if (mode === 'update') {
                        const {
                            skipped
                        } = psUpdateRepeaterRows(wrapper, data);
                        if (skipped.length) {
                            warnMsg = `\n⚠️ এই _row নাম্বার(গুলো) পাওয়া যায়নি তাই স্কিপ হয়েছে: ${skipped.join(', ')}`;
                        }
                    } else {
                        psFillRepeater(wrapper, data, mode);
                    }
                    psOpenSection(section);
                    psCloseImport();
                    alert('সেকশনের ডাটা বসানো হয়েছে ✅ চেক করে Save চাপুন।' + warnMsg);
                    return;
                } else {
                    if (Array.isArray(data)) {
                        psShowImportError('এই সেকশনের জন্য JSON একটা object { } আকারে দিতে হবে, অ্যারে নয়।');
                        return;
                    }
                    psApplyPlainFields(section, data);
                    psInitTagin();
                }

                psOpenSection(section);
                psCloseImport();
                alert('সেকশনের ডাটা বসানো হয়েছে ✅ চেক করে Save চাপুন।');
                return;
            }

            // ---- Whole-form import ----
            const repeaterWrappers = Array.from(document.querySelectorAll('.repeater'));
            const repeaterPrefixes = repeaterWrappers.map(w => w.dataset.prefix);

            const plainData = {};
            Object.keys(data).forEach(key => {
                if (!repeaterPrefixes.includes(key)) plainData[key] = data[key];
            });
            psApplyPlainFields(document, plainData);
            psInitTagin();

            repeaterWrappers.forEach(wrapper => {
                const prefix = wrapper.dataset.prefix;
                if (data[prefix]) psFillRepeater(wrapper, data[prefix]);
            });

            psToggleAll(true);
            psCloseImport();
            alert('ডাটা ফর্মে বসানো হয়েছে ✅\nএখন থাম্বনেইল ইমেজ সিলেক্ট করুন, সবকিছু চেক করে Save চাপুন।');
        }

        // ---- Inject a small "JSON" button into every section header for per-section import ----
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.premium-section').forEach(section => {
                const head = section.querySelector('.premium-section-head');
                const chevron = head ? head.querySelector('.premium-chevron') : null;
                if (!head || !chevron) return;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.title = 'শুধু এই সেকশনের জন্য JSON দিয়ে পূরণ করুন';
                btn.innerHTML = '<i class="fa fa-code"></i>';
                btn.style.cssText =
                    'border:1px solid var(--ps-line); background:#fff; color:var(--ps-primary-dark); width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:13px; margin-right:8px; flex:none;';
                btn.onclick = function(e) {
                    e.stopPropagation();
                    psOpenSectionImport(section);
                };
                head.insertBefore(btn, chevron);
            });
        });

        // ---- Accordion toggle for premium-section blocks ----
        function psToggleSection(headEl) {
            const section = headEl.closest('.premium-section');
            const body = section.querySelector('.premium-section-body');
            const collapsed = section.classList.toggle('ps-collapsed');
            body.classList.toggle('ps-collapsed', collapsed);
            psUpdateProgress();
        }

        function psToggleAll(open) {
            document.querySelectorAll('.premium-section').forEach(section => {
                const body = section.querySelector('.premium-section-body');
                section.classList.toggle('ps-collapsed', !open);
                if (body) body.classList.toggle('ps-collapsed', !open);
            });
            psUpdateProgress();
        }

        function psUpdateProgress() {
            const all = document.querySelectorAll('.premium-section');
            const openCount = document.querySelectorAll('.premium-section:not(.ps-collapsed)').length;
            const fill = document.getElementById('psProgressFill');
            const label = document.getElementById('psProgressLabel');
            if (fill && label && all.length) {
                fill.style.width = (openCount / all.length * 100) + '%';
                label.textContent = openCount + ' / ' + all.length + ' sections open';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            psUpdateProgress();

            document.querySelectorAll('#psQuickNav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (!target) return;
                    if (target.classList.contains('ps-collapsed')) {
                        const head = target.querySelector('.premium-section-head');
                        if (head) psToggleSection(head);
                    }
                    setTimeout(() => target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    }), 50);
                });
            });

            const navLinks = Array.from(document.querySelectorAll('#psQuickNav a'));
            const sections = navLinks.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

            function onScroll() {
                let currentIndex = 0;
                const scrollPos = window.scrollY + 110;
                sections.forEach((sec, idx) => {
                    if (sec.offsetTop <= scrollPos) currentIndex = idx;
                });
                navLinks.forEach(l => l.classList.remove('active'));
                if (navLinks[currentIndex]) navLinks[currentIndex].classList.add('active');
            }

            window.addEventListener('scroll', onScroll, {
                passive: true
            });
            onScroll();
        });
    </script>
@endsection
