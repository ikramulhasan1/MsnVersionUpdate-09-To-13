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
                                <input class="gq-input" id="q_company" type="text" name="company" placeholder="Optional"
                                    value="{{ old('company') }}">
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
@endsection
