<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('top_meta_tags')
    @yield('social_meta_tags')
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('web/css/msn-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/contact.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css" integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  </head>
<body>

@php
  $servicesMenu = $servicesMenu ?? [
      [
          'name' => 'Web Development',
          'slug' => 'web-development',
          'blurb' => 'Custom sites & platforms',
          'children' => [
              ['name' => 'Custom Web Applications', 'slug' => 'custom-web-applications'],
              ['name' => 'E-commerce Development', 'slug' => 'ecommerce-development'],
              ['name' => 'CMS & Headless CMS', 'slug' => 'cms-development'],
              ['name' => 'API Integration', 'slug' => 'api-integration'],
              ['name' => 'Progressive Web Apps', 'slug' => 'progressive-web-apps'],
              ['name' => 'Website Maintenance', 'slug' => 'website-maintenance'],
              ['name' => 'Performance Optimization', 'slug' => 'performance-optimization'],
              ['name' => 'Third-Party Integrations', 'slug' => 'third-party-integrations'],
          ],
      ],
      [
          'name' => 'Mobile App Development',
          'slug' => 'mobile-app-development',
          'blurb' => 'iOS, Android & cross-platform',
          'children' => [
              ['name' => 'iOS App Development', 'slug' => 'ios-app-development'],
              ['name' => 'Android App Development', 'slug' => 'android-app-development'],
              ['name' => 'Cross-platform (Flutter / RN)', 'slug' => 'cross-platform-apps'],
          ],
      ],
      [
          'name' => 'UI/UX Design',
          'slug' => 'ui-ux-design',
          'blurb' => 'Research-led product design',
          'children' => [
              ['name' => 'Product Design', 'slug' => 'product-design'],
              ['name' => 'Wireframing & Prototyping', 'slug' => 'wireframing-prototyping'],
              ['name' => 'Design Systems', 'slug' => 'design-systems'],
          ],
      ],
      [
          'name' => 'Cloud & DevOps',
          'slug' => 'cloud-devops',
          'blurb' => 'Ship faster, run reliably',
          'children' => [
              ['name' => 'Cloud Migration', 'slug' => 'cloud-migration'],
              ['name' => 'CI/CD Pipelines', 'slug' => 'ci-cd-pipelines'],
              ['name' => 'Infrastructure Management', 'slug' => 'infrastructure-management'],
          ],
      ],
      [
          'name' => 'Digital Marketing',
          'slug' => 'digital-marketing',
          'blurb' => 'Grow the right way',
          'children' => [
              ['name' => 'SEO', 'slug' => 'seo'],
              ['name' => 'Social Media Marketing', 'slug' => 'social-media-marketing'],
              ['name' => 'Content Strategy', 'slug' => 'content-strategy'],
          ],
      ],
  ];
@endphp

<nav id="msn-nav">
  <div class="msn-nav-row">
    <a href="{{ route('home') }}" class="msn-nav-logo">
      <img src="{{ asset('/uploads/setting/' . ($setting->logo_path ?? '')) }}" alt="{{ $setting->title ?? 'MSN Softtech' }}">
    </a>

    <ul class="msn-nav-links">
      <li><a href="{{ route('home') }}">Home</a></li>
      <li><a href="{{ route('about') ?? '#' }}">About</a></li>

      <li class="has-drop">
        <a href="{{ route('services') }}">
          Services
          <svg class="msn-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </a>

        {{-- MEGA MENU --}}
        <div class="msn-dropdown msn-mega" data-mega>
          <div class="msn-mega-inner">
            <div class="msn-mega-left">
              @foreach($servicesMenu as $i => $service)
                <button
                  type="button"
                  class="msn-mega-item{{ $i === 0 ? ' is-active' : '' }}"
                  data-panel-target="panel-{{ $service['slug'] }}"
                >
                  {{ $service['name'] }}
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                </button>
              @endforeach
              <a href="{{ route('services') }}" class="msn-mega-item" style="margin-top:8px;border-top:1px solid var(--msn-nav-border);border-radius:0;padding-top:14px;color:black;">
                View all services →
              </a>
            </div>

            <div class="msn-mega-right">
              @foreach($servicesMenu as $i => $service)
                <div class="msn-mega-panel{{ $i === 0 ? ' is-active' : '' }}" id="panel-{{ $service['slug'] }}">
                  <span class="msn-mega-panel-title">{{ $service['name'] }} — {{ $service['blurb'] }}</span>
                  <div class="msn-mega-panel-links">
                    @foreach($service['children'] as $child)
                      <a href="{{ route('services') }}#{{ $child['slug'] }}">{{ $child['name'] }}</a>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </li>

      <li><a href="{{ route('portfolios') }}">Work</a></li>
      <li><a href="{{ route('blogs') }}">Blog</a></li>
      <li><a href="{{ route('contact') ?? '#' }}">Contact</a></li>
    </ul>

    <div class="msn-nav-actions">
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->phone_two ?? '') }}"
   target="_blank"
   class="msn-nav-phone">

    <i class="bi bi-whatsapp whatsapp-icon"></i>

    {{ $setting->phone_two ?? '' }}
