@extends('admin.layouts.master')
@section('content')
    <style>
        .ts-hero {
            background: linear-gradient(135deg, #1e2233 0%, #14172a 100%);
            border-radius: 14px;
            padding: 28px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
            margin-bottom: 22px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ts-hero h4 {
            margin: 0 0 6px 0;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .ts-hero p {
            margin: 0;
            color: rgba(255, 255, 255, .65);
            font-size: .92rem;
        }

        .ts-progress-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .ts-progress-bar {
            flex: 1;
            min-width: 220px;
            height: 8px;
            background: #eceef4;
            border-radius: 999px;
            overflow: hidden;
        }

        .ts-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #5b6cf9, #7b5bf9);
            border-radius: 999px;
            transition: width .35s ease;
        }

        .ts-progress-label {
            font-size: .88rem;
            color: #6b7280;
            white-space: nowrap;
        }

        .ts-btn-ghost {
            border: 1px solid #e2e4ec;
            background: #fff;
            color: #444;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .ts-btn-ghost:hover {
            background: #f4f5f9;
        }

        .ts-btn-primary-solid {
            border: none;
            background: #5b52f9;
            color: #fff;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .ts-btn-primary-solid:hover {
            background: #4741e0;
        }

        .ts-section {
            background: #fff;
            border: 1px solid #eef0f5;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
            overflow: hidden;
        }

        .ts-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            cursor: pointer;
            user-select: none;
        }

        .ts-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ts-icon-box svg {
            width: 20px;
            height: 20px;
        }

        .bg-indigo {
            background: #eceafd;
            color: #6c5ce7;
        }

        .bg-teal {
            background: #dff7f2;
            color: #17a897;
        }

        .bg-amber {
            background: #fdf0da;
            color: #d9922f;
        }

        .bg-purple {
            background: #f1e7fb;
            color: #9b59d0;
        }

        .bg-blue {
            background: #e6eefc;
            color: #3f7fe0;
        }

        .bg-pink {
            background: #fde7ef;
            color: #e0508a;
        }

        .bg-green {
            background: #e5f7ec;
            color: #2fa965;
        }

        .ts-section-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #1f2430;
        }

        .ts-section-subtitle {
            margin: 2px 0 0 0;
            font-size: .82rem;
            color: #8b90a0;
        }

        .ts-chevron {
            margin-left: auto;
            transition: transform .25s ease;
            color: #9aa0b0;
            flex-shrink: 0;
        }

        .ts-section.open .ts-chevron {
            transform: rotate(180deg);
        }

        .ts-section-collapse {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .3s ease;
        }

        .ts-section.open .ts-section-collapse {
            grid-template-rows: 1fr;
        }

        .ts-section-collapse-inner {
            overflow: hidden;
        }

        .ts-section-body {
            padding: 4px 22px 22px 22px;
            border-top: 1px solid #f1f2f6;
        }

        .ts-repeat-row {
            border: 1px dashed #e3e5ee;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fbfbfd;
        }

        .ts-sticky-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #eef0f5;
            padding: 14px 20px;
            border-radius: 0 0 12px 12px;
            display: flex;
            justify-content: flex-end;
            margin-top: -1px;
        }
    </style>

    <div class="container-fluid">
        @include('admin.inc.breadcrumb')

        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        {{-- Hero header --}}
        <div class="ts-hero">
            <div>
                <h4>{{ __('dashboard.add') }} {{ $title ?? 'Technology' }}</h4>
                <p>Fill in every section below to publish a new sub-service</p>
            </div>
        </div>

        <form action="{{ route('admin.technologies.store') }}" method="POST" enctype="multipart/form-data"
            class="needs-validation" id="techForm" novalidate>
            @csrf

            {{-- Progress + controls --}}
            <div class="ts-progress-wrap">
                <div class="ts-progress-bar">
                    <div class="ts-progress-fill" id="tsProgressFill"></div>
                </div>
                <span class="ts-progress-label" id="tsProgressLabel">0 / 8 sections open</span>
                <button type="button" class="ts-btn-ghost" id="tsExpandAll">&#x2922; Expand all</button>
                <button type="button" class="ts-btn-ghost" id="tsCollapseAll">&#x2921; Collapse all</button>
            </div>

            {{-- 1. Basic Information --}}
            <div class="ts-section open" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-indigo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" stroke-dasharray="3 3" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Basic Information</h5>
                        <p class="ts-section-subtitle">Core details clients see first</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="form-group">
                                <label>{{ __('dashboard.select_service_id') }}</label>
                                <select name="service_id" class="wide form-control">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Banner Title <span>*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                    required>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Slug <span>*</span></label>
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}"
                                        required>
                                </div>
                                <div class="form-group col-6">
                                    <label>Short Title <span>*</span></label>
                                    <input type="text" name="short_title" class="form-control"
                                        value="{{ old('short_title') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Banner Description <span>*</span></label>
                                <textarea name="description" class="form-control" id="editor1" rows="5">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Toggle Content --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="10" rx="5" />
                            <circle cx="8" cy="12" r="3" fill="currentColor" stroke="none" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Toggle Content</h5>
                        <p class="ts-section-subtitle">Why choose this technology</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Toggle Title <span>*</span></label>
                                    <input type="text" name="toggle_title" class="form-control"
                                        value="{{ old('toggle_title') }}" required>
                                </div>
                                <div class="form-group col-6">
                                    <label>Toggle Sub Title <span>*</span></label>
                                    <input type="text" name="toggle_sub_title" class="form-control"
                                        value="{{ old('toggle_sub_title') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Tech Steps --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="4" y1="6" x2="20" y2="6" />
                            <line x1="4" y1="12" x2="20" y2="12" />
                            <line x1="4" y1="18" x2="20" y2="18" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Tech Steps</h5>
                        <p class="ts-section-subtitle">Process steps shown on the page</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="process-row-toggle">
                                <div class="ts-repeat-row">
                                    <input type="text" name="tech[0][tech_title]" class="form-control mb-2"
                                        placeholder="Title">
                                    <textarea name="tech[0][tech_description]" class="form-control" placeholder="Description"></textarea>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" onclick="addProcess()">+ Add
                                Tech</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Expertise Section --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-purple">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Expertise Section</h5>
                        <p class="ts-section-subtitle">Logos / links that showcase expertise</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="process-row-expertise">
                                <div class="ts-repeat-row">
                                    <input type="text" name="expertise[0][expertise_url]" class="form-control mb-2"
                                        placeholder="Url">
                                    <input type="file" name="expertise[0][expertise_image]" class="form-control mb-2">
                                    <label>Remove Background?</label>
                                    <select name="expertise[0][remove_bg]" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" onclick="addExpertise()">+ Add
                                Expertise</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. SEO & Meta --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" stroke-dasharray="3 3" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">SEO &amp; Meta</h5>
                        <p class="ts-section-subtitle">Controls how this page appears in search results</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="form-group">
                                <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title"
                                    value="{{ old('meta_title') }}" required>
                                <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                    {{ __('dashboard.meta_title') }}</div>
                            </div>
                            <div class="form-group">
                                <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                                <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ old('short_desc') }}</textarea>
                                <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                    {{ __('dashboard.meta_description') }}</div>
                            </div>
                            <div class="form-group">
                                <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                <input type="text" class="form-control tagin" data-tagin-separator=" "
                                    name="keywords" value="{{ old('keywords') }}" required>
                                <div class="invalid-feedback">{{ __('dashboard.please_provide') }}
                                    {{ __('dashboard.meta_keywords') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. Media --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="16" rx="2" />
                            <circle cx="8.5" cy="9.5" r="1.5" fill="currentColor" stroke="none" />
                            <path d="M21 15l-5-5-9 9" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Media</h5>
                        <p class="ts-section-subtitle">CTA image &amp; logo used on the live page</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>CTA Image <span>*</span></label>
                                    <input type="file" name="image" class="form-control" required>
                                    <label>Remove Background?</label>
                                    <select name="remove_bg_image" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label>Logo</label>
                                    <input type="file" name="logo" class="form-control">
                                    <label>Remove Background?</label>
                                    <select name="remove_bg_logo" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. Pricing & Ratings --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M20.59 13.41L11 3.83V3H4v7l.83.83L14.41 20.6a2 2 0 0 0 2.83 0l3.35-3.35a2 2 0 0 0 0-2.84z" />
                            <circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Pricing &amp; Ratings</h5>
                        <p class="ts-section-subtitle">Numbers shown on the pricing widget</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="row">
                                <div class="form-group col-3">
                                    <label>Price <span>*</span></label>
                                    <input type="number" name="price" class="form-control" value="499">
                                </div>
                                <div class="form-group col-3">
                                    <label>Starting Price <span>*</span></label>
                                    <input type="number" name="starting_price" class="form-control" value="499">
                                </div>
                                <div class="form-group col-3">
                                    <label>Review Count <span>*</span></label>
                                    <input type="number" name="review_count" class="form-control" value="150">
                                </div>
                                <div class="form-group col-3">
                                    <label>Average Rating <span>*</span></label>
                                    <input type="text" name="average_rating" class="form-control" value="4.9">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Price Currency <span>*</span></label>
                                <input type="text" name="priceCurrency" class="form-control" value="USD">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 8. Visibility & Status --}}
            <div class="ts-section" data-section>
                <div class="ts-section-header" data-toggle-section>
                    <div class="ts-icon-box bg-amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="ts-section-title">Visibility &amp; Status</h5>
                        <p class="ts-section-subtitle">Controls where and whether this shows publicly</p>
                    </div>
                    <span class="ts-chevron">&#9660;</span>
                </div>
                <div class="ts-section-collapse">
                    <div class="ts-section-collapse-inner">
                        <div class="ts-section-body">
                            <div class="form-group">
                                <label>Manu</label>
                                <select name="manu" class="form-control">
                                    <option value="0">Hidden</option>
                                    <option value="1">Show</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ts-sticky-footer">
                <button type="submit" class="ts-btn-primary-solid">{{ __('dashboard.save') }}</button>
            </div>
        </form>
    </div>

    {{-- JS: accordion + dynamic rows + editors --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const taginInputs = document.querySelectorAll(".tagin");
            taginInputs.forEach(input => new Tagin(input, {
                separator: ',',
                duplicate: false,
                enter: true,
                maxTags: 100
            }));

            CKEDITOR.replace('editor1');
            CKEDITOR.replace('editor');

            initAccordion();
        });

        // ---------- Accordion ----------
        function updateProgress() {
            const sections = document.querySelectorAll('[data-section]');
            const openCount = document.querySelectorAll('[data-section].open').length;
            const total = sections.length;
            document.getElementById('tsProgressLabel').textContent = `${openCount} / ${total} sections open`;
            document.getElementById('tsProgressFill').style.width = total ? `${(openCount / total) * 100}%` : '0%';
        }

        function initAccordion() {
            document.querySelectorAll('[data-toggle-section]').forEach(header => {
                header.addEventListener('click', () => {
                    header.closest('[data-section]').classList.toggle('open');
                    updateProgress();
                });
            });
            document.getElementById('tsExpandAll').addEventListener('click', () => {
                document.querySelectorAll('[data-section]').forEach(s => s.classList.add('open'));
                updateProgress();
            });
            document.getElementById('tsCollapseAll').addEventListener('click', () => {
                document.querySelectorAll('[data-section]').forEach(s => s.classList.remove('open'));
                updateProgress();
            });
            updateProgress();
        }

        // ---------- Dynamic rows ----------
        let processIndex = 1;
        let expertiseIndex = 1;

        function initCKEditorDynamic(id) {
            if (window.CKEDITOR) {
                if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
                CKEDITOR.replace(id, {
                    removeButtons: '',
                    height: 100
                });
            }
        }

        function addProcess() {
            const container = document.querySelector('.process-row-toggle');
            const editorId = `tech_editor_${processIndex}`;
            const html = `
            <div class="ts-repeat-row">
                <input type="text" name="tech[${processIndex}][tech_title]" class="form-control mb-2" placeholder="Title">
                <textarea id="${editorId}" name="tech[${processIndex}][tech_description]" class="form-control" placeholder="Description"></textarea>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
            initCKEditorDynamic(editorId);
            processIndex++;
        }

        function addExpertise() {
            const container = document.querySelector('.process-row-expertise');
            const editorId = `expertise_editor_${expertiseIndex}`;
            const html = `
            <div class="ts-repeat-row">
                <input type="text" name="expertise[${expertiseIndex}][expertise_url]" class="form-control mb-2" placeholder="Url">
                <input type="file" name="expertise[${expertiseIndex}][expertise_image]" class="form-control mb-2">
                <label>Remove Background?</label>
                <select name="expertise[${expertiseIndex}][remove_bg]" class="form-control">
                    <option value="no" selected>No</option>
                    <option value="yes">Yes</option>
                </select>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
            initCKEditorDynamic(editorId);
            expertiseIndex++;
        }
    </script>
@endsection
