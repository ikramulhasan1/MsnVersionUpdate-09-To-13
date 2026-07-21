@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('get-quote');
@endphp
@if (isset($header))

    @section('title', $header->meta_title)

    @section('top_meta_tags')
        @if (isset($header->meta_description))
            <meta name="description" content="{!! str_limit(strip_tags($header->meta_description), 160, ' ...') !!}">
        @else
            <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
        @endif

        @if (isset($header->meta_keywords))
            <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
        @else
            <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
        @endif
    @endsection

@endif

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        /* ==========================================================
                             GET A QUOTE — v2, aligned to the MSN SoftTech site system:
                             dark navy/teal hero, orange CTAs, "$ eyebrow" labels,
                             rounded card language borrowed from Home/About.
                             Scoped to .gq-scope so nothing leaks into the rest of the site.
                             ========================================================== */
        .gq-scope {
            --navy-950: #070c14;
            --navy-900: #0c1626;
            --navy-800: #142238;
            --navy-700: #1b2c48;
            --paper: #f7f7f4;
            --paper-alt: #eef0ea;
            --ink: #12181f;
            --ink-soft: #5c6672;
            --ink-invert-soft: rgba(255, 255, 255, .66);
            --orange: #17C9A8;
            --orange-dark: #D2241D;
            --teal: #2fd6c0;
            --teal-dim: rgba(47, 214, 192, .14);
            --line: #e3e6df;
            --line-dark: rgba(255, 255, 255, .12);
            --danger: #d9483f;
            --ok: #22b378;
            --radius: 16px;

            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--paper);
        }

        .gq-scope * {
            box-sizing: border-box;
        }

        .gq-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        .gq-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--orange);
            margin-bottom: 18px;
        }

        .gq-eyebrow::before {
            content: '$';
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
            color: var(--teal);
        }

        /* ---------- hero / info + form split ---------- */
        .gq-hero {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            align-items: stretch;
        }

        @media (max-width: 991px) {
            .gq-hero {
                grid-template-columns: 1fr;
            }
        }

        /* ----- left: dark info panel ----- */
        .gq-canvas {
            position: relative;
            background:
                radial-gradient(720px 480px at 15% 15%, rgba(47, 214, 192, .16), transparent 60%),
                radial-gradient(640px 420px at 90% 85%, rgba(245, 166, 35, .10), transparent 55%),
                linear-gradient(180deg, var(--navy-950), var(--navy-900));
            color: #fff;
            padding: 88px 56px 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .gq-canvas h1 {
            font-size: clamp(30px, 3.4vw, 44px);
            font-weight: 700;
            line-height: 1.16;
            margin-bottom: 18px;
            max-width: 480px;
        }

        .gq-canvas .gq-sub {
            font-size: 16px;
            line-height: 1.7;
            color: var(--ink-invert-soft);
            max-width: 440px;
        }

        .gq-canvas .gq-sub * {
            color: inherit !important;
            font-size: inherit !important;
        }

        .gq-canvas .gq-sub a {
            color: var(--teal) !important;
            font-weight: 600;
            text-decoration: underline;
        }

        /* trust strip — mirrors the homepage impact-numbers pattern */
        .gq-trust {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: var(--line-dark);
            border: 1px solid var(--line-dark);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 34px;
            max-width: 460px;
        }

        .gq-trust-item {
            background: rgba(255, 255, 255, .03);
            padding: 16px 14px;
        }

        .gq-trust-item strong {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            color: var(--teal);
        }

        .gq-trust-item span {
            display: block;
            font-size: 11px;
            color: var(--ink-invert-soft);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-top: 4px;
        }

        /* "what happens next" — rounded cards w/ ghost numeral, borrows Our Mission/Vision pattern */
        .gq-next {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .gq-next-card {
            position: relative;
            background: var(--navy-800);
            border: 1px solid var(--line-dark);
            border-radius: var(--radius);
            padding: 18px 20px;
            overflow: hidden;
        }

        .gq-next-card .gq-ghost-num {
            position: absolute;
            right: 14px;
            top: -6px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 46px;
            font-weight: 700;
            color: rgba(255, 255, 255, .05);
            line-height: 1;
        }

        .gq-next-card .gq-num-badge {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--teal-dim);
            color: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .gq-next-card h4 {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
            position: relative;
        }

        .gq-next-card p {
            font-size: 13.5px;
            color: var(--ink-invert-soft);
            line-height: 1.55;
            margin: 0;
            position: relative;
        }

        .gq-canvas-foot {
            margin-top: 44px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12.5px;
            color: var(--ink-invert-soft);
        }

        .gq-canvas-foot .gq-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--teal);
            box-shadow: 0 0 0 4px rgba(47, 214, 192, .18);
        }

        /* ----- right: form panel ----- */
        .gq-form-panel {
            background: #fff;
            padding: 70px 60px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 1200px) {
            .gq-form-panel {
                padding: 56px 42px;
            }
        }

        @media (max-width: 991px) {

            .gq-canvas,
            .gq-form-panel {
                padding: 52px 28px;
            }
        }

        @media (max-width: 560px) {

            .gq-canvas,
            .gq-form-panel {
                padding: 42px 18px;
            }
        }

        .gq-sheet-head {
            margin-bottom: 28px;
        }

        .gq-sheet-head h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 6px;
            color: var(--ink);
        }

        .gq-sheet-head p {
            font-size: 14px;
            color: var(--ink-soft);
            margin: 0;
        }

        .gq-alert {
            border: 1px solid var(--line);
            border-left: 3px solid var(--ok);
            background: #f2faf6;
            padding: 12px 40px 12px 16px;
            font-size: 14px;
            margin-bottom: 20px;
            position: relative;
            border-radius: 8px;
        }

        .gq-alert.alert-danger {
            border-left-color: var(--danger);
            background: #fdf3f2;
        }

        .gq-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .gq-alert .close {
            position: absolute;
            right: 10px;
            top: 8px;
            font-size: 18px;
            line-height: 1;
            color: var(--ink-soft);
            opacity: .7;
        }

        .gq-form {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .gq-group-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ink);
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 14px;
        }

        .gq-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 18px;
        }

        @media (max-width: 620px) {
            .gq-fields {
                grid-template-columns: 1fr;
            }
        }

        .gq-field {
            position: relative;
        }

        .gq-field.gq-full {
            grid-column: 1 / -1;
        }

        .gq-field label {
            display: block;
            font-size: 11.5px;
            color: var(--ink-soft);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .gq-input,
        .gq-textarea,
        .gq-select {
            width: 100%;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            background: var(--paper);
            padding: 12px 14px;
            font-size: 14.5px;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            transition: border-color .2s ease, background .2s ease;
        }

        .gq-input:focus,
        .gq-textarea:focus,
        .gq-select:focus {
            outline: none;
            border-color: var(--teal);
            background: #fff;
        }

        .gq-input::placeholder,
        .gq-textarea::placeholder {
            color: #9aa2a8;
        }

        .gq-textarea {
            resize: vertical;
            min-height: 110px;
        }

        /* prefer contact */
        .gq-radio-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .gq-radio {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            cursor: pointer;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            padding: 9px 16px;
            background: var(--paper);
            transition: border-color .2s ease, background .2s ease;
        }

        .gq-radio:has(input:checked) {
            border-color: var(--navy-900);
            background: var(--navy-900);
            color: #fff;
        }

        .gq-radio input {
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1.5px solid var(--line);
            position: relative;
            cursor: pointer;
            margin: 0;
            flex: none;
        }

        .gq-radio input:checked {
            border-color: var(--teal);
            background: var(--teal);
        }

        /* services as tags */
        .gq-services {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
        }

        .gq-service {
            position: relative;
        }

        .gq-service-label {
            display: inline-block;
            padding: 9px 18px;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
            background: #fff;
        }

        .gq-service-input:checked+.gq-service-label {
            background: var(--navy-900);
            border-color: var(--orange);
            color: #fff;
        }

        .gq-subservices {
            display: none;
            flex-wrap: wrap;
            gap: 8px;
            position: absolute;
            top: 112%;
            left: 0;
            width: max-content;
            max-width: 320px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 14px 34px rgba(7, 12, 20, .14);
            z-index: 10;
        }

        .gq-subservice {
            display: flex;
            align-items: center;
        }

        .gq-subservice input {
            display: none;
        }

        .gq-subservice label {
            font-size: 12.5px;
            padding: 7px 13px;
            border-radius: 999px;
            background: var(--paper-alt);
            cursor: pointer;
            margin: 0;
            white-space: nowrap;
        }

        .gq-subservice input:checked+label {
            background: var(--orange);
            color: #fff;
        }

        /* uploads */
        .gq-dropzone.dropzone {
            border: 1.5px dashed var(--line);
            border-radius: 12px;
            background: var(--paper);
            padding: 22px;
            min-height: auto;
            font-family: 'Inter', sans-serif;
        }

        .gq-dropzone.dropzone .dz-message {
            margin: 0;
            font-size: 14px;
            color: var(--ink-soft);
        }

        .gq-dropzone.dropzone .dz-message::before {
            content: '⤒ ';
            color: var(--orange-dark);
        }

        .gq-captcha {
            display: flex;
        }

        /* submit — matches the site's orange pill CTA */
        .gq-submit {
            align-self: flex-start;
            background: var(--orange);
            color: #fff;
            border: none;
            padding: 15px 32px;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: .01em;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background .2s ease, transform .2s ease;
        }

        .gq-submit:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .gq-submit svg {
            transition: transform .25s ease;
        }

        .gq-submit:hover svg {
            transform: translateX(4px);
        }

        @media (max-width: 560px) {
            .gq-submit {
                width: 100%;
                justify-content: center;
            }
        }

        /* ==========================================================
                             PROCESS — dark grid, matches "How We Make Work Successful"
                             Wraps instead of squeezing into one row, so it never goes
                             thin-and-tall regardless of how many steps there are.
                             ========================================================== */
        .gq-process {
            background: var(--navy-950);
            padding: 96px 15px 104px;
        }

        .gq-process-head {
            max-width: 640px;
            margin: 0 auto 48px;
            text-align: center;
        }

        .gq-process-head .gq-eyebrow {
            justify-content: center;
        }

        .gq-process-head h2 {
            font-size: clamp(26px, 3vw, 36px);
            font-weight: 700;
            color: #fff;
        }

        .gq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .gq-grid-card {
            background: var(--navy-800);
            border: 1px solid var(--line-dark);
            border-radius: var(--radius);
            padding: 28px 24px;
            transition: transform .2s ease, border-color .2s ease;
        }

        .gq-grid-card:hover {
            transform: translateY(-3px);
            border-color: rgb(248, 248, 248);
        }

        .gq-grid-num {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--orange);
            color: var(--navy-950);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .gq-grid-card h3 {
            font-size: 17px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 8px;
        }

        .gq-grid-card .gq-grid-desc,
        .gq-grid-card .gq-grid-desc p {
            font-size: 14.5px !important;
            color: var(--ink-invert-soft) !important;
            line-height: 1.6;
            margin: 0;
        }

        .gq-process-cta {
            text-align: center;
            margin-top: 50px;
        }

        .gq-process-cta a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--orange);
            color: var(--navy-950);
            padding: 14px 30px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: background .2s ease;
        }

        .gq-process-cta a:hover {
            background: var(--orange-dark);
            color: #fff;
        }
    </style>

    <div class="gq-scope">

        <section class="gq-hero" id="quoteHero">
            <div class="gq-canvas">
                <div>
                    <div class="gq-eyebrow">Get a Quote</div>

                    @if (isset($section_getquote))
                        <h1 class="gq-display">{{ $section_getquote->title }}</h1>
                        <div class="gq-sub">{!! $section_getquote->description !!}</div>
                    @else
                        <h1 class="gq-display">{{ __('Quote') }}</h1>
                    @endif

                    <div class="gq-trust">
                        <div class="gq-trust-item"><strong>1 day</strong><span>Avg. first reply</span></div>
                        <div class="gq-trust-item"><strong>3700+</strong><span>Projects shipped</span></div>
                        <div class="gq-trust-item"><strong>56+</strong><span>Developers on call</span></div>
                    </div>

                    <div class="gq-next">
                        <div class="gq-next-card">
                            <span class="gq-ghost-num gq-display">01</span>
                            <div class="gq-num-badge">01</div>
                            <h4>We read every brief</h4>
                            <p>A developer — not a salesperson — looks at what you've sent within one business day.</p>
                        </div>
                        <div class="gq-next-card">
                            <span class="gq-ghost-num gq-display">02</span>
                            <div class="gq-num-badge">02</div>
                            <h4>You get a real quote</h4>
                            <p>A clear scope, timeline, and price. No placeholder ranges, no follow‑up calls required to get
                                one.</p>
                        </div>
                        <div class="gq-next-card">
                            <span class="gq-ghost-num gq-display">03</span>
                            <div class="gq-num-badge">03</div>
                            <h4>We schedule the kickoff</h4>
                            <p>Once you're happy with the plan, we lock a start date and assign your team.</p>
                        </div>
                    </div>
                </div>

                <div class="gq-canvas-foot">
                    <span class="gq-dot"></span>
                    <span>Currently accepting new projects</span>
                </div>
            </div>

            <div class="gq-form-panel">
                <div class="gq-sheet-head">
                    <h2 class="gq-display">Project Brief</h2>
                    <p>Tell us what you're trying to build — we'll reply with a clear scope, timeline, and a real quote.</p>
                </div>

                @if (Session::has('success'))
                    <div class="gq-alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{ Session::get('success') }}
                    </div>
                @endif

                @if (Session::has('error'))
                    <div class="gq-alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{ Session::get('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="gq-alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="quoteForm" class="gq-form" method="post" action="{{ route('get-quote.store') }}"
                    enctype="multipart/form-data" accept-charset="utf-8">
                    @csrf
                    <input type="hidden" name="work_model" value="{{ $work_model }}">
                    <input type="hidden" name="work_scope" value="{{ $work_scope }}">

                    <div>
                        <div class="gq-group-label">Contact details</div>
                        <div class="gq-fields">
                            <div class="gq-field">
                                <label for="q_name">{{ __('form.your_name') }}</label>
                                <input class="gq-input" id="q_name" type="text" name="name"
                                    placeholder="Jane Cooper" value="{{ old('name') }}" required>
                            </div>
                            <div class="gq-field">
                                <label for="q_email">{{ __('form.email_address') }}</label>
                                <input class="gq-input" id="q_email" type="email" name="email"
                                    placeholder="jane@company.com" value="{{ old('email') }}" required>
                            </div>
                            <div class="gq-field">
                                <label for="q_phone">{{ __('form.phone_no') }}</label>
                                <input class="gq-input" id="q_phone" type="tel" name="phone"
                                    placeholder="+1 (___) ___ ____" value="{{ old('phone') }}" required>
                            </div>
                            <div class="gq-field">
                                <label for="q_company">{{ __('form.company') }}</label>
                                <input class="gq-input" id="q_company" type="text" name="company"
                                    placeholder="Optional" value="{{ old('company') }}">
                            </div>
                            <div class="gq-field">
                                <label for="q_address">{{ __('form.address') }}</label>
                                <input class="gq-input" id="q_address" type="text" name="address"
                                    placeholder="Street address" value="{{ old('address') }}" required>
                            </div>
                            <div class="gq-field">
                                <label for="q_city">{{ __('form.city') }}</label>
                                <input class="gq-input" id="q_city" type="text" name="city" placeholder="City"
                                    value="{{ old('city') }}" required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="gq-group-label">{{ __('form.prefer_contact') }}</div>
                        <div class="gq-radio-row">
                            <label class="gq-radio">
                                <input type="radio" name="prefer_contact" value="1" id="pre_email"
                                    @if (old('prefer_contact') != '2') checked @endif required> Email
                            </label>
                            <label class="gq-radio">
                                <input type="radio" name="prefer_contact" value="2" id="pre_phone"
                                    @if (old('prefer_contact') == '2') checked @endif required> Phone
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="gq-group-label">{{ __('form.services') }}</div>
                        <div class="gq-services">
                            @foreach ($services as $service)
                                <div class="gq-service">
                                    <input type="checkbox" class="gq-service-input" name="services[]"
                                        value="{{ $service->id }}" id="service-{{ $service->id }}">
                                    <label class="gq-service-label"
                                        for="service-{{ $service->id }}">{{ $service->short_title }}</label>

                                    @if ($service->subservices && $service->subservices->count() > 0)
                                        <div class="gq-subservices" id="subservices-{{ $service->id }}">
                                            @foreach ($service->subservices as $sub)
                                                <div class="gq-subservice">
                                                    <input type="checkbox" name="sub_service[]"
                                                        value="{{ $sub->short_title }}" id="sub-{{ $sub->id }}">
                                                    <label for="sub-{{ $sub->id }}">{{ $sub->short_title }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="gq-group-label">Tell us about the project</div>
                        <div class="gq-field gq-full">
                            <textarea class="gq-textarea" name="message" placeholder="What are you building? What does success look like?"
                                required>{{ old('message') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <div class="gq-group-label">Attachments</div>
                        <div id="quoteDropzone" class="gq-dropzone dropzone"></div>
                    </div>

                    <div class="gq-captcha">
                        <div>
                            <div class="g-recaptcha mb-2" data-sitekey="6Ldv410tAAAAAObli6t7JdOmtDeByqNt7m8CwuL_">
                            </div>
                            @if ($errors->has('captcha'))
                                <p class="text-danger" style="font-size:13px;color:var(--danger);">
                                    {{ $errors->first('captcha') }}</p>
                            @endif
                        </div>
                    </div>

                    <button class="gq-submit" type="submit" name="submit-form">
                        Send Project Brief
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M2 8H14M14 8L9 3M14 8L9 13" stroke="white" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </form>
            </div>
        </section>

        @php
            $section_process = \App\Models\Section::section('process');
        @endphp

        @if (count($processes) > 0 && isset($section_process))
            <section class="gq-process">
                <div class="container">
                    <div class="gq-process-head">
                        <div class="gq-eyebrow">How it works</div>
                        <h2 class="gq-display">{{ $section_process->title }}</h2>
                    </div>

                    <div class="gq-grid">
                        @foreach ($processes as $key => $process)
                            <div class="gq-grid-card">
                                <div class="gq-grid-num gq-display">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
                                <h3>{{ $process->title }}</h3>
                                <div class="gq-grid-desc">{!! $process->description !!}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="gq-process-cta">
                        <a href="#quoteHero">
                            Back to the form
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                <path d="M14 8H2M2 8L7 3M2 8L7 13" stroke="white" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        @endif

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Dropzone assets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        // IMPORTANT: this must run right after dropzone.min.js loads,
        // NOT inside a DOMContentLoaded handler. Dropzone registers its own
        // "auto discover" DOMContentLoaded listener the moment its script
        // executes, so if we wait for DOMContentLoaded ourselves before
        // setting autoDiscover = false, Dropzone's own listener has often
        // already fired first (auto-attaching to the .dropzone element with
        // no url -> "No URL provided" error, and then our manual init throws
        // "Dropzone already attached").
        Dropzone.autoDiscover = false;
    </script>

    <!-- reCAPTCHA (was missing entirely, so the widget never rendered and
                     g-recaptcha-response was always empty on submit) -->
    {{-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}

    <script>
        $(document).ready(function() {

            // Toggle subservice visibility when main service label is clicked
            $('.gq-service-label').on('click', function(e) {
                e.preventDefault();

                let parent = $(this).closest('.gq-service');
                let checkbox = parent.find('.gq-service-input');
                let subDiv = parent.find('.gq-subservices');

                if (subDiv.length > 0) {
                    if (!checkbox.is(':checked')) {
                        checkbox.prop('checked', true);
                    }
                    if (subDiv.is(':visible')) {
                        subDiv.stop(true, true).slideUp(300);
                    } else {
                        subDiv.stop(true, true).slideDown(300);
                    }
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });

            $(document).on('change', '.gq-subservice input[type="checkbox"]', function() {
                let parentService = $(this).closest('.gq-service');
                let parentCheckbox = parentService.find('.gq-service-input');
                let subDiv = parentService.find('.gq-subservices');

                if (parentService.find('.gq-subservice input:checked').length > 0) {
                    parentCheckbox.prop('checked', true);
                } else {
                    parentCheckbox.prop('checked', false);
                    subDiv.stop(true, true).slideUp(300);
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.gq-service').length) {
                    $('.gq-subservices').slideUp(200);
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const dzElem = document.getElementById("quoteDropzone");
            if (!dzElem) {
                console.error("Dropzone element not found!");
                return;
            }

            const quoteDropzone = new Dropzone(dzElem, {
                url: "{{ route('quote.upload') }}",
                paramName: "file",
                maxFilesize: 20,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.svg,.webp,.pdf,.doc,.docx,.txt,.zip,.rar,.csv,.xls,.xlsx,.ppt,.pptx,.mp3,.avi,.mp4,.mpeg,.3gp",
                addRemoveLinks: true,
                dictDefaultMessage: "Drag files here, or click to browse",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },

                success: function(file, response) {
                    if (response.file_name) {
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "uploaded_files[]";
                        hiddenInput.value = response.file_name;
                        document.querySelector("#quoteForm").appendChild(hiddenInput);
                        file._hiddenInput = hiddenInput;
                    }
                },

                removedfile: function(file) {
                    if (file.previewElement) file.previewElement.remove();
                    if (file._hiddenInput) file._hiddenInput.remove();
                },

                error: function(file, response) {
                    console.error("Dropzone error:", response);
                    alert("File upload failed!");
                },
            });
        });
    </script>

@endsection