</a>

    <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary msn-btn-sm">
        Get a Quote
    </a>

    <button type="button" class="msn-burger" id="msnBurger" aria-label="Toggle menu" aria-expanded="false" aria-controls="msnMobilePanel">
        <span></span><span></span><span></span>
    </button>
</div>
  </div>

  {{-- Backdrop (mobile only) --}}
  <div class="msn-mobile-backdrop" id="msnMobileBackdrop"></div>

  {{-- Slide-in mobile panel (mobile only, hidden entirely on desktop) --}}
  <div class="msn-mobile-panel" id="msnMobilePanel">
    <div class="msn-mobile-panel-inner">
      <ul class="msn-mobile-nav-list">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('about') ?? '#' }}">About</a></li>

        <li class="has-drop">
          <button type="button" class="msn-mobile-toggle" aria-expanded="false">
            Services
            <svg class="msn-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="msn-mobile-sub">
            <a href="{{ route('services') }}">All Services</a>
            <a href="{{ route('technologies') }}">Technologies</a>
            <a href="{{ route('portfolios') }}">Portfolio</a>

            @foreach($servicesMenu as $service)
              <div class="msn-mobile-service-group">
                <button type="button" class="msn-mobile-service-toggle">
                  {{ $service['name'] }}
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="msn-mobile-service-children">
                  @foreach($service['children'] as $child)
                    <a href="{{ route('services') }}#{{ $child['slug'] }}">{{ $child['name'] }}</a>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </li>

        <li><a href="{{ route('portfolios') }}">Work</a></li>
        <li><a href="{{ route('blogs') }}">Blog</a></li>
        <li><a href="{{ route('contact') ?? '#' }}">Contact</a></li>
      </ul>
      <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary">Get a Quote →</a>
    </div>
  </div>
</nav>

@yield('content')


{{-- =====================================================================
     FOOTER — web/inc/footer.blade.php (unchanged)
     ===================================================================== --}}
<footer id="msn-footer">

  <div class="msn-footer-cta">
    <div class="container">
      <span class="msn-eyebrow msn-eyebrow-on-dark">Let's Talk</span>
      <h3>Have a project in mind? Let's build it right the first time.</h3>
      <p>Tell us what you're trying to ship — we'll reply with a clear scope, timeline and a real quote, not a sales pitch.</p>
      <a href="{{ route('get-quote') }}" class="msn-btn msn-btn-primary">Start a Project →</a>
    </div>
  </div>

  <div class="msn-footer-main">
    <div class="container">
      <div class="msn-footer-grid">

        <div class="msn-footer-brand">
          <img src="{{ asset('/uploads/setting/' . ($setting->logo_path ?? '')) }}" alt="{{ $setting->title ?? 'MSN Softtech' }}">
          <p>{!! str_limit(strip_tags($setting->description ?? ''), 150, ' ...') !!}</p>
          <div class="msn-footer-social">
            @if(isset($setting->facebook))
              <a href="{{ $setting->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(isset($setting->twitter))
              <a href="{{ $setting->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
            @endif
            @if(isset($setting->linkedin))
              <a href="{{ $setting->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            @endif
            @if(isset($setting->instagram))
              <a href="{{ $setting->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
            @endif
          </div>
        </div>

        <div class="msn-footer-col">
          <h6>Company</h6>
          <ul>
            <li><a href="{{ route('about') ?? '#' }}">About Us</a></li>
            <li><a href="{{ route('portfolios') }}">Portfolio</a></li>
            <li><a href="{{ route('blogs') }}">Blog</a></li>
            <li><a href="{{ route('contact') ?? '#' }}">Contact</a></li>
          </ul>
        </div>

        <div class="msn-footer-col">
          <h6>Services</h6>
          <ul>
            <li><a href="{{ route('services') }}">Web Development</a></li>
            <li><a href="{{ route('technologies') }}">Technologies</a></li>
            <li><a href="{{ route('get-quote') }}">Get a Quote</a></li>
          </ul>
        </div>

        <div class="msn-footer-col">
          <h6>Contact</h6>
          <ul class="msn-footer-contact">
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <span>{{ $setting->phone_two ?? '' }}</span>
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
              <span>{{ $setting->email ?? '' }}</span>
            </li>
            @if(isset($setting->address))
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>{{ $setting->address }}</span>
              </li>
            @endif
          </ul>
        </div>

      </div>
    </div>
  </div>

  <div class="container">
    <div class="msn-footer-bottom">
      <p>© {{ date('Y') }} {{ $setting->title ?? 'MSN Softtech' }}. All rights reserved.</p>
      <ul class="msn-footer-legal">
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Service</a></li>
      </ul>
    </div>
  </div>
