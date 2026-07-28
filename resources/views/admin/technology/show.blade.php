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

        .ts-hero .ts-badge-status {
            font-size: .8rem;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
        }

        .ts-badge-active {
            background: #17c66422;
            color: #22c55e;
        }

        .ts-badge-inactive {
            background: #ef444422;
            color: #ef4444;
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
            text-decoration: none;
            display: inline-block;
        }

        .ts-btn-ghost:hover {
            background: #f4f5f9;
            color: #222;
            text-decoration: none;
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

        .ts-field {
            margin-bottom: 16px;
        }

        .ts-field-label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #9aa0b0;
            margin-bottom: 4px;
        }

        .ts-field-value {
            font-size: .96rem;
            color: #1f2430;
        }

        .ts-empty {
            color: #b7bac6;
            font-style: italic;
        }

        .ts-repeat-row {
            border: 1px dashed #e3e5ee;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fbfbfd;
        }

        .ts-thumb {
            max-width: 220px;
            border-radius: 8px;
            border: 1px solid #eef0f5;
            display: block;
            margin-top: 6px;
        }

        .ts-tag {
            display: inline-block;
            background: #eceafd;
            color: #6c5ce7;
            font-size: .8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            margin: 0 6px 6px 0;
        }
    </style>

    <div class="container-fluid">

        <div class="row my-3">
            <div class="col-12">
                <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        {{-- Hero header --}}
        <div class="ts-hero">
            <div>
                <h4>{{ __('dashboard.view') }} {{ $title ?? ($row->title ?? 'Technology') }}</h4>
                <p>Read-only preview of everything saved for this sub-service</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                @php $status = $row->status ?? $row->manu ?? null; @endphp
                @if (!is_null($status))
                    @if ((int) $status === 1)
                        <span class="ts-badge-status ts-badge-active">{{ __('dashboard.active') }}</span>
                    @else
                        <span class="ts-badge-status ts-badge-inactive">{{ __('dashboard.inactive') }}</span>
                    @endif
                @endif
                @isset($row->id)
                    <a href="{{ route('admin.technologies.edit', $row->id) }}" class="ts-btn-ghost">Edit</a>
                @endisset
            </div>
        </div>

        <div class="ts-progress-wrap" style="margin-bottom:16px;">
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
                        <div class="ts-field">
                            <span class="ts-field-label">Service</span>
                            <div class="ts-field-value">{{ optional($row->service ?? null)->title ?? '—' }}</div>
                        </div>
                        <div class="ts-field">
                            <span class="ts-field-label">Banner Title</span>
                            <div class="ts-field-value">{{ $row->title ?? '—' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">Slug</span>
                                <div class="ts-field-value">{{ $row->slug ?? '—' }}</div>
                            </div>
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">Short Title</span>
                                <div class="ts-field-value">{{ $row->short_title ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="ts-field">
                            <span class="ts-field-label">Banner Description</span>
                            @if (!empty($row->description))
                                <div class="ts-field-value">{!! $row->description !!}</div>
                            @else
                                <div class="ts-empty">Not provided</div>
                            @endif
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
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">Toggle Title</span>
                                <div class="ts-field-value">{{ $row->toggle_title ?? '—' }}</div>
                            </div>
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">Toggle Sub Title</span>
                                <div class="ts-field-value">{{ $row->toggle_sub_title ?? '—' }}</div>
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
                        @forelse (($row->techs ?? $row->tech ?? []) as $tech)
                            <div class="ts-repeat-row">
                                <div class="ts-field">
                                    <span class="ts-field-label">Title</span>
                                    <div class="ts-field-value">{{ $tech->tech_title ?? '—' }}</div>
                                </div>
                                @if (!empty($tech->tech_description ?? null))
                                    <div class="ts-field">
                                        <span class="ts-field-label">Description</span>
                                        <div class="ts-field-value">{!! $tech->tech_description !!}</div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="ts-empty">No tech steps added</div>
                        @endforelse
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
                        @forelse (($row->expertises ?? $row->expertise ?? []) as $expertise)
                            <div class="ts-repeat-row">
                                <div class="ts-field">
                                    <span class="ts-field-label">Url</span>
                                    <div class="ts-field-value">
                                        @if (!empty($expertise->expertise_url ?? null))
                                            <a href="{{ $expertise->expertise_url }}"
                                                target="_blank">{{ $expertise->expertise_url }}</a>
                                        @else
                                            <span class="ts-empty">—</span>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $expImg = $expertise->expertise_image ?? null;
                                    $expImgFullPath = $expImg ? 'uploads/' . ($path ?? '') . '/' . $expImg : null;
                                @endphp
                                @if ($expImg && $expImgFullPath && is_file($expImgFullPath))
                                    <img src="{{ asset($expImgFullPath) }}" class="ts-thumb" alt="expertise image">
                                @endif
                            </div>
                        @empty
                            <div class="ts-empty">No expertise items added</div>
                        @endforelse
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
                        <div class="ts-field">
                            <span class="ts-field-label">{{ __('dashboard.meta_title') }}</span>
                            <div class="ts-field-value">{{ $row->meta_title ?? '—' }}</div>
                        </div>
                        <div class="ts-field">
                            <span class="ts-field-label">{{ __('dashboard.meta_description') }}</span>
                            @if (!empty($row->short_desc))
                                <div class="ts-field-value">{!! $row->short_desc !!}</div>
                            @else
                                <div class="ts-empty">Not provided</div>
                            @endif
                        </div>
                        <div class="ts-field">
                            <span class="ts-field-label">{{ __('dashboard.meta_keywords') }}</span>
                            <div class="ts-field-value">
                                @php
                                    $keywords = $row->keywords ?? null;
                                    $keywordList = $keywords
                                        ? array_filter(array_map('trim', preg_split('/[,\s]+/', $keywords)))
                                        : [];
                                @endphp
                                @forelse ($keywordList as $keyword)
                                    <span class="ts-tag">{{ $keyword }}</span>
                                @empty
                                    <span class="ts-empty">—</span>
                                @endforelse
                            </div>
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
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">CTA Image</span>
                                @php
                                    $imagePath = $row->image_path ?? ($row->image ?? null);
                                    $imageFullPath = $imagePath ? 'uploads/' . ($path ?? '') . '/' . $imagePath : null;
                                @endphp
                                @if ($imagePath && $imageFullPath && is_file($imageFullPath))
                                    <img src="{{ asset($imageFullPath) }}" class="ts-thumb"
                                        alt="{{ $row->title ?? 'image' }}">
                                @else
                                    <div class="ts-empty">No image uploaded</div>
                                @endif
                            </div>
                            <div class="col-6 ts-field">
                                <span class="ts-field-label">Logo</span>
                                @php
                                    $logoPath = $row->logo_path ?? ($row->logo ?? null);
                                    $logoFullPath = $logoPath ? 'uploads/' . ($path ?? '') . '/' . $logoPath : null;
                                @endphp
                                @if ($logoPath && $logoFullPath && is_file($logoFullPath))
                                    <img src="{{ asset($logoFullPath) }}" class="ts-thumb" alt="logo">
                                @else
                                    <div class="ts-empty">No logo uploaded</div>
                                @endif
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
                            <div class="col-3 ts-field">
                                <span class="ts-field-label">Price</span>
                                <div class="ts-field-value">{{ $row->price ?? '—' }}</div>
                            </div>
                            <div class="col-3 ts-field">
                                <span class="ts-field-label">Starting Price</span>
                                <div class="ts-field-value">{{ $row->starting_price ?? '—' }}</div>
                            </div>
                            <div class="col-3 ts-field">
                                <span class="ts-field-label">Review Count</span>
                                <div class="ts-field-value">{{ $row->review_count ?? '—' }}</div>
                            </div>
                            <div class="col-3 ts-field">
                                <span class="ts-field-label">Average Rating</span>
                                <div class="ts-field-value">{{ $row->average_rating ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="ts-field">
                            <span class="ts-field-label">Price Currency</span>
                            <div class="ts-field-value">{{ $row->priceCurrency ?? '—' }}</div>
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
                        <div class="ts-field">
                            <span class="ts-field-label">Status</span>
                            <div class="ts-field-value">
                                @php $manu = $row->manu ?? null; @endphp
                                @if (!is_null($manu))
                                    {{ (int) $manu === 1 ? 'Show' : 'Hidden' }}
                                @else
                                    <span class="ts-empty">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('[data-toggle-section]').forEach(header => {
                header.addEventListener('click', () => {
                    header.closest('[data-section]').classList.toggle('open');
                });
            });
            document.getElementById('tsExpandAll').addEventListener('click', () => {
                document.querySelectorAll('[data-section]').forEach(s => s.classList.add('open'));
            });
            document.getElementById('tsCollapseAll').addEventListener('click', () => {
                document.querySelectorAll('[data-section]').forEach(s => s.classList.remove('open'));
            });
        });
    </script>
@endsection
