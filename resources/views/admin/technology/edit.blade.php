@extends('admin.layouts.master')

@section('content')
    @php
        $tech = json_decode($subservice->tech_steps, true) ?: [];
        $expertise = json_decode($subservice->expertise_steps, true) ?: [];
        $sectionCount = 7; // total collapsible sections below
    @endphp

    <div class="container-fluid tech-editor-wrap">
        @include('admin.inc.breadcrumb')

        <div class="row mb-2">
            <div class="col-12">
                <a href="{{ route('admin.technologies.index') }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> {{ __('dashboard.back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <form class="needs-validation te-form" novalidate
                    action="{{ route('admin.technologies.update', $subservice->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ===== Hero Header ===== --}}
                    <div class="te-hero">
                        <div>
                            <h2>{{ $subservice->title ?? 'Technology / Sub Service' }}</h2>
                            <p>Editing sub-service &mdash; keep every section up to date before publishing</p>
                        </div>
                        <button type="button" class="te-hero-btn"><i class="fa fa-pen"></i> Sub Service Editor</button>
                    </div>

                    {{-- ===== Progress / Toolbar ===== --}}
                    <div class="te-toolbar">
                        <div class="te-progress">
                            <div class="te-progress-bar">
                                <span id="teProgressFill"></span>
                            </div>
                            <small><span id="teOpenCount">0</span> / {{ $sectionCount }} sections open</small>
                        </div>
                        <div class="te-toolbar-actions">
                            <button type="button" class="te-chip te-chip-accent" onclick="teExpandAll()">
                                <i class="fa fa-expand"></i> Expand all
                            </button>
                            <button type="button" class="te-chip" onclick="teCollapseAll()">
                                <i class="fa fa-compress"></i> Collapse all
                            </button>
                        </div>
                    </div>

                    {{-- ===== 1. Basic Information ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-blue"><i class="fa fa-circle-info"></i></span>
                                <div>
                                    <h4>Basic Information</h4>
                                    <small>Core details clients see first</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="form-group">
                                <label for="service_id">{{ __('dashboard.select_service_id') }}</label>
                                <select class="wide form-control te-select" name="service_id" id="service_id"
                                    data-plugin="customselect">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            @if ($subservice->service_id == $service->id) selected @endif>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="title">{{ __('Banner Title') }} <span class="req">*</span></label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $subservice->title }}" required>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="slug">{{ __('dashboard.slug') }}</label>
                                    <input type="text" class="form-control" name="slug" id="slug"
                                        value="{{ $subservice->slug }}" readonly required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="short_title">{{ __('dashboard.short_title') }} <span
                                            class="req">*</span></label>
                                    <input type="text" class="form-control" name="short_title" id="short_title"
                                        value="{{ $subservice->short_title }}" required>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label for="editor1">{{ __('Banner Description') }} <span class="req">*</span></label>
                                <textarea class="form-control" name="description" id="editor1" rows="8" required>{!! $subservice->description !!}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ===== 2. Toggle Content ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-teal"><i class="fa fa-toggle-on"></i></span>
                                <div>
                                    <h4>Toggle Content</h4>
                                    <small>{{ $subservice->toggle_title ?? 'Expandable tech/process items shown on the page' }}</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Toggle Title <span class="req">*</span></label>
                                    <input type="text" name="toggle_title" class="form-control"
                                        value="{{ $subservice->toggle_title }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Toggle Sub Title <span class="req">*</span></label>
                                    <input type="text" name="toggle_sub_title" class="form-control"
                                        value="{{ $subservice->toggle_sub_title }}" required>
                                </div>
                            </div>

                            <div class="te-subhead">
                                <span>Toggle Items</span>
                            </div>

                            <div class="process-row-toggle">
                                @foreach ($tech as $index => $item)
                                    <div class="te-repeat-item">
                                        <div class="te-repeat-item-head">
                                            <span class="te-badge">#{{ $index + 1 }}</span>
                                            <button type="button" class="te-remove-btn"
                                                onclick="this.closest('.te-repeat-item').remove()">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="text" value="{{ $item['tech_title'] }}"
                                            class="form-control mb-2" name="tech[{{ $index }}][tech_title]"
                                            placeholder="Title">
                                        <textarea id="editor_toggle_{{ $index }}" class="form-control"
                                            name="tech[{{ $index }}][tech_description]" placeholder="Description">{!! $item['tech_description'] !!}</textarea>
                                    </div>
                                @endforeach
                            </div>
                            <button class="te-add-btn" type="button" onclick="addProcess()">
                                <i class="fa fa-plus"></i> Add Toggle Item
                            </button>
                        </div>
                    </div>

                    {{-- ===== 3. Expertise Section ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-purple"><i class="fa fa-star"></i></span>
                                <div>
                                    <h4>Expertise Section</h4>
                                    <small>Logos / links that showcase expertise</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="process-row-expertise te-expertise-grid">
                                @foreach ($expertise as $index => $item)
                                    <div class="te-repeat-item">
                                        <div class="te-repeat-item-head">
                                            <span class="te-badge">#{{ $index + 1 }}</span>
                                            <button type="button" class="te-remove-btn"
                                                onclick="this.closest('.te-repeat-item').remove()">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="text" value="{{ $item['expertise_url'] }}"
                                            class="form-control mb-2"
                                            name="expertise[{{ $index }}][expertise_url]" placeholder="Url">
                                        @if (!empty($item['expertise_image']))
                                            <div class="te-thumb">
                                                <img src="{{ asset('uploads/' . $path . '/' . $item['expertise_image']) }}"
                                                    alt="Expertise Image">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control mb-2"
                                            name="expertise[{{ $index }}][expertise_image]">
                                        <label class="te-checkbox">
                                            <input type="checkbox" name="expertise[{{ $index }}][remove_bg]"
                                                value="yes">
                                            Remove Background
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <button class="te-add-btn" type="button" onclick="addExpertise()">
                                <i class="fa fa-plus"></i> Add Expertise Item
                            </button>
                        </div>
                    </div>

                    {{-- ===== 4. SEO & Meta ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-indigo"><i class="fa fa-magnifying-glass"></i></span>
                                <div>
                                    <h4>SEO &amp; Meta</h4>
                                    <small>Controls how this page appears in search results</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="form-group">
                                <label for="meta_title">{{ __('dashboard.meta_title') }} <span
                                        class="req">*</span></label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title"
                                    value="{{ $subservice->meta_title }}" required>
                            </div>

                            <div class="form-group">
                                <label for="editor2">{{ __('dashboard.meta_description') }} <span
                                        class="req">*</span></label>
                                <textarea class="form-control" name="short_desc" id="editor2" rows="4" required>{!! $subservice->short_desc !!}</textarea>
                            </div>

                            <div class="form-group mb-0">
                                <label for="keywords">{{ __('dashboard.meta_keywords') }} <span
                                        class="req">*</span></label>
                                <input type="text" class="form-control tagin" data-tagin-separator=" "
                                    name="keywords" value="{{ $subservice->keywords ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- ===== 5. Media ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-pink"><i class="fa fa-image"></i></span>
                                <div>
                                    <h4>Media</h4>
                                    <small>CTA image &amp; logo used on the live page</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="image">{{ __('CTA Image') }}</label>
                                    @if (!empty($subservice->image_path))
                                        <div class="te-thumb">
                                            <img src="{{ asset('uploads/' . $path . '/' . $subservice->image_path) }}"
                                                alt="CTA Image">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control mb-2" name="image" id="image">
                                    <label class="te-checkbox">
                                        <input type="checkbox" name="image_remove_bg" value="yes"> Remove Background
                                    </label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="logo">{{ __('dashboard.logo') }}</label>
                                    @if (!empty($subservice->logo_path))
                                        <div class="te-thumb">
                                            <img src="{{ asset('uploads/' . $path . '/' . $subservice->logo_path) }}"
                                                alt="Logo">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control mb-2" name="logo" id="logo">
                                    <label class="te-checkbox">
                                        <input type="checkbox" name="logo_remove_bg" value="yes"> Remove Background
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== 6. Pricing & Ratings ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-green"><i class="fa fa-tag"></i></span>
                                <div>
                                    <h4>Pricing &amp; Ratings</h4>
                                    <small>Numbers shown on the pricing widget</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="row">
                                <div class="form-group col-md-4 col-lg-2">
                                    <label for="price">{{ __('dashboard.price') }}</label>
                                    <input type="number" class="form-control" name="price" id="price"
                                        value="{{ $subservice->price }}">
                                </div>
                                <div class="form-group col-md-4 col-lg-2">
                                    <label for="starting_price">{{ __('dashboard.starting_price') }}</label>
                                    <input type="number" class="form-control" name="starting_price" id="starting_price"
                                        value="{{ $subservice->starting_price }}">
                                </div>
                                <div class="form-group col-md-4 col-lg-2">
                                    <label for="review_count">{{ __('dashboard.review_count') }}</label>
                                    <input type="number" class="form-control" name="review_count" id="review_count"
                                        value="{{ $subservice->review_count }}">
                                </div>
                                <div class="form-group col-md-4 col-lg-3">
                                    <label for="priceCurrency">{{ __('dashboard.priceCurrency') }}</label>
                                    <input type="text" class="form-control" name="priceCurrency" id="priceCurrency"
                                        value="{{ $subservice->priceCurrency }}">
                                </div>
                                <div class="form-group col-md-4 col-lg-3">
                                    <label for="average_rating">{{ __('dashboard.average_rating') }}</label>
                                    <input type="text" class="form-control" name="average_rating" id="average_rating"
                                        value="{{ $subservice->average_rating }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== 7. Visibility & Status ===== --}}
                    <div class="te-card" data-te-section>
                        <div class="te-card-head" onclick="teToggle(this)">
                            <div class="te-card-head-left">
                                <span class="te-icon te-icon-amber"><i class="fa fa-eye"></i></span>
                                <div>
                                    <h4>Visibility &amp; Status</h4>
                                    <small>Controls where and whether this shows publicly</small>
                                </div>
                            </div>
                            <i class="fa fa-chevron-down te-chevron"></i>
                        </div>
                        <div class="te-card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="manu">Manu</label>
                                    <select class="wide form-control te-select" name="manu" id="manu"
                                        data-plugin="customselect">
                                        <option value="0" @if ($subservice->manu == 0) selected @endif>Hidden
                                        </option>
                                        <option value="1" @if ($subservice->manu == 1) selected @endif>Show
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">{{ __('dashboard.select_status') }}</label>
                                    <select class="wide form-control te-select" name="status" id="status"
                                        data-plugin="customselect">
                                        <option value="1" @if ($subservice->status == 1) selected @endif>
                                            {{ __('dashboard.active') }}
                                        </option>
                                        <option value="0" @if ($subservice->status == 0) selected @endif>
                                            {{ __('dashboard.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== Sticky Footer ===== --}}
                    <div class="te-footer">
                        <button type="submit" class="te-submit-btn">
                            <i class="fa fa-check"></i> {{ __('dashboard.update') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <style>
        .tech-editor-wrap {
            --te-accent: #4f5bd5;
            --te-accent-dark: #1b1f3b;
            --te-bg: #f4f6fb;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef0fb;
            color: var(--te-accent-dark);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: background .15s ease;
        }

        .btn-back:hover {
            background: #dfe2f7;
            color: var(--te-accent-dark);
            text-decoration: none;
        }

        /* Hero */
        .te-hero {
            background: linear-gradient(135deg, #1b1f3b 0%, #2a2f57 100%);
            border-radius: 14px;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(27, 31, 59, .18);
        }

        .te-hero h2 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .te-hero p {
            color: #b7bbe0;
            font-size: 13px;
            margin: 0;
        }

        .te-hero-btn {
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .25);
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .te-hero-btn:hover {
            background: rgba(255, 255, 255, .2);
        }

        /* Toolbar */
        .te-toolbar {
            background: #fff;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(20, 20, 50, .05);
        }

        .te-progress {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 220px;
        }

        .te-progress-bar {
            flex: 1;
            max-width: 260px;
            height: 6px;
            background: #e7e9f5;
            border-radius: 6px;
            overflow: hidden;
        }

        .te-progress-bar span {
            display: block;
            height: 100%;
            width: 0%;
            background: var(--te-accent);
            transition: width .25s ease;
        }

        .te-progress small {
            color: #8b8fb0;
            font-size: 12.5px;
            white-space: nowrap;
        }

        .te-toolbar-actions {
            display: flex;
            gap: 8px;
        }

        .te-chip {
            background: #eef0fb;
            color: var(--te-accent-dark);
            border: none;
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 14px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .te-chip:hover {
            background: #dfe2f7;
        }

        .te-chip-accent {
            background: var(--te-accent);
            color: #fff;
        }

        .te-chip-accent:hover {
            background: #3f4ac4;
        }

        /* Cards */
        .te-card {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(20, 20, 50, .05);
            border: 1px solid #eef0f7;
        }

        .te-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            cursor: pointer;
            user-select: none;
        }

        .te-card-head-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .te-card-head h4 {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            color: #22254a;
        }

        .te-card-head small {
            color: #9296b3;
            font-size: 12.5px;
        }

        .te-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .te-icon-blue {
            background: #e8ecff;
            color: #4f5bd5;
        }

        .te-icon-teal {
            background: #e2f7f3;
            color: #17a897;
        }

        .te-icon-purple {
            background: #f1e8ff;
            color: #8b5cf6;
        }

        .te-icon-indigo {
            background: #e6ecff;
            color: #3b5bdb;
        }

        .te-icon-pink {
            background: #ffe7f1;
            color: #db3b8f;
        }

        .te-icon-green {
            background: #e4f8ea;
            color: #1fa15a;
        }

        .te-icon-amber {
            background: #fff2df;
            color: #d98416;
        }

        .te-chevron {
            color: #a7abc7;
            transition: transform .2s ease;
        }

        .te-card.open .te-chevron {
            transform: rotate(180deg);
        }

        .te-card-body {
            display: none;
            padding: 4px 20px 22px;
            border-top: 1px solid #f1f2f9;
        }

        .te-card.open .te-card-body {
            display: block;
            padding-top: 18px;
        }

        .te-card label {
            font-size: 13px;
            font-weight: 600;
            color: #4a4d6b;
            margin-bottom: 6px;
        }

        .te-card .req {
            color: #e0457b;
        }

        .te-card .form-control {
            border-radius: 8px;
            border: 1px solid #e3e5f1;
            font-size: 13.5px;
            padding: 9px 12px;
        }

        .te-card .form-control:focus {
            border-color: var(--te-accent);
            box-shadow: 0 0 0 3px rgba(79, 91, 213, .12);
        }

        .te-subhead {
            margin: 6px 0 12px;
            font-size: 13px;
            font-weight: 700;
            color: #4a4d6b;
        }

        .te-expertise-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 14px;
        }

        .te-repeat-item {
            background: var(--te-bg);
            border: 1px solid #eceef7;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .te-repeat-item-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .te-badge {
            background: #e8ecff;
            color: var(--te-accent);
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .te-remove-btn {
            background: #fdeaea;
            color: #d64545;
            border: none;
            border-radius: 6px;
            width: 26px;
            height: 26px;
            font-size: 12px;
        }

        .te-remove-btn:hover {
            background: #fbd6d6;
        }

        .te-thumb {
            margin-bottom: 8px;
        }

        .te-thumb img {
            height: 70px;
            border-radius: 8px;
            border: 1px solid #eee;
            object-fit: cover;
        }

        .te-checkbox {
            font-size: 12.5px;
            font-weight: 500;
            color: #6a6d8c;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .te-add-btn {
            background: #fff;
            border: 1.5px dashed #c7cbea;
            color: var(--te-accent);
            font-weight: 700;
            font-size: 13px;
            padding: 9px 16px;
            border-radius: 8px;
            width: 100%;
            margin-top: 4px;
        }

        .te-add-btn:hover {
            background: #f5f6ff;
            border-color: var(--te-accent);
        }

        /* Footer */
        .te-footer {
            display: flex;
            justify-content: flex-end;
            padding: 10px 4px 30px;
        }

        .te-submit-btn {
            background: var(--te-accent);
            color: #fff;
            border: none;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 30px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 16px rgba(79, 91, 213, .3);
        }

        .te-submit-btn:hover {
            background: #3f4ac4;
        }

        @media (max-width: 576px) {
            .te-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .te-hero-btn {
                align-self: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tagin init
            document.querySelectorAll(".tagin").forEach(input => new Tagin(input, {
                separator: ',',
                duplicate: false,
                enter: true,
                maxTags: 100
            }));

            // CKEditor static
            ["editor1", "editor2"].forEach(id => {
                if (document.getElementById(id)) initCKEditor(id);
            });

            // CKEditor DB-loaded toggles
            document.querySelectorAll("textarea[id^='editor_toggle_']").forEach(el => initCKEditor(el.id));

            // Open the first section by default and sync progress bar
            const firstCard = document.querySelector('.te-card');
            if (firstCard) firstCard.classList.add('open');
            teUpdateProgress();
        });

        function initCKEditor(id) {
            if (window.CKEDITOR) {
                if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
                CKEDITOR.replace(id, {
                    toolbar: [{
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList']
                        },
                        {
                            name: 'links',
                            items: ['Link', 'Unlink']
                        },
                        {
                            name: 'insert',
                            items: ['Image', 'Table']
                        },
                        {
                            name: 'document',
                            items: ['Source']
                        }
                    ]
                });
            }
        }

        // ===== Accordion controls =====
        function teToggle(headEl) {
            headEl.closest('.te-card').classList.toggle('open');
            teUpdateProgress();
        }

        function teExpandAll() {
            document.querySelectorAll('.te-card').forEach(c => c.classList.add('open'));
            teUpdateProgress();
        }

        function teCollapseAll() {
            document.querySelectorAll('.te-card').forEach(c => c.classList.remove('open'));
            teUpdateProgress();
        }

        function teUpdateProgress() {
            const total = document.querySelectorAll('.te-card').length;
            const open = document.querySelectorAll('.te-card.open').length;
            document.getElementById('teOpenCount').textContent = open;
            document.getElementById('teProgressFill').style.width = total ? ((open / total) * 100) + '%' : '0%';
        }

        // Counters
        let toggleIndex = document.querySelectorAll(".process-row-toggle textarea").length || 0;
        let expertiseIndex = document.querySelectorAll(".process-row-expertise input[type='text']").length || 0;

        // Add Toggle
        function addProcess() {
            const wrapper = document.querySelector('.process-row-toggle');
            const editorId = `editor_toggle_${toggleIndex}`;
            const group = document.createElement('div');
            group.className = 'te-repeat-item';
            group.innerHTML = `
                <div class="te-repeat-item-head">
                    <span class="te-badge">#${toggleIndex + 1}</span>
                    <button type="button" class="te-remove-btn" onclick="this.closest('.te-repeat-item').remove()"><i class="fa fa-trash"></i></button>
                </div>
                <input type="text" class="form-control mb-2" name="tech[${toggleIndex}][tech_title]" placeholder="Title">
                <textarea id="${editorId}" class="form-control" name="tech[${toggleIndex}][tech_description]" placeholder="Description"></textarea>
            `;
            wrapper.appendChild(group);
            setTimeout(() => initCKEditor(editorId), 100);
            toggleIndex++;
        }

        // Add Expertise
        function addExpertise() {
            const wrapper = document.querySelector('.process-row-expertise');
            const group = document.createElement('div');
            group.className = 'te-repeat-item';
            group.innerHTML = `
                <div class="te-repeat-item-head">
                    <span class="te-badge">#${expertiseIndex + 1}</span>
                    <button type="button" class="te-remove-btn" onclick="this.closest('.te-repeat-item').remove()"><i class="fa fa-trash"></i></button>
                </div>
                <input type="text" class="form-control mb-2" name="expertise[${expertiseIndex}][expertise_url]" placeholder="Url">
                <input type="file" class="form-control mb-2" name="expertise[${expertiseIndex}][expertise_image]">
                <label class="te-checkbox"><input type="checkbox" name="expertise[${expertiseIndex}][remove_bg]" value="yes"> Remove Background</label>
            `;
            wrapper.appendChild(group);
            expertiseIndex++;
        }
    </script>
@endsection
