@extends('web.layouts.master')

@php
    $header = \App\Models\PageSetup::page('home');
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

@section('social_meta_tags')
    @if (isset($setting))
        <meta property="og:type" content="website">
        <meta property='og:site_name' content="{{ $setting->title }}" />
        <meta property='og:title' content="{{ $setting->title }}" />
        <meta property='og:description' content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
        <meta property='og:url' content="{{ route('home') }}" />
        <meta property='og:image' content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="{!! '@' . str_replace(' ', '', $setting->title) !!}" />
        <meta name="twitter:creator" content="@HiTechParks" />
        <meta name="twitter:url" content="{{ route('home') }}" />
        <meta name="twitter:title" content="{{ $setting->title }}" />
        <meta name="twitter:description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}" />
        <meta name="twitter:image" content="{{ asset('/uploads/setting/' . $setting->logo_path) }}" />
    @endif

    {{-- New design (design.html) dependencies: Bootstrap 5, Font Awesome 6, Plus Jakarta Sans.
       NOTE: this design ships with no CSS of its own (the <style> block in design.html
       was empty) — per instruction we're shifting the markup/structure only. --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
@endsection

@section('content')
    <style>
        .techImg {
            width: 50px;
            height: 50px;
        }

        .tch-stage {
            display: flex;
            flex-wrap: wrap;
            /* জায়গা শেষ হলে নিচে চলে যাবে */
            gap: 20px;
            justify-content: center;
        }

        .tch-card {
            width: 100px;
            height: 100px;
            flex: 0 0 auto;
        }
    </style>
    {{-- ============ IDX SECTION (hero) — wired to $sliders ============ --}}
    <div class="idx-scope">

        @if (count($sliders) > 0)
            @foreach ($sliders as $slider)
                <section class="idx-hero-section text-center">
                    <div class="container">
                        <span class="idx-hero-badge">
                            <span class="idx-dot"></span>
                            From AI strategy to product in weeks
                        </span>

                        <h1 class="idx-hero-title">
                            {!! $slider->title !!}
                        </h1>

                        <p class="idx-hero-sub">
                            {!! $slider->description !!}
                        </p>

                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                            <a href="{{ route('get-quote') }}" class="idx-btn-primary-red">Talk to an Expert <i
                                    class="fa-solid fa-arrow-right"></i></a>
                            <a href="{{ route('services') }}" class="idx-btn-outline-dark-pill">Explore Our Work <i
                                    class="fa-solid fa-arrow-right"></i></a>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-robot"></i></span>
                                Agentic AI
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-code-branch"></i></span>
                                AI Integration &amp; Automation
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-window-maximize"></i></span>
                                Web Applications
                            </span>
                            <span class="idx-pill-feature">
                                <span class="idx-pill-icon"><i class="fa-solid fa-mobile-screen"></i></span>
                                Mobile Applications
                            </span>
                        </div>
                    </div>
                </section>
            @endforeach
        @endif

        {{-- ================= TRUST STATEMENT (no matching Laravel section — kept as demo design) ================= --}}
        <section class="idx-trust-section">
            <div class="container">
                <p class="idx-trust-text mb-0">
                    <span class="idx-highlight">Trusted by Customers across 13+ countries to take products from concept
                        to scale.</span>
                    <span class="idx-muted"> We combine deep industry expertise with AI-native engineering to deliver
                        faster, iterate more, and ship products that work in production.</span>
                </p>

                <div class="idx-review-row">
                    <div class="idx-review-card">
                        <div>
                            <div class="idx-review-label">Reviewed on</div>
                            <div class="idx-review-brand">Clutch</div>
                        </div>
                        <div class="idx-stars-red">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                class="fa-solid fa-star"></i>
                            <div class="idx-review-count">52 Reviews</div>
                        </div>
                    </div>

                    <div class="idx-review-card idx-design-rush-badge">
                        <i class="fa-solid fa-award" style="color:var(--red-600); font-size:1.6rem;"></i>
                        <div>
                            <div class="idx-review-count">30 Reviews on DesignRush</div>
                            <div class="idx-stars-red">
                                <span class="idx-review-score" style="font-size:1rem;">4.9</span>
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="idx-review-card">
                        <i class="fa-solid fa-shield-halved" style="color:var(--red-600); font-size:1.6rem;"></i>
                        <div>
                            <div class="idx-review-count">Goodfirms &middot; 34 Reviews</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="idx-review-score">4.89</span>
                                <span class="idx-stars-red">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                        class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                        class="fa-solid fa-star"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    </div>

    {{-- ============ SVC SECTION (services) — wired to $services / $section_services ============ --}}
    <div class="svc-scope">
        @php
            $section_services = \App\Models\Section::section('services');
            // design.html shipped 6 static icons for 6 static cards; we cycle through the same
            // set since the Service model has no per-service icon field.
            $svcIcons = [
                'fa-palette',
                'fa-mobile-screen',
                'fa-robot',
                'fa-cloud-arrow-up',
                'fa-list-check',
                'fa-diagram-project',
            ];
        @endphp

        @if (count($services) > 0 && isset($section_services))
            <section class="svc-services-section">
                <div class="container">
                    <h2 class="svc-section-title">{{ $section_services->title }}</h2>

                    <div class="row g-4">
                        @foreach ($services as $key => $service)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('service.single', $service->slug) }}"
                                    class="svc-service-card text-decoration-none d-block">
                                    <div class="svc-service-icon"><i
                                            class="fa-solid {{ $svcIcons[$key % count($svcIcons)] }}"></i></div>
                                    <h3 class="svc-service-title">{{ $service->short_title }}</h3>
                                    @if (isset($service->short_description))
                                        <p class="svc-service-desc">
                                            {{ str_limit(strip_tags($service->short_description), 130, '...') }}</p>
                                    @endif
                                    @if (isset($service->tags) && trim($service->tags) !== '')
                                        <ul class="svc-service-list">
                                            @foreach (array_filter(array_map('trim', explode(',', $service->tags))) as $tag)
                                                <li>{{ $tag }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    </div>

    {{-- ============ TCH SECTION (technology logos — no matching Laravel section, kept as demo design) ============ --}}
    <div class="tch-scope ">
        <section class="tch-section container">
            <div class="container-fluid px-3">
                <p class="tch-eyebrow">Technologies We Work With</p>
                <h2 class="tch-heading">A Full Stack of Modern Tools</h2>

                <div class="tch-stage" id="tchStage" aria-label="Technology stack logos">
                    @foreach ($technologies->shuffle() as $technology)
                        <div class="tch-card" title="{{ $technology->short_title }}">
                            <span class="tch-tooltip">{{ $technology->short_title }}</span>

                            <div class="tch-float-inner" style="--tch-dur:4.47s; --tch-delay:1.61s; --tch-amp:-7.2px;">
                                <img class="techImg" src="{{ asset('uploads/technology/' . $technology->logo_path) }}"
                                     loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- <script>
          (function() {
              // Pool of technologies — Devicon class + display name.
              const TCH_TECH_POOL = [
                  { name: "HTML5", icon: "devicon-html5-plain colored" },
                  { name: "CSS3", icon: "devicon-css3-plain colored" },
                  { name: "JavaScript", icon: "devicon-javascript-plain colored" },
                  { name: "TypeScript", icon: "devicon-typescript-plain colored" },
                  { name: "React", icon: "devicon-react-original colored" },
                  { name: "Vue.js", icon: "devicon-vuejs-plain colored" },
                  { name: "Angular", icon: "devicon-angularjs-plain colored" },
                  { name: "Node.js", icon: "devicon-nodejs-plain colored" },
                  { name: "Express", icon: "devicon-express-original" },
                  { name: "MongoDB", icon: "devicon-mongodb-plain colored" },
                  { name: "MySQL", icon: "devicon-mysql-plain colored" },
                  { name: "PostgreSQL", icon: "devicon-postgresql-plain colored" },
                  { name: "Redis", icon: "devicon-redis-plain colored" },
                  { name: "Python", icon: "devicon-python-plain colored" },
                  { name: "Django", icon: "devicon-django-plain colored" },
                  { name: "Flask", icon: "devicon-flask-original" },
                  { name: "PHP", icon: "devicon-php-plain colored" },
                  { name: "Laravel", icon: "devicon-laravel-plain colored" },
                  { name: "WordPress", icon: "devicon-wordpress-plain colored" },
                  { name: "Bootstrap", icon: "devicon-bootstrap-plain colored" },
                  { name: "Tailwind CSS", icon: "devicon-tailwindcss-plain colored" },
                  { name: "Sass", icon: "devicon-sass-original colored" },
                  { name: "Git", icon: "devicon-git-plain colored" },
                  { name: "GitHub", icon: "devicon-github-original" },
                  { name: "GitLab", icon: "devicon-gitlab-plain colored" },
                  { name: "Docker", icon: "devicon-docker-plain colored" },
                  { name: "Kubernetes", icon: "devicon-kubernetes-plain colored" },
                  { name: "AWS", icon: "devicon-amazonwebservices-original" },
                  { name: "Firebase", icon: "devicon-firebase-plain colored" },
                  { name: "GraphQL", icon: "devicon-graphql-plain colored" },
                  { name: "Figma", icon: "devicon-figma-plain colored" },
                  { name: "Photoshop", icon: "devicon-photoshop-plain colored" },
                  { name: "Illustrator", icon: "devicon-illustrator-plain colored" },
                  { name: "Java", icon: "devicon-java-plain colored" },
                  { name: "Kotlin", icon: "devicon-kotlin-plain colored" },
                  { name: "Swift", icon: "devicon-swift-plain colored" },
                  { name: "Flutter", icon: "devicon-flutter-plain colored" },
                  { name: "Android", icon: "devicon-android-plain colored" },
                  { name: "Linux", icon: "devicon-linux-plain colored" },
                  { name: "Nginx", icon: "devicon-nginx-original colored" },
                  { name: "Webpack", icon: "devicon-webpack-plain colored" },
                  { name: "npm", icon: "devicon-npm-original-wordmark colored" },
                  { name: "C#", icon: "devicon-csharp-plain colored" },
                  { name: ".NET", icon: "devicon-dotnetcore-plain colored" },
                  { name: "VS Code", icon: "devicon-visualstudio-plain colored" },
              ];

              const tchStage = document.getElementById("tchStage");
              const tchScopeEl = document.querySelector(".tch-scope");

              function tchShuffle(arr) {
                  const a = arr.slice();
                  for (let i = a.length - 1; i > 0; i--) {
                      const j = Math.floor(Math.random() * (i + 1));
                      [a[i], a[j]] = [a[j], a[i]];
                  }
                  return a;
              }

              // Build a list of exactly `count` items from the pool, cycling/reshuffling
              // when the pool is smaller than the space available, and avoiding the
              // same logo appearing twice in a row.
              function tchPickItems(count) {
                  const result = [];
                  let bag = tchShuffle(TCH_TECH_POOL);
                  let idx = 0;
                  while (result.length < count) {
                      if (idx >= bag.length) {
                          bag = tchShuffle(TCH_TECH_POOL);
                          idx = 0;
                      }
                      const candidate = bag[idx++];
                      if (
                          result.length &&
                          result[result.length - 1].name === candidate.name
                      )
                          continue;
                      result.push(candidate);
                  }
                  return result;
              }

              function tchReadPx(varName) {
                  const val = getComputedStyle(tchScopeEl)
                      .getPropertyValue(varName)
                      .trim();
                  if (val.endsWith("vh")) {
                      return (parseFloat(val) / 100) * window.innerHeight;
                  }
                  return parseFloat(val);
              }

              function tchRender() {
                  const cellW = tchReadPx("--tch-cell-w");
                  const cellH = tchReadPx("--tch-cell-h");
                  const gap = tchReadPx("--tch-gap");
                  const stageH = tchReadPx("--tch-stage-h");

                  const stageW =
                      tchStage.clientWidth || tchStage.parentElement.clientWidth;

                  const cols = Math.max(
                      1,
                      Math.floor((stageW + gap) / (cellW + gap)),
                  );
                  const rows = Math.max(
                      2,
                      Math.floor((stageH + gap) / (cellH + gap)),
                  );
                  const total = cols * rows;

                  tchStage.style.gridTemplateColumns = `repeat(${cols}, var(--tch-cell-w))`;

                  const items = tchPickItems(total);

                  tchStage.innerHTML = items
                      .map((tech, i) => {
                          const rot = (Math.random() * 10 - 5).toFixed(1); // -5deg .. 5deg
                          const dur = (3.2 + Math.random() * 2.4).toFixed(2); // 3.2s .. 5.6s
                          const delay = (Math.random() * 2).toFixed(2); // 0 .. 2s
                          const amp = (4 + Math.random() * 5).toFixed(1); // 4px .. 9px
                          const animDelay = (i * 0.02).toFixed(2);

                          return `
        <div class="tch-card"
             style="--tch-rot:rotate(${rot}deg); animation-delay:${animDelay}s;"
             title="${tech.name}">
          <span class="tch-tooltip">${tech.name}</span>
          <div class="tch-float-inner" style="--tch-dur:${dur}s; --tch-delay:${delay}s; --tch-amp:-${amp}px;">
            <i class="${tech.icon}"></i>
          </div>
        </div>`;
                      })
                      .join("");
              }

              tchRender();

              // Recompute on resize (debounced) so the stage always stays exactly filled.
              let tchResizeTimer;
              window.addEventListener("resize", () => {
                  clearTimeout(tchResizeTimer);
                  tchResizeTimer = setTimeout(tchRender, 180);
              });
          })();
      </script> --}}
    </div>

    {{-- ============ PTN SECTION (technology partners — no matching Laravel section, kept as demo design) ============ --}}
    <div class="ptn-scope">
        <section class="ptn-partners-section">
            <div class="container">
                <p class="ptn-partners-label">Technology Partners</p>

                <div class="ptn-partners-panel">
                    <div class="ptn-partners-row">

                        <!-- Xano -->
                        <div class="ptn-partner-item">
                            <div class="ptn-xano-badge">
                                <div class="ptn-stripe"></div>
                                <div class="ptn-xano-body">
                                    <div class="ptn-xano-title">XANO</div>
                                    <div class="ptn-xano-sub">OFFICIAL <span>PARTNER</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Cloud -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-gcloud-icon">
                                <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M32.6 18.3l1.6-6.2a1 1 0 00-.3-1A15.9 15.9 0 0024 6c-6 0-11.2 3.2-14 8l6.7 5.4c1.4-4 5-6.4 9-6.4 2.5 0 4.7.9 6.4 2.3.4.3.9.4 1.3.0z"
                                        fill="#EA4335" />
                                    <path
                                        d="M8 32c-1.4-2.4-2.2-5.1-2.2-8 0-2.7.7-5.3 1.9-7.6l6.7 5.4c-.5 1-.8 2-.8 3.2 0 1.1.3 2.2.8 3.1z"
                                        fill="#FBBC05" />
                                    <path
                                        d="M24 44c5.2 0 9.7-1.9 12.9-5.1l-6.3-5.2c-1.7 1.2-3.9 2-6.6 2-4 0-7.4-2.4-8.9-6.4l-6.7 5.4C11 40.6 17 44 24 44z"
                                        fill="#34A853" />
                                    <path
                                        d="M42.7 24.3c0-1.4-.1-2.7-.4-4H24v8h10.6c-.4 2.2-1.7 4-3.5 5.3l6.3 5.2C40.9 35.6 42.7 30.4 42.7 24.3z"
                                        fill="#4285F4" />
                                </svg>
                            </div>
                            <span class="ptn-partner-name">Google Cloud<br><span class="ptn-light"
                                    style="font-size:1.1rem; font-weight:700; color:#3d4148;">Partner</span></span>
                        </div>

                        <!-- weweb -->
                        <div class="ptn-partner-item">
                            <span class="ptn-weweb-word">weweb</span>
                        </div>

                        <!-- Google Workspace -->
                        <div class="ptn-partner-item">
                            <div>
                                <span class="ptn-gws-title"><b style="color:#4285F4;">G</b><b
                                        style="color:#EA4335;">o</b><b style="color:#FBBC05;">o</b><b
                                        style="color:#4285F4;">g</b><b style="color:#34A853;">l</b><b
                                        style="color:#EA4335;">e</b> Workspace</span>
                                <span class="ptn-gws-partner">Partner</span>
                            </div>
                        </div>

                        <!-- JioCloud -->
                        <div class="ptn-partner-item">
                            <div class="ptn-partner-icon ptn-jio-icon">
                                <i class="fa-solid fa-cloud"></i>
                            </div>
                            <span class="ptn-jio-name">JioCloud</span>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ============ TST SECTION (testimonials) — wired to $testimonials / $section_testimonials ============ --}}
    <div class="tst-scope">
        @php
            $section_testimonials = \App\Models\Section::section('testimonials');
        @endphp

        @if (count($testimonials) > 0 && isset($section_testimonials))
            <section class="tst-testimonial-section">
                <div class="container">

                    <div class="tst-carousel-viewport">
                        <div class="tst-carousel-track" id="tstCarouselTrack">

                            @foreach ($testimonials as $testimonial)
                                <div class="tst-testi-card">
                                    <div class="tst-testi-stars">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                            class="fa-solid fa-star"></i>
                                    </div>
                                    <p class="tst-testi-quote">{!! $testimonial->description !!}</p>
                                    <div class="tst-testi-footer">
                                        <div class="tst-testi-avatar">
                                            @if (isset($testimonial->image_path))
                                                <img src="{{ asset('uploads/testimonial/' . $testimonial->image_path) }}"
                                                    alt="{{ $testimonial->title }}" loading="lazy"
                                                    style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                                            @else
                                                {{ strtoupper(substr($testimonial->title, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="tst-testi-name">{{ $testimonial->title }}</div>
                                            <div class="tst-testi-role">{{ $testimonial->designation }}@if (isset($testimonial->organization))
                                                    , {{ $testimonial->organization }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="tst-carousel-nav">
                        <button class="tst-nav-btn" id="tstPrevBtn" aria-label="Previous"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button class="tst-nav-btn" id="tstNextBtn" aria-label="Next"><i
                                class="fa-solid fa-chevron-right"></i></button>
                    </div>

                </div>
            </section>

            <script>
                const track = document.getElementById('tstCarouselTrack');
                const cards = Array.from(track.children);
                const tstPrevBtn = document.getElementById('tstPrevBtn');
                const tstNextBtn = document.getElementById('tstNextBtn');

                let currentIndex = 0;

                function getVisibleCount() {
                    const w = window.innerWidth;
                    if (w <= 575.98) return 1;
                    if (w <= 991.98) return 2;
                    return 3;
                }

                function getGap() {
                    return parseFloat(getComputedStyle(track).gap) || 24;
                }

                function update() {
                    const visible = getVisibleCount();
                    const maxIndex = Math.max(0, cards.length - visible);
                    currentIndex = Math.min(currentIndex, maxIndex);

                    const cardWidth = cards[0].getBoundingClientRect().width;
                    const gap = getGap();
                    const offset = currentIndex * (cardWidth + gap);

                    track.style.transform = `translateX(-${offset}px)`;

                    tstPrevBtn.disabled = currentIndex === 0;
                    tstNextBtn.disabled = currentIndex >= maxIndex;
                    tstNextBtn.classList.toggle('tst-active-next', currentIndex < maxIndex);
                }

                tstPrevBtn.addEventListener('click', () => {
                    currentIndex = Math.max(0, currentIndex - 1);
                    update();
                });

                tstNextBtn.addEventListener('click', () => {
                    const visible = getVisibleCount();
                    const maxIndex = Math.max(0, cards.length - visible);
                    currentIndex = Math.min(maxIndex, currentIndex + 1);
                    update();
                });

                window.addEventListener('resize', update);
                window.addEventListener('load', update);
                update();
            </script>
        @endif
    </div>

    {{-- ============ CLI SECTION (client logos — no matching Laravel section, kept as demo design) ============ --}}
    <div class="cli-scope">
        <section class="cli-section">
            <div class="container-fluid px-0">
                <p class="cli-eyebrow">Valued by Clients Worldwide</p>

                <div class="cli-track-wrap" id="cliWrap">
                    <div class="cli-track" id="cliTrack">
                        <!-- Set 1 -->
                        <div class="cli-card">
                            <span class="cli-text"
                                style="font-family:&quot;Brush Script MT&quot;, cursive; font-size:26px;">abc carpet
                                &amp; home</span>
                        </div>
                        <div class="cli-card">
                            <svg viewBox="0 0 160 60" width="140" height="52" xmlns="http://www.w3.org/2000/svg">
                                <rect width="160" height="60" fill="#1560E8" />
                                <circle cx="34" cy="30" r="16" fill="none" stroke="#fff"
                                    stroke-width="4" />
                                <path d="M34 18 a12 12 0 0 1 0 24" fill="none" stroke="#fff" stroke-width="4" />
                                <text x="60" y="26" fill="#fff" font-family="Arial, sans-serif" font-size="15"
                                    font-weight="700">
                                    Choice
                                </text>
                                <text x="60" y="42" fill="#fff" font-family="Arial, sans-serif" font-size="15"
                                    font-weight="700">
                                    Digital
                                </text>
                            </svg>
                        </div>
                        <div class="cli-card">
                            <svg viewBox="0 0 220 50" width="190" height="44" xmlns="http://www.w3.org/2000/svg"
                                font-family="Arial, sans-serif" font-weight="700" font-size="30">
                                <text x="0" y="36" fill="#F5811F">✳</text>
                                <text x="24" y="36" fill="#4a4a4a">worx</text>
                                <circle cx="132" cy="24" r="11" fill="#5FA023" />
                                <text x="150" y="36" fill="#1A6FE0">go</text>
                            </svg>
                        </div>
                        <div class="cli-card">
                            <span class="cli-text" style="font-weight:700; letter-spacing:0.05em;">SVS</span>
                        </div>
                        <div class="cli-card">
                            <span class="cli-text" style="font-weight:600;">Nordic&nbsp;Home</span>
                        </div>
                        <div class="cli-card">
                            <span class="cli-text" style="font-weight:600;">Lumen&nbsp;Studio</span>
                        </div>

                        <!-- Set 2 (duplicate for seamless loop) -->
                        <div class="cli-card" aria-hidden="true">
                            <span class="cli-text"
                                style="font-family:&quot;Brush Script MT&quot;, cursive; font-size:26px;">abc carpet
                                &amp; home</span>
                        </div>
                        <div class="cli-card" aria-hidden="true">
                            <svg viewBox="0 0 160 60" width="140" height="52" xmlns="http://www.w3.org/2000/svg">
                                <rect width="160" height="60" fill="#1560E8" />
                                <circle cx="34" cy="30" r="16" fill="none" stroke="#fff"
                                    stroke-width="4" />
                                <path d="M34 18 a12 12 0 0 1 0 24" fill="none" stroke="#fff" stroke-width="4" />
                                <text x="60" y="26" fill="#fff" font-family="Arial, sans-serif" font-size="15"
                                    font-weight="700">
                                    Choice
                                </text>
                                <text x="60" y="42" fill="#fff" font-family="Arial, sans-serif" font-size="15"
                                    font-weight="700">
                                    Digital
                                </text>
                            </svg>
                        </div>
                        <div class="cli-card" aria-hidden="true">
                            <svg viewBox="0 0 220 50" width="190" height="44" xmlns="http://www.w3.org/2000/svg"
                                font-family="Arial, sans-serif" font-weight="700" font-size="30">
                                <text x="0" y="36" fill="#F5811F">✳</text>
                                <text x="24" y="36" fill="#4a4a4a">worx</text>
                                <circle cx="132" cy="24" r="11" fill="#5FA023" />
                                <text x="150" y="36" fill="#1A6FE0">go</text>
                            </svg>
                        </div>
                        <div class="cli-card" aria-hidden="true">
                            <span class="cli-text" style="font-weight:700; letter-spacing:0.05em;">SVS</span>
                        </div>
                        <div class="cli-card" aria-hidden="true">
                            <span class="cli-text" style="font-weight:600;">Nordic&nbsp;Home</span>
                        </div>
                        <div class="cli-card" aria-hidden="true">
                            <span class="cli-text" style="font-weight:600;">Lumen&nbsp;Studio</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function() {
                const cliWrap = document.getElementById("cliWrap");
                const cliTrack = document.getElementById("cliTrack");

                // If you swap the markup for a single set of real <img> logos, this will
                // auto-duplicate it once so the loop stays seamless.
                if (!cliTrack.querySelector('[aria-hidden="true"]')) {
                    const clone = cliTrack.innerHTML;
                    cliTrack.innerHTML += clone;
                    [...cliTrack.children]
                    .slice(cliTrack.children.length / 2)
                        .forEach((el) => el.setAttribute("aria-hidden", "true"));
                }

                let cliSingleSetWidth = 0;

                function cliMeasure() {
                    // width of ONE set = half of total scrollWidth (since content is duplicated)
                    cliSingleSetWidth = cliTrack.scrollWidth / 2;
                }
                cliMeasure();
                window.addEventListener("resize", cliMeasure);

                const CLI_SPEED = 0.6; // px per frame auto-scroll speed
                let cliOffset = 0; // current translateX distance (positive = scrolled left)
                let cliIsDragging = false;
                let cliDragStartX = 0;
                let cliDragStartOffset = 0;
                let cliIsPaused = false; // paused on hover/pointer-down, resumes on release
                let cliRafId = null;

                function cliApplyTransform() {
                    // wrap offset into [0, singleSetWidth) for a seamless infinite loop
                    let wrapped = cliOffset % cliSingleSetWidth;
                    if (wrapped < 0) wrapped += cliSingleSetWidth;
                    cliTrack.style.transform = `translateX(${-wrapped}px)`;
                }

                function cliTick() {
                    if (!cliIsDragging && !cliIsPaused) {
                        cliOffset += CLI_SPEED;
                        cliApplyTransform();
                    }
                    cliRafId = requestAnimationFrame(cliTick);
                }
                cliRafId = requestAnimationFrame(cliTick);

                // Pause auto-scroll while the pointer is hovering (keeps things calm to inspect/drag)
                cliWrap.addEventListener("mouseenter", () => {
                    cliIsPaused = true;
                });
                cliWrap.addEventListener("mouseleave", () => {
                    if (!cliIsDragging) cliIsPaused = false;
                });

                function cliStartDrag(clientX) {
                    cliIsDragging = true;
                    cliIsPaused = true;
                    cliDragStartX = clientX;
                    cliDragStartOffset = cliOffset;
                    cliWrap.classList.add("cli-is-dragging");
                }

                function cliMoveDrag(clientX) {
                    if (!cliIsDragging) return;
                    const delta = clientX - cliDragStartX;
                    cliOffset = cliDragStartOffset - delta; // drag right -> content moves right -> offset decreases
                    cliApplyTransform();
                }

                function cliEndDrag() {
                    if (!cliIsDragging) return;
                    cliIsDragging = false;
                    cliWrap.classList.remove("cli-is-dragging");
                    // resume auto-scroll shortly after release
                    setTimeout(() => {
                        cliIsPaused = false;
                    }, 300);
                }

                // Mouse events
                cliWrap.addEventListener("mousedown", (e) => {
                    cliStartDrag(e.clientX);
                    e.preventDefault();
                });
                window.addEventListener("mousemove", (e) => cliMoveDrag(e.clientX));
                window.addEventListener("mouseup", cliEndDrag);

                // Touch events
                cliWrap.addEventListener(
                    "touchstart",
                    (e) => cliStartDrag(e.touches[0].clientX), {
                        passive: true
                    },
                );
                cliWrap.addEventListener(
                    "touchmove",
                    (e) => cliMoveDrag(e.touches[0].clientX), {
                        passive: true
                    },
                );
                cliWrap.addEventListener("touchend", cliEndDrag);
                cliWrap.addEventListener("touchcancel", cliEndDrag);

                // Prevent native image/element drag ghost
                cliTrack.addEventListener("dragstart", (e) => e.preventDefault());
            })();
        </script>
    </div>

    {{-- ============ CTA SECTION (Unico Difference + closing CTA — no matching Laravel section, kept as demo design) ============ --}}
    <div class="cta-scope">
        <section class="cta-unico-section">
            <div class="container">
                <h1 class="cta-unico-heading">The Unico Difference</h1>

                <div class="cta-hero-img-wrap">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1600&auto=format&fit=crop"
                        alt="Team celebrating together">
                </div>

                <div class="row">
                    <div class="col-6 col-md-3 cta-feature-col">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                        </div>
                        <h3 class="cta-feature-title">AI-Native Efficiency</h3>
                        <p class="cta-feature-text">Nearly 80% of our code is AI-generated with Claude Code, and every
                            line is reviewed by our engineers. Our team works with AI across the entire delivery
                            lifecycle — from code generation and review to testing and documentation.</p>
                    </div>

                    <div class="col-6 col-md-3 cta-feature-col">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l1.6 4.7L18 9l-4.4 1.3L12 15l-1.6-4.7L6 9l4.4-1.3L12 3z"></path>
                                <path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15z"></path>
                            </svg>
                        </div>
                        <h3 class="cta-feature-title">Loved by Clients</h3>
                        <p class="cta-feature-text">Highly rated across review platforms including Clutch, DesignRush,
                            and GoodFirms. Clients return, refer, and stay — because we treat every engagement as a
                            long-term partnership, not a one-time transaction.</p>
                    </div>

                    <div class="col-6 col-md-3 cta-feature-col">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M2 21v-1a7 7 0 0 1 7-7h0"></path>
                                <path d="M16 11l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="cta-feature-title">Versatile Team</h3>
                        <p class="cta-feature-text">Our team has evolved from traditional engineering through no-code
                            and low-code to AI-native development. Existing members have upskilled through structured AI
                            training, and new hires bring hands-on AI fluency from day one.</p>
                    </div>

                    <div class="col-6 col-md-3 cta-feature-col">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l8 3v6c0 5-3.4 8.5-8 11-4.6-2.5-8-6-8-11V5l8-3z"></path>
                                <path d="M9 12l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="cta-feature-title">Outcomes Over Output</h3>
                        <p class="cta-feature-text">We do not measure success by hours logged or features shipped. Every
                            engagement is structured around achieving your business objectives — and every AI
                            consultation ends with a working prototype, not a strategy deck.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-cta-section">
            <h2 class="cta-cta-heading">
                <span class="cta-line1">Ready to explore what AI can do</span><br>
                <span class="cta-line2">for your business?</span>
            </h2>
            <a href="{{ route('get-quote') }}" class="cta-cta-btn">
                Talk to an Expert
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

@endsection
