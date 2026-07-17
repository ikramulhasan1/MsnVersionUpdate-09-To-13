@extends('web.layouts.master')

@php
  $header = \App\Models\PageSetup::page('about-us');
@endphp
@if(isset($header))

@section('title', $header->meta_title)

@section('top_meta_tags')
  @if(isset($header->meta_description))
    <meta name="description" content="{!! str_limit(strip_tags($header->meta_description), 160, ' ...') !!}">
  @else
    <meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
  @endif

  @if(isset($header->meta_keywords))
    <meta name="keywords" content="{!! strip_tags($header->meta_keywords) !!}">
  @else
    <meta name="keywords" content="{!! strip_tags($setting->keywords) !!}">
  @endif
@endsection

@endif

@section('social_meta_tags')

@endsection


@section('content')

  <div class="msn-scope">

    <!-- Hero -->
    <section class="ap-hero ap-grain">
      <div class="container ap-hero-inner">
        <span class="ap-chip msn-reveal"><span class="dot"></span>{{ $setting->title ?? 'MSN Softtech' }}</span>
        <h1 class="ap-hero-title msn-reveal">{{ __('navbar.about') }}</h1>
        <div class="ap-hero-underline msn-reveal"></div>
        <p class="ap-hero-copy msn-reveal">Get to know the team, mission, and approach behind every project we build.
        </p>
        <div class="ap-breadcrumb msn-reveal">
          <a href="{{ route('home') }}">{{ __('navbar.home') }}</a>
          <span class="sep">/</span>
          <span>{{ __('navbar.about') }}</span>
        </div>
      </div>

      <!-- signature element: rotating red seal (matches home page accent) -->
      <div class="ap-seal msn-reveal" aria-hidden="true">
        <svg class="ap-seal-ring" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <path id="ap-seal-path" d="M100,100 m-82,0 a82,82 0 1,1 164,0 a82,82 0 1,1 -164,0" />
            <linearGradient id="ap-seal-red" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="#FCA5A5" />
              <stop offset=".5" stop-color="#DC2626" />
              <stop offset="1" stop-color="#F87171" />
            </linearGradient>
          </defs>
          <circle cx="100" cy="100" r="96" fill="none" stroke="url(#ap-seal-red)" stroke-width="1.25" />
          <circle cx="100" cy="100" r="68" fill="none" stroke="url(#ap-seal-red)" stroke-width="1"
            stroke-dasharray="1 5" />
          <text font-size="10.5" letter-spacing="3" fill="#DC2626" style="font-family:var(--bp-font-mono);">
            <textPath href="#ap-seal-path" startOffset="0%">MSN SOFTTECH • TRUSTED WORLDWIDE • MSN SOFTTECH • TRUSTED
              WORLDWIDE •</textPath>
          </text>
        </svg>
        <div class="ap-seal-center">
          <span class="ap-seal-num">10+</span>
          <span class="ap-seal-label">YEARS</span>
        </div>
      </div>
    </section>

    <!-- Who We Are -->
    @if(isset($about))
      <section class="ap-intro msn-section">
        <div class="container">
          <div class="ap-intro-grid">
            <div class="ap-intro-card msn-reveal">
              <span class="msn-eyebrow">Who We Are</span>
              <h3 class="mt-3">{{ $about->title }}</h3>
              <div class="about-content">
                {!! $about->description !!}
              </div>
            </div>
            <div class="ap-intro-media bp-frame msn-reveal">
              <span class="bp-crosshair tl"></span>
              <span class="bp-crosshair tr"></span>
              <span class="bp-crosshair bl"></span>
              <span class="bp-crosshair br"></span>
              <img src="{{ asset('uploads/about/' . $about->image_path) }}" alt="{{ $about->title }}">
            </div>
          </div>
        </div>
      </section>
    @endif

    <!-- Core Values (new) -->
    <section class="ap-values msn-section">
      <div class="container">
        <div class="msn-section-head msn-center msn-reveal">
          <span class="msn-eyebrow">What Drives Us</span>
          <h2>The Values Behind Every Delivery</h2>
        </div>
        <div class="ap-values-grid msn-reveal">
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-shield-check"></i></span>
            <h5>Integrity</h5>
            <p>We do what we say — clear pricing, clear timelines, no surprises.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-lightbulb"></i></span>
            <h5>Innovation</h5>
            <p>We stay curious about new tools, and use them only when they genuinely help you.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-award"></i></span>
            <h5>Excellence</h5>
            <p>Every release goes through real testing before it reaches your users.</p>
          </div>
          <div class="ap-value-card">
            <span class="ap-value-badge"><i class="bi bi-people"></i></span>
            <h5>Partnership</h5>
            <p>We work like an extension of your team, not a vendor that disappears after launch.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision -->
    @if(isset($about->mission_title) || isset($about->vision_title))
      <section class="ap-mv msn-section">
        <div class="container">
          <div class="ap-mv-grid">
            @if(isset($about->mission_title))
              <div class="msn-card ap-mv-card ap-mv-card--mission msn-reveal">
                <span class="ap-mv-index">01</span>
                <span class="ap-mv-icon"><i class="bi bi-bullseye"></i></span>
                <h3>{{ $about->mission_title }}</h3>
                <div class="about-content">{!! $about->mission_desc !!}</div>
              </div>
            @endif
            @if(isset($about->vision_title))
              <div class="msn-card ap-mv-card ap-mv-card--vision msn-reveal">
                <span class="ap-mv-index">02</span>
                <span class="ap-mv-icon"><i class="bi bi-binoculars"></i></span>
                <h3>{{ $about->vision_title }}</h3>
                <div class="about-content">{!! $about->vision_desc !!}</div>
              </div>
            @endif
          </div>
        </div>
      </section>
    @endif

    <!-- What We Provide -->
    <section class="ap-provide msn-section">
      <div class="container">
        <div class="msn-section-head msn-center msn-reveal">
          <span class="msn-eyebrow">What We Provide</span>
          <h2>10+ Years of IT Excellence, Delivered Globally</h2>
        </div>

        <div class="ap-provide-grid">
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-laptop"></i></span>
            <h5>Software Development</h5>
            <p>Innovative, custom-built solutions that fuel growth, optimize performance, and drive digital
              transformation.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-diagram-3-fill"></i></span>
            <h5>Website Development</h5>
            <p>High-performance, responsive websites crafted to deliver outstanding user experiences and business
              growth.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-phone"></i></span>
            <h5>Mobile App Development</h5>
            <p>Seamless, high-impact mobile experiences designed to captivate users and expand your global reach.</p>
          </div>
          <div class="msn-card ap-provide-card msn-reveal">
            <span class="ap-provide-icon"><i class="bi bi-bar-chart-line"></i></span>
            <h5>SEO & Marketing</h5>
            <p>Strategic SEO and marketing solutions that enhance visibility, increase engagement, and deliver
              measurable success.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Tech marquee (new premium touch) -->
    <div class="ap-marquee-wrap">
      <div class="ap-marquee-label">Our Core Toolkit</div>
      <div class="ap-marquee">
        @php
          $ap_stack = [
            ['bi-code-slash', 'Laravel'],
            ['bi-filetype-jsx', 'React'],
            ['bi-phone-fill', 'Flutter'],
            ['bi-cloud-fill', 'AWS'],
            ['bi-vector-pen', 'Figma'],
            ['bi-hexagon-fill', 'Node.js'],
            ['bi-shield-check', 'Security'],
            ['bi-search', 'SEO'],
            ['bi-database-fill', 'MySQL'],
            ['bi-apple', 'iOS'],
            ['bi-google-play', 'Android'],
            ['bi-gear-fill', 'DevOps'],
          ];
        @endphp
        @for($r = 0; $r < 2; $r++)
          @foreach($ap_stack as $ap_item)
            <span class="ap-marquee-item"><i class="bi {{ $ap_item[0] }}"></i>{{ $ap_item[1] }}</span>
          @endforeach
        @endfor
      </div>
    </div>

    <!-- Stats -->
    @if(count($counters) > 0)
      <section class="ap-stats-section msn-section ap-grain">
        <div class="container">
          <div class="msn-section-head msn-center msn-reveal">
            <span class="msn-eyebrow msn-eyebrow-on-dark">By The Numbers</span>
            <h2 style="color:#fff;">Trusted by Businesses Worldwide</h2>
          </div>
          <div class="ap-stats msn-reveal">
            @foreach($counters as $counter)
              <div class="ap-stat-col">
                <div class="ap-stat">
                  <div class="ap-stat-value" data-count="{{ (int) $counter->value }}">0</div>
                  <div class="ap-stat-label">{{ $counter->title }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Team -->
    @php
      $section_team = \App\Models\Section::section('team');
    @endphp
    @if(count($members) > 0 && isset($section_team))
      <section class="ap-team-section msn-section">
        <div class="container">
          <div class="msn-section-head msn-reveal">
            <span class="msn-eyebrow">Meet The Team</span>
            <h2>{{ $section_team->title }}</h2>
            <div class="text description">{!! $section_team->description !!}</div>
          </div>

          <div class="ap-team-grid msn-reveal">
            @foreach($members as $member)
              <div class="ap-team-block">
                <div class="ap-team-inner">
                  <div class="ap-team-photo">
                    <img src="{{ asset('uploads/member/' . $member->image_path) }}" alt="{{ $member->title }}" loading="lazy">
                  </div>
                  <h3 class="ap-team-name"><a>{{ $member->title }}</a></h3>
                  <span class="ap-team-role">{{ $member->designation->title }}@if(isset($member->designation->department)),
                  {{ $member->designation->department }}@endif</span>
                  @if(isset($member->email))
                    <span class="ap-team-meta"><i class="far fa-envelope"></i> {{ $member->email }}</span>
                  @endif
                  @if(isset($member->phone))
                    <span class="ap-team-meta"><i class="fas fa-phone-volume"></i> {{ $member->phone }}</span>
                  @endif

                  <ul class="ap-team-social">
                    @if(isset($member->facebook))
                      <li><a href="{{ $member->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    @endif
                    @if(isset($member->twitter))
                      <li><a href="{{ $member->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                    @endif
                    @if(isset($member->instagram))
                      <li><a href="{{ $member->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    @endif
                    @if(isset($member->linkedin))
                      <li><a href="{{ $member->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <!-- Our Promise — pull-quote (new) -->
    <section class="ap-quote-band msn-reveal">
      <div class="container">
        <span class="ap-quote-mark">"</span>
        <p class="ap-quote-text">We don't just ship projects. We build the software your business will still be proud of
          five years from now.</p>
        <div class="ap-quote-attrib">— MSN SoftTech</div>
      </div>
    </section>

    <!-- Process -->
    @php
      $section_process = \App\Models\Section::section('process');
    @endphp
    @if(count($processes) > 0 && isset($section_process))
      <section class="ap-process-section msn-section">
        <div class="container">
          <div class="ap-process-title msn-reveal">
            <span class="msn-eyebrow">How We Deliver</span>
            <h2 style="padding-top:14px;">{{ $section_process->title }}</h2>
          </div>

          <div class="row g-4 mb-4">
            @foreach($processes as $key => $process)
              @php
                $totalSteps = count($processes);
                $showArrow = ($key != $totalSteps - 1);
              @endphp
              <div class="col-md-4 mb-4 msn-reveal">
                <div class="ap-process-step">
                  <div class="ap-process-number">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
                  <div class="ap-process-heading">{{ $process->title }}</div>
                  <div class="ap-process-desc">{!! $process->description !!}</div>
                  <div
                    class="ap-process-arrow d-none d-md-block {{ $showArrow ? ($key == 2 ? 'ap-arrow-down' : '') : 'arrow-hidden' }}">
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="text-center mt-5">
            <a href="https://msnsofttech.com/get-quote" class="msn-btn msn-btn-primary ap-btn-shine">Get in Touch With Us
              →</a>
          </div>
        </div>
      </section>
    @endif

  </div><!-- /.msn-scope -->

  <script>
    (function () {
      // premium touch: count the stats up when they scroll into view
      var stats = document.querySelectorAll('.ap-stat-value[data-count]');
      if (!stats.length || !('IntersectionObserver' in window)) return;
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10) || 0;
          var current = 0;
          var step = Math.max(1, Math.ceil(target / 60));
          (function tick() {
            current += step;
            if (current >= target) {
              el.textContent = target;
            } else {
              el.textContent = current;
              requestAnimationFrame(tick);
            }
          })();
          io.unobserve(el);
        });
      }, { threshold: 0.4 });
      stats.forEach(function (el) { io.observe(el); });
    })();
  </script>

@endsection