</footer>

<script>
  /* Site-wide .msn-reveal scroll animation */
  (function(){
    if(!('IntersectionObserver' in window)){
      document.querySelectorAll('.msn-reveal').forEach(function(el){ el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.msn-reveal').forEach(function(el){ io.observe(el); });
  })();
</script>

<script>

  (function(){
    var burger   = document.getElementById('msnBurger');
    var panel    = document.getElementById('msnMobilePanel');
    var backdrop = document.getElementById('msnMobileBackdrop');
    var body     = document.body;

    function openMobile(){
      burger.classList.add('is-active');
      burger.setAttribute('aria-expanded', 'true');
      panel.classList.add('is-open');
      backdrop.classList.add('is-open');
      body.classList.add('msn-nav-open');
    }
    function closeMobile(){
      burger.classList.remove('is-active');
      burger.setAttribute('aria-expanded', 'false');
      panel.classList.remove('is-open');
      backdrop.classList.remove('is-open');
      body.classList.remove('msn-nav-open');
    }

    if(burger && panel && backdrop){
      burger.addEventListener('click', function(){
        panel.classList.contains('is-open') ? closeMobile() : openMobile();
      });
      backdrop.addEventListener('click', closeMobile);
      document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') closeMobile();
      });
      window.addEventListener('resize', function(){
        if(window.innerWidth >= 992) closeMobile();
      });
    }

    // Mobile: Services accordion (level 1)
    document.querySelectorAll('.msn-mobile-toggle').forEach(function(t){
      t.addEventListener('click', function(){
        var li = t.closest('li');
        var expanding = !li.classList.contains('is-open');
        li.classList.toggle('is-open', expanding);
        t.setAttribute('aria-expanded', expanding ? 'true' : 'false');
      });
    });

    // Mobile: per-service accordion (level 2)
    document.querySelectorAll('.msn-mobile-service-toggle').forEach(function(t){
      t.addEventListener('click', function(){
        t.closest('.msn-mobile-service-group').classList.toggle('is-open');
      });
    });

    // Desktop: mega menu panel switching
    document.querySelectorAll('[data-mega]').forEach(function(mega){
      var items  = mega.querySelectorAll('.msn-mega-item[data-panel-target]');
      var panels = mega.querySelectorAll('.msn-mega-panel');

      function activate(targetId){
        items.forEach(function(i){
          i.classList.toggle('is-active', i.dataset.panelTarget === targetId);
        });
        panels.forEach(function(p){
          p.classList.toggle('is-active', p.id === targetId);
        });
      }

      items.forEach(function(item){
        item.addEventListener('mouseenter', function(){ activate(item.dataset.panelTarget); });
        item.addEventListener('focus', function(){ activate(item.dataset.panelTarget); });
        item.addEventListener('click', function(e){ e.preventDefault(); activate(item.dataset.panelTarget); });
      });
    });

    // Touch devices: tapping "Services" toggles the mega menu open/closed
    var servicesTrigger = document.querySelector('#msn-nav .has-drop > a[href*="service"]');
    if(servicesTrigger){
      servicesTrigger.addEventListener('click', function(e){
        if(window.matchMedia('(hover: none)').matches){
          var mega = servicesTrigger.parentElement.querySelector('.msn-dropdown.msn-mega');
          if(mega && !mega.classList.contains('is-open')){
            e.preventDefault();
            document.querySelectorAll('.msn-dropdown.msn-mega.is-open').forEach(function(m){ m.classList.remove('is-open'); });
            mega.classList.add('is-open');
          }
        }
      });
      document.addEventListener('click', function(e){
        if(!servicesTrigger.parentElement.contains(e.target)){
          document.querySelectorAll('.msn-dropdown.msn-mega.is-open').forEach(function(m){ m.classList.remove('is-open'); });
        }
      });
    }
  })();
</script>
@yield('scriptjs')
</body>
</html>


